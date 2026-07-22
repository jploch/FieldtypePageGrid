<?php

namespace ProcessWire;

/**
 * PageGrid for ProcessWire
 * 
 * Copyright (C) 2023 by Jan Ploch
 * THIS IS A COMMERCIAL MODULE - DO NOT DISTRIBUTE
 */

class FieldtypePageGrid extends FieldtypeMulti implements Module, ConfigurableModule {

  /**
   * Returns module metadata used by ProcessWire to register and describe this module.
   *
   * @return array Module info array with title, version, permissions, and dependencies.
   */
  public static function getModuleInfo() {

    return array(
      'title' => __('PAGEGRID Page Builder'),
      'summary' => __('PAGEGRID is a visual page builder for ProcessWire that gives developers full control while enabling designers and editors to create responsive layouts without coding.', __FILE__),
      'version' => '2.3.14',
      'author' => 'Jan Ploch',
      'icon' => 'th',
      'href' => "https://page-grid.com",
      'installs' => array('InputfieldPageGrid', 'ProcessPageGrid', 'PageFrontEdit', 'ProcessPageClone'),
      'requires' => array('ProcessWire>=3.0.210', 'PHP>=5.4.0'),
      'autoload' => true,
      'permissions' => array(
        'pagegrid-process' => 'Allow PAGEGRID to process ajax calls. This permission is needed to edit/save pages with PAGEGRID!',
        'page-pagegrid-edit' => 'Edit PAGEGRID items in modal',
        'pagegrid-drag' => 'Drag PAGEGRID items',
        'pagegrid-resize' => 'Resize PAGEGRID items',
        'pagegrid-style-panel' => 'Enable styling of PAGEGRID items',
        'pagegrid-add' => 'Use the sidebar to drag new PAGEGRID items to the page (also needs pagegrid-drag permission to work)',
        'pagegrid-settings-tab' => 'Show the Settings tab in the PAGEGRID modal edit view',
        'pagegrid-draft' => 'Create and manage PAGEGRID drafts',
      ),
    );
  }

  /**
   * Runs on module install; calls createModule() to set up templates, pages, and roles.
   *
   * @return void
   */
  public function install() {
    $this->createModule();
  }

  //upgrade needs work
  // public function ___upgrade($fromVersion, $toVersion) {
  //   $this->createModule();
  // }

  /**
   * Creates all required templates, fieldgroups, pages, roles, and permissions for PAGEGRID.
   * Called on install and conditionally during ready() when required resources are missing.
   *
   * @return void
   */
  public function createModule() {

    // bd('create module');

    $fields = wire('fields');
    $adminPage = wire('pages')->get('name=pagegrid, template=admin');
    if (!$adminPage || !$adminPage->id) return;

    //create block folder if it does not exist
    $blockFolder = $this->config->paths->templates . 'blocks/';
    if (!file_exists($blockFolder) && !is_dir($blockFolder)) {
      mkdir($blockFolder);
    }

    // create template for pg container
    $t = $this->templates->get('pg_container');

    if (!$t || !$t->id) {
      //create container template
      $titleField = $fields->get('title');

      // fieldgroup for template
      $fg = new Fieldgroup();
      $fg->name = 'pg_container';
      $fg->add($titleField);
      $fg->save();

      $t = new Template();
      $t->name = 'pg_container';
      $t->fieldgroup = $fg; // add the field group
      // $t->flags = 8; // system template, to prevent use in backend, but also hides contaier permissions :(
      // $t->noParents = -1; //allow one more (2 pages can use this template)
      // $t->noParents = ''; //allow user to create/clone animation and class pages
      $t->icon = 'th';
      $t->tags = 'PageGrid';
      $t->save();
    }

    // create starter template for site/templates
    $t = $this->templates->get("pagegrid-page");
    if (!$t || !$t->id) {
      //create container template
      $titleField = $fields->get('title');

      // fieldgroup for template
      $fg = new Fieldgroup();
      $fg->name = 'pagegrid-page';
      $fg->add($titleField);
      $fg->save();

      $t = new Template();
      $t->name = 'pagegrid-page';
      $t->fieldgroup = $fg; // add the field group
      $t->icon = 'th';
      // $t->noAppendTemplateFile = 1;
      // $t->noPrependTemplateFile = 1;
      // $t->tags = 'PageGrid'; do not set tag so template is shown on add new page screen
      $t->save();

      // copy pagegrid-page.php template file to site template folder
      $moduleTemplate =  $this->config->paths->siteModules . $this->className() . '/pagegrid-page.php';
      $siteTemplate = $this->config->paths->templates . 'pagegrid-page.php';
      copy($moduleTemplate, $siteTemplate);

      //add pg field to starter
      //create field already
      $f = $this->fields->get("type=FieldtypePageGrid");
      if (!$f || !$f->id) {
        $nameTaken = $this->fields->get("name=grid");
        $f = new Field;
        $f->label = 'Grid';
        if ($nameTaken && $nameTaken->id) {
          $f->name = 'pgrid';
        } else {
          $f->name = 'grid';
        }
        $f->type = $this->modules->get('FieldtypePageGrid');
        $f->tags = 'PageGrid';
        $f->save();
      }
      $t->fieldgroup->add($f);
      $t->fieldgroup->save();
    }

    // create template for blueprints
    $t = $this->templates->get('pg_blueprint');

    if (!$t || !$t->id) {
      //create container template
      $titleField = $fields->get('title');

      // fieldgroup for template
      $fg = new Fieldgroup();
      $fg->name = 'pg_blueprint';
      $fg->add($titleField);
      $fg->save();

      $t = new Template();
      $t->name = 'pg_blueprint';
      $t->fieldgroup = $fg; // add the field group
      // $t->flags = 8; // system template, to prevent use in backend, but also hides contaier permissions :(
      // $t->noParents = -1; //allow one more (2 pages can use this template)
      $t->noPrependTemplateFile = 1;
      $t->noAppendTemplateFile = 1;
      $t->icon = 'th';
      $t->tags = 'PageGrid';
      $t->save();
    }

    //add blueprint parent field if not set
    $f = $fields->get('pg_blueprint_parent');

    if (!$f || !$f->id) {
      $f = new Field;
      $f->name = 'pg_blueprint_parent';
      $f->label = 'Blueprint Settings';
      $f->type = 'FieldtypePage';
      $f->tags = 'PageGrid';
      $f->description = 'Select a parent page here if you want to add the content of this blueprint to new child pages automatically.';
      $f->inputfield = 'InputfieldPageListSelect';
      $f->derefAsPage = 1;
      $f->contentType = 1;
      $f->collapsed = 1;
      $f->icon = 'cog';
      $f->labelFieldName = 'title';
      $f->findPagesSelector = 'template!=2';
      $f->appendMarkup = '<style>#wrap_Inputfield_pg_blueprint_parent .PageListTemplate_admin{display:none;}</style>';
      $f->save();
      // $t->fieldgroup->add($f);
      $t->fieldgroup->insertAfter($f, $t->fieldgroup->fields->get("title"));
      $t->fieldgroup->save();
    }
    // END create template for blueprints

    // create global system containers
    $this->createSystemContainer('pg-items',      'Items',      $adminPage, true);
    $this->createSystemContainer('pg-drafts',     'Drafts',     $adminPage, true);
    $this->createSystemContainer('pg-classes',    'Classes',    $adminPage, true);
    $this->createSystemContainer('pg-animations', 'Animations', $adminPage, true);
    $this->createSystemContainer('pg-blueprints', 'Blueprints', $adminPage, false);
    $this->createSystemContainer('pg-symbols',    'Symbols',    $adminPage, false);

    //create editor role
    //add role and permissions

    // pagegrid-editor role
    if (!$this->roles->get('pagegrid-editor') || !$this->roles->get('pagegrid-editor')->id) {
      $erole = $this->roles->add("pagegrid-editor");
    } else {
      $erole = $this->roles->get('pagegrid-editor');
    }

    // pagegrid-designer role
    if (!$this->roles->get('pagegrid-designer') || !$this->roles->get('pagegrid-designer')->id) {
      $drole = $this->roles->add("pagegrid-designer");
    } else {
      $drole = $this->roles->get('pagegrid-designer');
    }

    //add permissions
    $erole->addPermission("page-view");
    $erole->addPermission("page-edit");
    $erole->addPermission("profile-edit");
    if ($this->permissions->get('page-pagegrid-edit')->id) $erole->addPermission("page-pagegrid-edit");
    if ($this->permissions->get('page-pagegrid-edit')->id) $drole->addPermission("page-pagegrid-edit");

    $drole->addPermission("page-view");
    $drole->addPermission("page-edit");
    $drole->addPermission("profile-edit");
    $drole->addPermission("page-sort");
    if ($this->permissions->get('pagegrid-drag')->id) $drole->addPermission("pagegrid-drag");
    if ($this->permissions->get('pagegrid-resize')->id) $drole->addPermission("pagegrid-resize");
    if ($this->permissions->get('pagegrid-add')->id) $drole->addPermission("pagegrid-add");

    //rename for old installations
    if ($this->permissions->get('pagegrid-process')->id) {
      $permission = $this->permissions->get("pagegrid-process");
      $permission->of(false);
      $permission->title = 'Allow PAGEGRID to process ajax calls. This permission is needed to edit/save pages with PAGEGRID!';
      $permission->save();
      $drole->addPermission($permission->name);
    }

    //play animation
    if (!$this->permissions->get('page-pagegrid-play')->id) {
      $permission = $this->permissions->add("page-pagegrid-play");
      $permission->title = 'Play PAGEGRID animations in backend';
      $permission->save();
    }
    $drole->addPermission('page-pagegrid-play');

    if (!$this->permissions->get('pagegrid-select')->id) {
      $permission = $this->permissions->add("pagegrid-select");
      $permission->title = 'User can select editable elements by click instead of hover';
      $permission->save();
    }

    //breakpoints
    if (!$this->permissions->get('pagegrid-breakpoints')->id) {
      $permission = $this->permissions->add("pagegrid-breakpoints");
      $permission->title = 'Manage PAGEGRID breakpoints';
      $permission->save();
    }
    $drole->addPermission($permission->name);

    //pagegrid-delete (item header delete)
    if (!$this->permissions->get('page-pagegrid-delete')->id) {
      $permission = $this->permissions->add("page-pagegrid-delete");
      $permission->title = 'Delete PAGEGRID items ';
      $permission->save();
    }
    $drole->addPermission($permission->name);

    //pagegrid-settings-tab
    if (!$this->permissions->get('pagegrid-settings-tab')->id) {
      $permission = $this->permissions->add("pagegrid-settings-tab");
      $permission->title = 'Show the Settings tab in the PAGEGRID modal edit view';
      $permission->save();
    }

    //pagegrid-draft
    if (!$this->permissions->get('pagegrid-draft')->id) {
      $permission = $this->permissions->add("pagegrid-draft");
      $permission->title = 'Create and manage PAGEGRID drafts';
      $permission->save();
      $drole->addPermission('pagegrid-draft');
      $erole->addPermission('pagegrid-draft');
    }

    //setup permission
    if (!$this->permissions->get('pagegrid-setup')->id) {
      $permission = $this->permissions->add("pagegrid-setup");
      $permission->title = 'Access PAGEGRID setup page';
      $permission->save();
    }

    if (!$this->permissions->get('pagegrid-symbol-create')->id) {
      $permission = $this->permissions->add("pagegrid-symbol-create");
      $permission->title = 'Create PAGEGRID symbols';
      $permission->save();
      $drole->addPermission('pagegrid-symbol-create');
    }

    if (!$this->permissions->get('pagegrid-symbol-add')->id) {
      $permission = $this->permissions->add("pagegrid-symbol-add");
      $permission->title = 'Add PAGEGRID symbols';
      $permission->save();
    }

    if (!$this->permissions->get('page-edit-front')->id) {
      $permission = $this->permissions->add("page-edit-front");
      $permission->title = 'Use the front-end page editor';
      $permission->save();
    } else {
      $permission = $this->permissions->get('page-edit-front');
    }
    $erole->addPermission($permission->name);
    $drole->addPermission($permission->name);

    if (!$this->permissions->get('pagegrid-process')->id) {
      $permission = $this->permissions->add("pagegrid-process");
      $permission->title = 'Allow PAGEGRID to process ajax calls (needed for PAGEGRID to work)';
      $permission->save();
    } else {
      $permission = $this->permissions->get('pagegrid-process');
    }
    $erole->addPermission($permission->name);
    $drole->addPermission($permission->name);

    //save editor
    $erole->of(false);
    $erole->save();

    //save designer
    $drole->of(false);
    $drole->save();

    //guest
    $grole = $this->roles->get('guest');

    // add template permissions fpr pg container
    $etemplate = $this->templates->get("pg_container");

    $addRoles = $etemplate->get("addRoles");
    $addRoles[] = $drole->id;

    $editRoles = $etemplate->get("editRoles");
    $editRoles[] = $erole->id;
    $editRoles[] = $drole->id;

    $createRoles = $etemplate->get("createRoles");
    $createRoles[] = $drole->id;

    $etemplate->useRoles = 1;
    $etemplate->set("roles", array($grole->id, $erole->id, $drole->id));
    $etemplate->set("addRoles", $addRoles);
    $etemplate->set("editRoles", $editRoles);
    $etemplate->set("createRoles", $createRoles);
    $etemplate->save();

    $htemplate = $this->templates->get('home');
    if ($htemplate) {
      $editRoles = $htemplate->get("editRoles");
      $editRoles[] = $erole->id;
      $editRoles[] = $drole->id;
      $htemplate->set("editRoles", $editRoles);
      $htemplate->save();
    }
    //END create editor role

    // create symbol permissions
    $this->createSymbolPermissions();
  }

