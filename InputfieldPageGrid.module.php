<?php

namespace ProcessWire;

/**
 * PageGrid for ProcessWire
 * 
 * Copyright (C) 2023 by Jan Ploch
 * THIS IS A COMMERCIAL MODULE - DO NOT DISTRIBUTE
 */

class InputfieldPageGrid extends Inputfield {


    public static function getModuleInfo() {
        return array(
            'title' => __('PageGrid Inputfield', __FILE__), // Module Title
            'summary' => __('Inputfield for FieldtypePageGrid', __FILE__), // Module Summary
            'version' => '0.2.0',
            'author' => 'Jan Ploch',
            'icon' => 'th',
            'permanent' => false,
            'requires' => array('FieldtypePageGrid'),
            'installs' => array('FieldtypePageGrid', 'ProcessPageGrid', 'PageFrontEdit', 'ProcessPageClone'),
        );
    }

    /**
     * Array of Template objects used for each row
     *
     * @var array
     *
     */
    protected $rowTemplates = array();
    protected $ft;

    public function __construct() {
    }

    public function init() {
        parent::init();

        //set shortcut to FieldtypePageGrid
        $this->ft = $this->modules->get('FieldtypePageGrid');

        // defaults
        $this->set('collapsed', '');
        $this->set('showIf', '');
        $this->set('columnWidth', '');
        $this->set('sortfields', '-date');
        $this->set('template_id', 0); // placeholder only
    }

    public function ___renderValue() {
        return 'inputfield render value';
    }

    public function ___render() {
        $user = wire('user');
        $this->ft->createDummies();
        $this->config->styles->add($this->config->urls->InputfieldPageGrid . "css/main.css?v=" . $this->modules->getModuleInfo('FieldtypePageGrid')['version']);

        //needs newest jquery ui version comming with newer PW version, some only with dev flag, load if needed
        if (($this->config->debug !== 'dev' && $this->config->version() >= '3.0.221') || $this->config->version() <= '3.0.210') {
            $this->config->scripts->add($this->config->urls->InputfieldPageGrid . "js/jqueryUi.js'");
        }

        $this->config->scripts->add($this->config->urls->InputfieldPageGrid . "js/main.js?v=" . $this->modules->getModuleInfo('FieldtypePageGrid')['version']);
        return $this->renderField();
    }

    public function ___getData($classPages = null, $animationPages = null) {
        //make data available to js
        $globalPage = $this->pages->get('name=pg-classes, template=pg_container');
        $globalPageData = [];
        $globalPageData[$globalPage->id] = [];

        //get all classes if no arguments
        if ($classPages == null) {
            $classPages = $globalPage->find('');
        }

        //get classes data
        foreach ($classPages as $child) {
            $itemData = $child->meta()->pg_styles;
            if (isset($itemData)) {
                foreach ($itemData as $childData) {
                    if (isset($childData['id'])) {
                        $globalPageData[$globalPage->id][$child->name] = $childData;
                    }
                }
            }
        }

        //get animations data
        $globalPage = $this->pages->get('name=pg-animations, template=pg_container');
        $globalPageData[$globalPage->id] = [];
        $animations = $animationPages;

        //get all classes if no arguments
        if ($animationPages == null) {
            $animations = $globalPage->find('');
        }

        foreach ($animations as $child) {
            $itemData = $child->meta()->pg_styles;

            // rename animation if changed
            // if (!isset($itemData[$child->name])) {
            //     $this->renameItemData($child, $child->name);
            //     $itemData = $child->meta()->pg_styles;
            // }

            if (isset($itemData)) {
                foreach ($itemData as $childData) {
                    if (isset($childData['id'])) {
                        $globalPageData[$globalPage->id][$child->name][$childData['id']] = $childData;
                    }
                }
            }
        }

        //item data
        $itemsArray = new PageArray();
        $pageItems = $this->pages->get('name=pg-items, template=pg_container');
        $symbolsItems = $this->pages->get('name=pg-symbols, template=pg_container');

        // get all grandchildren with getAncestors function ($page->find('') not returning all levels)
        $itemsArray->add($this->getAncestors($pageItems));
        $itemsArray->add($this->getAncestors($symbolsItems));

        foreach ($itemsArray as $pageItem) {
            $itemData = $pageItem->meta()->pg_styles;
            if (isset($itemData)) {
                //remove hover states from older pg versions
                if (array_key_exists('pgitem:hover', $itemData)) {
                    $pageItem->meta()->remove('pg_styles')['pgitem:hover'];
                    continue;
                }
                $globalPageData[$pageItem->id] = $itemData;
            }
        }

        return json_encode($globalPageData);
    }

    public function renameItemData($item, $newName) {

        //currently only used to rename animation if page name changes
        //could be addapted to all items?
        $itemData = $item->meta()->pg_styles;
        $oldId = '';

        foreach ($itemData as $childData) {
            if (isset($childData['animation-event'])) {
                $oldId = $childData['id'];
                $childData['id'] = $newName;
                $childData['cssClass'] = $newName;
                $itemData[$newName] = $childData;
                //remove data with oldid
                unset($itemData[$oldId]);
                $this->notices->message('rename animation to: ' . $newName);
            }
        }
        //save animation
        $item->meta()->set('pg_styles', $itemData);

        //rename all animation names
        $pageItems = $this->pages->get('pg-items');
        $itemsArray = new PageArray();
        //build array with all pages
        foreach ($pageItems->children() as $child) {
            $itemsArray->add($child);
            foreach ($child->find('') as $c) {
                $itemsArray->add($c);
            }
        }
        //rename animation names on items
        foreach ($itemsArray as $pageItem) {
            $pageItemData = $pageItem->meta()->pg_styles;
            if (isset($pageItemData['pgitem']['breakpoints'])) {
                foreach ($pageItemData['pgitem']['breakpoints'] as $breakpoint) {
                    if (isset($pageItemData['pgitem']['breakpoints'][$breakpoint['name']]['css']['--pg-animation'])) {
                        $animations = $pageItemData['pgitem']['breakpoints'][$breakpoint['name']]['css']['--pg-animation'];
                        if ($animations && strpos($animations, $oldId) !== false) {
                            $animations = str_replace($oldId, $newName, $animations);
                            $pageItemData['pgitem']['breakpoints'][$breakpoint['name']]['css']['--pg-animation'] = $animations;
                            //save item
                            $pageItem->meta()->set('pg_styles', $pageItemData);
                        }
                    }
                }
            }
        }
        //END rename all animation names

    }

