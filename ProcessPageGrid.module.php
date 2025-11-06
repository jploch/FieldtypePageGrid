<?php

/**
 * PageGrid for ProcessWire
 * 
 * Copyright (C) 2023 by Jan Ploch
 * THIS IS A COMMERCIAL MODULE - DO NOT DISTRIBUTE
 */

class ProcessPageGrid extends Process {


    public static function getModuleinfo() {
        return [
            'title' => 'PageGrid Process',
            'summary' => 'Process Module for FieldtypePageGrid',
            'author' => 'Jan Ploch',
            'icon' => 'th',
            'requires' => array('FieldtypePageGrid'),
            'installs' => array('FieldtypePageGrid', 'InputfieldPageGrid', 'PageFrontEdit', 'ProcessPageClone'),
            'version' => '0.0.4',
            'useNavJSON' => true,
            'permission' => 'pagegrid-process',
            'page' => [
                'name' => 'pagegrid',
                'parent' => 'setup',
                'title' => 'PAGEGRID',
            ]
        ];
    }

    public function ___executeNavJSON(array $options = array()) {

        // $options['add'] = false;
        // $options['items'] = $this->page->children();
        // $options['edit'] = "";
        // $options['list'] = [];

        $config = $this->wire()->config;
        $urls = $this->wire()->urls;

        $data = array(
            'url' => $urls->admin . 'page/list/navJSON/',
            'label' => '',
            'icon' => 'sitemap',
            'list' => array()
        );

        $data = array_merge($options, $data);

        $items = $this->page->children();

        $parentID = (int) $this->wire()->input->get('parent_id');
        if (!$parentID) $parentID = 1;

        foreach ($items as $page) {
            if (!$page->hasChildren() && $page->name === 'pg-symbols') continue;

            //for multilanguage site title return object with multiple titles
            //always use deafult page title as label
            $label = $page->title;
            if (!is_string($label)) $label = $page->getLanguageValue('default', 'title');

            $a = array(
                'url' => '',
                'id' => $page->id,
                'label' => $label,
                // 'icon' => $page->getIcon(),
                'edit' => false
            );
            $a['navJSON'] = $data['url'] . "?parent_id=$page->id";
            $data['list'][] = $a;
        }

        if ($config->ajax) header("Content-Type: application/json");

        return json_encode($data);

        // return parent::___executeNavJSON($data);
    }