  /**
   * Removes PAGEGRID templates, fieldgroups, pages, and roles on module uninstall.
   *
   * @return void
   */
  public function uninstall() {

    //remove block modules, causes error so uncomment for now
    // $path = wire('config')->paths->siteModules . 'PageGridBlocks/blocks/';
    // $files = glob($path . '*.php');

    // foreach ($files as $file) {
    //   $fileName = str_replace($path, '', $file);
    //   $templateName = str_replace('.php', '', $fileName);
    //   $className = str_replace('pg_', '', $templateName);
    //   $className = str_replace('_', '', ucwords($className, '_'));
    //   $className = 'Blocks' . $className;
    //   $moduleExists = $this->modules->get($className);
    //   $installedBlock = $this->modules->isInstalled($className);

    //   //check if modules are installed
    //   if ($moduleExists && $installedBlock) $this->modules->uninstall($className);
    // }

    //first empty trash to prevent bug when process page already in trash
    $int = $this->pages->emptyTrash();

    $t = $this->templates->get('pg_container');
    $t2 = $this->templates->get('pg_blueprint');
    $t3 = $this->templates->get('pagegrid-page');
    // $fg = $this->fieldgroups->get('pg_container');

    $pgTemplates = array($t->id, $t2->id, $t3->id);
    $pgPages = $this->pages->find("template=$t|$t2|$t3, include=all");

    foreach ($pgPages as $p) {
      if ($p && $p->id && !count($p->children())) {
        $p->delete(true);
      }
    }

    foreach ($pgTemplates as $tId) {
      $t = $this->templates->get($tId);
      $fg = $this->fieldgroups->get($tId);

      if ($t && $t->id && $t->getNumPages() > 0) {
        $this->message("Can't delete template pg_container, because it's been used by some pages.");
      } else {
        $this->message("Remove PageGrid Page and Template");
        if ($t && $t->id) {
          $t->flags = Template::flagSystemOverride; // remove flasg system template, to be able to delete
          $t->flags = 0; // remove flasg system template, to be able to delete
          $t->save();
          wire('templates')->delete($t);
        }
        if ($fg) {
          wire('fieldgroups')->delete($fg);
        }
      }
    }

    //remove blueprint parent field
    $f = $this->fields->get('pg_blueprint_parent');
    if ($f && $f->id) $this->fields->delete($f);

    //delete role
    if ($this->roles->get('pagegrid-editor')->id) {
      $this->roles->delete($this->roles->get('pagegrid-editor'));
    }
  }

  /**
   * Initialises the module: registers the pagegrid fuel variable and attaches early hooks.
   * Hooks requiring a logged-in user are only registered when a user session is active.
   *
   * @return void
   */
  public function init() {
    // Register before any guards so it fires in CLI, frontend, and admin contexts
    $this->addHookAfter('Pages::added', $this, "createContainers");

    //make $pagegrid available to call functions from InputfieldPageGrid
    $this->fuel->set('pagegrid', $this->modules->get('InputfieldPageGrid'));
    $this->addHookBefore('Page::render', $this, 'disableAppendFile');

    //if user is not loggedin no need to run hooks (autoload needs to be true for blueprints to works, loading teplate file from module folder)
    if (wire('user') && !wire('user')->isLoggedin()) return;

    //these hooks are only needed when user is loggedin
    $this->setBlueprintTemplate();
    $this->addHookAfter('AdminTheme::getExtraMarkup', $this, 'addBodyClasses');
    $this->addHookBefore('PageFrontEdit::getPage', $this, 'disableInlineEdit');
    $this->config->styles->add($this->config->urls->InputfieldPageGrid . 'css/admin.css');
    //if superuser and debug on allways allow module download for block dependencies
    if (wire('user') && wire('user')->isSuperuser() && $this->config->debug) {
      $this->config->moduleInstall('download', true);
    }
  }

  /**
   * Runs after ProcessWire is ready; verifies required resources exist and registers admin hooks.
   * Only executes in the admin context for performance.
   *
   * @return void
   */
  public function ready() {

    //if not inside admin no need to run ready function (good for performance)
    if (wire('page')->template->name != 'admin') return;

    //create pages und templates if they don't exist
    $container = $this->pages->get("name=pg-items, template=pg_container");
    if (!$container->id) $this->createModule();

    $container = $this->pages->get("name=pg-drafts, template=pg_container");
    if (!$container->id) $this->createModule();

    $container = $this->pages->get("name=pg-animations, template=pg_container");
    if (!$container->id) $this->createModule();

    $container = $this->pages->get("name=pg-classes, template=pg_container");
    if (!$container->id) $this->createModule();

    $container = $this->pages->get("name=pg-blueprints, template=pg_container");
    if (!$container->id) $this->createModule();

    $container = $this->pages->get("name=pg-symbols, template=pg_container");
    if (!$container->id) $this->createModule();

    $gridTemplate = $this->templates->get("pagegrid-page");
    if (!$gridTemplate || !$gridTemplate->id) $this->createModule();

    if (!$this->permissions->get('pagegrid-select')->id) $this->createModule();

    //let users clone animations and classes
    $gridTemplate = $this->templates->get('pg_container');
    if ($gridTemplate && $gridTemplate->id) {
      $gridTemplate->noParents = '';
      $gridTemplate->save();
    }

    //let user change blueprint permissions
    $gridTemplate = $this->templates->get('pg_blueprint');
    if ($gridTemplate && $gridTemplate->useRoles != 1) {
      $gridTemplate->useRoles = 1;
      $gridTemplate->save();
    }

    //force inlineLimitPage setting for core PageFrontEdit module
    if ($this->modules->isInstalled('PageFrontEdit')) {
      $configCore = $this->modules->getConfig('PageFrontEdit');
      if (!array_key_exists('inlineLimitPage', $configCore) || $configCore['inlineLimitPage'] != '0') {
        $configCore['inlineLimitPage'] = '0';
        $this->modules->saveConfig('PageFrontEdit', $configCore);
      }
    }

    //enforce block childrenTab template settings
    // foreach ($this->templates as $t) {
    //   $options = $t->pgOptions ? json_decode($t->pgOptions, true) : [];
    //   if (isset($options['childrenTab']) && $t->tabChildren != $options['childrenTab']) {
    //     $t->tabChildren = $options['childrenTab'];
    //     $t->save();
    //   }
    // }

    $this->addHookAfter("Modules::refresh", $this, "createModule");
    $this->addHookAfter('Pages::cloned', $this, "clone");
    $this->addHookBefore('Page::changed(0:title)', $this, "titleChanged");
    $this->addHookAfter('Pages::delete', $this, "delete");
    $this->addHookAfter('Pages::delete', $this, "deleteDraft");
    $this->addHookAfter('ProcessPageList::find', $this, "hideDummies");
    $this->addHookAfter('Pages::added', $this, "copyFromBlueprint");
    $this->addHookAfter('Pages::added', $this, "activateLanguages");
    $this->addHookAfter("Pages::save", $this, "autoPuplish");
    $this->addHookAfter('ProcessPageEdit::buildForm', $this, "modalEdit");
    $this->addHookAfter('ProcessPageEdit::buildForm', $this, "draftTabs");
    $this->addHookAfter('ProcessPageEdit::getTabs', $this, 'draftTabsOrder');
    $this->addHookAfter('ProcessPageEdit::buildFormSettings', $this, 'filterTemplateSelect');
    $this->addHookAfter('ProcessPageEdit::processInput', $this, 'savePgPermissions');
    $this->addHookAfter('User::hasPagePermission', $this, 'hookHasPagePermission');
    $this->addHookAfter('Page::sortable', $this, 'hookPageSortable');
    $this->addHookAfter('ProcessPageEdit::buildFormChildren', $this, 'hookBuildFormChildren');
    $this->addHookBefore('ProcessPageAdd::buildForm', $this, "quickAdd");
    $this->addHookAfter('ProcessPageAdd::buildForm', $this, "pageAddForm");
    $this->addHookBefore('ProcessPageEdit::execute', $this, 'blueprintReady');
    $this->addHookBefore('ProcessPageEdit::execute', $this, 'draftActions');
    $this->addHookAfter('ProcessTemplate::executeAdd', $this, "addTemplate");
    $this->addHookBefore('ProcessTemplate::buildEditForm', $this, "setTemplateFile");
    $this->addHookBefore('ProcessTemplate::getListTableRow', $this, "setTemplateFile");

    //NEW add bluerint render template select
    if ($this->pages->get('template=pg_blueprint') && $this->pages->get('template=pg_blueprint')->id) {
      $this->addHookAfter('ProcessTemplate::buildEditForm', $this, 'addCustomTemplateSetting');
      $this->addHookBefore('ProcessTemplate::executeSave', $this, 'saveCustomTemplateSetting');
    }

    //this is needed to keep module and field settings in sync
    $this->addHookAfter('ProcessField::fieldSaved', $this, "updateFieldSettings");
    $this->addHookAfter('ProcessTemplate::fieldAdded', $this, "updateTemplateSettings");
    $this->addHookAfter('ProcessTemplate::fieldRemoved', $this, "updateTemplateSettings");

    //hide setup page for non superusers
    $pg = $this->pages->get('name=pagegrid, template=admin');
    $user =  $this->user;

    if ($pg->id) {
      $hidden = $pg->hasStatus('hidden');

      if (!$user->hasPermission('pagegrid-setup') && $hidden == 0) {
        $pg->addStatus(Page::statusHidden);
        $pg->save();
      }
      if ($user->hasPermission('pagegrid-setup') && $hidden) {
        $pg->removeStatus(Page::statusHidden);
        $pg->save();
      }
    }
    //END hide setup page for non superusers

    //create symbol permissions (if not already)
    if (wire('page')->name == 'roles') $this->createSymbolPermissions();
  }

  /**
   * Deactivates automatic prepend/append template files when the template contains
   * a PageGrid field and the template file calls $pagegrid->noAppendFile.
   *
   * @param HookEvent $event Hook event from Page::render.
   * @return void
   */
  // deactivate automatic appending of template file
  public function disableAppendFile($event) {
    $p = $event->object;

    //deactivate automatic appending of template file look for string
    if ($p && $p->id && $p->template->name !== 'admin' && $p->fields && $p->fields->get('type=FieldtypePageGrid')) {
      // $parsedTemplate = new TemplateFile($p->template->filename);
      $parsedTemplate = file_get_contents($p->template->filename);
      if (strpos($parsedTemplate, '$pagegrid->noAppendFile') !== false) {
        $this->config->prependTemplateFile = false;
        $this->config->appendTemplateFile = false;
      }
    }
  }