    public function ___renderField() {
        // $pagesToRender = $this->attr('value');

        //new pages to render based on items parent
        $editID = (int) $this->wire('input')->get('id');
        if (!$editID && $this->wire('process') instanceof WirePageEditor) $editID = $this->wire('process')->getPage()->id;

        if ($editID) {
        } else {
            return false;
        }

        $itemsParent = $this->pages->get('pg-' . $editID);

        //check if old id exists for imported pages via import module
        $mainPage = $this->pages->get($editID);
        $oldEditID = $mainPage->meta()->pg_itemsPage;

        if (isset($oldEditID)) {

            if ($oldEditID != $editID) {
                $itemsParentOld = $this->pages->get('pg-' . $oldEditID);
                if ($itemsParentOld->id && $itemsParent->id == 0) {
                    $itemsParentOld->name = 'pg-' . $editID;
                    $itemsParentOld->save();
                    $itemsParent = $itemsParentOld;
                }
            }
        }

        $mainPage->meta()->set('pg_itemsPage', $editID);

        //END check if old id exists for imported pages via import module

        if ($itemsParent->id) {
        } else {
            $itemsParent = new Page(); // create new page object
            $itemsParent->template = 'pg_container'; // set template
            $itemsParent->parent = 'pg-items'; // set the parent
            $itemsParent->name = 'pg-' . $editID; // give it a name used in the url for the page
            $itemsParent->title = $this->pages->get($editID)->title . ' items'; // set page title (not neccessary but recommended)
            $itemsParent->save();
        }

        //END new pages to render based on items parent

        //new for multiple fields collapse all fields but first
        $fieldFound = '';
        foreach ($mainPage->fields as $f) {
            if ($f->type instanceof FieldtypePageGrid) {
                $fieldFound = $f->name;
                break;
            }
        }

        if ($this->name !== $fieldFound) $this->addClass('InputfieldStateCollapsed', 'wrapClass'); // close if not first field
        $this->addClass('InputfieldStateWasCollapsed', 'wrapClass'); // add this so header can always be closed
        //END new for multiple fields collapse all fields but first

        $moduleUrl = $this->config->urls->InputfieldPageGrid;
        $user = wire('user');
        $settings = '';
        $topNav = '';
        $addItems = '';

        //make data available to js
        $globalPageData = $this->getData();
        $dataGlobal = '<script>$(".pg-container").data("pg", ' . $globalPageData . ')</script>';
        //END make data available to js

        // get parent page
        $parentPageId = (int) wire('input')->get('id');
        if ($parentPageId == 'undefined' || $parentPageId == null) {
            $parentPageId = wire('page')->id;
        }
        $parentPage = $this->pages->get($parentPageId);

        //show animation button only if animations set
        $animationParent = $this->pages->get('name=pg-animations, template=pg_container');
        if ($animationParent && $animationParent->id) {
            $hasAnimations = $animationParent->findOne() && $animationParent->findOne()->id ? true : false;
        } else {
            $hasAnimations = false;
        }

        if ($user->hasPermission('pagegrid-style-panel') && $this->ft->stylePanel) {
            include_once 'stylePanel.php';
            if ($settings) {
                $settings = '<div data-field=' . $this->name . ' class="ui-dialog pg-settings-container pg-stylepanel"><div class="pg-settings pg-stylepanel pg-settings-content">' . $settings . '</div></div>';
                $topNav = '<div class="pg-topnav uk-navbar-center">
            <i class="pg-item-list-button pg-topnav-margin-big fa fw fa-list-ul on" title="Item List"></i>
            <i class="pg-undo fa fa-fw fa-reply" data-name="fa-reply" title="Undo"></i>
            <i class="pg-redo pg-topnav-margin fa fa-fw fa-share" data-name="fa-share" title="Redo"></i>';
                if ($hasAnimations) $topNav .= '<i class="pg-play pg-topnav-margin fa fw fa-play" title="Play animations"></i>';
                $topNav .= '<div id="breakpoints-nav">
           <img src="' . $moduleUrl . '/img/phone-portrait-outline.svg" class="breakpoint-icon breakpoint-icon-s" value="@media (max-width: 640px)" breakpoint="s" title="Breakpoint Small">
           <img src="' . $moduleUrl . '/img/phone-landscape-outline.svg" class="breakpoint-icon breakpoint-icon-m" value="@media (max-width: 960px)" breakpoint="m" title="Breakpoint Medium">
           <img src="' . $moduleUrl . '/img/laptop-outline.svg" class="breakpoint-icon breakpoint-icon-base" value="@media (min-width: 640px)" breakpoint="base" title="Breakpoint Base">
           <img src="' . $moduleUrl . '/img/desktop-outline.svg" class="breakpoint-icon breakpoint-icon-l" value="@media (min-width: 1600px)" breakpoint="l" title="Breakpoint Large">
           </div></div>';
            }
        }

        $addItems = $this->renderAddItemBar();

        //add blueprint select
        $blueprintPages = $this->pages->get('name=pg-blueprints, template=pg_container')->children();
        $blueprintSelect = '';
        if (count($blueprintPages) && $this->name == $fieldFound) {
            $blueprintSelect = $this->modules->get('InputfieldSelect');
            $blueprintSelect->name = 'pg-blueprint-select';
            $blueprintSelect->label = 'Select blueprint';
            //add options
            foreach ($blueprintPages as $blueprint) {
                $blueprintSelect->addOption($blueprint->name);
            }
            $blueprintSelect = $blueprintSelect->render();
        }
        //END add blueprint select

        $renderMarkup = $topNav . $settings . '<div class="pg-container pg-container-' . $this->name . '" data-page-title="' . $mainPage->title . '" data-page="' . $editID . '" data-id="' . $this->pages->get('pg-classes')->id . '" data-animations-id="' . $this->pages->get('pg-animations')->id . '" data-field="' . $this->name . '" data-admin-url="' . $this->page->rootParent->url() . 'setup/pagegrid/" data-fallbackfonts="' . $this->ft->fallbackFonts . '">' . $addItems . $dataGlobal . $blueprintSelect;
        //loading animation
        $renderMarkup .= '<div class="pg-loading"><div class="fa fa-spin fa-spinner fa-fw"></div></div>';
        //container for item header (item header will be moved here with js)
        $renderMarkup .= '<div class="pg-item-header-container"></div>';
        $renderMarkup .= '<iframe data-field="' . $this->name . '" id="pg-iframe-canvas-' . $this->name . '" class="pg-iframe-canvas" src="' . wire('pages')->get($parentPageId)->url . '?backend=1&field=' . $this->name . '" loading="lazy" frameBorder="0" scrolling="no" style="width:100%; max-height:100vh; border:0;"></iframe>';
        $renderMarkup .= '</div>';

        //render delete button
        if ($user->hasPermission('page-delete')) {
            $button = $this->modules->get('InputfieldButton');
            $button->value = 'Delete items';
            $button->addClass('pg-button-delete-marked');
            $button->icon('trash');
            $renderMarkup .= '<div style="display:none;" class="pg-wraper-delete-marked">' . $button->render() . '</div>';
        }

        //init setup
        $l = $this->ft->setup();

        return $renderMarkup;
    }

    public function renderAddItemBar($getSymbolsOnly = 0) {

        // render the 'Add New' buttons for each template
        if (!$this->user->hasPermission('pagegrid-add') || !$this->user->hasPermission('pagegrid-drag')) return;

        // $editID = (int) $this->wire('input')->get('id');
        // if (!$editID && $this->wire('process') instanceof WirePageEditor) $editID = $this->wire('process')->getPage()->id;
        // $parentID = $itemsParent->id;
        $addItems = '';

        if (!$getSymbolsOnly) {
            $addItems = '<div data-field=' . $this->name . ' class="pg-add-container pg-add-container-' . $this->name . '"><div class="pg-add-content">';
            foreach ($this->rowTemplates as $template) {
                /** @var Template $template */

                if (!$this->user->isSuperuser() && $template->useRoles && !in_array($this->user->id, $template->createRoles)) continue;

                // keep this line for future updates, makes it possible to add items via modal if link is clicked, maybe alternative for non super users oneday
                // $url = $this->wire('config')->urls->admin . "page/add/?modal=1&template_id=$template->id&parent_id=$parentID&context=PageGrid";

                if ($template->icon == '') {
                    $tIcon = '<div class="pg-iconletter">' . substr($template->getLabel(), 0, 1) . '</div>';
                } else {
                    $tIcon = wireIconMarkup($template->icon);
                }

                $addItems .= '<div class="pg-add ' . $template->name . '" data-template-id="' . $template->id . '" template="' . $template->name . '">' . $tIcon . '<span class="ui-button-text">' . $template->getLabel() . '</span></div>';
            }
        }

        //add symbols
        $addItems .= '<div class="pg-add-symbol-container">';
        $symbolParent = $this->pages->get("name=pg-symbols, template=pg_container");
        $symbols = $symbolParent->children('sort=created');
        $linkedPages = $this->database->query("SELECT source_id FROM pages_meta WHERE name = 'pg_symbol'");
        $linkedPages = implode("|", $linkedPages->fetchAll(\PDO::FETCH_COLUMN, 0));
        $linkedPages = $this->pages->getByIDs($linkedPages);

        foreach ($symbols as $symbol) {
            if ($symbol->template->icon == '') {
                $tIcon = '<div class="pg-iconletter">' . substr($symbol->title, 0, 1) . '</div>';
            } else {
                $tIcon = wireIconMarkup($symbol->template->icon);
            }

            $linkedPagesCount = 0;
            foreach ($linkedPages as $lp) {
                if ($lp->meta()->pg_symbol !== null && $lp->meta()->pg_symbol == $symbol->id && $lp->parent->name !== 'trash') {
                    $linkedPagesCount++;
                }
            }

            $addItems .= '<div class="pg-add pg-add-symbol" data-id="' . $symbol->id . '" data-template-id="' . $symbol->template->id . '" template="' . $symbol->template->name . '">' . $tIcon . '<span class="ui-button-text"><span class="pg-symbol-title">' . $symbol->title . '</span><span class="pg-symbol-number">' . $linkedPagesCount . '</span></span></div>';
        }

        $addItems .= '</div>';
        //END add symbols

        if (!$getSymbolsOnly) {
            $addItems .= '</div></div>';
        }
        return $addItems;
    }

    public function isBackend() {
        $backend = 0;

        if (!wire('page') || !wire('page')->id || !$this->user->isLoggedin()) return 0;
        if (wire('page')->template->name === 'pg_blueprint' && !isset($_GET['backend'])) return 0;

        if ($this->user->isLoggedin() && (strpos(wire('page')->url, wire('config')->urls->admin) === 0 || isset($_GET['backend']))) {
            $backend = 1;
        }

        return $backend;
    }

