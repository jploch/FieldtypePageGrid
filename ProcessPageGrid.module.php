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
            'version' => '0.0.1',
            'useNavJSON' => true,
            'permission' => 'pagegrid-process',
            // page that you want created to execute this module
            // page will be at /youradmin/setup/pagegrid/
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
            $a = array(
                'url' => '',
                'id' => $page->id,
                'label' => $page->title,
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

        // $this->log->save("pagegrid", "type: " . $type);

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

                //on demo mode use user as namespace to have unique classnames and animation names
                if ($this->modules->isInstalled('PageGridDemoMode') && $this->user->hasRole('pagegrid-demo')) {
                    $className = $dataItem['cssClass'] . '-' . $this->user->id;
                }

                $settingsPage = $this->pages->get("name=$className, template=pg_container, has_parent=$parentID");

                if ($settingsPage && $settingsPage->id) {
                } else {
                    $settingsPage = new Page(); // create new page object
                    $settingsPage->template = 'pg_container'; // set template
                    $settingsPage->parent = $parentID; // set the parent
                    $settingsPage->name = $className; // give it a name used in the url for the page
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

                $p = $clone;
            } else {
                //create symbol
                $symbolParent = $this->pages->get("name=pg-symbols, template=pg_container");
                $symbolTitle = isset($_POST['name']) ? $_POST['name'] : $p->title;
                $symbolName = $this->sanitizer->pageName($symbolTitle, true);

                //clone item page to symbols
                $clone = $this->pages->clone($p);
                $clone->title = $symbolTitle;
                $clone->name = $symbolName . '-' . $p->id;
                $clone->parent = $symbolParent;
                $clone->save();

                //set meta symbol page
                $p->meta()->set('pg_symbol', $clone->id);
            }

            $css = $this->modules->get('InputfieldPageGrid')->renderStyles($p);

            foreach ($p->find('') as $child) {
                $css .= $this->modules->get('InputfieldPageGrid')->renderStyles($child);
            }

            $newPageClass = $p->name;
            if ($clone && $clone->name) $newPageClass = $clone->name;

            //get symbol markup for add item bar
            $addBar = $this->modules->get('InputfieldPageGrid')->renderAddItemBar(1);

            $response = array(
                'newPageClass' => $newPageClass,
                'markup' => $this->modules->get('InputfieldPageGrid')->renderItem($p),
                'css' => $css,
                'addBar' => $addBar
            );

            return (json_encode($response));
        }
        //END convert to symbol

        // change parent
        if (!empty($_POST['newParent'])) {

            // // // // bd('parent change');

            $p = $this->pages->get($_POST['pageId']);
            $newParent = $this->pages->get($_POST['newParent']);
            $p->of(false);
            $p->parent = $newParent;
            $p->save();
            $p->of(true);

            return;
        }
        // END change parent

        // change sort order of groups, sort must be pipe seperated string
        if (!empty($_POST['sort'])) {

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
                $this->pages->sort($first->parent(), true);
            }

            return;
        }
        // END change sort order of groups

        if ($type === 'delete' && !empty($removeId)) {

            //get page, for classes and animations use name and parent selector
            $p = $this->pages->get($removeId);
            if ($parentId) $p = $this->pages->findOne('name=' . $removeId . ', has_parent=' . $parentId);

            if (!$p || !$p->id) return;

            if ($this->modules->isInstalled('PageGridDemoMode') && $this->user->hasRole('pagegrid-demo') && $p->created_users_id !== $this->user->id) return;

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

            if ($pageId == 0 || $pageId == '0') {
                return false;
            }

            $p = $this->pages->get($pageId);
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

            $response = array(
                'markup' => $this->modules->get('InputfieldPageGrid')->renderItem($clone),
                'css' => $css,
                'newPages' => $newPages
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
                    $pItems->delete();
                    $cloneItems = $this->pages->clone($blueprintItemsPage);
                    $cloneItems->name = 'pg-' . $p->id;
                    $cloneItems->title = $p->title;
                    $cloneItems->save();

                    //rename itms to prevent naming conflicts
                    foreach ($cloneItems->find('') as $clone) {
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

            // create new page
            $p = new Page();
            $template =  $this->templates->get($templateId);
            $parent = $this->pages->get($parentId);
            $p->template = $template->name;
            $p->parent = $parent;

            $p->save();
            $p->setOutputFormatting(false);
            $p->save();

            $insertAfter = $this->sanitizer->int($_POST['insertAfter']);

            if (isset($insertAfter) && $insertAfter != 0) {
                $afterP = $this->pages->get($insertAfter);
                $this->pages->insertBefore($p, $afterP);
            }

            // set title after save to get unique id
            $templateName = str_replace('_', '-', $template->name);
            $p->setAndSave('title', $templateName . '-' . $p->id);
            $p->setAndSave('name', $templateName . '-' . $p->id);

            $response = array(
                'newPageClass' => $p->name,
                'markup' => $this->modules->get('InputfieldPageGrid')->renderItem($p)
            );

            return (json_encode($response));
        }

        if ($type === 'addSymbol' && !empty($pageId)) {

            $p = $this->pages->get($pageId);
            if (!$p || !$p->id) return;
            $parent = $this->pages->get($parentId);
            if (!$parent || !$parent->id) return;
            
            //return if parent is symbol to prevent nested symbols
            if (count($parent->parents('name=pg-symbols, template=pg_container'))) return;

            $clone = $this->pages->clone($p);
            $clone->name = $p->template->name . '-' . $clone->id; //generate unique name to support multiple symbold on same page
            $clone->parent = $parent;
            $clone->save();

            $insertAfter = $this->sanitizer->int($_POST['insertAfter']);

            if (isset($insertAfter) && $insertAfter != 0) {
                $afterP = $this->pages->get($insertAfter);
                $this->pages->insertBefore($clone, $afterP);
            }

            //set meta symbol page
            $clone->meta()->set('pg_symbol', $p->id);

            $response = array(
                'newPageClass' => $p->name,
                'markup' => $this->modules->get('InputfieldPageGrid')->renderItem($clone),
                'pageId' => $clone->id
            );

            return (json_encode($response));
        }

        // handel uploads to pages
        if ($type === 'upload' && !empty($pageId)) {

            $p = $this->pages->get($pageId);
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

            $this->log->save("pagegrid", $filePath);
            $p->save();
            return $fileRelativePath . $filename;
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

                $p->meta()->set('pg_ajax', true);

                $response = array(
                    'newPageClass' => $p->name,
                    'markup' => $this->modules->get('InputfieldPageGrid')->renderItem($p),
                    'message' => $message
                );

                $p->meta()->set('pg_ajax', false);

                return (json_encode($response));
            }
        }
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