  /**
   * Sets all languages active automatically for newly added PageGrid item pages.
   *
   * @param HookEvent $event Hook event from Pages::added.
   * @return void
   */
  // set all languages active automatically for new pg items
  public function activateLanguages($event) {
    if (!$this->templates->get('language')) return;
    if (!$this->modules->isInstalled('LanguageSupport')) return;
    $page = $event->arguments(0);
    if ($page->_cloning && $page->_cloning->id) return; // skip cloned pages, language status is inherited
    //only enable for pg items
    if (!$page->parents()->get('template=pg_container')) return;
    foreach ($this->wire->languages as $lang) $page->set("status$lang", 1);
    $page->save();
  }

  /**
   * Handles the pgquickadd GET action: creates a new page from the given template and parent
   * then redirects the user to the new page's edit URL.
   *
   * @param HookEvent $event Hook event from ProcessPageAdd::buildForm.
   * @return void
   */
  public function quickAdd($event) {
    if (isset($_GET['pgquickadd']) && isset($_GET['template_id']) && isset($_GET['parent_id'])) {
      // bd('quick-add');
      $parent_id = $_GET['parent_id'] ? $_GET['parent_id'] : 0;
      $template_id = $_GET['template_id'] ? $_GET['template_id'] : 0;
      $template =  $this->templates->get($template_id);
      $parent = $this->pages->get($parent_id);
      if ($parent->id && $template->id) {
        $p = new Page();
        $p->template = $template->name;
        $p->parent = $parent;
        $p->save();

        //set unique name
        $templateName = str_replace('_', '-', $template->name);
        $p->setAndSave('title', $templateName . '-' . $p->id);
        $p->setAndSave('name', $templateName . '-' . $p->id);

        //redirect to edit page
        $editUrl = $p->editUrl();
        $this->session->redirect($editUrl);
      }
    }
  }

  /**
   * Keeps the module config in sync when a PageGrid field is added to or removed from a template.
   *
   * @param HookEvent $event Hook event from ProcessTemplate::fieldAdded or ProcessTemplate::fieldRemoved.
   * @return void
   */
  public function updateTemplateSettings($event) {
    $f = $event->arguments[0];
    $t = $event->arguments[1];
    if (!$f->type instanceof FieldtypePageGrid) return;
    $data = $this->modules->getConfig('FieldtypePageGrid');
    if ($event->method === 'fieldAdded') $data['addTemplate_' . $f->id][] = $t->id;
    if ($event->method === 'fieldRemoved') {
      if (!isset($data['addTemplate_' . $f->id]) || !is_array($data['addTemplate_' . $f->id])) return;
      if (($key = array_search($t->id, $data['addTemplate_' . $f->id])) !== false) unset($data['addTemplate_' . $f->id][$key]);
    }

    $this->modules->saveConfig('FieldtypePageGrid', $data);
  }

  /**
   * Syncs the template_id field setting into the module config whenever a PageGrid field is saved.
   *
   * @param HookEvent $event Hook event from ProcessField::fieldSaved.
   * @return void
   */
  public function updateFieldSettings($event) {
    $f = $event->arguments(0);
    if (!$f->type instanceof FieldtypePageGrid) return;
    //get module config
    $data = $this->modules->getConfig('FieldtypePageGrid');
    if ($data['template_id_' . $f->id] == $f->template_id) return;
    if ($f->template_id == null || !$f->template_id) return;
    $data['template_id_' . $f->id] = $f->template_id;
    $this->modules->saveConfig('FieldtypePageGrid', $data);
  }

  /**
   * Lists block templates (from the blocks/ folder) on the Add Template screen.
   *
   * @param HookEvent $event Hook event from ProcessTemplate::executeAdd.
   * @return void
   */
  //list block templates when creating new pages
  public function addTemplate($event) {
    $templateFiles = array();
    $ext = "." . $this->config->templateExtension;
    $prependTemplateFile = $this->config->prependTemplateFile;
    $appendTemplateFile = $this->config->appendTemplateFile;
    $ignoreRegex = $this->config->ignoreTemplateFileRegex;
    $templatePath = $this->config->paths->templates;
    $dir = new \DirectoryIterator($templatePath);
    $dirBlocks = new \DirectoryIterator($templatePath . 'blocks/');
    $folders = [$dir, $dirBlocks];

    foreach ($folders as $folder) {
      foreach ($folder as $file) {
        if ($file->isDir() || $file->isDot()) continue;
        $filename = $file->getFilename();
        if ($filename == $prependTemplateFile || $filename == $appendTemplateFile) continue; // skip over prepend/append files
        if (substr($filename, -1 * strlen($ext)) != $ext) continue;
        if ($ignoreRegex && preg_match($ignoreRegex, $filename)) continue;
        $basename = basename($file->getFilename(), $ext);
        if ($this->sanitizer->name($basename) !== $basename) continue;
        if (ctype_digit($basename)) continue;
        // if(count($templates->find("name=$basename"))) continue; 
        if ($this->templates->get($basename)) continue;
        if ($file->getPath() === $templatePath . 'blocks') {
        }
        $templateFiles[] = $basename;
      }
    }

    // $this->input->post('test');

    $form = $this->modules->get('Processtemplate')->buildAddForm($templateFiles);
    $event->return = $form->render();
    $event->replace = true;
  }

  /**
   * Resolves the correct template file path, searching the blocks/ folder and module folder
   * when the default location does not contain a matching file.
   *
   * @param HookEvent $event Hook event from ProcessTemplate::buildEditForm or ProcessTemplate::getListTableRow.
   * @return void
   */
  //hide template not found warning for block templates 
  public function setTemplateFile($event) {
    $template = $event->arguments[0];
    $ext = "." . $this->config->templateExtension;
    $template_name = $template->altFilename ? basename($template->altFilename, $ext) : $template->name;
    $templateFilename = $this->config->paths->templates . $template_name . $ext;

    //if no template file found look inside blocks folder
    if (file_exists($templateFilename) == 0) {
      //look inside module block folder
      $templateFilename = $this->config->paths->templates . 'blocks/' . $template_name . $ext;
    }

    //if no template file found look inside module folder
    if (file_exists($templateFilename) == 0) {
      //look inside module block folder
      $templateFilename = $this->config->paths->siteModules . 'PageGridBlocks/blocks/' . $template_name . $ext;
    }

    if (file_exists($templateFilename)) {
      $template->filename = $templateFilename;
    }
  }

  /**
   * Ensures the pg_blueprint template has PageGrid fields added at runtime before editing.
   * Creates the items container page for the blueprint if it does not yet exist.
   *
   * @param HookEvent $event Hook event from ProcessPageEdit::execute.
   * @return void
   */
  public function blueprintReady($event) {
    // make sure blueprint has pg fields
    // the fields are added on runtime, based on parent pages found
    // supports one or more fields with the pg_blueprint template
    $p = $event->object->getPage();
    if (!$p->id) return;
    if ($p->template->name !== 'pg_blueprint') return;
    $t = $this->templates->get('pg_blueprint');
    if (!$t || !$t->id) return;

    //add the fields that have data at runtime
    $itemsParent = $this->pages->get('name=pg-' . $p->id . ', template=pg_container');

    if (!$itemsParent->id) {
      $itemsParent = new Page(); // create new page object
      $itemsParent->template = 'pg_container'; // set template
      $itemsParent->parent = 'pg-items'; // set the parent
      $itemsParent->name = 'pg-' . $p->id; // give it a name used in the url for the page
      $itemsParent->title = $p->title . ' items'; // set page title (not neccessary but recommended)
      $itemsParent->save();
    }

    //make sure older blueprints still work (to support old module versions, can be removed later)
    if (!count($itemsParent->children('template=pg_container'))) {
      $f = $this->fields->get('type=FieldtypePageGrid');
      if ($f && $f->id) {
        $itemsParentNew = new Page(); // create new page object
        $itemsParentNew->template = 'pg_container'; // set template
        $itemsParentNew->parent = $itemsParent->id; // set the parent
        $itemsParentNew->name = 'pg-' . $f->id; // give it a name used in the url for the page
        $itemsParentNew->title = $f->name; // set page title (not neccessary but recommended)
        $itemsParentNew->save();

        foreach ($itemsParent->children() as $p) {
          if ($p->template->name === 'pg_container') continue;
          $p->of(false);
          $p->parent = $itemsParentNew;
          $p->save();
          $p->of(true);
        }
      }
    }
    //END make sure older blueprints still work (to support old module versions, can be removed later)
    $fieldFound = 0;
    foreach ($itemsParent->children('template=pg_container') as $fieldPage) {
      $fName = str_replace('pg-', '', $fieldPage->name);
      $f = $this->fields->get("id=$fName, type=FieldtypePageGrid");
      if ($f && $f->id) {
        $t->fieldgroup->add($f);
        $fieldFound = 1;
      }
    }
    //if no items found get first field
    // if (!$fieldFound) $t->fieldgroup->add($this->fields->get('type=FieldtypePageGrid'));
    if (!$fieldFound) {
      $fields = $this->fields->find("type=FieldtypePageGrid");
      foreach ($fields as $f) {
        $hasPages = count($this->pages->find("pg-$f->id"));
        if ($hasPages) {
          $t->fieldgroup->add($f);
          $fieldFound = 1;
          break;
        }
      }
    }
  }

  /**
   * Handles draft actions (create, publish, discard) via URL parameters on the page edit screen.
   *
   * @param HookEvent $event Hook event from ProcessPageEdit::execute.
   */
  public function draftActions($event) {
    $action = $this->wire('input')->get('pg-draft');
    if (!$action || !in_array($action, ['create', 'publish', 'discard'])) return;

    $page = $event->object->getPage();
    if (!$page || !$page->id) return;

    if (!$this->user->isSuperuser() && !$this->user->hasPermission('pagegrid-draft')) return;

    if ($action === 'create') $this->createDraft($page->id);
    elseif ($action === 'publish') $this->publishDraft($page->id);
    elseif ($action === 'discard') $this->discardDraft($page->id);

    $this->wire('session')->redirect($page->editUrl() . ($action === 'create' ? '&showDraft=1' : ''));
  }

  /**
   * Adds draft tabs (Live/Draft/Create Draft) to the PageEdit tab navigation.
   *
   * @param HookEvent $event Hook event from ProcessPageEdit::buildForm.
   */
  public function draftTabs($event) {
    if ($this->process != 'ProcessPageEdit') return;
    if (!$this->wire('input')->get('showDraft')) return;

    if (!$this->user->isSuperuser() && !$this->user->hasPermission('pagegrid-draft')) {
      $page = $event->object->getPage();
      if ($page && $page->id) {
        $this->wire('session')->redirect($page->editUrl());
      }
    }

    $page = $event->object->getPage();
    if ($page && $page->id) {
      $draft = $this->pages->get("name=pg-draft-{$page->id}-1, template=pg_container");
      if (!$draft->id) {
        $this->wire('session')->redirect($page->editUrl());
      }
    }

    // TODO: When ProcessWire's PagesVersions module (wire/modules/Pages/PagesVersions/)
    // reaches the master release, consider using savePageVersion(1) / restorePageVersion(1)
    // / deletePageVersion(1) instead of hiding non-PageGrid fields. This would provide
    // a full-page draft experience without manual field filtering.

    $form = $event->return;
    $contentTab = $form->children->get('id=ProcessPageEditContent');

    // Move all PageGrid fields into the Content tab
    foreach ($form->children as $tab) {
      if (!$tab instanceof InputfieldWrapper) continue;
      foreach ($tab->children as $field) {
        $f = $field->hasField ? $field->hasField : null;
        if ($f && $f->type instanceof FieldtypePageGrid) {
          $tab->remove($field);
          $contentTab->append($field);
        }
      }
    }

    // Remove all tabs except Content
    $removeTabs = array();
    foreach ($form->children as $tab) {
      if ($tab->attr('id') !== 'ProcessPageEditContent' && $tab instanceof InputfieldWrapper) {
        $removeTabs[] = $tab;
      }
    }
    foreach ($removeTabs as $tab) {
      $id = $tab->attr('id');
      $form->remove($tab);
      if ($id) $event->object->removeTab($id);
    }

    $form->appendMarkup(
      '<style>'
      . '#ProcessPageEditContent .Inputfield:not(.InputfieldPageGrid):not(.InputfieldWrapper){display:none!important;}'
      . '#pw-content-head-buttons{display:none!important;}'
      . '#wrap_submit_save{display:none!important;}'
      . '#wrap_submit_save_unpublished{display:none!important;}'
      . '#_ProcessPageEditView{display:none!important;}'
      . '#ProcessPageEditTabs{display:none!important;}'
      . '#PageEditTabs{display:none!important;}'
      . 'body.AdminThemeUikit.hideTabs .pg-settings-dropdown #ProcessPageEditTabs,'
      . 'body.AdminThemeUikit.hideTabs .pg-settings-dropdown #PageEditTabs{display:none!important;}'
      . '#pg-tabs-nav-wrapper .pg-settings-divider{display:none!important;}'
      . '#pg-tabs-nav-wrapper:not(:has(.pg-settings-nav li)){display:none!important;}'
      . '</style>'
    );
  }