    public function renderGrid($mainPage, $field = 0) {
        $backend = $this->isBackend();
        $statusClass = '';
        $itemsParent = $this->pages->get('pg-' . $mainPage->id);
        $layout = "";
        $fieldCount = count($mainPage->fields->find('type=FieldtypePageGrid'));

        if (!$itemsParent->id) return;
        if (!$field) $field = $mainPage->fields->get('type=FieldtypePageGrid'); //if no argument get first field
        if (!$field->id) return;

        //NEW support for multiple fields
        // multiple fields: check if it's this field or return (prevents double rendering of fields in backend)
        if (isset($_GET['field']) && $_GET['field'] !== $field->name && $fieldCount > 1) return;

        //search $this->pages instead of $itemsParent to work with multi language (bug?)
        $itemsParentNew = $this->pages->get("name=pg-$field->id, parent=$itemsParent->id, template=pg_container");

        //create field container page if it doesn't exist
        if (!$itemsParentNew->id) {
            $itemsParentNew = new Page(); // create new page object
            $itemsParentNew->template = 'pg_container'; // set template
            $itemsParentNew->parent = $itemsParent->id; // set the parent
            $itemsParentNew->name = 'pg-' . $field->id; // give it a name used in the url for the page
            $itemsParentNew->title = $field->name; // set page title (not neccessary but recommended)
            $itemsParentNew->save();
        }

        //update older versions and move pages from page container to field container
        foreach ($itemsParent->children() as $p) {
            if ($p->template->name === 'pg_container') continue;
            $p->of(false);
            $p->parent = $itemsParentNew;
            $p->save();
            $p->of(true);
        }

        //set new container
        $itemsParent = $itemsParentNew;

        //END NEW support for multiple fields

        $pagesToRender = $itemsParent->children();

        foreach ($pagesToRender as $p) {
            $layout .= $this->renderItem($p);
        }

        if ($backend) {

            // trick inline editor to work for first items
            $PageFrontEditData = $this->modules->getConfig('PageFrontEdit');
            $dummies = '';

            if (isset($PageFrontEditData['inlineEditFields'])) {
                $PageFrontEditFields = $PageFrontEditData['inlineEditFields'];
                $templates = $this->fields->get('type=FieldtypePageGrid')->template_id;
                if ($templates !== null && count($templates)) {
                    foreach ($templates as $tId) {
                        $t = $this->templates->get($tId);
                        if (isset($t) == 0) continue;
                        foreach ($t->fields as $f) {
                            if (in_array($f->id, $PageFrontEditFields)) {
                                $dummy = $this->pages->get("$f->id!=''");
                                if ($dummy->id) {
                                    $this->ft->readyFrontEdit($dummy);
                                    $dummies .= $dummy->$f;
                                }
                            }
                        }
                    }
                }
            }
            // END trick inline editor to work for first items

            $statusClass = '';

            if ($this->user->hasPermission('pagegrid-drag')) $statusClass .= 'pg-sortable';
            if ($this->user->hasPermission('pagegrid-select'))  $statusClass .= " pg-permission-select";

            $out = '<div id="' . $itemsParent->name . '" class="pg-wrapper pg-item pg-main pg-droppable pg ' . $this->getCssClasses($itemsParent) . ' ' . $statusClass . '" data-id="' . $itemsParent->id . '" data-field="' . $field->name . '">' . $layout . '</div>';
            $out .= '<div class="pg-dummies" style="display:none!important;">' . $dummies . '</div>';
        } else {
            $out = '<div class="pg-wrapper pg pg-main ' . $this->getCssClasses($itemsParent) . '">' . $layout . '</div>';
        }

        return $out;
    }

    //disable automatic prepending/appending of template file by passing this string (checked on ready function of fieldtyle module)
    public function noAppendFile($p) {
        echo '<!--pgNoAppendTemplateFile-->';
    }

    //for items we never want prepending/appending of template file so disable it in DB
    public function noAppendFileSave($p) {
        if (!$p->template->noAppendTemplateFile) {
            $p->template->noAppendTemplateFile = 1;
            $p->template->noPrependTemplateFile = 1;
            $p->template->appendFile = "";
            $p->template->prependFile = "";
            $p->template->save();
        }
    }

