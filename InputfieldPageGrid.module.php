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
        // don't render inside modal
        if (isset($_GET['modal'])) return;
        
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

        //new pages to render based on items parent
        $editID = (int) $this->wire('input')->get('id');
        if (!$editID && $this->wire('process') instanceof WirePageEditor) $editID = $this->wire('process')->getPage()->id;
        if (!$editID) return;

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
        $modulePath = $this->config->paths->InputfieldPageGrid;
        $user = wire('user');
        $settings = '';
        $topNav = '';
        $addItems = '';

        //make data available to js
        $globalPageData = $this->getData();
        $dataGlobal = '<script>$(".pg-container").data("pg", ' . $globalPageData . ')</script>';
        //END make data available to js

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
            <i class="pg-item-list-button pg-topnav-margin fa fw fa-list-ul on" uk-tooltip="title:Item List; pos:bottom; delay:300;"></i>
            <i class="pg-undo fa fa-fw fa-reply" data-name="fa-reply" uk-tooltip="title:Undo; pos:bottom; delay:300;"></i>
            <i class="pg-redo pg-topnav-margin fa fa-fw fa-share" data-name="fa-share" uk-tooltip="title:Redo; pos:bottom; delay:300;"></i>';
                if ($hasAnimations) $topNav .= '<i class="pg-play pg-topnav-margin fa fw fa-play" uk-tooltip="title:Play Animations; pos:bottom; delay:300;"></i>';
                $topNav .= '<div id="breakpoints-nav">';
                $topNav .= '<div class="breakpoint-icon breakpoint-icon-s" value="@media (max-width: 640px)" breakpoint="s" uk-tooltip="title:Breakpoint Small < 640px; pos:bottom; delay:300;">' . file_get_contents($modulePath . '/img/phone-portrait-outline.svg') . '</div>';
                $topNav .= '<div class="breakpoint-icon breakpoint-icon-m" value="@media (max-width: 960px)" breakpoint="m" uk-tooltip="title:Breakpoint Medium < 960px; pos:bottom; delay:300;">' . file_get_contents($modulePath . '/img/phone-landscape-outline.svg') . '</div>';
                $topNav .= '<div class="breakpoint-icon breakpoint-icon-base" value="@media (min-width: 640px)" breakpoint="base" uk-tooltip="title:Breakpoint Base; pos:bottom; delay:300;">' . file_get_contents($modulePath . '/img/laptop-outline.svg') . '</div>';
                $topNav .= '<div class="breakpoint-icon breakpoint-icon-l" value="@media (min-width: 1600px)" breakpoint="l" uk-tooltip="title:Breakpoint Large > 1600px; pos:bottom; delay:300;">' . file_get_contents($modulePath . '/img/desktop-outline.svg') . '</div>';
                $topNav .= '</div>';
                // if ($user->admin_theme != 'AdminThemeCanvas') $topNav .= '<a class="pg-view" href="' . $mainPage->url() . '" target="_blank" uk-tooltip="View page in new tab"><i class="fa fw fa-eye"></i></a>';
                $topNav .= '</div>';
            }
        }

        $addItems = $this->renderAddItemBar();

        //add blueprint select
        $blueprintPages = $this->pages->get('name=pg-blueprints, template=pg_container')->children();
        $blueprintSelect = '';
        if (count($blueprintPages) && $this->name == $fieldFound) {
            $blueprintView = $this->user->hasPermission('page-view', $blueprintPages->first());
            $blueprintEdit = $this->user->hasPermission('page-edit', $blueprintPages->first());
            $blueprintSelect = $this->modules->get('InputfieldSelect');
            $blueprintSelect->name = 'pg-blueprint-select';
            $blueprintSelect->label = 'Select blueprint';

            if ($blueprintEdit) $blueprintSelect->addClass('pg-blueprint-nav-load');

            //add options
            foreach ($blueprintPages as $blueprint) {
                $blueprintSelect->addOption($blueprint->name);
            }
            if ($blueprintView) $blueprintSelect = $blueprintSelect->render();
        }
        //END add blueprint select


        $fId = $this->fields->get($this->name) ? $this->fields->get($this->name)->id : 0;
        $wrapperPage = $itemsParent->get('name=pg-' . $fId . ', template=pg_container');
        //create field container page if it doesn't exist
        if ($fId && !$wrapperPage->id) {
            $wrapperPage = new Page(); // create new page object
            $wrapperPage->template = 'pg_container'; // set template
            $wrapperPage->parent = $itemsParent->id; // set the parent
            $wrapperPage->name = 'pg-' . $fId; // give it a name used in the url for the page
            $wrapperPage->title = 'pg-' . $fId; // set page title (not neccessary but recommended)
            $wrapperPage->save();
        }

        //quick add button for main container
        $quickAddMain = '';
        if ($user->hasPermission('page-add', $wrapperPage)) {
            $quickAddButtons = $this->renderAddItemBar(0, [], 1);
            $quickAddMain = '<div class="pg-quick-add pg-quick-add-main" data-id-original="' . $wrapperPage->id . '" data-id="' . $wrapperPage->id . '"><span class="pg-quick-add-icon" uk-tooltip="title:Add Item; pos:bottom; delay:100;"></span>' . $quickAddButtons . '</div>';
        }

        //render language tabs to switch iframe language
        $languageTabs = '';
        $iframeUrl = $mainPage->url();
        if ($this->templates->get('language') && $this->templates->get('language')->id && $this->modules->isInstalled('LanguageSupport') && $this->modules->isInstalled('LanguageSupportPageNames') && count($this->languages) > 1) {
            $languageTabs = '<div class="pg-langTabs"><ul class="uk-tab">';
            $activeLang = isset($_GET['pglang']) ? $_GET['pglang'] : $this->user->language->name;
            foreach ($this->languages as $l) {
                $activeClass = $activeLang == $l->name ? 'uk-active' : '';
                if ($activeLang == $l->name) $iframeUrl = $mainPage->localUrl($l);
                $languageTabs .= '<li class="' . $activeClass . '"><a class="" href="' . $mainPage->editUrl() . '&pglang=' . $l->name . '" >' . $l->title . '</a></li>';
            }
            $languageTabs .= '</ul></div>';
        }


        $renderMarkup = $topNav . $settings . '<div class="pg-container pg-container-' . $this->name . '" data-page-title="' . $mainPage->title . '" data-page="' . $editID . '" data-id="' . $this->pages->get('pg-classes')->id . '" data-animations-id="' . $this->pages->get('pg-animations')->id . '" data-field="' . $this->name . '" data-site-url="' . $this->config->urls->site . '" data-module-url="' . $this->config->urls->siteModules . 'FieldtypePageGrid/" data-admin-url="' . $this->page->rootParent->url() . 'setup/pagegrid/" data-fallbackfonts="' . $this->ft->fallbackFonts . '">' . $addItems . $dataGlobal . $blueprintSelect;
        //loading animation
        $renderMarkup .= '<div class="pg-loading"><div class="fa fa-spin fa-spinner fa-fw"></div></div>';
        //container for item header (item header will be moved here with js)
        $renderMarkup .= $this->renderIconPicker();
        $renderMarkup .= '<div class="pg-item-header-container"></div>';
        $renderMarkup .= $languageTabs;
        $renderMarkup .= '<iframe data-field="' . $this->name . '" id="pg-iframe-canvas-' . $this->name . '" class="pg-iframe-canvas" src="' . $iframeUrl . '?backend=1&field=' . $this->name . '&page=' . $mainPage->id . '" loading="lazy" frameBorder="0" scrolling="no" style="width:100%; max-height:100vh; border:0;"></iframe>';
        $renderMarkup .= $quickAddMain;
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

    public function renderIconPicker() {
        $field = $this->modules->get('InputfieldIcon');
        $field->name = 'pg-icon-picker';
        $field->label = 'Icon';
        $field->addClass('pg-icon-picker', 'wrapClass');
        $scriptUrl = $this->config->urls->modules . 'Inputfield/InputfieldIcon/InputfieldIcon.js';
        $stylesUrl = $this->config->urls->modules . 'Inputfield/InputfieldIcon/InputfieldIcon.css';
        $styles = "<link type='text/css' href='$stylesUrl' rel='stylesheet'>";
        $script = "<script type='text/javascript' src='$scriptUrl'></script>";
        return '<div class="pg-icon-picker-wrapper">' . $field->render() . '</div>' . $script;
    }

    //optional $templatesArray
    public function renderAddItemBar($getSymbolsOnly = 0, $templatesArray = [], $quickAdd = 0) {
        $user = $this->user;

        // render the 'Add New' buttons for each template
        if (!$quickAdd && (!$this->user->hasPermission('pagegrid-add') || !$this->user->hasPermission('pagegrid-drag'))) return;

        $templates = $this->rowTemplates;

        // to render Quickadd bar from renderHeader function we need to get templates for all fields 
        // support multiple fields with different templates set
        if (!$templates && !count($templatesArray)) {
            $templates = [];
            $mainPage = $this->getPage();
            if ($mainPage && $mainPage->id) {
                $fields = $mainPage->fields->find('type=FieldtypePageGrid');
                foreach ($fields as $f) {
                    $templateArray = $f->template_id ? $f->template_id : [];
                    foreach ($templateArray as $id) {
                        $template = $this->wire('templates')->get($id);
                        if ($template) $templates[$id] = $template;
                    }
                }
            }
        }

        if (count($templatesArray)) $templates = $templatesArray;

        // $editID = (int) $this->wire('input')->get('id');
        // if (!$editID && $this->wire('process') instanceof WirePageEditor) $editID = $this->wire('process')->getPage()->id;
        // $parentID = $itemsParent->id;
        $addItems = '';

        if (!$getSymbolsOnly) {
            $fieldName = $this->name ? $this->name : '';
            $addItems = '<div data-field=' . $fieldName . ' class="pg-add-container pg-add-container-' . $fieldName . '">';
            if ($quickAdd) $addItems = '<div class="pg-quick-add-container pg-quick-add-inner">'; //change wrapper for quick edit to preven events
            $addItems .= '<div class="pg-add-tabs"><div class="pg-add-tab pg-add-tab-items pg-add-tab-active"><i class="fa fw fa-th-large" title="fa-th-large"></i></div><div class="pg-add-tab pg-add-tab-symbols" uk-tooltip="title:Symbols; pos:right; delay:300;"><i class="fa fw fa-cube"></i></div></div><div class="pg-add-content">';
            $addItems .= '<div class="pg-add-item-container">';
            foreach ($templates as $template) {
                /** @var Template $template */

                if ($template && is_string($template)) {
                    $template = $this->templates->get($template);
                    if (!$template->id) continue;
                }

                if (!$user->isSuperuser() && $template->useRoles && !in_array($user->id, $template->createRoles)) continue;

                // keep this line for future updates, makes it possible to add items via modal if link is clicked, maybe alternative for non super users oneday
                // $url = $this->wire('config')->urls->admin . "page/add/?modal=1&template_id=$template->id&parent_id=$parentID&context=PageGrid";

                if ($template->icon == '') {
                    $tIcon = '<div class="pg-iconletter">' . substr($template->getLabel(), 0, 1) . '</div>';
                } else {
                    $tIcon = wireIconMarkup($template->icon);
                }

                $addItems .= '<div class="pg-add ' . $template->name . '" data-template-id="' . $template->id . '" template="' . $template->name . '">' . $tIcon . '<span class="ui-button-text">' . $template->getLabel() . '</span></div>';
            }
            $addItems .= '</div>';
        }

        //add symbols
        $addItems .= '<div class="pg-add-symbol-container">';
        $symbolParent = $this->pages->get("name=pg-symbols, template=pg_container");
        $symbols = $symbolParent->children('sort=created');
        $linkedPages = $this->database->query("SELECT source_id FROM pages_meta WHERE name = 'pg_symbol'");
        $linkedPages = implode("|", $linkedPages->fetchAll(\PDO::FETCH_COLUMN, 0));
        $linkedPages = $this->pages->getByIDs($linkedPages);

        //sort symbols
        $syncedSymbols = new PageArray();
        $unSyncedSymbols = new PageArray();

        foreach ($symbols as $symbol) {
            $sync = $symbol->meta()->pg_sync === 0 ? 0 : 1;
            if ($sync) $syncedSymbols->add($symbol);
            if (!$sync) $unSyncedSymbols->add($symbol);
        }

        $symbolsSorted = new PageArray();
        $symbolsSorted->add($syncedSymbols);
        $symbolsSorted->add($unSyncedSymbols);

        foreach ($symbolsSorted as $symbol) {
            $sync = $symbol->meta()->pg_sync === 0 ? 0 : 1;
            $icon = $symbol->meta()->pg_icon ? $symbol->meta()->pg_icon : $symbol->template->icon;

            if (!$icon) {
                $tIcon = '<div class="pg-iconletter">' . substr($symbol->title, 0, 1) . '</div>';
            } else {
                $tIcon = wireIconMarkup($icon);
            }

            $linkedPagesCount = 0;
            foreach ($linkedPages as $lp) {
                if ($lp->meta()->pg_symbol !== null && $lp->meta()->pg_symbol == $symbol->id && $lp->parent->name !== 'trash') {
                    $linkedPagesCount++;
                }
            }
            if ($user->isSuperuser() || $user->hasRole('pagegrid-admin') || $user->hasRole('pagegrid-designer') || $user->hasPermission('pagegrid-symbol-add')) {
                $addItems .= '<div class="pg-add pg-add-symbol" data-sync="' . $sync . '" data-id="' . $symbol->id . '" data-template-id="' . $symbol->template->id . '" template="' . $symbol->template->name . '">' . $tIcon . '<span class="ui-button-text"><span class="pg-symbol-title">' . $symbol->title . '</span><span class="pg-symbol-number">' . $linkedPagesCount . '</span></span></div>';
            }
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
        if (!$mainPage->id)  return;

        $backend = $this->isBackend();
        $statusClass = '';
        $itemsParent = $this->pages->get('pg-' . $mainPage->id);
        $layout = "";
        $fieldCount = count($mainPage->fields->find('type=FieldtypePageGrid'));

        if (!$itemsParent->id) return;
        if (!$field) $field = $mainPage->fields->get('type=FieldtypePageGrid'); //if no argument get first field

        //the blueprint template has no pg fields, so we check the field container page, if no specific field is called
        if (!$field && $mainPage->template->name == 'pg_blueprint') {
            $fieldPage = $itemsParent->get('template=pg_container');
            $fId = str_replace('pg-', '', $fieldPage->name);
            $field = $this->fields->get("id=$fId, type=FieldtypePageGrid");
        }

        if (!$field || !$field->id) return;

        //on frontend get cached markup if exists and return early
        $lang = $this->user->language && $this->user->language->id ? '-' . $this->user->language->id : '';
        $cache = $this->cache->get('pgCache-markup-' . $mainPage->id . '-' . $field->id . $lang);
        if ($cache && $this->user->isLoggedin()) $this->cache->delete("pgCache-*");
        if ($cache && !$backend) return $cache;

        //save main page id to session so we can use it for getPage() when not set via ajax call
        $this->session->set('pg_page', $mainPage->id);

        //NEW support for multiple fields
        // multiple fields: check if it's this field or return (prevents double rendering of fields in backend)
        if (isset($_GET['field']) && $_GET['field'] !== $field->name && $fieldCount > 1) return;

        //search $this->pages instead of $itemsParent to work with multi language (bug?)
        $itemsParentNew = $this->pages->get("name=pg-$field->id, parent=$itemsParent->id, template=pg_container");

        //make sure imported pages get new field container name/id
        $oldFieldContainer = $this->pages->get("name!=pg-$field->id, parent=$itemsParent->id, template=pg_container");
        if ($oldFieldContainer->id && !$itemsParentNew->id) {
            $oldFieldId = (int)str_replace("pg-", "", $oldFieldContainer->name);
            if (!$mainPage->fields->get($oldFieldId)) {
                $oldFieldContainer->of(false);
                $oldFieldContainer->name = 'pg-' . $field->id;
                $oldFieldContainer->title = $field->name;
                $oldFieldContainer->save();
                $oldFieldContainer->of(false);
                $itemsParentNew = $oldFieldContainer;
            }
        }
        //END make sure imported pages get new field container name/id

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
            // $PageFrontEditData = $this->modules->getConfig('PageFrontEdit');
            // $dummies = '';

            // if (isset($PageFrontEditData['inlineEditFields'])) {
            //     $PageFrontEditFields = $PageFrontEditData['inlineEditFields'];
            //     $templates = $this->fields->get('type=FieldtypePageGrid')->template_id;
            //     if ($templates !== null && count($templates)) {
            //         foreach ($templates as $tId) {
            //             $t = $this->templates->get($tId);
            //             if (isset($t) == 0) continue;
            //             foreach ($t->fields as $f) {
            //                 if (in_array($f->id, $PageFrontEditFields)) {
            //                     $dummy = $this->pages->get("$f->id!=''");
            //                     if ($dummy->id) {
            //                         $this->ft->readyFrontEdit($dummy);
            //                         $dummies .= $dummy->$f;
            //                     }
            //                 }
            //             }
            //         }
            //     }
            // }
            // END trick inline editor to work for first item

            //new dummy render save resources
            $PageFrontEditData = $this->modules->getConfig('PageFrontEdit');
            $dummies = '';

            if (isset($PageFrontEditData['inlineEditFields'])) {
                $PageFrontEditFields = $PageFrontEditData['inlineEditFields'];
                // bd($PageFrontEditFields);
                foreach ($PageFrontEditFields as $f) {
                    $dummy = $this->pages->get("$f!=''");
                    if ($dummy->id) {
                        if (count($dummy->parents('template=pg_container'))) {
                            // bd($dummy->name);
                            $this->ft->readyFrontEdit($dummy);
                            $dummies .= $dummy->$f;
                        }
                    }
                }
            }
            //END new dummy render save resources

            $statusClass = '';

            if ($this->user->hasPermission('pagegrid-drag')) $statusClass .= 'pg-sortable';
            if ($this->user->hasPermission('pagegrid-select'))  $statusClass .= " pg-permission-select";

            $out = '<div id="' . $itemsParent->name . '" class="pg-is-backend pg-wrapper pg-item pg-main pg-droppable pg ' . $this->getCssClasses($itemsParent) . ' ' . $statusClass . '" data-id="' . $itemsParent->id . '" data-field="' . $field->name . '">' . $layout . '</div>';
            $out .= '<div class="pg-dummies" style="display:none!important;">' . $dummies . '</div>';
        } else {
            $out = '<div class="pg-wrapper pg pg-main ' . $this->getCssClasses($itemsParent) . '">' . $layout . '</div>';
        }

        if (!$backend) $this->cache->save('pgCache-markup-' . $mainPage->id . '-' . $field->id . $lang, $out);
        return $out;
    }

    //disable automatic prepending/appending of template file by passing this string (checked on ready function of fieldtyle module)
    public function noAppendFile($p) {
        echo '<!--pgNoAppendTemplateFile-->';
    }

    //for items we never want prepending/appending of template file so disable it in DB
    public function noAppendFileSave($p) {
        $isPgPage = count($p->parents('template=pg_container'));
        if ($isPgPage && !$p->template->noAppendTemplateFile) {
            $p->template->noAppendTemplateFile = 1;
            $p->template->noPrependTemplateFile = 1;
            $p->template->appendFile = "";
            $p->template->prependFile = "";
            $p->template->save();
        }
    }

    public function renderItem($p) {

        $backend = $this->isBackend();
        $layout = '';

        //check if symbol page was found
        $pOriginal = $p; // set original as data attribute later to be able to convert back
        if ($p->meta('pg_symbol') !== null && $p->meta('pg_symbol')) {
            $symbolId = $p->meta('pg_symbol');
            $symbol = $this->pages->get($symbolId);

            if ($symbol && $symbol->id && $symbol->parent->name === 'pg-symbols') {

                // fix: check if there is the same symbol inside this symbol creating a death loop
                // JS should prevents this, but just in case something goes wrong
                if ($backend) {
                    foreach ($symbol->find('') as $child) {
                        if ($child->meta('pg_symbol') == $symbolId) $child->delete(true);
                    }
                }
                // END fix: check if there is the same symbol inside this symbol creating a death loop 

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

        //look inside site/templates folder if no block file found (might no be a good idea?)
        // if (file_exists($templateFilename) == 0) {
        //     $templateFilename = $this->config->paths->templates . $template_name . $ext;
        // }

        if (file_exists($templateFilename) == 0) {
            return false;
        }

        //if settings inlineEditorFrontDisable on frontend disable inline editor markup
        if (!$backend && $this->ft->inlineEditorFrontDisable) $p->edit(false);

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

        $itemData = $p->meta()->pg_styles;

        if (isset($itemData)) {
            if (isset($itemData['pgitem'])) {
                $PageGridItem = $itemData['pgitem'];

                if (isset($PageGridItem['attributes'])) {
                    $attributes = $PageGridItem['attributes'];
                }
            }
        }
        //end Read item Settings

        //make sure outpuformatting is on before render
        $p->of(true);

        //disable inline edit for frontend, when inlineEditorFrontDisable = true
        if (!$backend && $this->ft->inlineEditorFrontDisable) $p->edit(false);

        //RENDER OPTIONS depricated
        //pass page to template be able to set render options specifically for this page
        //render options are depricated and will be removed in future versions
        $p->template->page = $p;
        //END RENDER OPTIONS depricated

        // parse template markup and inssert file uploader
        $templateRender = $parsedTemplate->render();
        $templateRender = $this->ft->enableInlineEditFile($templateRender, $p);
        $tagNameSaved = $this->getTagName($p);
        $tagName = $tagNameSaved;
        $classes = $this->getCssClasses($p);
        $docClasses = '';
        $optionAutoTitle = 1; // deafult enable auto title and puplish of pg items via modal
        $optionTags = '';
        $optionChildren = '';
        $optionChildrenTab = '';
        $optionChildrenLabel = '';
        $optionReloadScript = '';

        //RENDER OPTIONS depricated
        $options = $p->pgOptions ? json_decode($p->pgOptions, true) : [];
        if (isset($options['classes'])) {
            $classNames = $this->sanitizer->htmlClasses($options['classes']);
            $classNames = strtolower($classNames);
            if ($classNames) $classes .= ' ' . $classNames;
        }
        if (isset($options['children']) && $options['children']) {
            $optionChildren = true;
        }
        if ($backend) {
            $oldSession = $this->session->get('pg_template_' . $p->template->name);
            $jsonOptions = json_encode($options);
            if ($oldSession != $jsonOptions) {
                $this->session->set('pg_template_' . $p->template->name, $jsonOptions);
            }
        }
        //END RENDER OPTIONS depricated

        //NEW get wrapper tag and add classes and attributes via DOMDocument
        $doc = new \DOMDocument();
        @$doc->loadHTML('<?xml encoding="utf-8" ?><html>' . $templateRender . '</html>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $docWrapper = $doc->documentElement->firstElementChild;
        $hasWrapper = $docWrapper && $docWrapper->hasAttribute('pg-wrapper') ? true : false;

        //if first node is style or script tag look for other wrapper
        if ($docWrapper && ($docWrapper->tagName == 'style' || $docWrapper->tagName == 'script')) {
            foreach ($doc->documentElement->childNodes as $node) {
                if (isset($node->tagName) && $node->hasAttribute('pg-wrapper')) {
                    $hasWrapper = true;
                    $docWrapper = $node;
                }
            }
        }

        //if wrapper found parse markup and get classes and options set via attributes
        if ($hasWrapper && isset($docWrapper->tagName)) {
            $tagName = $docWrapper->tagName;
            $docClasses = $docWrapper->getAttribute('class');

            //get options set via attributes
            $optionTags = $docWrapper->getAttribute('pg-tags');
            $optionChildren = $docWrapper->getAttribute('pg-children');
            $optionAutoTitle = $docWrapper->getAttribute('pg-autotitle');
            $optionChildrenTab = $docWrapper->getAttribute('pg-children-tab');
            $optionChildrenLabel = $docWrapper->getAttribute('pg-children-label');
            $optionReloadScript = $docWrapper->getAttribute('pg-reload-script');

            //remove option attribute on frontend
            if (!$backend) {
                $docWrapper->removeAttribute('pg-wrapper');
                $docWrapper->removeAttribute('pg-tags');
                $docWrapper->removeAttribute('pg-children');
                $docWrapper->removeAttribute('pg-autotitle');
                $docWrapper->removeAttribute('pg-children-tab');
                $docWrapper->removeAttribute('pg-children-label');
                $docWrapper->removeAttribute('pg-reload-script');
            }

            //get attributes
            foreach ($docWrapper->attributes as $attr) {
                $name = $attr->nodeName;
                $value = $attr->nodeValue;
                if ($name != 'class' || $name != 'id') $attributes .= $name . '="' . $value . '" ';
            }

            //get markup without the wrapper
            foreach ($docWrapper->childNodes as $child) {
                $clone = $child->cloneNode(true);
                $docWrapper->parentNode->insertBefore($clone, $docWrapper);
            }
            //remove wrapper
            $docWrapper->parentNode->removeChild($docWrapper);

            $templateRender = $doc->saveHTML();
            $templateRender = str_replace('<?xml encoding="utf-8" ?>', '', $templateRender);
            $templateRender = str_replace("</html>", "", $templateRender);
            $templateRender = str_replace("<html>", "", $templateRender);
        }
        //END NEW get wrapper tag and add classes and attributes via DOMDocument

        //set children classes
        if ($optionChildren) {
            if ($backend) {
                $classes .= ' pg pg-nested pg-droppable';
            } else {
                $classes .= ' pg';
            }
        }

        //add doc classes in the end
        if ($docClasses) $classes .= ' ' . $docClasses;

        //tagname
        //allow tags to change
        if ($optionTags && $tagNameSaved !== 'div') $tagName = $tagNameSaved;

        //prevent nested links, breaking layout in HTML
        if ($tagName == 'a') {
            if ($backend) $tagName = 'div';
            $templateRender = str_replace("<a ", "<span ", $templateRender);
            $templateRender = str_replace("</a>", "</span>", $templateRender);
        }

        // replace p tag with custom el to prevent nesting bug with <p> and inline editor <div>
        if ($tagName == 'p') {
            if ($this->user->isLoggedin()) $tagName = 'pg-ptag';
            if (!$backend && $this->ft->inlineEditorFrontDisable) $tagName = 'p';
        }

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

        //save render options to session
        //some options we need to access in hooks so we save it as session
        if ($backend) {
            $options = [];
            $optionChildren = $optionChildren && $optionChildren != 'true' ? explode(' ', $optionChildren) : $optionChildren;
            if ($optionChildren) $options['children'] = $optionChildren;
            if ($optionTags) $options['tags'] = $optionTags;
            if ($optionAutoTitle) $options['autoTitle'] = $optionAutoTitle;
            if ($optionChildrenTab) $options['childrenTab'] = $optionChildrenTab;
            if ($optionChildrenLabel) $options['childrenLabel'] = $optionChildrenLabel;
            if ($optionReloadScript && $optionReloadScript == 'false') $options['reloadScript'] = false;

            $oldSession = $this->session->get('pg_template_' . $p->template->name);
            $jsonOptions = json_encode($options);
            if ($oldSession != $jsonOptions) {
                $this->session->set('pg_template_' . $p->template->name, $jsonOptions);
            }

            //add header and status classes needed for backend
            $header = $this->renderItemHeader($p, $p->template->label, $pOriginal, $options);
            $classes .= $this->getStatusClasses($p);
        }

        //render item
        if ($backend) {
            $layout = "<" . $tagName . " id='" . $p->name . "' data-id='" . $p->id . "' data-id-original='" . $pOriginal->id . "' class='" . $classes . "' data-template='" . $p->template->name . "' data-template-label='" . $p->template->label . "' data-field='" . $this->name . "' data-title='" . $p->getUnformatted('title') . "' data-name='" . $p->name . "' " . $attributes . ">";
            $layout .= '<pg-icon>' . wireIconMarkup($p->template->icon) . '</pg-icon>';
            $layout .= $header;
            $layout .= $templateRender;
            $layout .= '</' . $tagName . '>';
        } else {
            $layout = '<' . $tagName . ' class="' . $classes . '" ' . $attributes . '>' .  $templateRender . '</' . $tagName . '>';
        }

        return $layout;
    }

    //function to render file uploader
    // @param Page $p
    // @param Field Name or Field Id $f
    public function renderFileUploader($p, $fName, $pRender) {

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
                    $imageUpload = '<img class="pg-fileupload pg-media-responsive" src="">';
                } else {
                    $imageUpload = '<video muted loop class="pg-fileupload pg-media-responsive">';
                    $imageUpload .= '<source src="" type="video/mp4">';
                    $imageUpload .= '</video>';
                }
            }

            $imageUpload .= '
                        <pg-uploader class="setting pg-file-picker pg-file-picker-' . $f->name . $imageUploadEmpty . '">
                          <pg-uploader class="settings_wrap">
                            <pg-uploader class="drop_target">
                              <pg-uploader class="input_button"></pg-uploader>
                                <input class="inputFile" type="file" data-quality="' . $f->clientQuality . '" data-max-width="' . $f->maxWidth . '" data-max-height="' . $f->maxHeight . '" data-field="' . $f->name . '" data-id="' . $p->id . '" data-id-render="' . $pRender->id . '" data-type="upload"/>
                                </pg-uploader>
                            </pg-uploader>
                        </pg-uploader>
                        ';
        }

        return $imageUpload;
    }

    //function to render item header
    public function renderItemHeader($p, $title = '', $pOriginal = 0, $options = []) {

        //if frontend return empty string
        if (!$this->isBackend()) return "";

        $header = "";
        $user = $this->user;
        $layoutTitle = $p->template->label ? $p->template->label : $p->template->name;
        $layoutTitle = $title ? $title : $layoutTitle;
        $statusClass = $this->getStatusClasses($p);
        $isPgPage = count($p->parents('template=pg_container'));
        $addButton = isset($options['children']) && $options['children'] ? 1 : 0;

        //disbale inline edit on title field
        $p->edit(false);

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
            $header .= '<pg-item-header id="pg-item-header-' . $pOriginal->id . '" data-id="' . $p->id . '" data-id-original="' . $pOriginal->id . '" class="pg-item-header' . $statusClass . '">';
            $header .= '<span>' . $layoutTitle . '</span>';

            //quick add button
            // <i class="fa fw fa-plus" title="Add Item"></i>
            // if ($addButton) $header .= '<a class="pg-quick-add pg-edit" href="#" data-url="/admin/page/add/?parent_id=' . $p->id . '&template_id=' . $this->templates->get('pg_editor')->id . '&pgquickadd=1&modal=1&pgmodal=1">+</a>';
            if ($addButton && $user->hasPermission('page-add', $p)) {
                $childrenTemplates = isset($options['children']) && is_array($options['children']) ? $options['children'] : [];
                // if template->childTemplate is set via admin and not superuser, use settings
                if ($p->template->childTemplates && !$user->isSuperuser()) {
                    $childrenTemplates = $p->template->childTemplates;
                    foreach ($childrenTemplates as $childTemplate) {
                        $childTemplate = $this->templates->get($childTemplate);
                        if ($childTemplate->id) {
                            // bd($childTemplate->name);
                            $childrenTemplates = [];
                            $childrenTemplates[] = $childTemplate->name;
                        }
                    }
                }
                // bd($childrenTemplates);
                $quickAddButtons = $this->renderAddItemBar(0, $childrenTemplates, 1);
                $header .= '<div class="pg-quick-add" data-id-original="' . $pOriginal->id . '" data-id="' . $p->id . '"><span class="pg-quick-add-icon" uk-tooltip="title:Add Item to ' . $layoutTitle . '; pos:bottom; delay:100;"></span>' . $quickAddButtons . '</div>';
            }
            //edit
            $header .= '<pg-item-header-button class="pg-edit" uk-tooltip="' . $this->_('Edit') . '" data-url="./?id=' . $p->id . '&amp;modal=1&pgmodal=1" href="#"><i class="fa fa-pencil"></i></pg-item-header-button>';

            if ($user->hasPermission('page-clone', $p) && $isPgPage) {
                //clone
                $header .= '<pg-item-header-button class="pg-clone" uk-tooltip="' . $this->_('Clone') . '" data-template="' . $p->template->name . '" data-parent="' . $p->parent()->id . '"><i class="fa fa-fw fa-clone" data-name="fa-clone"></i></pg-item-header-button>';
            }
            if ($user->hasPermission('page-lock', $p) && $isPgPage) {
                //lock
                $header .= '<pg-item-header-button class="pg-lock" uk-tooltip="' . $this->_('Lock') . '" href="#"><i class="fa fa-lock"></i><i class="fa fa-unlock"></i></pg-item-header-button>';
            }
            if (($user->isSuperuser() || $user->hasPermission('pagegrid-symbol-create') || $user->hasRole('pagegrid-admin') || $user->hasRole('pagegrid-designer')) && $isPgPage) {
                //symbol
                $header .= '<pg-item-header-button class="pg-symbol" uk-tooltip="' . $this->_('Create Symbol') . '" href="#"><i class="fa fw fa-cube"></i></pg-item-header-button>';
            }
            if ($user->hasPermission('page-add', $p->parent()) && $user->hasPermission('page-delete', $p) && $isPgPage) {
                //delete
                $header .= '<pg-item-header-button class="pg-delete" uk-tooltip="' . $this->_('Mark for deletion') . '" href="#"><i class="fa fa-trash"></i></pg-item-header-button>';
            }
            $header .= '</pg-item-header>';
        }

        //reanable inline edit on title field (if set)
        $p->edit(true);

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
        if ($user->hasPermission('pagegrid-style-panel'))  $statusClass .= " pg-permission-style-panel";
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

    // function to get default classnames and classes added with style panel
    // can also be used to allow adding classes to subitems, if called from block template
    // $item = $page object
    public function getCssClasses($item, $itemId = 'pgitem', $options = null) {

        $itemData = $item->meta()->pg_styles;
        $templateName = str_replace('_', '-', $item->template->name);
        $defaultClasses = $item->name . ' ' . $templateName . ' pg-item';
        $cssClasses = '';
        $backend = $this->isBackend();

        //return classes for subitems/elements inside block templates
        if ($itemId !== 'pgitem') {
            $defaultClasses = '';
            //in the backend we add a class to allow adding new classes via style panel
            if ($backend) $defaultClasses = 'pg-add-classes';
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
                        //add event class
                        $cssClasses .= ' ' . $eventClass . ' ';

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

        $Classes = $defaultClasses . ' ' . $cssClasses;
        //remove last empty space
        $Classes = rtrim($Classes);

        return $Classes;
    }

    // function to get tagnames set via stylepanel
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
        if (!$mainPage->id) return;

        $backend = $this->isBackend();

        //on frontend get cached markup if exists and return early
        $cache = $this->cache->get('pgCache-js-' . $mainPage->id);
        if ($cache && $this->user->isLoggedin()) $this->cache->delete("pgCache-*");
        if ($cache && !$backend) return $cache;

        $arrayFiles = [];
        $jsFiles = "";
        $customJs = $this->ft->customScript && !$backend ? '<script>' . $this->ft->customScript . '</script>' : '';

        //load js plugins
        foreach ($this->ft->plugins as $pluginName) {
            $jsFiles .= '<script type="text/javascript" src="' . $this->config->urls->InputfieldPageGrid . 'js/' . $pluginName . '.js"></script>';
        }

        $items = new PageArray();
        $itemsParent = $this->pages->get('pg-' . $mainPage->id);

        if ($itemsParent->id) {
            //getAncestors returns parent and grandchildren. needed because of bug. $page->find('') is not returning all pages
            $items->add($this->getAncestors($itemsParent));
            //on multilanguage pages sometimes don't return children
            if (count($items) <= 1) $items->add($itemsParent->find(''));
        } else {
            //getAncestors returns parent and grandchildren. needed because of bug. $page->find('') is not returning all pages
            $items->add($this->getAncestors($mainPage));
        }

        foreach ($items as $item) {

            $filename = wire('config')->paths->templates . 'blocks/' . $item->template->name . '.js';
            $filenameUrl = wire('config')->urls->templates . 'blocks/' . $item->template->name . '.js';

            if (!in_array($item->template->name, $arrayFiles) && file_exists($filename)) {
                $jsFiles .= '<script type="text/javascript" src="' . $filenameUrl . '"></script>';
                $arrayFiles[] = $item->template->name;
            }

            if (file_exists($filename) == 0) {
                //if no file found check in module
                $filename = wire('config')->paths->siteModules . 'PageGridBlocks/blocks/' . $item->template->name . '.js';
                $filenameUrl = wire('config')->urls->siteModules . 'PageGridBlocks/blocks/' . $item->template->name . '.js';

                if (!in_array($item->template->name, $arrayFiles) && file_exists($filename)) {
                    $jsFiles .= '<script type="text/javascript" src="' . $filenameUrl . '"></script>';
                    $arrayFiles[] = $item->template->name;
                }
            }
        }

        //add animation for classes
        $classNames = '';

        //new load all classes
        $classesParent = $this->pages->get('name=pg-classes, template=pg_container');
        foreach ($classesParent->children() as $p) {
            $classNames .= $p->name . '|';
        }
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
                // if ($item->name == 'button') {
                //      $itemData = $item->meta()->pg_styles;
                //     bdb($itemData);
                // }

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

                // bdb($animationNames);

                foreach ($animationNames as $animationName) {
                    if (!$animationName) continue;
                    $animationPage = $animationsParent->findOne('name=' . $animationName);
                    if (!$animationPage) continue;
                    if (!$animationPage->id) continue;
                    $itemData = $animationPage->meta()->pg_styles;
                    if (isset($itemData)) {
                        foreach ($itemData as $childData) {
                            if (isset($childData['id'])) {
                                if (isset($childData['keyframe']) && str_starts_with($childData['id'], 'pg-keyframe-')) {
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

        $scriptOutput = $jsAnimationData . $jsFiles . $customJs;
        if (!$backend) $this->cache->save('pgCache-js-' . $mainPage->id, $scriptOutput);

        // bd($animationsSelectors);
        // $updateAnimations = 1;

        // if ($updateAnimations) bdb([$dataJson, $dataJsonSelectors]);
        //if $updateAnimations is true, no neeed to return js files
        if ($updateAnimations) return json_encode([$animationData, $animationsSelectors]);
        return $scriptOutput;
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

                // breakpoint s is more specific (html:root can overwrite parent + child class selectors)
                if ($breakpoint['name'] == 's' && $backend == 0) {
                    $css .= $breakpoint['size'] . '{ html:root ';
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
                            //for tagname
                            $item['cssClass'] = $p->name . ' ' . $item['id'];
                        } else {
                            //for classes
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

                $coverImage = 0;
                foreach ($breakpoint['css'] as $style => $val) {

                    $fallbackFonts = $this->ft->fallbackFonts;

                    if ($style === 'font-family' && $fallbackFonts) {
                        $val = $val . ', ' . $this->ft->fallbackFonts;
                    }

                    if ($style === 'align-items' && $val === 'stretch') {
                        //force strech of rows with grid-template-rows=1fr
                        $css .= 'grid-template-rows:1fr;';
                        $coverImage = 1;
                    }

                    if ($style === '--pg-animation' && $val === 'unset') {
                        $css .= 'animation-name:unset!important;';
                    }

                    $css .= $style . ': ' . $val . '; ';
                }

                $css .= ' } ';

                if ($coverImage && $item['cssClass'] != strtolower($item['tagName'])) {
                    $css .= '.' . $item['cssClass'] . ' .pg-media-responsive { ';
                    $css .= 'height:100%; min-height: 100%; object-fit:cover; max-width:none;';
                    $css .= '}';
                }

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

        if (!$mainPage->id) return;

        $backend = $this->isBackend();

        //on frontend get cached markup if exists and return early
        $cache = $this->cache->get('pgCache-css-' . $mainPage->id);
        if ($cache && $this->user->isLoggedin()) $this->cache->delete("pgCache-*");
        if ($cache && !$backend) return $cache;

        // bd('styles');

        $arrayFiles = [];
        $itemCss = '';
        $cssBackend = '';
        $cssTemplates = '';
        $defaults = '';
        $fonts = '';
        $cssMainPage = '';
        $customCss = '';
        $animations = '';

        // page array to hold items to load files
        $itemsArray = new PageArray();
        $itemsParent = $this->pages->get('pg-' . $mainPage->id);

        //load backend css only if rendering page with pg field
        if ($backend && $itemsParent->id) {
            $cssBackendUrl = wire('config')->urls->InputfieldPageGrid . "css/main.css";
            $cssBackend = '<link rel="stylesheet" type="text/css" href="' . $cssBackendUrl . '">';
        }

        //add global page for custom classes
        if ($loadGlobalClasses) {
            $globalClasses = $this->pages->get('pg-classes');

            foreach ($globalClasses->find('sort=sort') as $class) {
                $itemsArray->add($class);
            }
        }

        //get items
        if ($itemsParent->id) {
            //getAncestors returns parent and grandchildren. needed because of bug. $page->find('') is not returning all pages
            $pageItems = $this->getAncestors($itemsParent);

            // for some multilang pages getAncestors is not returning the children, so get them as a backup like this
            // with multi lang we also need to make sure to get all containers (for some reason find is not returning all of them)
            if (count($pageItems) <= 1) {
                $pageItems = $itemsParent->find('');
                $rootPath = $itemsParent->url('');
                foreach ($mainPage->fields as $f) {
                    if ($f->type instanceof FieldtypePageGrid) {
                        $pgWrapper = $this->pages->get($rootPath . 'pg-' . $f->id . '/');
                        $pageItems->add($pgWrapper);
                    }
                }
            }

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
        if ($loadDefaults == true) {
            $defaults = include 'styleDefaults.php';
            //minify output
            $defaults = str_replace(array("\r", "\n"), '', $defaults);
            $defaults = preg_replace('/\s+/', ' ', $defaults);
        }

        foreach ($itemsArray as $item) {

            //load template file children
            if ($loadFiles) {
                $filename = wire('config')->paths->templates . 'blocks/' . $item->template->name . '.css';
                $filenameUrl = wire('config')->urls->templates . 'blocks/' . $item->template->name . '.css';

                if (!in_array($item->template->name, $arrayFiles) && file_exists($filename)) {
                    $cssTemplates .= '
    <link rel="stylesheet" type="text/css" href="' . $filenameUrl . '">';
                    $arrayFiles[] = $item->template->name;
                }

                if (file_exists($filename) == 0) {
                    //if no file found check in module
                    $filename = wire('config')->paths->siteModules . 'PageGridBlocks/blocks/' . $item->template->name . '.css';
                    $filenameUrl = wire('config')->urls->siteModules . 'PageGridBlocks/blocks/' . $item->template->name . '.css';

                    if (!in_array($item->template->name, $arrayFiles) && file_exists($filename)) {
                        $cssTemplates .= '
    <link rel="stylesheet" type="text/css" href="' . $filenameUrl . '">';
                        $arrayFiles[] = $item->template->name;
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
                $preconnect = '<link rel="preconnect" href="https://fonts.bunny.net" crossorigin>';
                $fonts = $preconnect . '<link rel="stylesheet" type="text/css" href="https://fonts.bunny.net/css2?' . $fonts . '">';
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

        $cssOutput = $cssBackend . $defaults . $fonts . $cssTemplates . $itemCss . $customCss;

        //cache output
        if (!$backend) $this->cache->save('pgCache-css-' . $mainPage->id, $cssOutput);

        return  $cssOutput;
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
    public function getPage($page = 0) {

        //This always returns mainpage on frontend
        $p = wire('page');

        //after ajax inside backend we get the admin page, so we need to find the edit page
        if ($this->session->pg_page) $p = $this->pages->get($this->session->pg_page);
        if (!$p || !$p->id) return false;

        if ($p->template->name === 'admin' && isset($_GET['page'])) $p = $this->pages->get($_GET['page']);
        if (!$p || !$p->id) return false;

        if ($p->template->name === 'admin') return false;

        return $p;
    }

    // pass options to template file before render
    //render options are depricated and will be removed in future versions
    public function renderOptions($options = []) {
        //set options to template at runtime
        $templateName = basename(debug_backtrace()[0]['file'], '.php');
        $t = $this->templates->get($templateName);
        if ($t->id) {
            $optionsJson = json_encode($options);
            $p = $t->page;

            $t->pgOptions = $optionsJson;
            //allow page specific options (we set page to the template object on render, so we can get it here)
            if ($p && $p->id) $p->pgOptions = $optionsJson;
        }
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