  /**
   * Moves draft tabs after the Content tab in the PageEdit tab navigation.
   *
   * @param HookEvent $event Hook event from ProcessPageEdit::getTabs.
   */
  public function draftTabsOrder($event) {
    $tabs = $event->return;
    $ordered = [];
    $draftKeys = ['PgDraftDraft'];
    foreach ($tabs as $id => $label) {
        $ordered[$id] = $label;
        if ($id === 'ProcessPageEditContent') {
            foreach ($draftKeys as $dk) {
                if (isset($tabs[$dk])) $ordered[$dk] = $tabs[$dk];
            }
        }
    }
    $event->return = $ordered;
  }

  /**
   * Sets the template file for the pg_blueprint template to the module's bundled file.
   *
   * @return void
   */
  public function setBlueprintTemplate() {
    $t = $this->templates->get('pg_blueprint');
    if (!$t) return;
    if (!$t->id) return;
    $file = $this->config->paths->siteModules . 'FieldtypePageGrid/pg_blueprint.php';
    $t->filename = $file;
    $t->compile = 0; // prevent bug where file compiler caches emtpy file
    // load pages into var to force init page
    $bpPages = $this->pages->find('template=pg_blueprint, include=all');
  }

  /**
   * Adds a PAGEGRID Blueprint select field to the template edit form in the admin.
   *
   * @param HookEvent $event Hook event from ProcessTemplate::buildEditForm.
   * @return void
   */
  public function addCustomTemplateSetting($event) {
    if ($this->page->template->name != 'admin') return;
    $form = $event->return;
    $template = $this->templates->get($event->arguments('template'));

    // Create a custom Inputfield
    $bpParent = $this->pages->get('name=pg-blueprints, template=pg_container');
    $field = $this->modules->get('InputfieldSelect');
    $field->icon = 'th';
    $field->attr('id+name', 'blueprint');
    $field->label = $this->_("PAGEGRID Blueprint");
    $field->description = 'Select a Blueprint, to connect this template to a PAGEGRID layout. To use this feature follow this [guide](https://page-grid.com/docs/developer/templates/#option-c).';
    $field->showRootPage = false;
    $field->collapsed = 2;
    foreach ($bpParent->children() as $bp) {
      $field->addOption($bp->id, $bp->title);
    }
    $field->attr('value', $template->blueprint);

    // Add the field to the form
    $form->insertAfter($field, 'fieldgroup_fields');
    // $form->add($field);
  }

  /**
   * Saves the selected blueprint ID to the template's blueprint property on template save.
   *
   * @param HookEvent $event Hook event from ProcessTemplate::executeSave.
   * @return void
   */
  public function saveCustomTemplateSetting($event) {
    $template = $this->templates->get($this->input->post->id);
    $blueprintValue = $this->input->post->blueprint ? $this->input->post->blueprint : null;
    $template->blueprint = $blueprintValue;
    // bd($blueprintValue);
  }

  /**
   * Disables the inline front-end editor when the inlineEditorFrontDisable module setting is active.
   *
   * @param HookEvent $event Hook event from PageFrontEdit::getPage.
   * @return void
   */
  //disable inline editor if settings checkbox inlineEditorFrontDisable is checked
  public function disableInlineEdit($event) {
    $isBackend = isset($_GET['backend']);
    if (!$isBackend && $this->inlineEditorFrontDisable && !$this->config->ajax) {
      //select admin page to make frontend editor return false 
      $p = $this->pages->get($this->config->urls->admin);
      if ($p && $p->id) {
        $p->edit(false);
        $event->return = $p;
        $event->replace = true;
      }
    }
  }

  /**
   * Checks if a template is a PageGrid block template.
   * A block template has a corresponding PHP file in site/templates/blocks/
   * or site/modules/PageGridBlocks/blocks/.
   *
   * @param Template|string $template Template object or template name.
   * @return bool
   */
  public function isBlockTemplate($template) {
    $name = is_object($template) ? $template->name : $template;
    if (file_exists($this->config->paths->templates . 'blocks/' . $name . '.php')) return true;
    if (file_exists($this->config->paths->siteModules . 'PageGridBlocks/blocks/' . $name . '.php')) return true;
    return false;
  }

  /**
   * Checks if a template's block file contains pg-children, indicating it supports child items.
   *
   * @param Template|string $template Template object or template name.
   * @return bool
   */
  public function templateHasChildren($template) {
    $name = is_object($template) ? $template->name : $template;
    $paths = array(
      $this->config->paths->templates . 'blocks/' . $name . '.php',
      $this->config->paths->siteModules . 'PageGridBlocks/blocks/' . $name . '.php',
    );
    foreach ($paths as $path) {
      if (file_exists($path) && strpos(file_get_contents($path), 'pg-children') !== false) return true;
    }
    return false;
  }

  /**
   * Checks whether the current user has a PageGrid permission for a given page,
   * using the page's parent meta (pg_permissions) as an override layer.
   * Falls back to the global permission if no parent-level grant is found.
   *
   * @param string $permission Permission name.
   * @param Page   $page       The item page (permission is checked against its parent).
   * @return bool
   */
  public function hasPgPermissions($permission, $page) {
    $user = $this->user;
    if ($user->isSuperuser()) return true;

    $meta = json_decode((string) $page->meta('pg_permissions'), true);
    if (!empty($meta['_manage'])) {
      if ($permission === 'pagegrid-drag' || $permission === 'pagegrid-resize') return false;
      if (!empty($meta[$permission])) {
        foreach ($user->roles as $role) {
          if (in_array($role->id, $meta[$permission])) return true;
        }
      }
      return false;
    }

    $parent = $page->parent();
    while ($parent->id && $parent->template->name === 'pg_container') {
      $parent = $parent->parent();
    }
    if ($parent->id) {
      $parentMeta = json_decode((string) $parent->meta('pg_permissions'), true);
      if (!empty($parentMeta['_manage'])) {
        if (!empty($parentMeta[$permission])) {
          foreach ($user->roles as $role) {
            if (in_array($role->id, $parentMeta[$permission])) return true;
          }
        }
        return false;
      }
    }

    if ($user->hasPermission($permission, $page)) return true;

    return false;
  }

  /**
   * Check if any of the user's roles has page-create access
   * on the given template via Template::hasRole().
   *
   * Uses the Template API directly to avoid hasTemplatePermission()
   * edge cases on older ProcessWire versions.
   *
   * @param User     $user
   * @param Template $template
   * @return bool
   */
  public function pgTemplateCreateAccess($user, $template) {
    if ($user->isSuperuser()) return true;
    if (!$template || !$template->id) return false;
    foreach ($user->roles as $role) {
      if (in_array($role->id, $template->createRoles)) return true;
    }
    return false;
  }

  /**
   * Saves per-group PageGrid permission grants from the page edit form
   * into the page's meta data.
   *
   * Hooks ProcessPageEdit::processInput.
   *
   * @param HookEvent $event Hook event from ProcessPageEdit::processInput.
   * @return void
   */
  public function savePgPermissions($event) {
    if (($event->arguments(1)) > 0) return;
    $page = $event->object->getPage();
    if (!$page || !$page->id) return;
    if (!$this->templateHasChildren($page->template)) return;

    if (!$this->input->post('pg_perm_manage_access')) {
      $page->meta('pg_permissions', '');
      return;
    }

    $input = $this->input;
    $permissions = array('page-add', 'pagegrid-drag', 'pagegrid-resize', 'page-sort', 'page-move');
    $data = array();
    foreach ($permissions as $perm) {
      $val = $input->post("pg_perm_$perm");
      if (is_array($val)) {
        $data[$perm] = array_map('intval', $val);
      }
    }

    // Save child template restrictions
    $childrenVal = $input->post('pg_perm_child_templates');
    if ($childrenVal !== null) {
      if (is_array($childrenVal)) {
        $templateIds = array_map('intval', $childrenVal);
        $templateNames = array();
        foreach ($templateIds as $id) {
          $t = $this->templates->get($id);
          if ($t && $t->id) $templateNames[] = $t->name;
        }
        $data['children'] = $templateNames;
      }
    }

    // Save symbol restrictions
    $symbolsVal = $input->post('pg_perm_symbols');
    if ($symbolsVal !== null) {
      if (is_array($symbolsVal)) {
        $data['symbols'] = array_map('intval', $symbolsVal);
      }
    }

    $data['_manage'] = true;
    $page->meta('pg_permissions', json_encode($data));
  }

  /**
   * Intercepts user permission checks and grants PageGrid permissions based on
   * per-group pg_permissions meta, for both the given page and its parent.
   *
   * Hooks User::hasPagePermission so all code paths (ProcessPageSort,
   * ProcessPageEdit, ProcessPageGrid, etc.) automatically respect per-group grants.
   *
   * @param HookEvent $event Hook event from User::hasPagePermission.
   * @return void
   */
  public function hookHasPagePermission($event) {
    $permission = $event->arguments(0);
    $page = $event->arguments(1);

    $pgPermissions = array('page-add', 'pagegrid-drag', 'pagegrid-resize', 'page-sort', 'page-move');
    if (!in_array($permission, $pgPermissions)) return;
    if (!$page instanceof Page || !$page->id) return;

    $user = $event->object;
    if ($user->isSuperuser()) return;

    $meta = json_decode((string) $page->meta('pg_permissions'), true);
    if (!empty($meta['_manage'])) {
      if ($permission === 'pagegrid-drag' || $permission === 'pagegrid-resize') { $event->return = false; return; }
      if (!empty($meta[$permission])) {
        foreach ($user->roles as $role) {
          if (in_array($role->id, $meta[$permission])) {
            $event->return = true;
            return;
          }
        }
      }
      $event->return = false;
      return;
    }

    $parent = $page->parent();
    while ($parent->id && $parent->template->name === 'pg_container') {
      $parent = $parent->parent();
    }
    if ($parent->id) {
      $parentMeta = json_decode((string) $parent->meta('pg_permissions'), true);
      if (!empty($parentMeta['_manage'])) {
        if (!empty($parentMeta[$permission])) {
          foreach ($user->roles as $role) {
            if (in_array($role->id, $parentMeta[$permission])) {
              $event->return = true;
              return;
            }
          }
        }
        $event->return = false;
        return;
      }
      if (!$event->return && !empty($parentMeta[$permission])) {
        foreach ($user->roles as $role) {
          if (in_array($role->id, $parentMeta[$permission])) {
            $event->return = true;
            return;
          }
        }
      }
    }

    if (!$event->return && !empty($meta[$permission])) {
      foreach ($user->roles as $role) {
        if (in_array($role->id, $meta[$permission])) {
          $event->return = true;
          return;
        }
      }
    }
  }