    public function ___execute() {

        if (!$this->config->ajax) {
            //redirect to module settings if setup link is visited
            $moduleSettingsLink = $this->config->urls->admin . 'module/edit?name=FieldtypePageGrid&collapse_info=1';
            $this->session->redirect($moduleSettingsLink, true);
            return ("Request must be via AJAX");
        }

        // $this->log->save("pagegrid", "processing ajax...");

        $pageId = isset($_POST['pageId']) ? $this->sanitizer->int($_POST['pageId']) : '';
        $insertAfter = isset($_POST['insertAfter']) ? $this->sanitizer->int($_POST['insertAfter']) : '';
        $parentId = isset($_POST['parentId']) ? $this->sanitizer->int($_POST['parentId']) : '';
        $templateId = isset($_POST['templateId']) ? $this->sanitizer->text($_POST['templateId']) : '';
        $pgField = isset($_POST['pgField']) ? $this->sanitizer->text($_POST['pgField']) : '';
        $field_name = isset($_POST['field_name']) ? $_POST['field_name'] : '';
        $clone = isset($_POST['clone']) ? $_POST['clone'] : '';
        $removeId = isset($_POST['removeId']) ? $this->sanitizer->text($_POST['removeId']) : '';
        $mainPageId = isset($_POST['mainPageId']) ? $this->sanitizer->int($_POST['mainPageId']) : '';
        $data = isset($_POST['data']) ? $_POST['data'] : '';
        $type = isset($_POST['type']) ? $_POST['type'] : '';
        $getToolTip = isset($_POST['getToolTip']) ? $_POST['getToolTip'] : '';

        // function to get tooltip help text from MDN CSS docs
        if ($getToolTip) {
            $url = "https://developer.mozilla.org/en-US/docs/Web/CSS/$getToolTip/index.json";
            if ($getToolTip == 'scale' || $getToolTip == 'rotate' || $getToolTip == 'translate') $url = "https://developer.mozilla.org/en-US/docs/Web/CSS/transform-function/$getToolTip/index.json";
            $headers = get_headers($url);
            $urlExists = stripos($headers[0], "200 OK") ? true : false;
            if (!$urlExists) return '';
            $json = file_get_contents($url);
            if (!$json) return '';
            $obj = json_decode($json);
            if ($obj && $obj->doc && $obj->doc->summary) return $obj->doc->summary;
            return '';
        }

        if ($type === 'updateAnimation') {
            $p = $this->pages->get($pageId);
            if (!$p || !$p->id) return;
            $animationData = $this->modules->get('InputfieldPageGrid')->scripts($p, true);
            return $animationData;
        }

        // lock/unlock page
        if ($type === 'lock') {
            $data = json_decode($data, true);

            if (isset($pageId) && $pageId != 0) {
                $settingsPage = $this->pages->get($pageId);
            } else {
                return false;
            }

            if ($data == 1) {
                // // // // bd('totalLock');
                $settingsPage->addStatus(Page::statusLocked);
            } else {
                $settingsPage->removeStatus(Page::statusLocked);
            }

            $settingsPage->meta()->set('pg_lock', $data);
            $settingsPage->save();
        }

        // save item settings
        if ($type === 'save') {

            if (empty($data)) {
                return false;
            }
            if (isset($pageId) && $pageId != 0) {
                $settingsPage = $this->pages->get($pageId);
            } else {
                return false;
            }

            // get data 
            $dataItem = json_decode($data, true);

            if (!isset($dataItem) || empty($dataItem)) {
                return false;
            }

            //create page for class if not already
            if ($settingsPage->name == 'pg-classes' || $settingsPage->name == 'pg-animations') {
                $parent = $settingsPage;
                $parentID = $parent->id;
                $className = $dataItem['cssClass'];
                $classNameSanitized = $this->sanitizer->pageName($className, true);
                $settingsPage = $this->pages->get("name=$classNameSanitized, template=pg_container, parent=$parentID");

                if ($settingsPage && $settingsPage->id) {
                } else {
                    $settingsPage = new Page(); // create new page object
                    $settingsPage->template = 'pg_container'; // set template
                    $settingsPage->parent = $parentID; // set the parent
                    $settingsPage->name = $classNameSanitized; // give it a name used in the url for the page
                    $settingsPage->title = $className; // set page title (not neccessary but recommended)
                    // $settingsPage->addStatus(Page::statusHidden);
                    $settingsPage->save();
                }
            }

            $dataArray = $settingsPage->meta()->pg_styles;

            if (isset($dataArray) == 0) {
                $dataArray = [];
            }

            if (!isset($dataItem['id'])) {
                return false;
            }

            //add item data
            $dataArray[$dataItem['id']] = $dataItem;

            //remove itemdata if empty
            // if (count($dataItem['breakpoints']) == 1 && count($dataItem['breakpoints']['base']['css']) == 0) {
            //     unset($dataArray[$dataItem['id']]);
            // }

            //remove empty arrays
            $dataArray = array_filter($dataArray);

            //save data to page meta
            $settingsPage->meta()->set('pg_styles', $dataArray);
            // $settingsPage->meta()->set('pg_field', $pgField);
            $settingsPage->meta()->set('pg_page', $mainPageId);
        }
        // END save item settings

        //convert to symbol
        if ($type === 'convertSymbol' && !empty($_POST['pageId'])) {
            $p = $this->pages->get($_POST['pageId']);
            if (!$p || !$p->id) return;
            $isSymbol = isset($_POST['isSymbol']) ? $_POST['isSymbol'] : 0;
            $isSymbol = json_decode($isSymbol, true);
            $sync = isset($_POST['sync']) ? $_POST['sync'] : 1;
            $sync = json_decode($sync, true);

            //if no symbol unlink
            if (!$isSymbol) {
                $originalId = $_POST['originalId'];
                $originalP = $this->pages->get($originalId);
                if (!$originalP || !$originalP->id) return;

                //clone symbol back to page
                $templateName = str_replace('_', '-', $p->template->name);
                $clone = $this->pages->clone($p);
                $clone->title = $templateName . '-' . $clone->id;
                $clone->name = $templateName . '-' . $clone->id;
                $this->pages->insertAfter($clone, $originalP);
                $clone->save();
                $clone->meta()->remove('pg_symbol');

                //remove old original item placeholder
                $originalP->removeStatus(Page::statusLocked);
                $originalP->save();
                $originalP->trash();

                //rename children to prevent duplicated classes
                foreach ($clone->find('') as $cloneItem) {
                    $cloneItem->of(false);
                    $templateName = str_replace('_', '-', $cloneItem->template->name);
                    $cloneItem->title = $templateName . '-' . $cloneItem->id;
                    $cloneItem->name = $templateName . '-' . $cloneItem->id;
                    $cloneItem->save();
                    $cloneItem->of(true);
                }

                $p = $clone;
            } else {
                //create symbol
                $symbolParent = $this->pages->get("name=pg-symbols, template=pg_container");
                $symbolTitle = isset($_POST['name']) ? $_POST['name'] : $p->title;
                $symbolName = $this->sanitizer->pageName($symbolTitle, true);
                $symbolIcon = isset($_POST['icon']) ? $_POST['icon'] : '';

                //clone item page to symbols
                $clone = $this->pages->clone($p);
                $clone->title = $symbolTitle;
                $clone->name = $symbolName . '-' . $p->id;
                $clone->parent = $symbolParent;
                $clone->save();

                //set meta symbol page
                if ($sync) $p->meta()->set('pg_symbol', $clone->id);
                if ($symbolIcon) $clone->meta()->set('pg_icon', $symbolIcon);
                $clone->meta()->set('pg_sync', $sync);
            }

            $css = $this->modules->get('InputfieldPageGrid')->renderStyles($p);

            foreach ($p->find('') as $child) {
                $css .= $this->modules->get('InputfieldPageGrid')->renderStyles($child);
            }

            $newPageClass = $p->name;
            if ($clone && $clone->name && $sync) $newPageClass = $clone->name;

            //get symbol markup for add item bar
            $addBar = $this->modules->get('InputfieldPageGrid')->renderAddItemBar(1);

            $cssFiles = $this->getBlockFiles($p->parent(), 'css');
            $jsFiles = $this->getBlockFiles($p->parent(), 'js');

            $response = array(
                'newPageClass' => $newPageClass,
                'markup' => $this->modules->get('InputfieldPageGrid')->renderItem($p),
                'css' => $css,
                'addBar' => $addBar,
                'cssFiles' => $cssFiles,
                'jsFiles' => $jsFiles
            );

            return (json_encode($response));
        }
        //END convert to symbol

        // change parent
        if (!empty($_POST['newParent'])) {

            // bd('parent change');

            $p = $this->pages->get($_POST['pageId']);
            $insertAfter = $this->pages->get($_POST['insertAfter']);
            $newParent = $this->pages->get($_POST['newParent']);
            if ($p->id && $newParent->id) {
                $top = $newParent->children('sort=sort')->first();
                $p->of(false);
                $p->parent = $newParent;
                $p->save();
                $p->of(true);

                if ($insertAfter->id && $insertAfter->template->name != 'pg_container') {
                    $this->pages->insertBefore($p, $insertAfter);
                } else {
                    if ($top->id) $this->pages->insertBefore($p, $top);
                }
            }
            return;
        }
        // END change parent

        // change sort order of groups, sort must be pipe seperated string
        if (!empty($_POST['sort'])) {

            $returnMarkup = $_POST['returnMarkup'];
            $sort = $_POST['sort'];
            $ids = explode('|', $sort);
            $i = 0;

            foreach ($ids as $id) {
                $i++;
                $p = $this->pages->get($id);
                $this->pages->sort($p, $i);
            }

            // re-build sort values for children of parent, removing duplicates and gaps needed?
            $first = $this->pages->get($ids[0]);
            if ($first->id) {
                $parent = $first->parent();
                $this->pages->sort($parent, true);
                if ($returnMarkup) {
                    $jsFiles = $this->getBlockFiles($parent, 'js');
                    $response = array(
                        'pageId' => $parent->id,
                        'markup' => $this->modules->get('InputfieldPageGrid')->renderItem($parent),
                        'jsFiles' => $jsFiles
                    );
                    return (json_encode($response));
                }
            }
            return 0;
        }
        // END change sort order of groups

        if ($type === 'delete' && !empty($removeId)) {

            //get page, for classes and animations use name and parent selector
            $p = $this->pages->get($removeId);
            if ($parentId) $p = $this->pages->findOne('name=' . $removeId . ', has_parent=' . $parentId);

            if (!$p || !$p->id) return;
            if ($p->template->name == 'admin' || $p->template->name == 'pg_container') return;

            $p->removeStatus(Page::statusLocked);
            $p->save();

            foreach ($p->find('') as $item) {
                $item->removeStatus(Page::statusLocked);
                $item->save();
            }

            $p->trash();
            $this->log->save("pagegrid", "page removed: " . $removeId);
            return;
        }

        if ($type === 'getData') {
            //already JSON encoded
            $globalPageData = $this->modules->get('InputfieldPageGrid')->getData();
            return ($globalPageData);
        }

        if ($type === 'deleteData') {
            $pageName = isset($_POST['pageName']) ? $_POST['pageName'] : '';
            $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : '';
            $settingsPage = $this->pages->get($pageName);

            if (!$settingsPage->id) {
                return false;
            }

            $dataArray = $settingsPage->meta()->pg_styles;

            if (!isset($dataArray) || empty($dataArray)) {
                return false;
            }

            if (!isset($dataArray[$itemId])) {
                return false;
            }

            //remove item data 
            unset($dataArray[$itemId]);

            //remove empty arrays
            $dataArray = array_filter($dataArray);

            //save data to page meta
            $settingsPage->meta()->set('pg_styles', $dataArray);
        }

        if ($type === 'clone' && !empty($_POST['pageId'])) {

            if (!$pageId || $pageId == 0 || $pageId == '0') return false;
            $p = $this->pages->get($pageId);
            if (!$p || !$p->id) return;
            if ($p->template->name == 'admin' || $p->template->name == 'pg_container') return;

            $insertAfter = $this->pages->get($insertAfter);
            $newPages = array();
            $clone = $this->pages->clone($p);

            //insert after different item than clone if copy to another page
            if ($insertAfter->id) {

                if ($insertAfter->template->name == 'pg_container') {
                    $clone->parent = $insertAfter;
                    $clone->save();
                } else {
                    $this->pages->insertAfter($clone, $insertAfter);
                }
            }

            $templateName = str_replace('_', '-', $p->template->name);
            $clone->setAndSave('name', $templateName . '-' . $clone->id);
            $clone->setAndSave('title', $templateName . '-' . $clone->id);

            // $newPages array to keep a refernce to old pages
            $newPages[$p->id] = $clone->id;

            //add css for clone
            $css = $this->modules->get('InputfieldPageGrid')->renderStyles($clone);

            //set page id as meta for children to load data from original via $newPages
            foreach ($p->find('') as $pChild) {
                $pChild->meta()->set('old_id', $pChild->id);
            }

            // rename children, for unique ID
            foreach ($clone->find('') as $cloneChild) {
                $templateName = str_replace('_', '-', $cloneChild->template->name);
                $cloneChild->setAndSave('name', $templateName . '-' . $cloneChild->id);
                $cloneChild->setAndSave('title', $templateName . '-' . $cloneChild->id);
                $newPages[$cloneChild->meta('old_id')] = $cloneChild->id;
                $css .= $this->modules->get('InputfieldPageGrid')->renderStyles($cloneChild);
            }

            $cssFiles = $this->getBlockFiles($clone, 'css');
            $jsFiles = $this->getBlockFiles($clone, 'js');

            $response = array(
                'markup' => $this->modules->get('InputfieldPageGrid')->renderItem($clone),
                'css' => $css,
                'newPages' => $newPages,
                'cssFiles' => $cssFiles,
                'jsFiles' => $jsFiles
            );

            return (json_encode($response));
        }

        if ($type === 'loadBlueprint' && !empty($_POST['pageId'])) {
            $p = $this->pages->get($pageId);
            $blueprintParent = $this->pages->get("name=pg-blueprints, template=pg_container");

            if (!$pageId || !$p || !$p->id || !$blueprintParent || !$blueprintParent->id || $p->template->name === 'admin' || $p->template->name === 'pg_container') {
                return false;
            }

            $blueprintTitle = isset($_POST['name']) ? $_POST['name'] : '';
            $blueprintName = $this->sanitizer->pageName($blueprintTitle, true);
            $blueprint = $this->pages->get("name=$blueprintName, template=pg_blueprint");

            if ($blueprint && $blueprint->id) {
                $blueprintItemsPage = $this->pages->get("pg-$blueprint->id");
                $pItems = $this->pages->get("pg-$p->id");

                if ($pItems && $pItems->id && $blueprintItemsPage && $blueprintItemsPage->id) {
                    //remove old items page
                    $pItems->delete(true);
                    $cloneItems = $this->pages->clone($blueprintItemsPage);
                    $cloneItems->name = 'pg-' . $p->id;
                    $cloneItems->title = $p->title;
                    $cloneItems->save();

                    //rename itms to prevent naming conflicts
                    foreach ($cloneItems->find('') as $clone) {
                        //skip field page containers
                        if ($clone->template->name === 'pg_container') continue;
                        $newName = $clone->template->name . '-' . $clone->id;
                        $clone->setAndSave('name', $newName);
                        $clone->setAndSave('title', $newName);
                    }

                    //return true to reload page
                    return true;
                }
            }
        }


        if ($type === 'add') {
            $template =  $this->templates->get($templateId);
            $parent = $this->pages->get($parentId);
            if (!$parent || !$parent->id) return;
            if (!$template || !$template->id) return;
            if ($template->name == 'admin' || $template->name == 'pg_container') return;

            // create new page
            $p = new Page();
            $p->template = $template->name;
            $p->parent = $parent;
            $p->name = $template->name . time(); // temporary name to pass unique name requirement
            $p->title = $template->name . time(); // temporary name to pass unique name requirement
            $p->save();

            $insertAfter = $this->sanitizer->int($_POST['insertAfter']);
            $replaceParentId = $this->sanitizer->int($_POST['replaceParentId']);

            if (isset($insertAfter) && $insertAfter != 0) {
                $afterP = $this->pages->get($insertAfter);
                if ($afterP->id) $this->pages->insertBefore($p, $afterP);
            }

            //test: sometimes parent is not set correctly after insertAfter, so set it here again
            if ($parent && $parent->id && $p->parent()->id != $parent->id) {
                $p->of(false);
                $p->parent = $parent;
                $p->save();
            }

            // set title after save to get unique id
            $templateName = str_replace('_', '-', $template->name);
            $p->of(false);
            $p->setAndSave('title', $templateName . '-' . $p->id);
            $p->setAndSave('name', $templateName . '-' . $p->id);

            //set the page that will be replaced by the returned markup
            //refactor note: it might make sense to allways just replace parent if not root in the future
            $replacPage = $replaceParentId ? $this->pages->get($replaceParentId) : 0;
            $updatePage = $replacPage && $replacPage->id && $replacPage->template->name != 'pg_container' ? $replacPage : $p;

            //get block CSS and JS files
            $cssFiles = $this->getBlockFiles($updatePage, 'css');
            $jsFiles = $this->getBlockFiles($updatePage, 'js');
            //END get block CSS and JS files

            $response = array(
                'newPageClass' => $p->name,
                'markup' => $this->modules->get('InputfieldPageGrid')->renderItem($updatePage),
                'replaceClass' => $updatePage->name,
                'cssFiles' => $cssFiles,
                'jsFiles' => $jsFiles
            );

            return (json_encode($response));
        }

        if (($type === 'addSymbol' || $type === 'addFromSymbol') && !empty($pageId)) {

            $p = $this->pages->get($pageId);
            if (!$p || !$p->id) return;
            $parent = $this->pages->get($parentId);
            if (!$parent || !$parent->id) return;
            if ($p->template->name == 'admin' || $p->template->name == 'pg_container') return;

            //return if parent is symbol to prevent nested symbols
            if (count($parent->parents('name=pg-symbols, template=pg_container'))) return;

            $clone = $this->pages->clone($p);
            $templateName = str_replace('_', '-', $p->template->name);
            $clone->name = $templateName . '-' . $clone->id; //generate unique name to support multiple symbold on same page
            // $clone->title = $templateName . '-' . $clone->id;
            $clone->title = $p->title;
            $clone->parent = $parent;
            $clone->save();

            $itemData = $clone->meta()->pg_styles ? $clone->meta()->pg_styles : '';

            // bd($parent);

            $insertAfter = $this->sanitizer->int($_POST['insertAfter']);

            if (isset($insertAfter) && $insertAfter != 0) {
                $afterP = $this->pages->get($insertAfter);
                $this->pages->insertBefore($clone, $afterP);
            }

            $newPageClass = $p->name;

            //set meta symbol page
            if ($type == 'addSymbol') $clone->meta()->set('pg_symbol', $p->id);

            //add non-synced symbols/patterns
            $css = '';

            if ($type == 'addFromSymbol') {
                $clone->meta()->remove('pg_symbol');

                $css = $this->modules->get('InputfieldPageGrid')->renderStyles($clone);

                foreach ($clone->find('') as $cloneItem) {
                    $cloneItem->of(false);
                    $templateName = str_replace('_', '-', $cloneItem->template->name);
                    $cloneItem->title = $templateName . '-' . $cloneItem->id;
                    $cloneItem->name = $templateName . '-' . $cloneItem->id;
                    $cloneItem->save();
                    $cloneItem->of(true);
                    $cloneItem->meta()->remove('pg_symbol');
                    $css .= $this->modules->get('InputfieldPageGrid')->renderStyles($cloneItem);
                }
                $newPageClass = $clone->name;
            }

            $replaceClass = $newPageClass;
            if ($type === 'addSymbol') $replaceClass = '';

            $cssFiles = $this->getBlockFiles($clone, 'css');
            $jsFiles = $this->getBlockFiles($clone, 'js');

            $response = array(
                'newPageClass' => $newPageClass,
                'markup' => $this->modules->get('InputfieldPageGrid')->renderItem($clone),
                'pageId' => $clone->id,
                'css' => $css,
                'replaceClass' => $replaceClass,
                'itemData' => $itemData,
                'cssFiles' => $cssFiles,
                'jsFiles' => $jsFiles
            );

            return (json_encode($response));
        }

        // handel uploads to pages
        if ($type === 'upload' && !empty($pageId)) {
            $pageRenderId = isset($_POST['pageRenderId']) ? $this->sanitizer->int($_POST['pageRenderId']) : '';
            $p = $this->pages->get($pageId);
            $pRender = $this->pages->get($pageRenderId);
            $fileRelativePath = $p->filesUrl();
            $extensions = 'jpg jpeg gif png svg';
            $fileField = 0;

            if (!($p->id)) {
                return;
            }

            if ($p->hasField($field_name)) {
                $fileField = $p->$field_name;
            }

            if ($fileField) {
                $fileField->deleteAll();
                $filePath = (string) $fileField->path();
                $f = $this->fields->get($field_name);
                $extensions = $f->extensions ? $f->extensions : '';
            } else {
                // add custom path
                $filePath = $p->filesManager->path;
            }

            $extensionArray = explode(" ", $extensions);
            $u = new WireUpload('preview_name');
            $u->setMaxFiles(1);
            $u->setOverwrite(false);
            //            $filePath = ( string ) $fileField->path();
            $u->setDestinationPath($filePath);
            $u->setValidExtensions($extensionArray);

            // execute upload and check for errors
            $filename = $u->execute();

            //check if upload has errors and is correct file type
            if (!count($filename)) {
                header('HTTP/1.1 500 Internal Server Booboo');
                header('Content-Type: application/json; charset=UTF-8');
                die('Sorry, this file type is not supported! Upload one of these files: ' . $extensions);
                // return false;
            }

            foreach ($u->execute() as $filename) {
                if ($fileField) {
                    $fileField->add($filename);
                }
            }

            // $this->log->save("pagegrid", $filePath);
            //test to save only the field so we don't overwrite other fields wich might have changed since upload
            if ($p->hasField($field_name)) $this->pages->saveField($p, $field_name);
            // $p->save();

            // if (!count($p->parents('pg-items'))) {
            //     $p = $p->parent();
            // }

            // bd($p);

            $markup = $fileRelativePath . $filename;
            if ($pRender->id) $markup = $this->modules->get('InputfieldPageGrid')->renderItem($pRender);

            $p->meta()->set('pg_ajax', true);
            $response = array(
                'newPageClass' => $pRender->name,
                'newChildPageClass' => $p->name,
                'markup' => $markup,
                'message' => ''
            );
            $p->meta()->set('pg_ajax', false);

            // bd($response);

            return (json_encode($response));
        }
        //excude end
    }

