<?php

namespace ProcessWire;

/**
 * PageGrid for ProcessWire
 * 
 * Copyright (C) 2023 by Jan Ploch
 * THIS IS A COMMERCIAL MODULE - DO NOT DISTRIBUTE
 */

class FieldtypePageGrid extends FieldtypeMulti implements Module, ConfigurableModule {

  public static function getModuleInfo() {

    return array(
      'title' => __('PAGEGRID'),
      'summary' => __('Commercial page builder module that renders block templates and adds drag and drop functionality in admin.', __FILE__),
      'version' => '2.0.7',
      'author' => 'Jan Ploch',
      'icon' => 'th',
      'href' => "https://page-grid.com",
      'installs' => array('InputfieldPageGrid', 'ProcessPageGrid', 'PageFrontEdit', 'ProcessPageClone'),
      'requires' => array('ProcessWire>=3.0.210', 'PHP>=5.4.0'),
      'autoload' => 'template=admin',
      'permissions' => array(
        'pagegrid-process' => 'Allow PAGEGRID to process ajax calls',
        'page-pagegrid-edit' => 'Edit PAGEGRID items in modal (applies to all editable templates)',
        'pagegrid-drag' => 'Drag PAGEGRID items',
        'pagegrid-resize' => 'Resize PAGEGRID items',
        'pagegrid-style-panel' => 'Enable styling of PAGEGRID items',
        'pagegrid-add' => 'Use the sidebar to drag new items into the page (also needs pagegrid-drag permission to work)',
      ),
    );
  }

  public function install() {
    $this->createModule();
  }

  public function createModule() {

    $fields = wire('fields');
    $adminPage = wire('pages')->get('name=pagegrid, template=admin');
    if (!$adminPage || !$adminPage->id) return;

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
      $t->noParents = -1; //allow one more (2 pages can use this template)
      $t->icon = 'th';
      $t->tags = 'pagegrid';
      $t->save();
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
      $t->icon = 'th';
      $t->tags = 'pagegrid';
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

    //create page to hold items
    $p = $this->pages->get("name=pg-items, template=pg_container");
    if (!$p->id) {
      // page needs to be created
      $p = new Page(); // create new page object
      $p->template = 'pg_container'; // set template
      $p->parent = $adminPage; // set the parent
      $p->name = 'pg-items'; // give it a name used in the url for the page
      $p->title = 'Items'; // set page title (not neccessary but recommended)
      $p->addStatus(Page::statusHidden);
      $p->save();
    }

    //force new parent for older versions
    $p->parent = $adminPage; // set the parent
    $p->save();
    // END create page to hold items  

    // create page to hold classes
    $p = $this->pages->get("name=pg-classes, template=pg_container");

    if (!$p->id) {
      $p = new Page(); // create new page object
      $p->template = 'pg_container'; // set template
      $p->parent = $adminPage; // set the parent
      $p->name = 'pg-classes'; // give it a name used in the url for the page
      $p->title = 'Classes'; // set page title (not neccessary but recommended)
      $p->addStatus(Page::statusHidden);
      $p->save();
    }

    //force new parent for older versions
    $p->parent = $adminPage; // set the parent
    $p->save();
    // END create page to hold classes  

    // create page to hold animations
    $p = $this->pages->get("name=pg-animations, template=pg_container");

    if (!$p->id) {
      $p = new Page(); // create new page object
      $p->template = 'pg_container'; // set template
      $p->parent = $adminPage; // set the parent
      $p->name = 'pg-animations'; // give it a name used in the url for the page
      $p->title = 'Animations'; // set page title (not neccessary but recommended)
      $p->addStatus(Page::statusHidden);
      $p->save();
    }
    // END create page to hold animations 

    // create page to hold blueprints
    $p = $this->pages->get("name=pg-blueprints, template=pg_container");

    if (!$p->id) {
      $p = new Page(); // create new page object
      $p->template = 'pg_container'; // set template
      $p->parent = $adminPage; // set the parent
      $p->name = 'pg-blueprints'; // give it a name used in the url for the page
      $p->title = 'Blueprints'; // set page title (not neccessary but recommended)
      // $p->addStatus(Page::statusHidden);
      $p->save();
    }
    // END create page to hold blueprints 

    // create page to hold symbols
    $p = $this->pages->get("name=pg-symbols, template=pg_container");

    if (!$p->id) {
      $p = new Page(); // create new page object
      $p->template = 'pg_container'; // set template
      $p->parent = $adminPage; // set the parent
      $p->name = 'pg-symbols'; // give it a name used in the url for the page
      $p->title = 'Symbols'; // set page title (not neccessary but recommended)
      // $p->addStatus(Page::statusHidden);
      $p->save();
    }
    // END create page to hold symbols 

    //create editor role
    //add role and permissions
    if (!$this->roles->get('pagegrid-editor')->id) {
      $erole = $this->roles->add("pagegrid-editor");
    } else {
      $erole = $this->roles->get('pagegrid-editor');
    }

    $erole->addPermission("page-view");
    $erole->addPermission("page-edit");
    // $erole->addPermission("page-sort");
    $erole->addPermission("profile-edit");

    if (!$this->permissions->get('page-edit-front')->id) {
      $permission = $this->permissions->add("page-edit-front");
      $permission->title = 'Use the front-end page editor';
      $permission->save();
    } else {
      $permission = $this->permissions->get('page-edit-front');
    }
    $erole->addPermission($permission->name);

    if (!$this->permissions->get('pagegrid-process')->id) {
      $permission = $this->permissions->add("pagegrid-process");
      $permission->title = 'Allow PAGEGRID to process ajax calls';
      $permission->save();
    } else {
      $permission = $this->permissions->get('pagegrid-process');
    }
    $erole->addPermission($permission->name);

    $erole->of(false);
    $erole->save();

    $grole = $this->roles->get('guest');

    // add template permissions fpr pg container
    $etemplate = $this->templates->get("pg_container");

    $addRoles = $etemplate->get("addRoles");
    $addRoles[] = $erole->id;

    $editRoles = $etemplate->get("editRoles");
    $editRoles[] = $erole->id;

    $createRoles = $etemplate->get("createRoles");
    $createRoles[] = $erole->id;

    $etemplate->useRoles = 1;
    $etemplate->set("roles", array($grole->id, $erole->id));
    // $etemplate->set("addRoles", $addRoles);
    $etemplate->set("editRoles", $editRoles);
    // $etemplate->set("createRoles", $createRoles);
    $etemplate->save();

    $htemplate = $this->templates->get('home');
    if ($htemplate) {
      $editRoles = $htemplate->get("editRoles");
      $editRoles[] = $erole->id;
      $htemplate->set("editRoles", $editRoles);
      $htemplate->save();
    }

    //END create editor role
  }