  /**
   * Allows sorting of child pages when the user has page-sort granted via
   * per-group permissions (pg_permissions meta). Hooks Page::sortable.
   *
   * @param HookEvent $event Hook event from Page::sortable.
   * @return void
   */
  public function hookPageSortable($event) {
    if ($event->return) return;
    $page = $event->object;
    if ($this->hasPgPermissions('page-sort', $page)) {
      $event->return = true;
    }
  }

  /**
   * Injects the sort settings fieldset into the children tab when the user has
   * page-sort granted via per-group permissions, but ProcessWire's built-in
   * check would skip it.
   *
   * @param HookEvent $event Hook event from ProcessPageEdit::buildFormChildren.
   * @return void
   */
  public function hookBuildFormChildren($event) {
    $page = $event->object->getPage();
    if (!$page || !$page->id) return;
    if ($this->user->hasPermission('page-sort', $page)) return;
    if (!$this->hasPgPermissions('page-sort', $page)) return;

    $wrapper = $event->return;
    $fieldset = ProcessPageEdit::buildFormSortfield($page->template->sortfield, $event->object);
    if ($fieldset) {
      $fieldset->set('class', 'ProcessPageEditSortSettings');
      $wrapper->append($fieldset);
    }
  }

  /**
   * Filters template select in the Settings tab for PageGrid items in modal mode:
   * removes pg_container/pg_blueprint, and for non-superusers limits to block templates only.
   *
   * Hooks ProcessPageEdit::buildFormSettings so filtering runs both on initial render
   * and when the field lazily loads options via AJAX.
   *
   * @param HookEvent $event Hook event from ProcessPageEdit::buildFormSettings.
   * @return void
   */
  public function filterTemplateSelect($event) {
    if ($this->process != 'ProcessPageEdit') return;
    $page = $event->object->getPage();
    if (!$page || !$page->id) return;

    $isPageGrid = false;
    if (isset($_GET['modal']) && count($page->parents('template=pg_container'))) $isPageGrid = true;
    if (isset($_GET['pgmodal'])) $isPageGrid = true;
    if (!$isPageGrid) return;

    $wrapper = $event->return;

    //add child permission config for blocks that support children
    $options = $this->session->get('pg_template_' . $page->template->name) ? json_decode($this->session->get('pg_template_' . $page->template->name), true) : [];
    if (($this->user->isSuperuser() || $this->user->hasPermission('pagegrid-settings-tab')) && (isset($options['children']) || $this->templateHasChildren($page->template))) {
      $currentPerms = json_decode((string) $page->meta('pg_permissions'), true) ?: array();

      $permFieldset = $this->modules->get('InputfieldFieldset');
      $permFieldset->name = 'pg_permissions';
      $permFieldset->label = __('Permissions');
      $permFieldset->icon = 'key';
      $permFieldset->description = __('When checked, you can define which roles have access to this item. When unchecked, template and global permissions apply.');

      $manageAccess = $this->modules->get('InputfieldCheckbox');
      $manageAccess->name = 'pg_perm_manage_access';
      $manageAccess->checkboxLabel = __('Manage access');
      $manageAccess->attr('value', '1');
      if (!empty($currentPerms)) $manageAccess->attr('checked', '1');
      $manageAccess->collapsed = Inputfield::collapsedNever;
      $manageAccess->addClass('InputfieldNoBorder', 'wrapClass');
      $manageAccess->skipLabel = Inputfield::skipLabelHeader;
      $permFieldset->add($manageAccess);

      $permissions = array(
        'page-add'        => 'Allow adding new child items to this block',
        'pagegrid-drag'   => 'Allow dragging items within this block in the editor',
        'pagegrid-resize' => 'Allow resizing items within this block in the editor',
        'page-sort'       => 'Allow reordering children of this block',
        'page-move'       => 'Allow moving child items to a different parent block',
      );
      $roles = $this->roles->find("name!=guest, name!=superuser");

      foreach ($permissions as $perm => $desc) {
        $f = $this->modules->get('InputfieldCheckboxes');
        $f->name = "pg_perm_$perm";
        $f->label = $perm;
        $f->description = $desc;
        foreach ($roles as $role) {
          $f->addOption($role->id, $role->name);
        }
        if (!empty($currentPerms[$perm])) {
          $f->value = $currentPerms[$perm];
        }
        $f->optionColumns = 1;
        $f->showIf = 'pg_perm_manage_access=1';
        $f->addClass('pg-perm-item', 'wrapClass');
        $permFieldset->add($f);
      }

      // Allowed child templates
      $childTemplateField = $this->modules->get('InputfieldCheckboxes');
      $childTemplateField->name = 'pg_perm_child_templates';
      $childTemplateField->label = __('Allowed Child Templates');
      $childTemplateField->description = __('Block templates that can be added as children.');
      $currentChildren = isset($currentPerms['children']) ? $currentPerms['children'] : [];
      foreach ($this->templates as $t) {
        if ($this->isBlockTemplate($t)) {
          $childTemplateField->addOption($t->id, $t->name);
        }
      }
      if (!empty($currentChildren)) {
        $selectedIds = [];
        foreach ($this->templates as $t) {
          if ($this->isBlockTemplate($t) && in_array($t->name, $currentChildren)) {
            $selectedIds[] = $t->id;
          }
        }
        $childTemplateField->value = $selectedIds;
      }
      $childTemplateField->optionColumns = 1;
      $childTemplateField->showIf = 'pg_perm_manage_access=1';
      $childTemplateField->addClass('pg-perm-item', 'wrapClass');
      $permFieldset->add($childTemplateField);

      // Allowed symbols
      $symbolParent = $this->pages->get("name=pg-symbols, template=pg_container");
      if ($symbolParent->id) {
        $symbolField = $this->modules->get('InputfieldCheckboxes');
        $symbolField->name = 'pg_perm_symbols';
        $symbolField->label = __('Allowed Symbols');
        $symbolField->description = __('Symbols that can be added as children.');
        $currentSymbols = isset($currentPerms['symbols']) ? $currentPerms['symbols'] : [];
        foreach ($symbolParent->children('sort=title') as $symbol) {
          $symbolField->addOption($symbol->id, $symbol->title);
        }
        if (!empty($currentSymbols)) {
          $symbolField->value = $currentSymbols;
        }
        $symbolField->optionColumns = 1;
        $symbolField->showIf = 'pg_perm_manage_access=1';
        $symbolField->addClass('pg-perm-item', 'wrapClass');
        $permFieldset->add($symbolField);
      }

      $statusField = $wrapper->get('status');
      if ($statusField) {
        $wrapper->insertAfter($permFieldset, $statusField);
      } else {
        $wrapper->add($permFieldset);
      }

      $wrapper->appendMarkup('<style>#wrap_Inputfield_pg_perm_child_templates li,#wrap_Inputfield_pg_perm_symbols li{float:none!important;width:100%!important}</style>');
    }

    if (!$wrapper instanceof InputfieldWrapper) return;

    $field = $wrapper->get('template');
    if (!$field instanceof InputfieldSelect) return;

    foreach ($field->options as $key => $value) {
      $t = $this->templates->get($key);
      if (!$t || !$t->id) continue;

      if ($t->name == 'pg_container' || $t->name == 'pg_blueprint') {
        if ($page->template->name != 'pg_container' && $page->template->name != 'pg_blueprint') {
          $field->removeOption($key);
        }
        continue;
      }

      if (!$this->user->isSuperuser() && $t->id != $page->template->id && !$this->isBlockTemplate($t)) {
        $field->removeOption($key);
      }
    }
  }

  /**
   * Modifies the Add Page form: filters available templates to block templates for PageGrid
   * contexts, sets up auto-title/name for quick-add items, and configures the blueprint picker.
   *
   * @param HookEvent $event Hook event from ProcessPageAdd::buildForm.
   * @return void
   */
  public function pageAddForm($event) {

    // Retrieve the form
    $form = $event->return;

    // Retrieve GET input "parent_id"
    $parentIdInput = $this->wire('input')->get['parent_id'];
    // Sanitize GET input
    $parentId = $this->wire('sanitizer')->int($parentIdInput);
    // Get page
    $parentPage = $this->wire('pages')->get($parentId);

    if (!$parentPage || !$parentPage->id) return;
    if ($parentPage->template->name === 'pg_container' && $parentPage->name !== 'pg-blueprints') return;

    //check if inside pg modal
    $isPageGridModal = false;
    if (isset($_GET['pgmodal'])) $isPageGridModal = true;

    //get children settings for parent from template file
    $options = $parentPage->template->pgOptions ? json_decode($parentPage->template->pgOptions, true) : [];
    $optionAutoTitle = 1;
    if (isset($options['autoTitle'])) $optionAutoTitle = $options['autoTitle'];
    if ($optionAutoTitle == 'false') $optionAutoTitle = 0;

    //check if pagegrid item page
    $isPageGrid = false;
    if (count($parentPage->parents('template=pg_container'))) {
      $isPageGrid = true;
    }

    if ($isPageGridModal && $isPageGrid) {
      $form->prependFile = $this->config->styles->add($this->config->urls->InputfieldPageGrid . "css/main.css?v=333");
      $form->addClass('pg-hide-language');
      $form->addClass('pg-settings-body');
    }

    //hide title if auto publish
    if ($isPageGrid && $isPageGridModal && $optionAutoTitle && isset($options['children'])) {
      $form->appendMarkup('<style>#wrap_Inputfield_submit_save, #wrap_submit_publish, #wrap_Inputfield_title, #wrap_Inputfield__pw_page_name {display:none!important;}</style>');
    }

    foreach ($form as $inputfield) {

      //set auto publish title (catched later with ahook and converted to real title)
      if ($inputfield->name == 'title' && $optionAutoTitle && $isPageGrid && $isPageGridModal) {
        $inputfield->value = 'pg-autotitle';
      }

      //set auto publish name (catched later with ahook and converted to real name)
      if ($inputfield->name == '_pw_page_name' && $optionAutoTitle && $isPageGrid && $isPageGridModal) {
        $inputfield->value = 'pg-autotitle-' . rand(1000, 9999);
      }

      //set allowed templates
      if ($inputfield->name == 'template') {
        $templates = $inputfield['options'];
        if (!$templates) continue;

        foreach ($templates as $key => $value) {
          $t = $this->templates->get($key);
          $isBlock = $this->isBlockTemplate($t);

          //allways filter out pg_container
          if ($t->name == 'pg_container') unset($templates[$key]);

          //for normal pages hide block templates
          if (!$isPageGrid && $isBlock) unset($templates[$key]);

          //remove templates with tagname PageGrid (auto set via module)
          if ($t && $t->id && $t->tags) {
            if (!$isPageGrid && (strpos($t->tags, 'PageGrid') !== false)) unset($templates[$key]);
            if (!$isPageGrid && (strpos($t->tags, 'pagegrid') !== false)) unset($templates[$key]);
          }

          //pagegrid page
          //only show block templates
          if ($isPageGrid && !$isBlock) unset($templates[$key]);
          //check if allowed children option is set
          if ($isPageGrid && $isBlock && isset($options['children']) && is_array($options['children']) && !in_array($t->name, $options['children'])) unset($templates[$key]);
        }

        //for blueprint set blueprint template, and hide inputfield
        if ($parentPage->name === 'pg-blueprints' && $parentPage->template->name === 'pg_container') {
          $t = $this->templates->get('pg_blueprint');
          $templates = [];
          $templates[$t->id] = $t->name;
          $inputfield->value = $t->id;
          $inputfield->addClass('uk-hidden', 'wrapClass');

          //check if blueprintAdd is set
          $addBlueprint = $this->wire('input')->get['addBlueprint'];
          $addBlueprint = $this->wire('sanitizer')->int($addBlueprint);
          $addBlueprintPage = $this->wire('pages')->get($addBlueprint);

          $f = $this->wire()->modules->get('InputfieldPageListSelect');
          $f->label = $this->_("Create Blueprint from Page");
          $f->description = $this->_("Choose a page if you don't want to start with an empty page.");
          if ($addBlueprintPage && $addBlueprintPage->id) $f->description .= $this->_(" The last page you edited was selected automatically.");
          $f->attr('name', 'blueprintPageId');
          $f->columnWidth('100');
          $f->parent_id = 1;
          $f->collapsed = 2;
          if ($addBlueprintPage && $addBlueprintPage->id) $f->attr('value', $addBlueprint);
          $f->lazy = 0;
          $f->appendMarkup = '<style>#wrap_Inputfield_blueprintPageId .PageListItem {display:none;} .PageListTemplate_admin {display:none!important;}</style>';

          //bug:field not respecting template, hide via css
          foreach ($this->fields as $pgf) {
            if ($pgf->type instanceof FieldtypePageGrid) {
              $template_has_pg = $this->fields->get($pgf->name)->getFieldgroups()->implode('|', 'name');
              $pgPages = $this->pages->find("template=$template_has_pg, has_parent!=2, include=all");

              foreach ($pgPages as $item) {
                $f->appendMarkup .= "<style>#wrap_Inputfield_blueprintPageId .PageListID$item->id {display:block;}</style>";
              }
            }
          }

          $form->prepend($f);

          //set page headline
          $form->appendMarkup('<style>#pw-content-head h1::after { content: " Blueprint";}</style>');
          //set page headline
          $form->appendMarkup('<style>#wrap_Inputfield__pw_page_name { display:none!important;}</style>');
        }

        $inputfield['options'] = $templates;
      }
    }

    // Populate back argument
    $event->return = $form;
  }