    /**
     * Save the page via Ajax for modal and update PageGrid
     * we don't use ProcessPageEdit::ajaxSave() as it doesn't seem to work with language fields
     * so we use some technic to build the form and process it like ProcessPageEdit does with a regular save
     *
     * @return json array with status of save with messages
     */
    public function executeAjaxSave() {

        if (count($_POST)) {

            // // // // bd($_POST);

            $this->pageId = (int) $this->input->post("id");
            $this->replaceId = (int) $this->input->post("replaceId");
            $this->pageContext = $this->pages->get($this->pageId);
            $this->pageContext->setTrackChanges(true); // not sure this is needed, what does it do? Leftover from AutoSave?
            $this->pageEdit = $this->modules->get("ProcessPageEdit");
            $form = $this->buildForm();

            //get old values before processing and save
            $tNameOld = $this->pageContext->template->name;
            $oldF = $this->pageContext->template->fields->get("inputfieldClass=InputfieldTinyMCE");
            $oldValue = '';
            if ($oldF && $oldF->id) {
                $oldFName = $oldF->name;
                $oldValue = $this->pageContext->$oldFName;
            }
            //end get old values before processing and save

            // save
            $this->processInput($form);
            $this->pageContext->save();
            // END save

            //if template change keep old value
            $tNameNew = $this->pageContext->template->name;
            if ($tNameOld != $tNameNew) {
                $newF = $this->pageContext->template->fields->get("inputfieldClass=InputfieldTinyMCE");
                if ($newF && $newF->id && $oldValue) {
                    $oldValue = strip_tags($oldValue, '<br></br><a>');
                    $this->pageContext->setAndSave($newF->name, $oldValue);
                }

                //change tagname back to default
                $itemData = $this->pageContext->meta()->pg_styles;
                if (isset($itemData) && isset($itemData['pgitem'])) {
                    $itemData['pgitem']['tagName'] = 'div';
                    $this->pageContext->meta()->set('pg_styles', $itemData);
                }
            }
            //END if template change keep old value

            if (count($form->getErrors())) $errors = true;
            else $errors = false;

            $message = array();
            if (!$errors) {
                $message['error'] = false;
                $message['message'] = $this->_("Saved");
                // return json_encode($message);
            } else {
                $message['error'] = true;
                $message['message'] = $this->_("Saved but with errors...");
                // get errors for showing and clear them, so they don't stack (true)
                $message['errors'] = $form->getErrors(true);
                // return json_encode($message);
            }

            // return markup
            $p = $this->pageContext;

            //use replaceId if set to have diffrent replace target that saved page
            if ($this->replaceId) {
                $p = $this->pages->get($this->replaceId);
            }

            if ($p->id) {

                //get parent markup to be able to show new children
                $parent = $p->parent();
                if ($parent->template->name !== 'pg_container' && $parent->template->name !== 'admin') {
                    $p = $parent;
                }

                //get block CSS and JS files
                $cssFiles = $this->getBlockFiles($parent, 'css');
                $jsFiles = $this->getBlockFiles($parent, 'js');
                //END get block CSS and JS files

                $p->meta()->set('pg_ajax', true);

                $response = array(
                    'newPageClass' => $p->name,
                    'newChildPageClass' => $this->pageContext->name,
                    'markup' => $this->modules->get('InputfieldPageGrid')->renderItem($p),
                    'message' => $message,
                    'cssFiles' => $cssFiles,
                    'jsFiles' => $jsFiles
                );

                $p->meta()->set('pg_ajax', false);

                return (json_encode($response));
            }
        }
    }