  public function uninstall() {

    //first empty trash to prevent bug when process page already in trash
    $int = $this->pages->emptyTrash();

    $t = $this->templates->get('pg_container');
    $t2 = $this->templates->get('pg_blueprint');
    // $fg = $this->fieldgroups->get('pg_container');

    $pgTemplates = array($t->id, $t2->id);
    $pgDummies = $this->pages->get("name=pg-dummies, template=pg_container");

    if ($pgDummies && $pgDummies->id) {
      $pgDummies->delete(true);
    }

    $pgPages = $this->pages->find('template=pg_container, include=all');
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

    //delete role
    if ($this->roles->get('pagegrid-editor')->id) {
      $this->roles->delete($this->roles->get('pagegrid-editor'));
    }
  }

  public function init() {
    //make $pagegrid available to call functions from InputfieldPageGrid
    $this->fuel->set('pagegrid', $this->modules->get('InputfieldPageGrid'));
    $this->addHookAfter('AdminTheme::getExtraMarkup', $this, 'addBodyClasses');
    $this->addHookBefore("PageFrontEdit::getPage", $this, "disableInlineEdit");
    // $this->addHookAfter('Page::render', $this, 'enableInlineEditFile', ['priority' => 999]);
    $this->config->styles->add($this->config->urls->InputfieldPageGrid . "css/AdminThemeCanvas-fix.css");
    $this->setBlueprintTemplate();
  }