  /**
   * Processes <pg-edit> custom tags in rendered HTML: injects file-uploader markup in the
   * backend and strips the custom tags cleanly for frontend output.
   *
   * @param string $out     The rendered HTML output to process.
   * @param Page   $pRender The page being rendered.
   * @return string Processed HTML with <pg-edit> tags handled or removed.
   */
  public function enableInlineEditFile($out, $pRender) {

    $editRegionTag = 'pg-edit';
    $hasEditTags = strpos($out, "<$editRegionTag") !== false; // i.e. <edit title>
    $backend = $this->modules->get('InputfieldPageGrid')->isBackend();

    // return if no tag found
    if (!$hasEditTags) return $out;

    //use DOMDocument to interact with dom nodes
    //wrap in html to fix parser, remove html wrapper later
    $doc = new \DOMDocument();
    @$doc->loadHTML('<?xml encoding="utf-8" ?><html>' . $out . '</html>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    $tags = $doc->getElementsByTagName('pg-edit');
    $oldUploader = $doc->getElementsByTagName('pg-uploader');

    if (count($tags) === 0) return $out;
    if (count($oldUploader) !== 0) return $out;

    if ($backend && $this->user->hasPermission('page-edit-front')) {
      // For Backend add uploader
      foreach ($tags as $tag) {
        $pId = $tag->getAttribute('page');
        $f = $tag->getAttribute('field');
        // $pRenderId = $tag->getAttribute('render');

        $p = wire('pages')->get($pId);

        //get uploader markup
        if ($p && $f && $p->id && $p->hasField($f)) {
          $uploaderString = $this->modules->get('InputfieldPageGrid')->renderFileUploader($p, $f, $pRender);
          libxml_use_internal_errors(true);
          $uploader = new \DOMDocument;
          $uploader->loadHTML('<html>' . $uploaderString . '</html>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
          $tag->appendChild($doc->importNode($uploader->documentElement, TRUE));
          libxml_use_internal_errors(false);
        }
      }
    } else {
      // For frontend remove tagname
      $domElemsToRemove = array();

      foreach ($tags as $tag) {
        //move edit tag content to parent
        foreach ($tag->childNodes as $child) {
          $clone = $child->cloneNode(true);
          $tag->parentNode->insertBefore($clone, $tag);
        }
        $domElemsToRemove[] = $tag;
      }
      // remove <pg-edit> tags
      foreach ($domElemsToRemove as $domElement) {
        $domElement->parentNode->removeChild($domElement);
      }
    }

    //remove html wrapper before return
    $returnValue = $doc->saveHTML();
    $returnValue = str_replace("</html>", "", $returnValue);
    $returnValue = str_replace("<html>", "", $returnValue);
    $returnValue = str_replace('<?xml encoding="utf-8" ?>', '', $returnValue);

    return $returnValue;
  }

  /**
   * Formats the field value for $page->fieldname calls by rendering the PageGrid.
   * Hookable.
   *
   * @param Page  $page  The page containing the field.
   * @param Field $field The PageGrid field being formatted.
   * @param mixed $value The raw field value.
   * @return string Rendered HTML output of the grid.
   */
  //function gets called for $page->fieldname calls render function as alternative
  public function ___formatValue(Page $page, Field $field, $value) {
    return $this->modules->get('InputfieldPageGrid')->renderGrid($page, $field);
  }

  /**
   * Adds template, role, user, permission, and breakpoint classes to the admin body element.
   *
   * @param HookEvent $event Hook event from AdminTheme::getExtraMarkup.
   * @return void
   */
  // add interface classes to body
  public function addBodyClasses($event) {

    if ($this->process != 'ProcessPageEdit') return;

    $p = $this->pages->get((int) wire('input')->get('id'));
    if (!$p || !$p->id) return;

    $theme = $event->object;
    $user = $this->user;
    $isPg = $p->fields('type=FieldtypePageGrid') ? 1 : 0;

    if (!$isPg) return; // only add classes if page has pg field

    if ($this->modules->get('FieldtypePageGrid')->stylePanel) $theme->maxWidth = ''; // allow custom max width to make space for style panel

    $theme->addBodyClass("template-{$p->template}");

    foreach ($user->roles as $role) {
      $theme->addBodyClass("role-{$role->name}");
    }
    if (!$user->isSuperuser()) $theme->addBodyClass("role-nonsuperuser");
    $theme->addBodyClass("user-{$user->name}");
    if ($user->hasPermission('pagegrid-add')) {
      $theme->addBodyClass("permission-pagegrid-add");
    }

    if (!$user->isSuperuser() && $user->hasPermission('page-pagegrid-play')) $theme->addBodyClass("permission-page-pagegrid-play");

    if ($this->config->debug) {
      $theme->addBodyClass("pw-debug-on");
    }

    //interface classes
    foreach ($this->interface as $interfaceClass) {
      $theme->addBodyClass($interfaceClass);
    }

    //add active breakpoint classes
    $theme->addBodyClass('breakpoint-base');

    if ($p->id && $this->pages->get("name=pg-draft-{$p->id}-1, template=pg_container")->id) {
      $theme->addBodyClass('pg-has-draft');
      $theme->addBodyClass($this->wire('input')->get('showDraft') ? 'pg-draft-view' : 'pg-live-view');
    }
  }



  /**
   * Clones the PageGrid items container and renames all cloned items when a page is cloned.
   *
   * @param HookEvent $event Hook event from Pages::cloned, with original page and copy as arguments.
   * @return void
   */
  // clone items of page if page gets cloned
  public function clone($event) {

    $pages = $event->wire('pages');
    $page = $event->arguments(0);
    $copy = $event->arguments(1);

    //if no pg field return
    if (!$copy->fields->get('type=FieldtypePageGrid')) return;

    $itemsParent = $pages->get('pg-' . $page->id);
    if ($itemsParent->id) {
      $cloneItemsParent = $pages->clone($itemsParent);
      $cloneItemsParent->setAndSave('name', 'pg-' . $copy->id);
      $cloneItemsParent->setAndSave('title', $copy->title . ' items');

      // renaming of items to have uniue names
      foreach ($cloneItemsParent->find('') as $clone) {
        //skip field page containers
        if ($clone->template->name === 'pg_container') continue;
        $newName = $clone->template->name . '-' . $clone->id;
        $clone->setAndSave('name', $newName);
        $clone->setAndSave('title', $newName);
      }
    }
  }

  /**
   * Propagates a page title change to the associated PageGrid items container page.
   *
   * @param HookEvent $event Hook event from Page::changed(0:title).
   * @return void
   */
  //handle title change of main page to reflect on items parent
  public function titleChanged($event) {

    $pages = $event->wire('pages');
    $page = $event->object;
    $newValue = $event->arguments(2);
    $itemsParent = $pages->get('pg-' . $page->id);

    if ($itemsParent->id) {
      $itemsParent->setAndSave('title', $newValue);
    }
  }

  /**
   * Deletes the PageGrid items container and its children when the parent page is deleted.
   * Also removes the per-symbol permission if one exists for the deleted page.
   *
   * @param HookEvent $event Hook event from Pages::delete.
   * @return void
   */
  //delete pagegrid items when page gets deleted
  public function delete($event) {
    $pages = $event->wire('pages');
    $page = $event->arguments(0);
    if ($page->template->name === 'admin') return;
    $hasField = $page->fields->get('type=FieldtypePageGrid') ? 1 : 0;

    //delete symbol permission if it exists
    $permission = $this->permissions->get("pagegrid-symbol-add-$page->id");
    if ($permission && $permission->id) $this->permissions->delete($permission);

    //blueprint page has no field (added at runtime) so set var in this case
    if ($page->template->name === 'pg_blueprint') $hasField = 1;
    if (!$hasField) return;

    $itemsParent = $pages->get('pg-' . $page->id);
    if ($itemsParent->id) {
      foreach ($itemsParent->find('') as $item) {
        $item->removeStatus(Page::statusLocked);
        $item->save();
      }

      $itemsParent->delete(true); // true allow to delete children too
      $this->message("PageGrid items for " . $page->id . " removed");
    }
  }

  /**
   * Removes the pg-dummies container page from ProcessPageList results to keep it hidden.
   *
   * @param HookEvent $event Hook event from ProcessPageList::find.
   * @return void
   */
  //hide dummy pages for init inline editor
  function hideDummies($event) {
    $event->return->each(function ($p) use ($event) {
      if ($p->template != 'pg_container') return;
      if ($p->name != 'pg-dummies') return;
      $event->return->remove($p);
    });
  }

  /**
   * Creates a blueprint items container by cloning the source page's items into the blueprint.
   *
   * @param int $pageId      ID of the source page whose items will be cloned.
   * @param int $blueprintId ID of the target blueprint page.
   * @return void
   */
  public function createBlueprint($pageId, $blueprintId) {

    if (!$pageId || !$blueprintId) return;

    //clone items
    $pItems = $this->pages->get("name=pg-$pageId, template=pg_container");
    $blueprint = $this->pages->get($blueprintId);
    $blueprintItems = $this->pages->get("name=pg-$blueprintId, template=pg_container");

    if ((!$blueprintItems || !$blueprintItems->id) && $pItems && $pItems->id && $blueprint && $blueprint->id) {
      $cloneItems = $this->pages->clone($pItems);
      $cloneItems->name = 'pg-' . $blueprintId;
      $cloneItems->title = $blueprint->name;
      $cloneItems->save();
    }
  }

  /**
   * Create a draft of the PageGrid items for a page by cloning its items container.
   *
   * @param int $pageId The main page ID.
   * @return bool True if draft was created successfully.
   */
  public function createDraft($pageId) {
    if (!$pageId) return false;
    $pItems = $this->pages->get("name=pg-$pageId, template=pg_container");
    if (!$pItems || !$pItems->id) return false;

    $pgDrafts = $this->pages->get('name=pg-drafts, template=pg_container');
    if (!$pgDrafts->id) return false;

    $draftsParent = $this->pages->get("name=pg-drafts-$pageId, parent={$pgDrafts->id}, template=pg_container");
    if (!$draftsParent->id) {
      $draftsParent = new Page();
      $draftsParent->template = 'pg_container';
      $draftsParent->parent = $pgDrafts;
      $draftsParent->name = 'pg-drafts-' . $pageId;
      $draftsParent->title = 'pg-drafts-' . $pageId;
      $draftsParent->addStatus(Page::statusHidden);
      $draftsParent->save();
    }

    $draft = $this->pages->get("name=pg-draft-{$pageId}-1, parent={$draftsParent->id}, template=pg_container");
    if ($draft->id) $draft->delete(true);

    $clone = $this->pages->clone($pItems);
    $clone->parent = $draftsParent;
    $clone->name = 'pg-draft-' . $pageId . '-1';
    $clone->title = $this->pages->get($pageId)->title . ' draft';
    $clone->save();
    return true;
  }

  /**
   * Publish a draft by replacing the live items container with the draft container.
   *
   * @param int $pageId The main page ID.
   * @return bool True if draft was published successfully.
   */
  public function publishDraft($pageId) {
    if (!$pageId) return false;
    $draft = $this->pages->get("name=pg-draft-{$pageId}-1, template=pg_container");
    if (!$draft->id) return false;

    $live = $this->pages->get("name=pg-$pageId, template=pg_container");
    if ($live->id) {
        foreach ($live->find("status=" . Page::statusLocked . ", include=all") as $locked) {
            $locked->removeStatus(Page::statusLocked);
            $locked->save();
        }
        $live->delete(true);
    }

    $pgItems = $this->pages->get('name=pg-items, template=pg_container');
    $draft->parent = $pgItems;
    $draft->name = 'pg-' . $pageId;
    $draft->save();

    foreach ($draft->find('include=all') as $child) {
        if ($child->template->name === 'pg_container') continue;
        $newName = $child->template->name . '-' . $child->id;
        $child->setAndSave('name', $newName);
        $child->setAndSave('title', $newName);
    }

    $draftsParent = $this->pages->get("name=pg-drafts-$pageId, template=pg_container");
    if ($draftsParent->id) $draftsParent->delete(true);

    return true;
  }

  /**
   * Discard a draft by deleting the draft items container.
   *
   * @param int $pageId The main page ID.
   * @return bool True if draft was discarded.
   */
  public function discardDraft($pageId) {
    if (!$pageId) return false;
    $draft = $this->pages->get("name=pg-draft-{$pageId}-1, template=pg_container");
    if ($draft->id) {
        foreach ($draft->find("status=" . Page::statusLocked . ", include=all") as $locked) {
            $locked->removeStatus(Page::statusLocked);
            $locked->save();
        }
        $draft->delete(true);
        $draftsParent = $this->pages->get("name=pg-drafts-$pageId, template=pg_container");
        if ($draftsParent->id) $draftsParent->delete(true);
        return true;
    }
    return false;
  }

  /**
   * Clean up draft container when the main page is deleted.
   *
   * @param HookEvent $event Hook event from Pages::delete.
   */
  public function deleteDraft($event) {
    $page = $event->arguments(0);
    $draftsParent = $this->pages->get("name=pg-drafts-$page->id, template=pg_container");
    if ($draftsParent->id) {
        foreach ($draftsParent->find("status=" . Page::statusLocked . ", include=all") as $locked) {
            $locked->removeStatus(Page::statusLocked);
            $locked->save();
        }
        $draftsParent->delete(true);
    }
  }

  /**
   * Copies PageGrid items from a blueprint to a newly added page when a blueprint parent is configured.
   * Also handles auto-publish for blueprints and pages without a title.
   *
   * @param HookEvent $event Hook event from Pages::added.
   * @return void
   */
  // blueprint feature and auto publish
  public function copyFromBlueprint($event) {
    $page = $event->arguments(0);
    $pages = $event->wire('pages');
    $input = $this->input;

    if ($page->_cloning && $page->_cloning->id) return; //skip if page is cloned

    //create blueprint
    if ($page->template->name === 'pg_blueprint') {

      //auto puplish
      $page->removeStatus('unpublished');
      $page->save();
      //create blueprint from page if post var is set
      if ($input->requestMethod('POST') !== null) {
        if ($input->post('blueprintPageId') !== null && $input->post('blueprintPageId')) {
          $cloneId = $this->wire('sanitizer')->int($input->post('blueprintPageId'));
          $blueprintId = $page->id;
          $this->createBlueprint($cloneId, $blueprintId);
        }
      }
      return false;
    }
    //END create blueprint

    // auto puplish PAGEGRID items or pages without title
    if ($page->hasField('title') && !$page->title) {
      $page->title = $page->name;
      $page->removeStatus('unpublished');
      $page->save();
    }
    // END auto puplish PAGEGRID items or pages without title

    // COPY FROM TEMPLATE -------------------------------------------
    $blueprintParent = $this->pages->get("name=pg-blueprints, template=pg_container");
    $blueprintPage = false;
    $isPg = false;

    foreach ($blueprintParent->children() as $blueprint) {
      if ($blueprint->pg_blueprint_parent && $blueprint->pg_blueprint_parent->id === $page->parent()->id) {
        $blueprintPage = $blueprint;
        break;
      }
    }

    foreach ($page->fields as $f) {
      if ($f->type instanceof FieldtypePageGrid) {
        $isPg = true;
        break;
      }
    }

    //return if no blueprint or pagegrid pagefound
    if (!$blueprintPage || !$isPg) return false;

    $page1ID = $blueprintPage->id;
    $page1 = $blueprintPage;
    $page1Items = $pages->get('pg-' . $page1ID);
    $pageID = $page->id;
    $pageOldItems = $pages->get('pg-' . $pageID);

    //return if there is an old page already 
    if ($pageOldItems->id || !$page1Items->id) return false;

    // $page1 is page containing pageGrid you want to copy from, defined in field settings
    $cloneItemsParent = $pages->clone($page1Items);
    $cloneItemsParent->setAndSave('name', 'pg-' . $page->id);
    $cloneItemsParent->setAndSave('title', $page->title . ' items');

    foreach ($cloneItemsParent->find('') as $clone) {
      if ($clone->template->name === 'pg_container') continue;
      $newName = $clone->template->name . '-' . $clone->id;
      $clone->setAndSave('name', $newName);
      $clone->setAndSave('title', $newName);
    }
    $this->message('New page created based on blueprint: ' . $page1->name);


    // END COPY FROM TEMPLATE -------------------------------------------

  }

  /**
   * Auto-publishes a PageGrid item page whose title was set to 'pg-autotitle', assigning a
   * real title/name based on template and ID, then redirects to its edit URL.
   *
   * @param HookEvent $event Hook event from Pages::save.
   * @return void
   */
  // autopuplish pages with one template and 'pg-autotitle' set as childNameFormat (automatically set in fieldtype)
  public function autoPuplish($event) {
    // remove statusTemp (flash icon on page)
    $page = $event->arguments(0);
    $isPgPage = count($page->parents('template=pg_container'));

    if ($page->title == 'pg-autotitle' && $isPgPage) {
      $event->arguments(0)->status = 1;
      $templateName = str_replace('_', '-', $page->template->name);
      $page->of(false);
      $page->set('title', $templateName . '-' . $page->id);
      $page->set('name', $templateName . '-' . $page->id);
      $page->removeStatus('unpublished');
      $page->save();
      $this->session->redirect($page->editUrl() . '&modal=1&pgmodal=1'); //test
    }
  }

  /**
   * Modifies the ProcessPageEdit form when editing a PageGrid item in a modal: adds CSS,
   * manages the children tab, controls the back button, and hides irrelevant UI elements.
   *
   * @param HookEvent $event Hook event from ProcessPageEdit::buildForm.
   * @return void
   */
  // add function to load children inside modal when no other fields, function gets called from js when needed
  public function modalEdit($event) {

    // make sure we're editing a page and not a user
    if ($this->process != 'ProcessPageEdit') return;

    $page = $event->object->getPage();
    if (!$page || !$page->id) return;

    //check if pagegrid item page
    $isPageGrid = false;
    if (isset($_GET['modal']) && count($page->parents('template=pg_container'))) $isPageGrid = true;
    if (isset($_GET['pgmodal'])) $isPageGrid = true;
    if (!$isPageGrid) return;
    //END check if pagegrid item page

    $options = $this->session->get('pg_template_' . $page->template->name) ? json_decode($this->session->get('pg_template_' . $page->template->name), true) : [];
    $parentOptions = [];
    $form = $event->return;
    $contentTab = $form->children->get('id=ProcessPageEditContent');
    $childrenTab = $form->children->get('id=ProcessPageEditChildren');

    if (!$this->user->isSuperuser() && !$this->user->hasPermission('pagegrid-settings-tab')) {
      foreach ($form as $child) {
        if ($child->attr('id') === 'ProcessPageEditSettings') {
          $nameField = $child->get('_pw_page_name');
          if ($nameField) {
            $contentTab->append($nameField);
            $form->appendMarkup = '<style>#wrap_Inputfield__pw_page_name{display:none!important;}</style>';
          }
          $form->remove($child);
          $event->object->removeTab('ProcessPageEditSettings');
          break;
        }
      }
    }

    if ($page->parent() && $page->parent()->id) {
      $parentOptions = $this->session->get('pg_template_' . $page->parent()->template->name) ? json_decode($this->session->get('pg_template_' . $page->parent()->template->name), true) : [];
    }

    //add css to modal
    $form->prependFile = $this->config->styles->add($this->config->urls->InputfieldPageGrid . "css/main.css?v=333");
    $form->addClass('pg-settings-body');

    //show title if autoTitle is set to 'false' in parent template
    if ($parentOptions && isset($parentOptions['autoTitle']) && $parentOptions['autoTitle'] == 'false') {
      $form->addClass('pg-show-title');
    }

    //if pg item hide language
    if (count($page->parents('template=pg_container'))) {
      $form->addClass('pg-hide-language');
    }

    if ($page->template->name == 'pg_code') {
      $form->prependFile = $this->config->styles->add($this->config->urls->InputfieldPageGrid . "css/prism.css");
      $form->prependFile = $this->config->styles->add($this->config->urls->InputfieldPageGrid . "css/prism-vs.css");
      $form->prependFile = $this->config->scripts->add($this->config->urls->InputfieldPageGrid . "prism.js");
    }

    // render back button
    // check if referrer is parent page
    $refParts = parse_url($_SERVER['HTTP_REFERER']);
    parse_str($refParts['query'], $refPartsQuery);
    $refPageId = isset($refPartsQuery['id']) ? (int) $refPartsQuery['id'] : 0;

    if ($refPageId && $page->parent()->id == $refPageId) {
      $f = $this->modules->get('InputfieldMarkup');
      $f->id = 'pg-back-button-field';
      $f->value = "<div title='" . __('Back to the previous page') . "' id='pg-back-button' data-page='" . $page->parent->id . "' data-href='" . $_SERVER['HTTP_REFERER'] . "'>←</div>";
      $form->prepend($f);
    }

    // END render back button

    if ($childrenTab && $childrenTab->id) {
      $addBtn = $form->get('AddPageBtn');
      $childrenPageList = $form->get('ChildrenPageList');

      if ($addBtn) $addBtn->set('value', __('Add New'));

      if ($childrenPageList) {
        $childrenPageList->description = '';

        if ($page->name == 'pg-classes') {
          $childrenPageList->addClass('pg-class-list-wrap', 'wrapClass');

          $f = $this->modules->get('InputfieldMarkup');
          $f->id = 'stylelist-intro';
          $f->value = "<style>#stylelist-intro{margin-top:0;}</style><p class='description'>An overview of classes and tags you’ve created or edited on your site. Classes further down in the list overwrite the styles of classes above them.</p>";
          $form->prepend($f);
        }

        if ($page->name == 'pg-animations') {
          $childrenPageList->addClass('pg-animation-list-wrap', 'wrapClass');

          $f = $this->modules->get('InputfieldMarkup');
          $f->id = 'stylelist-intro';
          $f->value = "<style>#stylelist-intro{margin-top:0;}</style><p class='description'>An overview of all the animations you’ve created on your site.</p>";
          $form->prepend($f);
        }

        //remove/change children/page label
        $childrenPageList->label = $this->_("Children");
        if (!isset($options['childrenTab'])) $childrenPageList->label = '';
        if (isset($options['childrenLabel'])) $childrenPageList->label = $options['childrenLabel'];
      }

      //move children to content tab for classes and animations
      if ($page->name == 'pg-animations' || $page->name == 'pg-classes') {
        foreach ($childrenTab->children() as $f) {
          $contentTab->append($f);
        }
      }

      // move children to content tab if option is set
      if (isset($options['childrenTab']) && ($options['childrenTab'] == 'append' || $options['childrenTab'] == 'prepend')) {
        $lastContentField = $contentTab->children()->last();

        foreach ($childrenTab->children() as $f) {
          if ($options['childrenTab'] == 'append') $contentTab->append($f);
          if ($options['childrenTab'] == 'prepend' && $lastContentField->id) $contentTab->insertBefore($f, $lastContentField);
        }
      }
      if (!isset($options['children']) || isset($options['childrenTab'])) {
        $form->remove($childrenTab);
        $form->appendMarkup = "<style>li:has(#_ProcessPageEditChildren){display:none!important;}</style>";
      }
    }
  }

  /**
   * Re-initialises PageFrontEdit for a given page so that dynamically loaded AJAX items
   * are prepared for inline editing.
   *
   * @param Page $p The page to enable inline editing for.
   * @return void
   */
  //reinit PageFrontEdit for ajax items
  public function readyFrontEdit($p) {

    //hook PageFrontEdit to ready ajax items for inline edit
    $this->addHookBefore('PageFrontEdit::getPage', function (HookEvent $event) use ($p) {
      $event->return = $p;
      $event->replace = true;
    });

    $edit = $this->modules->get('PageFrontEdit');
    $p->edit(true);
    $edit->ready();
  }

  /**
   * Returns the InputfieldPageGrid inputfield used to edit this field's value.
   *
   * @param Page  $page  The page being edited.
   * @param Field $field The PageGrid field.
   * @return Inputfield The InputfieldPageGrid instance.
   */
  public function getInputfield(Page $page, Field $field) {
    /** @var InputfieldPageGrid $inputfield */
    $inputfield = $this->modules->get('InputfieldPageGrid');

    // foreach ($this->interface as $interfaceClass) {
    //   $inputfield->addClass($interfaceClass, 'wrapClass');
    // }

    return $inputfield;
  }

  /**
   * Returns the configuration inputfields for a PageGrid field in the admin field editor.
   * Hookable.
   *
   * @param Field $field The PageGrid field being configured.
   * @return InputfieldWrapper Inputfields for the field configuration form.
   */
  public function ___getConfigInputfields(Field $field) {

    $inputfields = parent::___getConfigInputfields($field);
    $f = $this->modules->get('FieldtypePageGridConfig')->getBlockSettings($field);
    $f->value = $field->template_id;
    $inputfields->add($f);
    // bd($field->template_id);
    $this->config->scripts->add($this->config->urls->InputfieldPageGrid . "js/FieldtypePageGridConfig.js'");

    return $inputfields;
  }

  /**
   * Creates dummy pages for each inline-editable field so the PageFrontEdit inline editor
   * initialises correctly before any real items exist.
   *
   * @return void
   */
  //create dummies to trick inline editor to work for first items
  protected function createDummies() {

    //return if PageFrontEdit settings not found
    if (!$this->modules->isInstalled('PageFrontEdit')) return;
    if (!isset($this->modules->getConfig('PageFrontEdit')['inlineEditFields'])) return;
    if (!$this->user->hasPermission('page-edit-front')) return;

    // create dummy container page to hold dummys for inline init
    $adminPage = wire('page')->rootParent;
    $p = $this->pages->get("name=pg-dummies, template=pg_container");

    if (!$p->id) {
      $p = new Page(); // create new page object
      $p->template = 'pg_container'; // set template
      $p->parent = $adminPage; // set the parent
      $p->name = 'pg-dummies'; // give it a name used in the url for the page
      $p->title = 'PageGrid Dummies'; // set page title (not neccessary but recommended)
      $p->addStatus(Page::statusHidden);
      $p->addStatus(Page::statusUnpublished);
      $p->save();
    }
    // END create dummy container page to hold dummys for inline init

    // create dummies
    $PageFrontEditData = $this->modules->getConfig('PageFrontEdit');
    $PageFrontEditFields = $PageFrontEditData['inlineEditFields'];
    $templates = $this->fields->get('type=FieldtypePageGrid')->template_id;
    if ($templates !== null && count($templates)) {
      foreach ($templates as $tId) {
        $t = $this->templates->get($tId);
        if (isset($t) == 0) continue;
        foreach ($t->fields as $f) {
          if (in_array($f->id, $PageFrontEditFields)) {
            $dummy = $this->pages->get("$f->id!=''");
            if (!$dummy->id && !$this->pages->get('pg-dummy-' . $f->id)->id) {
              $dummy = new Page(); // create new page object
              $dummy->template = $t->name; // set template
              $dummy->parent = 'pg-dummies'; // set the parent
              $dummy->name = 'pg-dummy-' . $f->id; // give it a name used in the url for the page
              $dummy->title = 'pg-dummy-' . $f->name; // set page title (not neccessary but recommended)
              $dummy->$f = '<p>Edit</p>';
              $dummy->addStatus(Page::statusHidden);
              $dummy->addStatus(Page::statusUnpublished);
              $dummy->save();
            }
          }
        }
      }
    }
  }

  /**
   * Creates per-symbol add permissions for every child of the pg-symbols container
   * and assigns them to any role that already holds the pagegrid-symbol-add permission.
   *
   * @return void
   */
  function createSymbolPermissions() {

    //create symbol permissions
    $symbolContainer = $this->pages->get("name=pg-symbols, template=pg_container");
    foreach ($symbolContainer->children() as $p) {
      if (!$this->permissions->get("pagegrid-symbol-add-$p->id")->id) {
        $sync = $p->meta()->pg_sync === 0 ? '' : '(synchronized)';
        $permission = $this->permissions->add("pagegrid-symbol-add-$p->id");
        $permission->title = "Add  $p->title $sync";
        $permission->save();

        //enable new permissions as default
        foreach ($this->roles as $role) {
          if (!$role->hasPermission('pagegrid-symbol-add')) continue;
          $role->addPermission($permission->name);
          $role->of(false);
          $role->save();
        }
      }
    }
  }

  /**
   * Module setup function 
   */
  protected function setup() {
    $curl = curl_init();
    $lKey = $this->lKey;
    $lUrl = $this->lUrl;
    $host = $_SERVER['HTTP_HOST'];
    // $host_e = pathinfo(parse_url($host, PHP_URL_PATH), PATHINFO_EXTENSION);
    $validHost = false;
    $valid = false;
    $hash = hash('md2', $lKey);

    if ($host == $lUrl) {
      $validHost = true;
      $valid = 1;
    }

    if ($hash == '8faa4c9ae8583dce18bf41217689ed33') {
      return 1;
    }

    $whitelist = array(
      '127.0.0.1',
      '::1',
      'page-grid.com',
      'cloud.page-grid.com',
      'pagegrid.uber.space',
      'cloud.pagegrid.uber.space'
    );


    //localhost
    if (in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
      $validHost = true;
      $valid = 2;
    }

    //check whitelist urls
    if (in_array($host, $whitelist)) {
      $validHost = true;
      $valid = 2;
    }

    if ($validHost) {
      return $valid;
    }

    $post_data = array(
      'product_id' => '71jpQn0sTqdzpzhNPOG08g==',
      'license_key' => $lKey,
      'increment_uses_count' => ($validHost) ? 'false' : 'true'
    );

    $url = "https://api.gumroad.com/v2/licenses/verify";

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

    $result = curl_exec($curl);
    $array = json_decode($result, true);
    $error = 'Please buy a PAGEGRID license before you launch your project. For test and staging domains you don’t need a license. <a style="text-decoration:underline;" title="Got to settings" href="' . $this->config->urls->admin . 'module/edit?name=FieldtypePageGrid&collapse_info=1">Enter license key</a>';

    if ($array['success'] === true) {

      $variant = $array['purchase']['variants'];
      $uses = $array['uses'];
      $valid = 1;

      if (isset($variant)) {

        if ($variant == '(1 License)' && $uses > 1) {
          $valid = 3;
        }

        if ($variant == '(3 Licenses)' && $uses > 3) {
          $valid = 3;
        }

        if ($variant == '(10 Licenses)' && $uses > 10) {
          $valid = 3;
        }
      }
    } else {
      $valid = 0;
    }

    if ($valid == 1 || $valid == 2) {
      $data = $this->modules->getConfig($this->className);
      $data['lUrl'] = $host;
      $this->modules->saveConfig('FieldtypePageGrid', $data);
    } else {
      $name = isset($_GET['name']) ? $_GET['name'] : '';
      if ($name !== $this->className) {
        $this->notices->error($error, Notice::allowMarkup);
      }
    }

    curl_close($curl);
    return $valid;
  }


  // add deafult template context settings, this can also be done in admin with override tab in field settings
  //  public function ___getConfigAllowContext(Field $f) {
  //		$a = parent::___getConfigAllowContext($f);
  //		$a[] = 'template_id';
  //        $a[] = 'pageTemplate';
  //		return $a;
  //	}

  /**
   * Creates or retrieves a global system container page (pg-items, pg-classes, etc.).
   * Forces the parent to $parent so containers are correctly placed after module upgrades.
   *
   * @param string $name  Page name, e.g. 'pg-items'
   * @param string $title Page title
   * @param Page   $parent Parent page (admin)
   * @param bool   $hidden Whether to add statusHidden
   * @return Page
   */
  private function createSystemContainer(string $name, string $title, Page $parent, bool $hidden = true): Page {
    $p = $this->pages->get("name=$name, template=pg_container");
    if (!$p->id) {
      $p = new Page();
      $p->template = 'pg_container';
      $p->parent = $parent;
      $p->name = $name;
      $p->title = $title;
      if ($hidden) $p->addStatus(Page::statusHidden);
      $p->save();
    }
    // force correct parent for upgrades from older versions
    $p->parent = $parent;
    $p->save();
    return $p;
  }

  /**
   * Auto-creates pg-{pageId} and pg-{fieldId} containers when a page with a PageGrid field is added.
   * Runs after copyFromBlueprint and activateLanguages so blueprint-cloned containers are never overwritten.
   *
   * @param HookEvent $event
   * @return void
   */
  public function createContainers($event) {
    $page = $event->arguments(0);

    // Skip cloned pages — Pages::cloned hook handles those separately
    if ($page->_cloning && $page->_cloning->id) return;
    // Skip container and block pages
    if ($page->template->name === 'pg_container') return;
    // Skip pages already living under pg-items
    if ($page->parents->get('template=pg_container') && $page->parents->get('template=pg_container')->id) return;

    // Collect all PageGrid fields on this page's template
    $pgFields = [];
    foreach ($page->fields as $f) {
      if ($f->type instanceof FieldtypePageGrid) $pgFields[] = $f;
    }
    if (!count($pgFields)) return;

    // Locate the global pg-items root
    $pgItems = $this->pages->get('name=pg-items, template=pg_container');
    if (!$pgItems->id) return;

    // Get or create pg-{pageId} container under pg-items
    $itemsParent = $this->pages->get("name=pg-{$page->id}, parent={$pgItems->id}, template=pg_container");
    if (!$itemsParent->id) {
      $itemsParent = new Page();
      $itemsParent->template = 'pg_container';
      $itemsParent->parent = $pgItems;
      $itemsParent->name = 'pg-' . $page->id;
      $itemsParent->title = $page->title . ' items';
      $itemsParent->save();
    }

    // Get or create pg-{fieldId} container for each PageGrid field
    foreach ($pgFields as $f) {
      $fieldContainer = $itemsParent->get("name=pg-{$f->id}, template=pg_container");
      if (!$fieldContainer->id) {
        $fieldContainer = new Page();
        $fieldContainer->template = 'pg_container';
        $fieldContainer->parent = $itemsParent;
        $fieldContainer->name = 'pg-' . $f->id;
        $fieldContainer->title = 'pg-' . $f->id;
        $fieldContainer->save();
      }
    }
  }
}