    public function getBlockFileUrl($templateName, $type) {
        //check if block file exists
        $fileUrl = '';
        $sitePath = $this->config->paths->site;
        $siteUrl = $this->config->urls->site;
        $blockFile = 'templates/blocks/' . $templateName . '.' . $type;
        $moduleFile = 'modules/PageGridBlocks/blocks/' . $templateName . '.' . $type;
        if (file_exists($sitePath . $blockFile)) $fileUrl = $siteUrl . $blockFile;
        if (!$fileUrl && file_exists($sitePath . $moduleFile)) $fileUrl = $siteUrl . $moduleFile;

        return $fileUrl;
    }

    //finding all files for $parent (page object) and its children
    public function getBlockFiles($parent, $type) {
        if (!$parent && !$parent->id) return [];

        $files = [];
        $options = $parent->template->pgOptions ? json_decode($parent->template->pgOptions, true) : [];
        $loadScript = isset($options['reloadScript']) && $options['reloadScript'] == false && $type == 'js' ? false : true;
        $fileUrl = $this->getBlockFileUrl($parent->template->name, $type);
        if ($fileUrl && $loadScript) $files[] = $fileUrl;

        foreach ($parent->find('') as $c) {
            $options = $c->template->pgOptions ? json_decode($c->template->pgOptions, true) : [];
            $loadScript = isset($options['reloadScript']) && $options['reloadScript'] == false && $type == 'js' ? false : true;
            $fileUrl = $this->getBlockFileUrl($c->template->name, $type);
            if ($fileUrl && $loadScript && !in_array($fileUrl, $files)) $files[] = $fileUrl;
        }
        return $files;
    }