  public function ready() {

    //create pages und templates if they don't exist
    $container = $this->pages->get("name=pg-items, template=pg_container");
    if (!$container->id) $this->createModule();

    $container = $this->pages->get("name=pg-animations, template=pg_container");
    if (!$container->id) $this->createModule();

    $container = $this->pages->get("name=pg-classes, template=pg_container");
    if (!$container->id) $this->createModule();

    $container = $this->pages->get("name=pg-blueprints, template=pg_container");
    if (!$container->id) $this->createModule();

    $container = $this->pages->get("name=pg-symbols, template=pg_container");
    if (!$container->id) $this->createModule();

    $this->addHookAfter("Modules::refresh", $this, "createModule");
    $this->addHookAfter('Pages::cloned', $this, "clone");
    $this->addHookBefore('Page::changed(0:title)', $this, "titleChanged");
    $this->addHookAfter('Pages::delete', $this, "delete");
    $this->addHookAfter('ProcessPageList::find', $this, "hideDummies");
    $this->addHookAfter('Pages::added', $this, "copyFromBlueprint");
    $this->addHookAfter("Pages::save", $this, "autoPuplish");
    $this->addHookAfter("ProcessPageEdit::buildForm", $this, "modalEdit");
    $this->addHookAfter('ProcessPageAdd::buildForm', $this, "pageAddForm");
    $this->addHookBefore('ProcessPageEdit::execute', $this, 'blueprintReady');

    //hide setup page for non superusers
    $pg = $this->pages->get('name=pagegrid, template=admin');
    $user =  $this->user;

    if ($pg->id) {
      $hidden = $pg->hasStatus('hidden');

      if ($user->isSuperuser() == 0 && $hidden == 0) {
        $pg->addStatus(Page::statusHidden);
        $pg->save();
      }
      if ($user->isSuperuser() && $hidden) {
        $pg->removeStatus(Page::statusHidden);
        $pg->save();
      }
    }
    //END hide setup page for non superusers
  }

  public function blueprintReady($event) {
    //make sure blueprint has pg field
    $page = $event->object->getPage();
    if ($page->template->name !== 'pg_blueprint') return;

    //add pg field if not set
    $t = $this->templates->get('pg_blueprint');
    if (!$t || !$t->id) return;

    $hasPgField = false;
    foreach ($t->fields as $f) {
      if ($f->type instanceof FieldtypePageGrid) {
        $hasPgField = true;
        break;
      }
    }

    $pgField = $this->fields->get('type=FieldtypePageGrid');
    if (!$hasPgField && $pgField) {
      $t->fieldgroup->add($pgField);
      $t->fieldgroup->save();
    }
  }


  public function setBlueprintTemplate() {
    $t = $this->templates->get('pg_blueprint');
    if (!$t && !$t->id) return;
    $file = $this->config->paths->siteModules . 'FieldtypePageGrid/pg_blueprint.php';
    $t->filename = $file;
    // load pages into var to force init page
    $bpPages = $this->pages->find('template=pg_blueprint, include=all');
  }

  //disable inline editor if settings checkbox inlineEditorFrontDisable is checked
  public function disableInlineEdit($event) {
    $isBackend = isset($_GET['backend']);
    if (!$isBackend && $this->inlineEditorFrontDisable && !$this->config->ajax) {
      //select admin page to make frontend editor return false
      $p = $this->pages->get('admin');
      $p->edit(false);
      $event->return = $p;
      $event->replace = true;
    }
  }


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

    //check if pagegrid item page
    $isPageGrid = false;
    if (count($parentPage->parents('template=pg_container'))) {
      $isPageGrid = true;
    }

