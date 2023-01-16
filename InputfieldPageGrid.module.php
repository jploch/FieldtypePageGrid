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
            'version' => '0.0.1',
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
        $this->set('columnWidth', '');
        $this->set('sortfields', '-date');
        $this->set('template_id', 0); // placeholder only 
    }

    public function ___renderValue() {
        return 'inputfield render value';
    }

    public function ___render() {

        $user = wire('user');
        $this->config->styles->add($this->config->urls->InputfieldPageGrid . "css/main.css");
        $this->config->scripts->add($this->config->urls->InputfieldPageGrid . "js/main.js'");
        return $this->renderField();
    }

    public function getData() {
        //make data available to js
        $globalPage = $this->pages->get('pg-classes');
        $globalPageData = [];
        $globalPageData[$globalPage->id] = [];

        foreach ($globalPage->find('') as $child) {
            $itemData = $child->meta()->pg_styles;
            if (isset($itemData)) {
                foreach ($itemData as $childData) {
                    if (isset($childData['id'])) {
                        $globalPageData[$globalPage->id][$child->name] = $childData;
                    }
                }
            }
        }

        //just get all data, this will work for nested items, as well as ref fields, performance?
        $pageItems = $this->pages->get('pg-items');
        $itemsArray = new PageArray();

        foreach ($pageItems->children() as $child) {
            $itemsArray->add($child);
            foreach ($child->find('') as $c) {
                $itemsArray->add($c);
            }
        }

        foreach ($itemsArray as $child) {
            $itemData = $child->meta()->pg_styles;
            if (isset($itemData)) {
                $globalPageData[$child->id] = $itemData;
            }
        }

        return json_encode($globalPageData);
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

            // // // bd('Old id found:' . $oldEditID);
            // // // bd($editID);

            if ($oldEditID != $editID) {
                $itemsParentOld = $this->pages->get('pg-' . $oldEditID);
                if ($itemsParentOld->id && $itemsParent->id == 0) {
                    // // // bd('change name');
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

        // set field name to item meta, not needed, but convinient?
        // $pagesToRender = $itemsParent->find('');
        // $itemsParent->meta()->set('pg_field', $this->name);

        // foreach ($pagesToRender as $pgItem) {
        //     $pgItem->meta()->set('pg_field', $this->name);
        // }
        // END set field name to item meta, not needed, but convinient?

        //import old data
        // $pagesToRenderOld = $this->attr('value');

        // if ($pagesToRenderOld) {
        //     foreach ($pagesToRenderOld as $item) {
        //         $item->parent = $itemsParent;
        //         $item->save();
        //     }
        // }
        //import old data

        //END new pages to render based on items parent

        $moduleUrl = $this->config->urls->InputfieldPageGrid;
        $user = wire('user');
        $settings = '';
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

        if ($user->hasPermission('pagegrid-style-panel') && $this->ft->stylePanel) {
            include 'stylePanel.php';
            $settings = '<div class="pg-settings pg-settings-content">' . $settings . '</div>';
        }

        // render the 'Add New' buttons for each template
        if ($user->hasPermission('pagegrid-add') && $user->hasPermission('pagegrid-drag')) {
            $editID = (int) $this->wire('input')->get('id');
            if (!$editID && $this->wire('process') instanceof WirePageEditor) $editID = $this->wire('process')->getPage()->id;
            $parentID = $itemsParent->id;
            $addItems = '<div class="pg-add-container"><div class="pg-add-content">';
            foreach ($this->rowTemplates as $template) {
                /** @var Template $template */

                // keep this line for future updates, makes it possible to add items via modal if link is clicked, maybe alternative for non super users oneday
                // $url = $this->wire('config')->urls->admin . "page/add/?modal=1&template_id=$template->id&parent_id=$parentID&context=PageGrid";

                if ($template->icon == '') {
                    $tIcon = '<div class="pg-iconletter">' . substr($template->getLabel(), 0, 1) . '</div>';
                } else {
                    $tIcon = wireIconMarkup($template->icon);
                }

                $addItems .= '<div class="pg-add ' . $template->name . '" data-parent-id="' . $parentID . '" data-template-id="' . $template->id . '" template="' . $template->name . '">' . $tIcon . '<span class="ui-button-text">' . $template->getLabel() . '</span></div>';
            }
            $addItems .= '</div></div>';
        }
        // END render the 'Add New' buttons for each template

        $renderMarkup = '<div class="pg-topnav uk-navbar-center">
        <i class="pg-item-list-button pg-topnav-margin-big fa fw fa-list-ul on" title="Item List"></i>
        <i class="pg-undo fa fa-fw fa-reply" data-name="fa-reply" title="Undo" style="cursor: pointer;"></i><i class="pg-redo pg-topnav-margin fa fa-fw fa-share" data-name="fa-share" title="Redo" style="cursor: pointer;"></i>
        <div id="breakpoints-nav">
       <img src="' . $moduleUrl . '/img/phone-portrait-outline.svg" class="breakpoint-icon breakpoint-icon-s" value="@media (max-width: 640px)" breakpoint="s" title="Breakpoint Small">
       <img src="' . $moduleUrl . '/img/phone-landscape-outline.svg" class="breakpoint-icon breakpoint-icon-m" value="@media (max-width: 960px)" breakpoint="m" title="Breakpoint Medium">
       <img src="' . $moduleUrl . '/img/laptop-outline.svg" class="breakpoint-icon breakpoint-icon-base" value="@media (min-width: 640px)" breakpoint="base" title="Breakpoint Base">
       <img src="' . $moduleUrl . '/img/desktop-outline.svg" class="breakpoint-icon breakpoint-icon-l" value="@media (min-width: 1600px)" breakpoint="l" title="Breakpoint Large">
       </div></div>';

        $renderMarkup .= $settings . '<div class="pg-container" data-id="' . $this->pages->get('pg-classes')->id . '" data-field="' . $this->name . '" data-admin-url="' . $this->page->rootParent->url() . 'setup/pagegrid/" data-fallbackfonts="' . $this->ft->fallbackFonts . '">' . $addItems . $dataGlobal;

        //container for item header (item header will be moved here with js)
        $renderMarkup .= '<div id="pg-item-header"></div>';

        $renderMarkup .= '<iframe id="pg-iframe-canvas" src="' . wire('pages')->get($parentPageId)->url . '?backend=1" frameBorder="0" scrolling="no" style="width:100%; max-height:100vh; border:0;"></iframe>';

        $renderMarkup .= '</div>';

        $l = $this->ft->setup();

        return $renderMarkup;
    }

    public function isBackend() {
        $backend = 0;

        if ($this->user->isLoggedin() && (strpos(wire('page')->url, wire('config')->urls->admin) === 0 || isset($_GET['backend']))) {
            $backend = 1;
        }

        // // bd($backend);

        return $backend;
    }

    public function renderGrid($mainPage) {
        $backend = $this->isBackend();
        $statusClass = '';
        $itemsParent = $this->pages->get('pg-' . $mainPage->id);
        $layout = "";

        if ($itemsParent->id) {
            $pagesToRender = $itemsParent->children();
        } else {
            return;
        }

        foreach ($mainPage->fields as $field) {
            if ($field->type instanceof FieldtypePageGrid) {
                $pg = $field;
            }
        }

        foreach ($pagesToRender as $p) {
            $layout .= $this->renderItem($p);
        }

        if ($backend) {

            // trick inline editor to work for first items
            $PageFrontEditData = wire('modules')->getConfig('PageFrontEdit');
            $dummies = '';

            if (isset($PageFrontEditData['inlineEditFields'])) {
                $PageFrontEditFields = $PageFrontEditData['inlineEditFields'];
                foreach ($PageFrontEditFields as $fieldId) {
                    $f = $this->fields->get($fieldId)->name;
                    $dummy = $this->pages->get("$f!=''");
                    if ($dummy->id) {
                        $dummies .= $dummy->$f;
                    }
                }
            }
            // END trick inline editor to work for first items

            $statusClass = '';

            if ($this->user->hasPermission('pagegrid-drag')) {
                $statusClass .= 'pg-sortable';
            }

            $out = '<div id="' . $itemsParent->name . '" class="pg-wrapper pg-main pg-drop pg ' . $this->getCssClasses($itemsParent) . ' ' . $statusClass . '" data-id="' . $itemsParent->id . '" data-field="' . $pg->name . '">' . $layout . '</div>';
            $out .= '<div class="pg-dummies" style="display:none!important;">' . $dummies . '</div>';
        } else {
            $out = '<div class="pg-wrapper pg pg-main ' . $this->getCssClasses($itemsParent) . '">' . $layout . '</div>';
        }

        return $out;
    }

    public function renderItem($p) {
        $user = wire('user');

        //force autonaming/puplishing for all children if only one template selected
        if (count($p->template->childTemplates) == 1) {
            $p->template->childNameFormat = 'pg-autotitle';
            $p->template->save();
        } else {
            $p->template->childNameFormat = '';
            $p->template->save();
        }
        //END force autonaming for all children if only one template selected

        //disable automatic prepending/appending of template file
        $p->template->noAppendTemplateFile = 1;
        $p->template->noPrependTemplateFile = 1;
        $p->template->appendFile = "";
        $p->template->prependFile = "";
        $p->template->save();
        //END automatic prepending/appending of template file

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
        // // // bd($p->parent()->meta()->pg_ajax);
        if (($backend && $this->config->ajax) || ($backend && $p->parent()->meta()->pg_ajax)) {
            // hack: change name to reinit new children of groups after modal edit
            $oldName = $p->name;
            $p->setAndSave('name', $p->name . '-');
            $p->setAndSave('name', $oldName);

            $this->ft->readyFrontEdit($p);
        }
        // END force init inline editor markup

        $imageUpload = '';
        $imageUploadEmpty = '';

        // insert uploader based on module PageGridEdit settings
        $inlineEditFieldsUpload = $this->ft->inlineEditFieldsUpload;

        if (isset($inlineEditFieldsUpload)) {

            foreach ($inlineEditFieldsUpload as $fieldId) {

                $field = wire('fields')->get($fieldId);
                if ($p->template->hasField($field)) {

                    if ($p->$field) {
                        $imageUploadEmpty = ' upload-notEmpty';
                    } else {
                        $imageUploadEmpty = ' upload-empty';
                    }

                    if ($p->$field == '') {
                        $imageUploadEmpty = ' upload-empty';
                    }

                    $imageUpload = '
                        <div class="setting pg-file-picker pg-file-picker-' . $field . ' pg-style-panel">
                          <div class="settings_wrap">
                            <div class="drop_target">
                              <div class="input_button"></div>
                                <input class="inputFile" type="file" data-field="' . $field . '" data-id="' . $p->id . '" data-type="upload"/>
                                </div>
                            </div>
                        </div>
                        ';
                }
            }
        }

        //END insert uploader based on module PageGridEdit settings

        //Read item Settings
        $attributes = '';
        $nestedClasses = '';

        // foreach ($p->meta()->pg_styles as $item) {
        //     $item['state'] = '';
        // }

        $itemData = $p->meta()->pg_styles;

        if (isset($itemData)) {
            if (isset($itemData['pgitem'])) {
                $PageGridItem = $itemData['pgitem'];

                if (isset($PageGridItem['attributes'])) {
                    $attributes = $PageGridItem['attributes'];
                    // // // bd($attributes);
                }
                if (isset($PageGridItem['children'])) {
                    $nestedClasses = 'pg pg-nested ';

                    if ($this->user->hasPermission('pagegrid-drag')) {
                        $nestedClasses .= 'pg-sortable ';
                    }
                }
            }
        }

        //end Read item Settings

        // add class for reffield
        $refPages = $this->getRef($p);

        foreach ($refPages as $refPage) {
            if ($refPage->id) {
                $refStyle = $refPage->meta()->pg_styles;
                if (isset($refStyle)) {
                    $nestedClasses .= 'pg-ref ';
                }
            }
        }
        // END add class for reffield

        // END insert uploader
        $p->of(true);

        // status classes
        $statusClass = "";

        if ($p->editable() == 0) {
            $statusClass .= " pg-no-edit";
        }

        if ($user->hasPermission('page-pagegrid-edit', $p) == 0) {
            $statusClass .= " pg-no-edit";
        }

        if ($p->is(Page::statusUnpublished))
            $statusClass .= " pg-unpublished";
        if ($p->is(Page::statusHidden))
            $statusClass .= " pg-hidden";
        if ($p->is(Page::statusLocked))
            $statusClass .= " pg-locked";

        // add class for permisssions
        if (!($this->user->hasPermission('page-edit', $p))) {
            $statusClass .= " pg-locked";
        }

        if ($this->user->hasPermission('pagegrid-drag')) {

            $statusClass .= " pg-item-draggable";
        }

        if ($this->user->hasPermission('pagegrid-resize')) {

            $statusClass .= " pg-item-resizable";
        }

        // create header
        $header = "";
        $layoutTitle = $p->template->label ? $p->template->label : $p->template->name;

        if ($user->hasPermission('page-add', $p)) {
            $statusClass .= " pg-permission-add";
        }

        if ($user->hasPermission('page-create', $p)) {
            $statusClass .= " pg-permission-create";
        }

        if ($user->hasPermission('page-delete', $p)) {
            $statusClass .= " pg-permission-delete";
        }

        if ($user->hasPermission('page-clone', $p) && $user->hasPermission('page-create', $p)) {
            $statusClass .= " pg-permission-clone";
        }

        if ($p->is(Page::statusUnpublished))
            $statusClass .= " pg-unpublished";
        if ($p->is(Page::statusHidden))
            $statusClass .= " pg-hidden";
        if ($p->is(Page::statusLocked))
            $statusClass .= " pg-locked";

        //set bind class 
        if (null !== $p->meta('pg_bind')) {
            if ($p->meta('pg_bind'))
                $statusClass .= " pg-binded";
        }
        //END set bind class

        if ($p->editable() && $user->hasPermission('page-pagegrid-edit', $p)) {
            $header .= '<span id="pg-item-header-' . $p->id . '" data-id="' . $p->id . '" class="pg-item-header' . $statusClass . '">';
            $header .= '<span>' . $layoutTitle . '</span>';
            $header .= '<a class="pg-edit" title="' . $this->_('Edit') . '" data-url="./?id=' . $p->id . '&amp;modal=1" href="#"><i class="fa fa-pencil"></i></a>';
            $header .= '<a class="pg-clone" data-template="' . $p->template->name . '" data-parent="' . $p->parent()->id . '"><i class="fa fa-fw fa-clone" data-name="fa-clone" title="Clone"></i></a>';
            if ($user->isSuperuser()) {
                $header .= '<a class="pg-lock" href="#"><i class="fa fa-lock" title="' . $this->_('Unlock') . '"></i><i class="fa fa-unlock" title="' . $this->_('Lock') . '"></i></a>';
                $header .= '<a class="pg-bind" title="' . $this->_('Bind data') . '" href="#"><i class="fa fa-database"></i></a>';
            }
            $header .= '<a class="pg-delete" title="' . $this->_('Mark for deletion') . '" href="#"><i class="fa fa-trash"></i></a>';
            $header .= '</span>';
        }
        // END create header

        if ($backend) {
            $layout .= '<' . $this->getTagName($p) . ' id="' . $p->name . '" data-id="' . $p->id . '" class="' . $this->getCssClasses($p) . ' ' . $nestedClasses . $statusClass . $imageUploadEmpty . '" data-template="' . $p->template->name . '" data-field="' . $this->name . '" data-name="' . $p->name . '" ' . $attributes . '>';
            $layout .= $header;
            $layout .= $parsedTemplate->render() . $imageUpload;
            $layout .= '</' . $this->getTagName($p) . '>';
        } else {
            $layout = '<' . $this->getTagName($p) . ' class="' . $nestedClasses . $this->getCssClasses($p) . '" ' . $attributes . '>' . $parsedTemplate->render() . '</' . $this->getTagName($p) . '>';
        }

        return $layout;
    }

    public function ___getConfigInputfields() {
        $inputfields = parent::___getConfigInputfields();
        //add inputfields here if needed
        return $inputfields;
    }

    // Methodes returns classnames and tagnames for rendering items in frontend + backend
    // get classes

    public function getCssClasses($item, $options = null) {

        $itemData = $item->meta()->pg_styles;
        $defaultClasses = 'pg-item ' . $item->name . ' ' . $item->template;
        $cssClasses = '';
        $backend = $this->isBackend();

        if ($options == 'parentClasses') {

            $defaultClasses = ' pg ' . $item->name . ' ' . $item->template;

            if ($backend) {
                $defaultClasses .= ' ui-droppable';
            }
        }


        if (isset($itemData)) {

            if (isset($itemData['pgitem'])) {
                if (!(empty($itemData['pgitem']['cssClasses']))) {
                    $cssClasses = $itemData['pgitem']['cssClasses'] . ' ';
                    $cssClasses = preg_replace('/\s+/', ' ', $cssClasses);
                }
            }
        }


        if ($options == 'addedClasses') {
            $Classes = $cssClasses;
        } else {
            $Classes = $cssClasses . $defaultClasses;
        }

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

        // fix for groups changing tagname
        if ($item->template->name == 'pg_group') {
            $tagName = 'div';
        }

        // // // bd($tagName);

        return $tagName;
    }
    // END callable Methodes -------------------------------

    //add scripts with same name as block file

    public function scripts($mainPage) {
        $lastItem = null;
        $jsFiles = "";

        if ($mainPage->id) {
        } else {
            return;
        }

        $itemsParent = $this->pages->get('pg-' . $mainPage->id);

        if ($itemsParent->id) {
            // mainPage has pg field and items
            $items = $itemsParent->find('');
        } else {
            // mainPage has no pg field
            $items = $mainPage->find('');
            $items->add($mainPage);
        }

        // handle reference fields
        $itemsArray = new PageArray();

        foreach ($items as $item) {
            $refPages = $this->getRef($item);
            $itemsArray->add($refPages);
        }
        $items->add($itemsArray);
        // END handle reference fields

        foreach ($items as $item) {
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

        echo $jsFiles;
    }

    public function renderStyles($p, $id = 0) {

        $css = '';
        $items = $p->meta()->pg_styles;
        $backend = $this->isBackend();

        if ($id) {
            $items = [];
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
                if ($item['id'] == 'pgitem') {
                    $cssId = $p->name;
                    // $item['id'] = $p->name;
                } elseif ($item['id'] == 'pgitem:hover') {
                    $cssId = $p->name . ':hover';
                } else {
                    $cssId = $p->name . $item['id'];
                }

                $globalClass = '';
                if ($p->parent()->name == 'pg-classes') {
                    $globalClass = 'pg-global';
                    //add g- prfix for global classes to make unique id
                    $cssId = 'g-' . $cssId;
                }

                $css .= '<style class="pg-style pg-style-id-' . $cssId . ' ' . $globalClass . '">';
            }


            // add hover only if device supports it
            if (isset($item['state'])) {

                if ($item['state'] == 'hover') {
                    $css .= '@media (hover: hover), (-ms-high-contrast:none) { ';
                }

                //if set to none remove value (fix for older version)
                if ($item['state'] == 'none') {
                    $item['state'] = '';
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
                    $css .= ' html .breakpoint-' . $breakpoint['name'] . ' ';
                }

                if (isset($item['cssClass'])) {

                    //if no cssClass use page name
                    if ($item['cssClass'] == '') {
                        $item['cssClass'] = $p->name;
                    }

                    if ($item['id'] == 'pgitem:hover') {
                        $item['cssClass'] = $p->name . ':hover';
                    }

                    //force parent class if class is not page name (subitem)
                    if ($item['id'] !== 'pgitem' && $item['id'] !== 'pgitem:hover' && $rootEl == 0) {
                        if ($item['id'] == strtolower($item['tagName']) || $item['id'] == strtolower($item['tagName']) . ':hover') {
                            $item['cssClass'] = $p->name . ' ' . $item['id'];
                        } else {
                            $item['cssClass'] = $p->name . ' .' . $item['id'];
                        }
                    }
                    //END force parent class if class is not page name (subitem)

                    if ($item['cssClass'] == strtolower($item['tagName'])) {
                        $css .= strtolower($item['tagName']) . '{ ';
                    } else {
                        $css .= '.' . $item['cssClass'] . '{ ';
                    }
                }

                foreach ($breakpoint['css'] as $style => $val) {

                    $fallbackFonts = $this->ft->fallbackFonts;

                    if ($style == 'font-family' && $fallbackFonts) {
                        $val = $val . ', ' . $this->ft->fallbackFonts;
                    }

                    $css .= $style . ': ' . $val . '; ';
                }

                $css .= ' } ';

                if (!($breakpoint['name'] == 'base') && $backend == 0) {
                    $css .= ' } ';
                }
            }

            if (isset($item['state'])) {
                if ($item['state'] == 'hover') {
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

                        $fontWeight = '';
                        $fontStyle = '';

                        foreach ($breakpoint->css as $style2 => $fvariant) {
                            if ($style2 == 'font-weight') {
                                $fontWeight = ':wght@' . $fvariant;
                            } else {
                                $fontWeight = ':wght@400';
                            }
                            if ($style2 == 'font-style') {
                                $fontStyle = $fvariant;
                            }
                        }

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
                            $font = $fontName . $fontWeight . $fontStyle;
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
        return $this->config->paths->templates . 'fonts/';
    }

    public function getFontNames() {
        //list files
        $filePath = $this->getFontPath();
        $files = array_diff(scandir($filePath), array('.', '..', '.DS_Store'));
        $fontFiles = array();

        //create font folder if not present
        if (!file_exists($filePath)) {
            mkdir($filePath, 0755, true);
        }

        foreach ($files as $file) {
            $ext = pathinfo($filePath . $file, PATHINFO_EXTENSION);
            if ($ext == 'woff' || $ext == 'woff2') {
                $fontFiles[] = $file;
            }
        }

        // // bd($fontFiles);

        return $fontFiles;
    }

    public function styles($mainPage, $loadDefaults = 1, $loadGlobalClasses = 1, $loadFiles = 1, $loadFonts = 1) {

        // // // bd($mainPage);
        $itemCss = '';
        $cssBackend = '';
        $backend = $this->isBackend();
        $lastItem = 0;
        $cssTemplates = '';
        $defaults = '';
        $fonts = '';
        $cssMainPage = '';
        $customCss = '';

        // page array to hold items to load files
        $itemsArray = new PageArray();

        if ($mainPage->id) {
        } else {
            return;
        }

        $itemsParent = $this->pages->get('pg-' . $mainPage->id);

        if ($itemsParent->id) {
            // mainPage has pg field and items
            $items = $itemsParent->find('');
        } else {
            // mainPage has no field and items
            $items = $mainPage->find('');
        }

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

        // render wrapper styles 
        $cssMainPage = $this->renderStyles($mainPage);

        //render defaults 
        if ($loadDefaults == 1) {
            $defaults = include 'styleDefaults.php';;
        }

        //add parent container styles
        if ($itemsParent->id) {
            $itemsArray->add($itemsParent);
        } else {
            $itemsArray->add($mainPage);
        }

        foreach ($items as $item) {
            $itemsArray->add($item);

            // handle reference fields
            $refPages = $this->getRef($item);
            $itemsArray->add($refPages);
            foreach ($refPages as $refChild) {
                $refPages = $this->getRef($refChild);
                $itemsArray->add($refPages);
            }
            // END handle reference fields

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
        }

        //load google fonts
        if ($fonts !== '') {
            if ($this->ft->fontPrivacy) {
                $preconnect = '<link rel="preconnect" href="https://api.fonts.coollabs.io" crossorigin>';
                $fonts = $preconnect . '<link rel="stylesheet" type="text/css" href="https://api.fonts.coollabs.io/css2?' . $fonts . '">'; // display swap not working for multiple fonts
            } else {
                $preconnect = '<link rel="preconnect" href="https://fonts.googleapis.com">';
                $preconnect .= '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
                $fonts = $preconnect . '<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css2?' . $fonts . '&display=swap">';
            }
        }

        //backend already returns multiple style tags for easy replacement
        if (!$backend) {
            $itemCss = '<style class="pg-style pg-style-items">' . $cssMainPage . $itemCss . '</style>';
        } else {
            $itemCss = $cssMainPage . $itemCss;
        }

        return $cssTemplates . $cssBackend . $defaults . $fonts . $itemCss . $customCss;
    }

    public function getRef($item) {
        // handle reference fields
        // page array to hold items to load files
        $itemsArray = new PageArray();
        $refFields  = array();

        foreach ($item->fields as $field) {
            if ($field->type instanceof FieldtypePage) {
                $refFields[$field->name] = $field->name;
            }
        }

        foreach ($refFields as $refField) {

            if ($refField) {
                // get ref page
                $refPages = $item->$refField;

                if ($refPages) {

                    // set to array if single object
                    if (!is_array($refPages)) $refPages = array($refPages);

                    if (count($refPages)) {
                        foreach ($refPages as $refPage) {
                            if ($refPage->id) {

                                //check if ref is a pagegrid page
                                $pgContainer = $refPage->parents('template=pg_container')->first();

                                if (isset($pgContainer->id)) {

                                    $itemsArray->add($refPage);
                                    foreach ($refPage->find('') as $refChild) {
                                        $itemsArray->add($refChild);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        // END handle reference fields
        return $itemsArray;
    }

    public function renderOptions($options = null) {
        $renderOptions = '';
        $backend = $this->isBackend();

        if ($backend) {

            if (isset($options["children"])) {
                $renderOptions = 'data-pg-children';
            }

            if (isset($options["tag"]) && isset($options["page"])) {

                if (isset(wire('config')->pgRef)) {
                    $tag = $this->getTagName($options["page"]);
                } else {
                    $tag = $this->getTagName($options["page"]);
                }

                if ($tag == 'div' || $tag == 'DIV') {
                    $renderOptions .= 'data-pg-tagName="h2" data-pg-tags="h1 h2 h3 h4 h5 h6 p"';
                }
            }
            // needs span instead of div to work inside p tags
            echo '<span class="pg-data"' . $renderOptions . '></span>';
        }
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