    /*
     * Borrowed from Fredi front end edit
     *
     * This method is simplified version of /wire/modules/process/ProcessPageEdit.module processInput() method
     *
     * First process the input, then loops through all the form fields, set page field values and finally saves the page
     *
     * If field is wrapper, then iterates (this case only with full page save)
     *
     */
    public function ___processInput(Inputfield $form, $level = 0) {

        $form->setTrackChanges(true);

        if (!$level) $form->processInput($this->input->post);

        // Loop all the elements on the form
        foreach ($form->children as $field) {
            // Process their values
            $field->setTrackChanges(true);

            if (wire('languages') && $field->useLanguages) {
                $v = $this->pageContext->get($field->name);
                if (is_object($v)) {
                    $v->setFromInputfield($field);
                    $this->pageContext->set($field->name, $v);
                    $this->pageContext->trackChange($field->name);
                }
            } else {
                // prevent page status being changed for autosave
                // or it will publish unpublished pages
                if ($field->name == "status") continue;
                $this->pageContext->set($field->name, $field->value);
            }

            if ($field instanceof InputfieldWrapper && count($field->getChildren())) $this->processInput($field, $level + 1);
        }
    }

    /**
     * build the form for saving
     * @return InputfieldWrapper the form with fields
     */
    public function buildForm() {
        $form = $this->modules->get('InputfieldForm');
        $form = $this->pageEdit->buildForm($form);
        return $form;
    }
}