    foreach ($form as $inputfield) {

      if ($inputfield->name !== 'template') return;
      $templates = $inputfield['options'];
      if (!$templates) return;

      foreach ($templates as $key => $value) {
        $t = $this->templates->get($key);

        if ($t && $t->id && $t->tags) {
          //normal page
          // remove templates with tagname pagegrid (auto set via module)
          if (!$isPageGrid && (strpos($t->tags, 'pagegrid') !== false)) unset($templates[$key]);
        }

        //pagegrid page
        //remove templates without tagname pagegrid
        if ($isPageGrid && (!$t->tags || (strpos($t->tags, 'pagegrid') === false))) unset($templates[$key]);
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

    // Populate back argument
    $event->return = $form;
  }

  public function enableInlineEditFile($out) {

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
        $p = wire('pages')->get($pId);

        //get uploader markup
        if ($p && $f && $p->id && $p->hasField($f)) {
          $uploaderString = $this->modules->get('InputfieldPageGrid')->renderFileUploader($p, $f);
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

  //function gets called for $page->fieldname calls render function as alternative
  public function ___formatValue(Page $page, Field $field, $value) {
    return $this->modules->get('InputfieldPageGrid')->renderGrid($page);
  }

  // add interface classes to body
  public function addBodyClasses($event) {

    if ($this->process != 'ProcessPageEdit') {
      return;
    }

    $theme = $event->object;
    $user = $this->user;
    $theme->addBodyClass("template-{$this->pages->get((int) wire('input')->get('id'))->template}");

    foreach ($user->roles as $role) {
      $theme->addBodyClass("role-{$role->name}");
    }
    if (!$user->isSuperuser()) $theme->addBodyClass("role-nonsuperuser");
    $theme->addBodyClass("user-{$user->name}");
    if ($user->hasPermission('pagegrid-add')) {
      $theme->addBodyClass("permission-pagegrid-add");
    }

    if ($this->config->debug) {
      $theme->addBodyClass("pw-debug-on");
    }

    //interface classes
    foreach ($this->interface as $interfaceClass) {
      $theme->addBodyClass($interfaceClass);
    }

    //add active breakpoint classes
    $theme->addBodyClass('breakpoint-base');
  }



  // clone items of page if page gets cloned
  public function clone($event) {

    $pages = $event->wire('pages');
    $page = $event->arguments(0);
    $copy = $event->arguments(1);


    foreach ($copy->template->fieldgroup as $field) {

      if (!$field->type instanceof FieldtypePageGrid) continue;
      $itemsParent = $pages->get('pg-' . $page->id);
      if ($itemsParent->id) {
        $cloneItemsParent = $pages->clone($itemsParent);
        $cloneItemsParent->setAndSave('name', 'pg-' . $copy->id);
        $cloneItemsParent->setAndSave('title', $copy->title . ' items');

        foreach ($cloneItemsParent->find('') as $clone) {
          $newName = $clone->template->name . '-' . $clone->id;
          // // // // bd($newName);
          $clone->setAndSave('name', $newName);
          $clone->setAndSave('title', $newName);
        }
      }
    }
  }

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

  //delete pagegrid items when page gets deleted
  public function delete($event) {
    $pages = $event->wire('pages');
    $page = $event->arguments(0);
    foreach ($page->template->fieldgroup as $field) {
      if (!$field->type instanceof FieldtypePageGrid) continue;
      $itemsParent = $pages->get('pg-' . $page->id);
      if ($itemsParent->id) {

        foreach ($itemsParent->find('') as $item) {
          $item->removeStatus(Page::statusLocked);
          $item->save();
        }

        $itemsParent->delete(true); // true allown to delete children too
        $this->message("PageGrid items for " . $page->id . " removed");
      }
    }
  }

  //hide dummy pages for init inline editor
  function hideDummies($event) {
    $event->return->each(function ($p) use ($event) {
      if ($p->template != 'pg_container') return;
      if ($p->name != 'pg-dummies') return;
      $event->return->remove($p);
    });
  }

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

  // blueprint feature and auto publish
  public function copyFromBlueprint($event) {
    $page = $event->arguments(0);
    $pages = $event->wire('pages');
    $input = $this->input;

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

    // auto puplish pagetable items or pages without title
    if ($page->hasField('title') && !$page->title) {
      $page->title = $page->name;
      $page->removeStatus('unpublished');
      $page->save();
    }
    // END auto puplish pagetable items or pages without title

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
      $newName = $clone->template->name . '-' . $clone->id;
      $clone->setAndSave('name', $newName);
      $clone->setAndSave('title', $newName);
    }
    $this->message('New page created based on blueprint: ' . $page1->name);


    // END COPY FROM TEMPLATE -------------------------------------------

  }

  // autopuplish pages with one template and 'pg-autotitle' set as childNameFormat (automatically set in fieldtype)
  public function autoPuplish($event) {
    // remove statusTemp (flash icon on page)
    $page = $event->arguments(0);

    if ($page->title == 'pg-autotitle') {
      $event->arguments(0)->status = 1;
      $templateName = str_replace('_', '-', $page->template->name);
      $page->of(false);
      $page->set('title', $templateName . '-' . $page->id);
      $page->set('name', $templateName . '-' . $page->id);
      $page->removeStatus('unpublished');
      $page->save();
    }
  }

  // add function to load children inside modal when no other fields, function gets called from js when needed
  public function modalEdit($event) {

    if ($this->process != 'ProcessPageEdit') return;
    if (isset($_GET['pgmodal']) == 0) return;

    $this->ppe = $event->object;
    $this->form = $event->return;
    $page = $this->ppe->getPage();

    //add css to modal
    $this->form->prependFile = $this->config->styles->add($this->config->urls->InputfieldPageGrid . "css/main.css");

    if (count($page->fields) > 1) return;

    $this->form->appendMarkup = "
    <script>
    function loadChildrenTab() {
      $('#_ProcessPageEditChildren').trigger('click');
    }
</script>
";
  }

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
   * Get the Inputfield used for input by PageTable
   * 
   * @param Page $page
   * @param Field $field
   * @return Inputfield
   * 
   */
  public function getInputfield(Page $page, Field $field) {
    /** @var InputfieldPageGrid $inputfield */
    $inputfield = $this->modules->get('InputfieldPageGrid');

    // foreach ($this->interface as $interfaceClass) {
    //   $inputfield->addClass($interfaceClass, 'wrapClass');
    // }

    return $inputfield;
  }


  public function ___getConfigInputfields(Field $field) {

    $inputfields = parent::___getConfigInputfields($field);

    /** @var InputfieldAsmSelect $f */
    $f = $this->wire('modules')->get('InputfieldAsmSelect');
    $f->attr('name', 'template_id');
    $f->label = $this->_('Select one or more templates for items');
    $f->icon = 'th';
    foreach ($this->wire('templates') as $template) {
      if ($template->flags & Template::flagSystem) continue;
      $filename = wire('config')->paths->templates . 'blocks/' . $template->name . '.php';
      if (file_exists($filename)) {
        $f->addOption($template->id, $template->name);
      } else {
        $filename = wire('config')->paths->siteModules . 'PageGridBlocks/blocks/' . $template->name .  '.php';
        if (file_exists($filename)) {
          $f->addOption($template->id, $template->name);
        }
      }
    }
    $value = $field->get('template_id');
    if (!is_array($value)) $value = $value ? array($value) : array();
    $f->attr('value', $value);
    $f->required = true;
    $f->description = $this->_('The template files must be placed in site/templates/blocks/ folder before you can select them here. These are the templates that will be used by pages managed from this field'); // Templates selection description
    // $f->notes = $this->_('Please hit Save after selecting a template and the remaining configuration on the Input tab will contain more context.'); // Templates selection notes 
    $inputfields->add($f);

    return $inputfields;
  }

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

  protected function setup() {
    $curl = curl_init();
    $lKey = $this->lKey;
    $lUrl = $this->lUrl;
    $host = $_SERVER['HTTP_HOST'];
    $host_e = pathinfo(parse_url($host, PHP_URL_PATH), PATHINFO_EXTENSION);
    $validHost = false;
    $valid = false;

    // $data = $this->modules->getConfig($this->className);
    // $data['lUrl'] = '';
    // $this->modules->saveConfig('FieldtypePageGrid', $data);

    // // // bd($this->lUrl);

    if ($host == $lUrl) {
      $validHost = true;
      $valid = 1;
    }

    if (substr($host, 0, 9) == 'localhost') {
      $validHost = true;
      $valid = 2;
    }

    if ($host_e == 'test') {
      $validHost = true;
      $valid = 2;
    }

    if ($host_e == 'dev') {
      $validHost = true;
      $valid = 2;
    }

    // // // bd($validHost);

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

    $result = curl_exec($curl);
    $array = json_decode($result, true);
    $error = 'Your PageGrid license is invalid <a style="text-decoration:underline;" title="Got to settings" href="' . $this->config->urls->admin . 'module/edit?name=FieldtypePageGrid&collapse_info=1">Enter license key</a>';

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

}