    public function renderItem($p) {

        //check if symbol page was found
        $pOriginal = $p; // set original as data attribute later to be able to convert back
        if ($p->meta('pg_symbol') !== null && $p->meta('pg_symbol')) {
            $symbolId = $p->meta('pg_symbol');
            $symbol = $this->pages->get($symbolId);
            if ($symbol && $symbol->id && $symbol->parent->name === 'pg-symbols') {
                $p = $symbol;
            } else {
                //if no symbol page found remove reference
                $p->meta()->remove('pg_symbol');
            }
        }
        //END check if symbol page was found

        //disable automatic prepending/appending of template file
        $this->noAppendFileSave($p);

        if ($p->template->tags !== 'Blocks') {
            $p->template->tags = 'Blocks';
            $p->template->save();
        }

        $backend = $this->isBackend();

        $layout = '';

        $layoutTitle = $p->template->label ? $p->template->label : $p->template->name;
        //            $layoutTitle = wireIconMarkup( $p->template->icon ) . ' ' . $p->title;
        $ext = "." . $this->config->templateExtension;
        $template_name = $p->template->altFilename ? basename($p->template->altFilename, $ext) : $p->template->name;
        // $templateFilename = $this->config->paths->templates . $pg->pathToTemplates . $template_name . $ext;
        $templateFilename = $this->config->paths->templates . 'blocks/' . $template_name . $ext;

        //if no template file found look inside module folder
        if (file_exists($templateFilename) == 0) {
            //look inside module block folder
            $templateFilename = $this->config->paths->siteModules . 'PageGridBlocks/blocks/' . $template_name . $ext;
        }

        if (file_exists($templateFilename) == 0) {
            return false;
        }

        $parsedTemplate = new TemplateFile($templateFilename);
        $parsedTemplate->set("page", $p);
        $parsedTemplate->set("isAdmin", 1);
        $parsedTemplate->pageGrid = array('backend' => $backend, 'tag' => $this->getTagName($p));

        // force init inline editor markup
        if (($backend && $this->config->ajax) || ($backend && $p->parent()->meta()->pg_ajax)) {
            // hack: change name to reinit new children of groups after modal edit
            $oldName = $p->name;
            $p->setAndSave('name', $p->name . '-');
            $p->setAndSave('name', $oldName);

            $this->ft->readyFrontEdit($p);
        }
        // END force init inline editor markup

        //add placeholder to text fields
        // prefil inline fields with dummy content (without saving value)
        $PageFrontEditData = wire('modules')->getConfig('PageFrontEdit');
        $FieldtypePageGridData = wire('modules')->getConfig('FieldtypePageGrid');

        if (isset($PageFrontEditData['inlineEditFields'])) {
            $PageFrontEditFields = $PageFrontEditData['inlineEditFields'];
            foreach ($PageFrontEditFields as $fieldId) {
                $field = wire('fields')->get($fieldId);
                if ($p->template->hasField($field)) {
                    if (!array_key_exists("placeholderText_$field->id", $FieldtypePageGridData)) continue;

                    $fieldName = $field->name;
                    $placeholder = $FieldtypePageGridData["placeholderText_$field->id"];

                    //need to check for unformated value because frontEdit module already populated markup
                    $value = $p->getUnformatted($fieldName);
                    $value = strip_tags($value);

                    //continue if not empty
                    if ($value) continue;
                    if (!$placeholder) continue;

                    if ($field->inputfieldClass == 'InputfieldCKEditor' || $field->inputfieldClass == 'InputfieldTinyMCE') {
                        $rtWrapTag = '<p>';
                        $rtWrapTagClose = '</p>';

                        //change wrap tag based on InputfieldTinyMCE JSON settings
                        if ($field->inputfieldClass == 'InputfieldTinyMCE' && $field->settingsJSON) {
                            $rtOptions = json_decode($field->settingsJSON);
                            if (isset($rtOptions) && isset($rtOptions->forced_root_block) && $rtOptions->forced_root_block) {
                                $rtWrapTag = '<' . $rtOptions->forced_root_block . '>';
                                $rtWrapTagClose = '</' . $rtOptions->forced_root_block . '>';
                            }
                        }
                        //END change wrap tag based on InputfieldTinyMCE JSON settings

                        $p->$fieldName = $rtWrapTag . $placeholder . $rtWrapTagClose;
                    } else {
                        $p->$fieldName = $placeholder;
                    }
                }
            }
        }
        //END prefil inline fields with dummy content

        //prevent bug with div nested in p if block has <p> as wrapper
        //div is needed for inline editor to work
        foreach ($p->template->fields as $field) {
            if ($field->inputfieldClass !== 'InputfieldTinyMCE') continue;
            if (!$field->settingsJSON) continue;
            $rtOptions = json_decode($field->settingsJSON);
            $fieldName = $field->name;
            if (isset($rtOptions) && isset($rtOptions->forced_root_block) && $rtOptions->forced_root_block === 'div' && $p->$fieldName) {
                $validElements = isset($rtOptions->valid_elements) ? explode(' ', $this->sanitizer->words($rtOptions->valid_elements)) : [];
                if (($key = array_search('div', $validElements)) !== false) {
                    if (!$this->user->isLoggedin() || (!$backend && $this->ft->inlineEditorFrontDisable)) unset($validElements[$key]);
                }
                $validElementsString = count($validElements) ? "<" . implode("><", $validElements) . ">" : "";
                $p->$fieldName = strip_tags($p->getFormatted($fieldName), $validElementsString);
            }
        }
        //prevent bug with div nested in p if block has <p> as wrapper

        //Read item Settings
        $attributes = '';
        $nestedClasses = '';

        $itemData = $p->meta()->pg_styles;

        if (isset($itemData)) {
            if (isset($itemData['pgitem'])) {
                $PageGridItem = $itemData['pgitem'];

                if (isset($PageGridItem['attributes'])) {
                    $attributes = $PageGridItem['attributes'];
                }
                if (isset($PageGridItem['children'])) {

                    if ($backend) {
                        $nestedClasses = 'pg pg-nested pg-droppable ';
                    } else {
                        $nestedClasses = 'pg ';
                    }

                    if ($this->user->hasPermission('pagegrid-drag')) {
                        // $nestedClasses .= 'pg-sortable ';
                    }
                }
            }
        }

        //end Read item Settings

        $header = $this->renderItemHeader($p, $p->template->label, $pOriginal);
        $statusClass = $this->getStatusClasses($p);

        //make sure outpuformatting is on before render
        $p->of(true);

        //disable inline edit for frontend, when inlineEditorFrontDisable = true
        if (!$backend && $this->ft->inlineEditorFrontDisable) $p->edit(false);

        // parse template markup and inssert file uploader
        $templateRender = $parsedTemplate->render();
        $templateRender = $this->ft->enableInlineEditFile($templateRender);

        // PARSE RENDER OPTIONS
        // parse options set via renderOptions
        $docHtml = new \DOMDocument();
        @$docHtml->loadHTML('<?xml encoding="utf-8" ?><html>' . $templateRender . '</html>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $tag = $docHtml->getElementsByTagName('pg-data') ? $docHtml->getElementsByTagName('pg-data')[0] : 0;
        $options = $tag ? json_decode($tag->getAttribute('pg-data-options')) : [];
        $tagNameSaved = $this->getTagName($p);
        $tagName = $tagNameSaved;
        $classes = $this->getCssClasses($p);
        $optionAutoTitle = 1; // deafult enable auto title and puplish of pg items via modal

        //tagname
        if ($tag && isset($options->tag)) {
            $tagName = $this->sanitizer->htmlClass($options->tag);
            $tagName = strtolower($tagName);
            // replace p tag with custom el to prevent nesting bug with <p> and inline editor <div>
            if ($tagName == 'p') {
                if ($this->user->isLoggedin()) $tagName = 'pg-ptag';
                if (!$backend && $this->ft->inlineEditorFrontDisable) $tagName = 'p';
            }

            //allow tags to change
            if (isset($options->tags) && $tagNameSaved !== 'div') $tagName = $tagNameSaved;
        }

        //classes
        if ($tag && isset($options->classes)) {
            $classNames = $this->sanitizer->htmlClasses($options->classes);
            $classNames = strtolower($classNames);
            if ($classNames) $classes .= ' ' . $classNames;
        }

        //children
        if ($tag && isset($options->children) && $options->children) {
            if ($backend) {
                $nestedClasses = 'pg pg-nested pg-droppable ';
            } else {
                $nestedClasses = 'pg ';
            }
        }

        if ($tag && isset($options->autoTitle)) {
            $optionAutoTitle = $options->autoTitle;
        }

        // remove tag from render on frontend
        // needed for js to get data in backend
        if ($tag && !$backend) {
            $dataTag = 'pg-data';
            $hasOptionTags = strpos($templateRender, "<$dataTag") !== false;

            if ($hasOptionTags) {
                // remove option tags
                $templateRender = preg_replace('!</?' . $dataTag . '(?:\s[^>]*>|>)\s*!is', '', $templateRender);
            }
        }
        //END new set render options

        //force autonaming/puplishing for all children if only one template selected
        //if autoTitle render option is set to true (default)
        if (count($p->template->childTemplates) == 1 && $optionAutoTitle) {
            $p->template->childNameFormat = 'pg-autotitle';
            $p->template->save();
        } else {
            $p->template->childNameFormat = '';
            $p->template->save();
        }
        //END force autonaming for all children if only one template selected

        if ($backend) {
            $layout .= '<' . $tagName . ' id="' . $p->name . '" data-id="' . $p->id . '" data-id-original="' . $pOriginal->id . '" class="' . $classes . ' ' . $nestedClasses . $statusClass . '" data-template="' . $p->template->name . '" data-template-label="' . $p->template->label . '" data-field="' . $this->name . '" data-title="' . $p->getUnformatted('title') . '" data-name="' . $p->name . '" ' . $attributes . '>';
            $layout .= '<pg-icon>' . wireIconMarkup($p->template->icon) . '</pg-icon>';
            $layout .= $header;
            $layout .= $templateRender;
            $layout .= '</' . $tagName . '>';
        } else {
            $layout = '<' . $tagName . ' class="' . $nestedClasses . $classes . '" ' . $attributes . '>' .  $templateRender . '</' . $tagName . '>';
        }

        return $layout;
    }

    //function to render file uploader
    // @param Page $p
    // @param Field Name or Field Id $f
    public function renderFileUploader($p, $fName) {

        if (!$this->isBackend()) return;
        if (!$p && !$p->id) return;

        $f = wire('fields')->get($fName);
        $imageUpload = '';
        $imageUploadEmpty = '';

        if ($p->template->hasField($f)) {

            $p->of(true);

            if (!$p->$fName) {
                $imageUploadEmpty = ' pg-file-picker-empty';

                if ($f->type instanceof FieldtypeImage) {
                    $imageUpload = '<img class="pg-fileupload" src="">';
                } else {
                    $imageUpload = '<video muted loop class="pg-fileupload">';
                    $imageUpload .= '<source src="" type="video/mp4">';
                    $imageUpload .= '</video>';
                }
            }

            $imageUpload .= '
                        <pg-uploader class="setting pg-file-picker pg-file-picker-' . $f->name . $imageUploadEmpty . '">
                          <pg-uploader class="settings_wrap">
                            <pg-uploader class="drop_target">
                              <pg-uploader class="input_button"></pg-uploader>
                                <input class="inputFile" type="file" data-quality="' . $f->clientQuality . '" data-max-width="' . $f->maxWidth . '" data-max-height="' . $f->maxHeight . '" data-field="' . $f->name . '" data-id="' . $p->id . '" data-type="upload"/>
                                </pg-uploader>
                            </pg-uploader>
                        </pg-uploader>
                        ';
        }

        return $imageUpload;
    }

    //function to render item header
    public function renderItemHeader($p, $title = '', $pOriginal = 0) {

        $header = "";
        $user = $this->user;
        $layoutTitle = $p->template->label ? $p->template->label : $p->template->name;
        $layoutTitle = $title ? $title : $layoutTitle;
        $statusClass = $this->getStatusClasses($p);
        $isPgPage = $p->parents('template=pg_container')->first();

        //if frontend return empty string
        if (!$this->isBackend()) return $header;

        //make sure outpuformatting is on before render
        $p->of(true);

        //for normal pages $pOriginal and $p are the same ($pOriginal is needed for symbols)
        if (!$pOriginal) {
            $pOriginal = $p;
        }

        if ($pOriginal->meta('pg_symbol') !== null && $pOriginal->meta('pg_symbol')) {
            $statusClass .= " pg-symbol pg-ref";
            $layoutTitle = $p->title ? $p->title : $p->name;
        }

        // use custom html element for header, to be able to nest inside "<a>"
        if ($p->editable() && $user->hasPermission('page-pagegrid-edit', $p)) {
            $header .= '<span id="pg-item-header-' . $pOriginal->id . '" data-id="' . $p->id . '" data-id-original="' . $pOriginal->id . '" class="pg-item-header' . $statusClass . '">';
            $header .= '<span>' . $layoutTitle . '</span>';

            //edit
            $header .= '<pg-item-header-button class="pg-edit" title="' . $this->_('Edit') . '" data-url="./?id=' . $p->id . '&amp;modal=1&pgmodal=1" href="#"><i class="fa fa-pencil"></i></pg-item-header-button>';

            if ($user->hasPermission('page-clone', $p) && $isPgPage) {
                //clone
                $header .= '<pg-item-header-button class="pg-clone" data-template="' . $p->template->name . '" data-parent="' . $p->parent()->id . '"><i class="fa fa-fw fa-clone" data-name="fa-clone" title="Clone"></i></pg-item-header-button>';
            }
            if ($user->hasPermission('page-lock', $p) && $isPgPage) {
                //lock
                $header .= '<pg-item-header-button class="pg-lock" href="#"><i class="fa fa-lock" title="' . $this->_('Unlock') . '"></i><i class="fa fa-unlock" title="' . $this->_('Lock') . '"></i></pg-item-header-button>';
            }
            if ($user->isSuperuser() && $isPgPage && !$this->modules->isInstalled('PageGridDemoMode')) {
                //symbol
                $header .= '<pg-item-header-button class="pg-symbol" title="' . $this->_('Create Symbol') . '" href="#"><i class="fa fw fa-cube"></i></pg-item-header-button>';
            }
            if ($user->hasPermission('page-delete', $p) && $isPgPage) {
                //delete
                $header .= '<pg-item-header-button class="pg-delete" title="' . $this->_('Mark for deletion') . '" href="#"><i class="fa fa-trash"></i></pg-item-header-button>';
            }
            $header .= '</span>';
        }
        return $header;
    }

    //function to get status classes for permissions 
    public function getStatusClasses($p) {
        $user = $this->user;
        $statusClass = "";

        //if frontend return empty string
        if (!$this->isBackend()) return $statusClass;

        //set css classes
        if ($p->editable() == 0) $statusClass .= " pg-no-edit";
        if ($user->hasPermission('page-pagegrid-edit', $p) == 0) $statusClass .= " pg-no-edit";
        if ($p->is(Page::statusUnpublished)) $statusClass .= " pg-unpublished";
        if ($p->is(Page::statusHidden)) $statusClass .= " pg-hidden";
        if ($p->is(Page::statusLocked)) $statusClass .= " pg-locked";
        if (!($user->hasPermission('page-edit', $p))) $statusClass .= " pg-locked";
        if ($user->hasPermission('pagegrid-drag')) $statusClass .= " pg-item-draggable";
        if ($user->hasPermission('pagegrid-resize')) $statusClass .= " pg-item-resizable";
        if ($user->hasPermission('pagegrid-select'))  $statusClass .= " pg-permission-select";
        if ($user->hasPermission('page-add', $p)) $statusClass .= " pg-permission-add";
        if ($user->hasPermission('page-create', $p)) $statusClass .= " pg-permission-create";
        if ($user->hasPermission('page-delete', $p)) $statusClass .= " pg-permission-delete";
        if ($user->hasPermission('page-clone', $p) && $user->hasPermission('page-create', $p)) $statusClass .= " pg-permission-clone";
        if ($p->is(Page::statusUnpublished)) $statusClass .= " pg-unpublished";
        if ($p->is(Page::statusHidden)) $statusClass .= " pg-hidden";
        if ($p->is(Page::statusLocked)) $statusClass .= " pg-locked";

        if ($p->parent->name === 'pg-symbols' && $p->parent->template->name === 'pg_container') $statusClass .= " pg-symbol pg-ref";
        //END set symbol class

        return $statusClass;
    }

    public function ___getConfigInputfields() {
        $inputfields = parent::___getConfigInputfields();
        //add inputfields here if needed
        return $inputfields;
    }

    // Methodes returns classnames and tagnames for rendering items in frontend + backend
    // get classes

    public function getCssClasses($item, $options = null, $itemId = 'pgitem') {

        $itemData = $item->meta()->pg_styles;
        $templateName = str_replace('_', '-', $item->template->name);
        $defaultClasses = $item->name . ' ' . $templateName . ' pg-item';
        $cssClasses = '';
        $backend = $this->isBackend();

        if ($options == 'parentClasses') {

            $defaultClasses = ' pg ' . $item->name . ' ' . $templateName;

            if ($backend) {
                $defaultClasses .= ' pg-droppable ';
            }
        }

        if ($itemId !== 'pgitem') {
            $defaultClasses = '';
        }

        if (isset($itemData)) {

            if (isset($itemData[$itemId])) {
                if (!(empty($itemData[$itemId]['cssClasses']))) {
                    $cssClasses = $itemData[$itemId]['cssClasses'] . ' ';
                    $cssClasses = preg_replace('/\s+/', ' ', $cssClasses);
                }

                // add animation classes 
                // aaaa
                $animations = '';

                //look for all breakpoints
                if (isset($itemData[$itemId]['breakpoints'])) {
                    foreach ($itemData[$itemId]['breakpoints'] as $breakpoint) {
                        $animations .= isset($itemData[$itemId]['breakpoints'][$breakpoint['name']]['css']['--pg-animation']) ? $itemData[$itemId]['breakpoints'][$breakpoint['name']]['css']['--pg-animation'] . ',' : '';
                    }
                }

                //look if gloabl classes have animations set
                $cssClassesArray = explode(' ', $cssClasses);
                $cssClassesParentId = $this->pages->findOne('name=pg-classes, template=pg_container, include=all')->id;
                foreach ($cssClassesArray as $cssClassName) {
                    $animation = $this->pages->findOne("name=$cssClassName, template=pg_container, include=all, has_parent=$cssClassesParentId");
                    if ($animation->id) {
                        $itemData = $animation->meta()->pg_styles;
                        if (isset($itemData[$itemId]['breakpoints'])) {
                            foreach ($itemData[$itemId]['breakpoints'] as $breakpoint) {
                                $animations .= isset($itemData[$itemId]['breakpoints'][$breakpoint['name']]['css']['--pg-animation']) ? $itemData[$itemId]['breakpoints'][$breakpoint['name']]['css']['--pg-animation'] . ',' : '';
                            }
                        }
                    }
                }

                $animationsArray = explode(',', $animations);
                $animationsArray = array_unique($animationsArray);

                foreach ($animationsArray as $animationName) {
                    $animationParentId = $this->pages->findOne('name=pg-animations, template=pg_container, include=all')->id;
                    $animation = $this->pages->findOne("name=$animationName, template=pg_container, include=all, has_parent=$animationParentId");
                    if ($animation->id) {
                        $animationData = $animation->meta()->pg_styles;
                        $event = isset($animationData[$animation->name]['animation-event']) ? $animationData[$animation->name]['animation-event'] : 'load';
                        $eventClass = 'pg-event-' . $event;
                        //add prfex pg-animation- to animation classes
                        $cssClasses .= ' ' . $eventClass . ' pg-animation-' . $animationName . ' ';

                        //add first keyframe class to start from first frame
                        if (!$backend) {
                            foreach ($animationData as $aData) {
                                if (isset($aData['keyframe']) && $aData['keyframe'] === '0') {
                                    $cssClasses .= ' ' . $aData['id'];
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($options == 'addedClasses') {
            $Classes = $cssClasses;
        } else {
            $Classes = $defaultClasses . ' ' . $cssClasses;
        }

        //remove last empty space
        $Classes = rtrim($Classes);

        return $Classes;
    }

    //get tag name
    public function getTagName($item) {

        $tagName = 'div';
        $itemData = $item->meta()->pg_styles;

        if (isset($itemData)) {
            if (isset($itemData['pgitem'])) {
                $tagName = $itemData['pgitem']['tagName'];
            }
        }

        // if p change to custom tag to prevent html wrapping break with inline editor divs
        if ($tagName == 'p' && $this->user->isLoggedin()) {
            $tagName = 'pg-ptag';
            if (!$this->isBackend() && $this->ft->inlineEditorFrontDisable) $tagName = 'p';
        }

        return $tagName;
    }
    // END callable Methodes -------------------------------

    //add scripts with same name as block file

    public function scripts($mainPage, $updateAnimations = false) {
        $lastItem = null;
        $jsFiles = "";
        $backend = $this->isBackend();
        $customJs = $this->ft->customScript && !$backend ? '<script>' . $this->ft->customScript . '</script>' : '';

        //load js plugins
        foreach ($this->ft->plugins as $pluginName) {
            $jsFiles .= '<script type="text/javascript" src="' . $this->config->urls->InputfieldPageGrid . 'js/' . $pluginName . '.js"></script>';
        }

        if ($mainPage->id) {
        } else {
            return;
        }

        $items = new PageArray();
        $itemsParent = $this->pages->get('pg-' . $mainPage->id);

        if ($itemsParent->id) {
            //getAncestors returns parent and grandchildren. needed because of bug. $page->find('') is not returning all pages
            $items->add($this->getAncestors($itemsParent));
        } else {
            //getAncestors returns parent and grandchildren. needed because of bug. $page->find('') is not returning all pages
            $items->add($this->getAncestors($mainPage));
        }

        $classNames = '';
        foreach ($items as $item) {

            //build classes array to check for animations
            $itemData = $item->meta()->pg_styles;
            if (isset($itemData)) {
                foreach ($itemData as $data) {
                    if (isset($data['cssClasses'])) $classNames .= $data['cssClasses'];
                }
            }
            //END build classes array to check for animations

            $filename = wire('config')->paths->templates . 'blocks/' . $item->template->name . '.js';
            $filenameUrl = wire('config')->urls->templates . 'blocks/' . $item->template->name . '.js';

            if ($item->template->name !== $lastItem && file_exists($filename)) {
                $jsFiles .= '<script type="text/javascript" src="' . $filenameUrl . '"></script>';
                $lastItem = $item->template->name;
            }

            if (file_exists($filename) == 0) {
                //if no file found check in module
                $filename = wire('config')->paths->siteModules . 'PageGridBlocks/blocks/' . $item->template->name . '.js';
                $filenameUrl = wire('config')->urls->siteModules . 'PageGridBlocks/blocks/' . $item->template->name . '.js';

                if ($item->template->name !== $lastItem && file_exists($filename)) {
                    $jsFiles .= '<script type="text/javascript" src="' . $filenameUrl . '"></script>';
                    $lastItem = $item->template->name;
                }
            }
        }

        //aaaa
        //animation data to access with js on frontend if animation found
        $jsAnimationData = "";
        $animationData = [];
        $animationsSelectors = [];
        $animationsParent = $this->pages->get('name=pg-animations, template=pg_container');
        $classNames = trim($classNames);
        $classNames = str_replace(' ', '|', $classNames);

        if ($classNames && $animationsParent->findOne('')) {
            $cssClassesParent = $this->pages->get('name=pg-classes, template=pg_container');
            $animationItems = new PageArray();
            $animationItems->add($cssClassesParent->find("name=$classNames"));
            $animationItems->add($items);
            $animationNames = '';

            //get items to init animations for
            foreach ($animationItems as $item) {
                //check if animation exists on page
                //look for all breakpoints
                $itemData = $item->meta()->pg_styles;
                if (isset($itemData)) {
                    foreach ($itemData as $childData) {
                        if (isset($childData['id']) && isset($childData['breakpoints'])) {
                            foreach ($childData['breakpoints'] as $breakpoint) {
                                if (!isset($childData['breakpoints'][$breakpoint['name']]['css']['--pg-animation'])) continue;
                                $animationNames .= $childData['breakpoints'][$breakpoint['name']]['css']['--pg-animation'] . ',';
                                $selector = '.';
                                $selector2 = '.';
                                if (isset($childData['tagName']) && $childData['tagName'] === $item->name) {
                                    //we have a tag so we remove "."
                                    $selector = '';
                                }

                                if (isset($childData['tagName']) && $childData['tagName'] === $childData['id']) {
                                    $selector2 = '';
                                }

                                if ($childData['id'] === 'pgitem') {
                                    $animationsSelectors[] = $selector . $item->name;
                                } else {
                                    $animationsSelectors[] = $selector . $item->name . ' ' . $selector2 . $childData['id'];
                                }
                            }
                        }
                    }
                }
            }

            if (count($animationsSelectors) && $animationNames) {

                $animationsSelectors = array_unique($animationsSelectors);
                $animationNames = explode(',', $animationNames);

                foreach ($animationNames as $animationName) {
                    if (!$animationName) continue;
                    $animationPage = $animationsParent->findOne('name=' . $animationName);
                    if (!$animationPage) continue;
                    if (!$animationPage->id) continue;
                    $itemData = $animationPage->meta()->pg_styles;
                    if (isset($itemData)) {
                        foreach ($itemData as $childData) {
                            if (isset($childData['id'])) {
                                if (isset($childData['keyframe'])) {
                                    $animationData[$animationPage->name]['keyframes'][$childData['keyframe']] = $childData;
                                } else {
                                    $animationData[$animationPage->name][$childData['id']] = $childData;
                                }
                            }
                        }
                    }
                }
                //add js var to access data
                $dataJson = json_encode($animationData);
                $dataJsonSelectors = json_encode($animationsSelectors);
                $jsAnimationData = '<script id="pg-animation-data">var pgAnimations = ' . $dataJson . '; var pgAnimationsSelectors = ' . $dataJsonSelectors . ';</script>';
                //add animation js file
                $jsFiles .= '<script type="text/javascript" src="' . $this->config->urls->InputfieldPageGrid . 'js/pg-animations.js"></script>';
            }
        }

        // if ($updateAnimations) bdb([$dataJson, $dataJsonSelectors]);
        //if $updateAnimations is true, no neeed to return js files
        if ($updateAnimations) return json_encode([$animationData, $animationsSelectors]);
        echo $jsAnimationData . $jsFiles . $customJs;
    }

    public function fonts($p) {

        $items = json_encode($p->meta()->pg_styles);
        $items = json_decode($items, false); //convert to object
        $fonts = '';
        $font = '';
        $googleFontsJson = file_get_contents(($this->config->paths->InputfieldPageGrid . "googleFonts.json"));
        $googleFontsList = json_decode($googleFontsJson, true);

        if (isset($items) == 0) {
            return;
        }

        foreach ($items as $item) {

            if (isset($item->breakpoints) == 0) {
                return;
            }

            foreach ($item->breakpoints as $breakpoint) {
                foreach ($breakpoint->css as $style => $val) {
                    if ($style == 'font-family') {

                        //check if font is a google font
                        $validFont = array_key_exists($val, $googleFontsList);
                        if (!$validFont) {
                            continue;
                        }

                        $fontVariants = [];
                        $fontVariantPrefix = '';
                        $fontStyle = '';
                        $fontWeight = '';

                        //get variants for this font as array
                        if (array_key_exists('variants', $googleFontsList[$val])) $fontVariants = explode(",", $googleFontsList[$val]['variants']);

                        foreach ($breakpoint->css as $style2 => $fvariant) {

                            if (!in_array($fvariant, $fontVariants)) continue;

                            if ($style2 == 'font-style' && $fvariant === 'italic') {
                                if ($fontStyle) $fontStyle .= ',';
                                $fontStyle .= '1';
                            }
                            if ($style2 == 'font-weight') {
                                if ($fontWeight || $fontStyle) $fontWeight .= ',';
                                $fontWeight .= $fvariant;
                            }
                        }

                        //add prefix if variant is set
                        if ($fontStyle || $fontWeight) $fontVariantPrefix = ':';
                        if ($fontStyle) $fontVariantPrefix .= 'ital';
                        if ($fontStyle && $fontWeight) $fontVariantPrefix .= ',';
                        if ($fontWeight) $fontVariantPrefix .= 'wght@';

                        // skip font loading for local fonts
                        $localFontNamesArray = $this->getFontNames();
                        $localFontNames = '';

                        foreach ($localFontNamesArray as $localFont) {
                            $fontExt = '.' . pathinfo($localFont, PATHINFO_EXTENSION);
                            $localFontNames .= str_replace($fontExt, '', $localFont) . ',';
                        }

                        // skip google font loading for these fonts
                        $systemFontsString = 'Arial,Helvetica+Neue,Courier+New,Times+New+Roman,Comic+Sans+MS,Verdana,Impact,' . $localFontNames;
                        $systemFontsString = trim($systemFontsString);
                        $systemFonts = explode(',', $systemFontsString);

                        // $fontName = strstr( $val, ',', true );
                        $fontName = preg_replace('/\s+/', '+', $val);
                        $fontName = str_replace('"', "", $fontName);

                        if ($fontName !== '') {
                            $font = $fontName . $fontVariantPrefix . $fontStyle . $fontWeight;
                        }

                        foreach ($systemFonts as $systemFont) {
                            if ($fontName == $systemFont) {
                                $font = "";
                            }
                        }

                        if ($fonts !== '' && $font !== '' && strpos($val, ',') == false) {
                            $fonts .= '&';
                        }

                        //add font only if it is single value and has no comma list
                        if (strpos($val, ',') == false) {
                            $fonts .= $font;
                        }
                    }
                }
            }
        }
        return $fonts;
    }

    //helper methode to get fonts
    public function getFontPath() {

        $filePath = $this->config->paths->templates . 'fonts/';

        //create font folder if not present
        if (!file_exists($filePath)) {
            mkdir($filePath, 0755, true);
        }

        return $filePath;
    }

    public function getFontNames() {
        //list files
        $filePath = $this->getFontPath();
        $files = array_diff(scandir($filePath), array('.', '..', '.DS_Store'));
        $fontFiles = array();

        foreach ($files as $file) {
            $ext = pathinfo($filePath . $file, PATHINFO_EXTENSION);
            if ($ext == 'woff' || $ext == 'woff2') {
                $fontFiles[] = $file;
            }
        }

        return $fontFiles;
    }

    public function ___renderStyles($p, $id = 0, $keyframeClass = 0) {

        if (!$p || !$p->id) return;

        //check if symbol page was found
        if ($p->meta('pg_symbol') !== null && $p->meta('pg_symbol')) {
            $symbolId = $p->meta('pg_symbol');
            $symbol = $this->pages->get($symbolId);
            if ($symbol && $symbol->id && $symbol->parent->name === 'pg-symbols') {
                $p = $symbol;
            } else {
                //if no symbol page found remove reference
                $p->meta()->remove('pg_symbol');
            }
        }
        //END check if symbol page was found

        $css = '';
        $cssSelector = '';
        $items = $p->meta()->pg_styles;
        $backend = $this->isBackend();

        if ($id) {
            $items = [];
            if (!isset($p->meta()->pg_styles[$id])) {
                return;
            }
            $items[$id] = $p->meta()->pg_styles[$id];
        }

        if (isset($items) == 0) {
            return;
        }

        //if page is item container skip parent selector
        $rootEl = 0;
        if ($p->template->name == 'pg_container' && $p->parent()->name !== 'pg-classes') {
            $rootEl = 1;
        }
        //END if page is item container skip parent selector

        foreach ($items as $item) {


            if (isset($item['breakpoints']) == 0) {
                return;
            }

            //to be able to replace style tag in backend
            if ($backend) {

                $cssId = $item['id'];

                // if pgitem allways use page name
                // do not save page name in item to be able to change page name later
                if (substr($item['id'], 0, 6) == 'pgitem') {
                    $cssId = $p->name;
                    // $item['id'] = $p->name;
                } else {
                    $cssId = $p->name . $item['id'];
                }

                $globalClass = '';
                if ($p->parent()->name == 'pg-classes') {
                    $globalClass = 'pg-global';
                    //add g- prfix for global classes to make unique id
                    $cssId = 'g-' . $cssId;
                }

                if ($p->parent()->name === 'pg-animations') {
                    $cssId = $item['id'];
                }

                $css .= '<style class="pg-style pg-style-id-' . $cssId . ' ' . $globalClass . '">';
            }


            if ($p->parent()->name === 'pg-animations' && isset($item['animation-event']) && $p->name !== 'unset') {

                // add hover only if device supports it
                if ($item['animation-event'] === 'hover') {
                    $css .= '@media (hover: hover), (-ms-high-contrast:none) { ';
                }

                $css .= '.pg-animation-' . $p->name . '.pg-event-trigger-' . $item['animation-event'] . '{ animation-name:' . $p->name . '; }';

                if ($item['animation-event'] === 'hover') {
                    $css .= '} ';
                }
            }

            foreach ($item['breakpoints'] as $breakpoint) {

                //if no css continue to next
                if (empty($breakpoint['css'])) {
                    continue;
                }

                if (!($breakpoint['name'] == 'base') && !($breakpoint['name'] == 's') && $backend == 0) {
                    $css .= $breakpoint['size'] . '{ ';
                }

                if (!($breakpoint['name'] == 'base') && !($breakpoint['name'] == 's') && $backend) {
                    $css .= '.breakpoint-' . $breakpoint['name'] . ' ';
                }

                // breakpoint s is more specific
                if ($breakpoint['name'] == 's' && $backend == 0) {
                    $css .= $breakpoint['size'] . '{ html ';
                }

                if ($breakpoint['name'] == 's' && $backend) {
                    $css .= ' html.breakpoint-' . $breakpoint['name'] . ' ';
                }

                //set selector
                //set class or tagname as selector
                $cssSelector = '';

                if (isset($item['cssClass'])) {

                    //if no cssClass use page name
                    if ($item['cssClass'] == '') {
                        $item['cssClass'] = $p->name;
                    }

                    //for animations and classes there is no tagname so set it to empty
                    if (!isset($item['tagName'])) {
                        $item['tagName'] = '';
                    }

                    // if ($item['id'] == 'pgitem:hover') {
                    //     $item['cssClass'] = $p->name . ':hover';
                    // }

                    //force parent class if class is not page name (subitem)
                    if (substr($item['id'], 0, 6) !== 'pgitem' && $rootEl == 0) {
                        if ($item['id'] == strtolower($item['tagName']) || $item['id'] == strtolower($item['tagName']) . ':hover') {
                            $item['cssClass'] = $p->name . ' ' . $item['id'];
                        } else {
                            $item['cssClass'] = $p->name . ' .' . $item['id'];
                        }
                    }
                    //END force parent class if class is not page name (subitem)

                    if ($item['cssClass'] == strtolower($item['tagName'])) {

                        $cssSelector = '';
                        //style p custom tag the same as p (html nesting bug with inline editor)
                        if ($item['tagName'] == 'p' && $this->user->isLoggedin()) {
                            $cssSelector = 'pg-ptag, ';
                        }

                        $cssSelector .= strtolower($item['tagName']);
                    } else {
                        $cssSelector = '.' . $item['cssClass'];
                    }

                    //if animations use prefix and pagename as classname
                    if ($p->parent()->name === 'pg-animations') {
                        $cssSelector = '.pg-animation-' . $p->name;
                    }
                    //if animations keyframe use keyframe as classname
                    if ($p->parent()->name === 'pg-animations' && isset($item['keyframe'])) {

                        //if backend set keyframe as class (animation will be triggered on keyrame click)
                        if ($keyframeClass) {
                            $cssSelector = '.' . $item['id'];
                            //last keyframe needs to be more specific
                            if ($item['keyframe'] === '100')  $cssSelector = '.pg .' . $item['id'];
                        } else {
                            $cssSelector = $item['keyframe'] . '%';
                        }
                    }

                    $css .= $cssSelector . '{ ';
                }

                foreach ($breakpoint['css'] as $style => $val) {

                    $fallbackFonts = $this->ft->fallbackFonts;

                    if ($style === 'font-family' && $fallbackFonts) {
                        $val = $val . ', ' . $this->ft->fallbackFonts;
                    }

                    if ($style === '--pg-animation' && $val === 'unset') {
                        $css .= 'animation-name:unset!important;';
                    }

                    $css .= $style . ': ' . $val . '; ';
                }

                $css .= ' } ';

                if (!($breakpoint['name'] == 'base') && $backend == 0) {
                    $css .= ' } ';
                }
            }

            //to be able to replace style tag in backend
            if ($backend) {
                $css .= '</style>';
            }
        }

        return $css;
    }

    public function styles($mainPage, $loadDefaults = 1, $loadGlobalClasses = 1, $loadFiles = 1, $loadFonts = 1) {

        $itemCss = '';
        $cssBackend = '';
        $backend = $this->isBackend();
        $lastItem = 0;
        $cssTemplates = '';
        $defaults = '';
        $fonts = '';
        $cssMainPage = '';
        $customCss = '';
        $animations = '';

        // page array to hold items to load files
        $itemsArray = new PageArray();

        if ($mainPage->id) {
        } else {
            return;
        }

        $itemsParent = $this->pages->get('pg-' . $mainPage->id);

        //load backend css only if rendering page with pg field
        if ($backend && $itemsParent->id) {
            $cssBackendUrl = wire('config')->urls->InputfieldPageGrid . "css/main.css";
            $cssBackend = '<link rel="stylesheet" type="text/css" href="' . $cssBackendUrl . '">';
        }

        //add global page for custom classes
        if ($loadGlobalClasses) {
            $globalClasses = $this->pages->get('pg-classes');

            foreach ($globalClasses->find('') as $class) {
                $itemsArray->add($class);
            }
        }

        //get items
        if ($itemsParent->id) {
            //getAncestors returns parent and grandchildren. needed because of bug. $page->find('') is not returning all pages
            $pageItems = $this->getAncestors($itemsParent);
            $itemsArray->add($pageItems);
            //add symbol children, fix
            foreach ($pageItems as $pageItem) {
                if ($pageItem->meta('pg_symbol') !== null && $pageItem->meta('pg_symbol')) {
                    $symbolId = $pageItem->meta('pg_symbol');
                    $symbol = $this->pages->get($symbolId);
                    if ($symbol && $symbol->id && $symbol->hasChildren()) {
                        $itemsArray->add($this->getAncestors($symbol));
                    }
                }
            }
        } else {
            //look for styles on main page
            //might be good to be able to use regular pages to save stuff?
            //getAncestors returns parent and grandchildren. needed because of bug. $page->find('') is not returning all pages
            $itemsArray->add($this->getAncestors($mainPage));
        }
        //END get items

        //in backend get all symbol css
        if ($backend) {
            $symbolParent = $this->pages->get("name=pg-symbols, template=pg_container");
            $itemsArray->add($this->getAncestors($symbolParent));
        }
        //end in backend get all symbol css

        // render wrapper styles 
        $cssMainPage = $this->renderStyles($mainPage);

        //render defaults 
        if ($loadDefaults == 1) {
            $defaults = include 'styleDefaults.php';
        }

        foreach ($itemsArray as $item) {

            //load template file children
            if ($loadFiles) {
                $filename = wire('config')->paths->templates . 'blocks/' . $item->template->name . '.css';
                $filenameUrl = wire('config')->urls->templates . 'blocks/' . $item->template->name . '.css';

                if ($item->template->name !== $lastItem && file_exists($filename)) {
                    $cssTemplates .= '
    <link rel="stylesheet" type="text/css" href="' . $filenameUrl . '">';
                    $lastItem = $item->template->name;
                }

                if (file_exists($filename) == 0) {
                    //if no file found check in module
                    $filename = wire('config')->paths->siteModules . 'PageGridBlocks/blocks/' . $item->template->name . '.css';
                    $filenameUrl = wire('config')->urls->siteModules . 'PageGridBlocks/blocks/' . $item->template->name . '.css';

                    if ($item->template->name !== $lastItem && file_exists($filename)) {
                        $cssTemplates .= '
    <link rel="stylesheet" type="text/css" href="' . $filenameUrl . '">';
                        $lastItem = $item->template->name;
                    }
                }
            }

            //render item css
            $itemCss .= $this->renderStyles($item);

            //render google fonts
            $font = $this->fonts($item);
            if ($loadFonts && $font) {
                $fonts .= '&family=' . $font;
            }

            //load animations
            // aaaa
            $itemData = $item->meta()->pg_styles;
            if (isset($itemData)) {
                foreach ($itemData as $childData) {
                    if (isset($childData['id']) && isset($childData['breakpoints'])) {
                        foreach ($childData['breakpoints'] as $breakpoint) {
                            $animations .= isset($childData['breakpoints'][$breakpoint['name']]['css']['--pg-animation']) ? $childData['breakpoints'][$breakpoint['name']]['css']['--pg-animation'] . ',' : '';
                        }
                    }
                }
            }
        }

        //render animations css if they exists on page
        //aaaa
        $animationsCss = '';
        $animationsParent = $this->pages->get('pg-animations');

        if ($loadGlobalClasses && $animationsParent->id) {

            // if backend get all animation css
            if ($backend) {
                foreach ($animationsParent->find('') as $animationPage) {
                    $animationsCss .= $this->getAnimationCss($animationPage);
                }
            }

            //if demo get all user data (only demo code in this file)
            if (!$backend && $this->modules->isInstalled('PageGridDemoMode') && $this->user->hasRole('pagegrid-demo')) {
                $user = $this->user;
                $parentIDAnimations = $this->pages->get('name=pg-animations, template=pg_container')->id;
                $uPages = $this->pages->find("created_users_id=$user, parent=$parentIDAnimations");
                foreach ($uPages as $animationPage) {
                    $animationsCss .= $this->getAnimationCss($animationPage);
                }
            }
            //END if demo get all user data (only demo code in this file)

            // if frontend get just the animation for the page, load also in backend to get correct order of classes (animations can be sorted)
            if ($animations) {
                $animationsArray = explode(',', $animations);
                $animationsArray = array_unique($animationsArray);
                foreach ($animationsArray as $animationName) {
                    $animationPage = $this->pages->findOne("name=$animationName, template=pg_container, has_parent=$animationsParent->id");
                    if ($animationPage->id) {
                        $animationsCss .= $this->getAnimationCss($animationPage);
                    }
                }
            }
        }
        //END render animations

        //load google fonts
        if ($fonts) {
            if ($this->ft->fontPrivacy) {
                $preconnect = '<link rel="preconnect" href="https://api.fonts.coollabs.io" crossorigin>';
                $fonts = $preconnect . '<link rel="stylesheet" type="text/css" href="https://api.fonts.coollabs.io/css2?' . $fonts . '">';
            } else {
                $preconnect = '<link rel="preconnect" href="https://fonts.googleapis.com">';
                $preconnect .= '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
                $fonts = $preconnect . '<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css2?' . $fonts . '&display=swap">';
            }
        }

        //backend already returns multiple style tags for easy replacement
        if (!$backend) {
            $itemCss = '<style class="pg-style pg-style-items">' . $cssMainPage . $itemCss . $animationsCss . '</style>';
        } else {
            $itemCss = $cssMainPage . $itemCss . $animationsCss;
        }

        return  $cssBackend . $cssTemplates . $defaults . $fonts . $itemCss . $customCss;
    }

    public function getAnimationCss($animationPage) {
        if (!$animationPage->id) return '';

        $backend = $this->isBackend();
        $animationsCss = '';

        //render animation class
        $aniCss = $this->renderStyles($animationPage, $animationPage->name);

        //render animation keyframes
        $keyframesData = $animationPage->meta()->pg_styles;
        $keyframesCss = '';

        if (!isset($keyframesData)) return '';

        foreach ($keyframesData as $keyframeData) {
            //first item is not a keyframe, but animation so skip it

            if (isset($keyframeData['keyframe'])) {
                //set 3. parameter to get classes or % for keyframe (for backend we all keyframes as classes, so we can select them)
                $keyframesCss .= $this->renderStyles($animationPage, $keyframeData['id'], $backend);

                //add first keyframe css (on frontend only first and last keyframe are needed)
                if ($keyframeData['keyframe'] === '0' && !$backend) {
                    $aniCss .= $this->renderStyles($animationPage, $keyframeData['id'], true);
                }

                //add last keyframe css to preserve states (on frontend only first and last keyframe are needed)
                if ($keyframeData['keyframe'] === '100' && !$backend) {
                    $aniCss .= $this->renderStyles($animationPage, $keyframeData['id'], true);
                }
            }
        }

        //if backend get keyframes as classes for steps and build animation with js
        if ($backend) {
            $animationsCss .= $aniCss . $keyframesCss;
        } else {
            $animationsCss .= $aniCss . ' @keyframes ' . $animationPage->name . ' {' . $keyframesCss . '} ';
        }

        return $animationsCss;
    }

    //helper to return main page from item (argument: $page inside item template)
    public function getPage($page) {
        if (!$page->id) return false;
        if (!$page->parents()->get('template=pg_container')) return false;
        $itemParent = $page->closest('template=pg_container');

        //get field container
        if ($itemParent->parent('template=pg_container')->id) $itemParent = $itemParent->parent();

        if (!$itemParent->id) return false;
        $mainPageId = preg_replace("/[^0-9]/", "", $itemParent->name);
        if (!$mainPageId) return false;
        $mainPageId = $this->sanitizer->intUnsigned($mainPageId); // force a positive number
        if (!$mainPageId) return false;

        $mainPage = $this->pages->get($mainPageId);
        if (!$mainPage->id) return false;
        return $mainPage;
    }

    // options gets rendered inside template and read before render
    public function renderOptions($options = []) {
        if (!$this->isBackend()) return;
        // if (!isset($options["page"])) return;
        // $item = $options["page"];
        // if (!$item->id) return;

        // convert array to json and set as data-atribite
        $renderOptions = htmlspecialchars(json_encode($options), ENT_QUOTES, 'UTF-8');

        //put tags in data attribute for convenience
        $tags = '';
        if (isset($options["tags"])) {
            $tagsValue = $this->sanitizer->htmlClasses($options["tags"]);
            $tagsValue = strtolower($tagsValue);
            $tags = ' data-pg-tags="' . $tagsValue . '"';
        }

        // needs echo instead of return
        // will be processed before render on itemRender()
        echo '<pg-data class="pg-data" ' . $tags . ' pg-data-options="' . $renderOptions . '"></pg-data>';
    }

    //returns grandchildren. needed because of bug. $page->find('') is not returning all pages
    public function getAncestors($p, $level = 20) {
        $retPages = (new PageArray())->add($p);
        if ($level > 0) {
            foreach ($p->children as $child)
                $retPages->add($this->getAncestors($child, $level - 1));
        }

        // $p->parents()->rebuildAll();
        // $retPages->add($p->find(''));

        return $retPages;
    }

    /**
     * Set a property to this Inputfield
     * 
     * @param string $key
     * @param mixed $value
     * @return $this
     *
     */
    public function set($key, $value) {
        if ($key == 'template_id' && $value) {
            // convert template_id to $this->rowTemplates array
            if (!is_array($value)) $value = array($value);
            foreach ($value as $id) {
                $template = $this->wire('templates')->get($id);
                if ($template) $this->rowTemplates[$id] = $template;
            }
            return $this;
        } else {
            return parent::set($key, $value);
        }
    }
}
