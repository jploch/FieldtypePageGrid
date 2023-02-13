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
      'version' => '0.1.2',
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
    $fs = wire('fields');
    $adminPage = wire('page')->rootParent;

    // create page to hold items
    $p = wire('pages')->findOne("name=pg-items, template=pg_container, include=hidden");

    if ($p->id) {
      // page already exists

    } else {
      // page needs to be created
      $titleField = $fs->get('title');

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
      $t->save();

      $p = new Page(); // create new page object
      $p->template = 'pg_container'; // set template
      $p->parent = $adminPage; // set the parent
      $p->name = 'pg-items'; // give it a name used in the url for the page
      $p->title = 'PageGrid Items'; // set page title (not neccessary but recommended)
      $p->addStatus(Page::statusHidden);
      $p->save();
    }
    // END create page to hold items  

    // create page to hold classes
    $p = wire('pages')->findOne("name=pg-classes, template=pg_container, include=hidden");

    if ($p->id) {
      // page already exists

    } else {
      $p = new Page(); // create new page object
      $p->template = 'pg_container'; // set template
      $p->parent = $adminPage; // set the parent
      $p->name = 'pg-classes'; // give it a name used in the url for the page
      $p->title = 'PageGrid Classes'; // set page title (not neccessary but recommended)
      $p->addStatus(Page::statusHidden);
      $p->save();
    }
    // END create page to hold classes  

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
    $t = $this->templates->get('pg_container');
    $fg = $this->fieldgroups->get('pg_container');
    $p = wire('pages')->findOne("name=pg-items, include=hidden");
    $p2 = wire('pages')->findOne("name=pg-classes, include=hidden");

    if ($p->id && count($p->children()) == 0) {
      $p->delete();
    }

    if ($p2->id && count($p2->children()) == 0) {
      $p2->delete();
    }

    if ($t && $t->getNumPages() > 0) {
      $this->message("Can't delete template pg_container, because it's been used by some pages.");
    } else {
      $this->message("Remove PageGrid Page and Template");
      if ($t) {
        $t->flags = Template::flagSystemOverride; // remove flasg system template, to be able to delete
        $t->flags = 0; // remove flasg system template, to be able to delete
        $t->save();
        wire('templates')->delete($t);
      }
      if ($fg) {
        wire('fieldgroups')->delete($fg);
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

    $this->addHook('Field::styles', $this, "styles");
    $this->addHookAfter('AdminTheme::getExtraMarkup', $this, 'addBodyClasses');
  }

  public function ready() {
    $this->addHookAfter("Modules::refresh", $this, "createModule");
    $this->addHook('Field::styles', $this, "styles");
    $this->addHookBefore('Pages::cloned', $this, 'flagClone');
    $this->addHookAfter('Pages::cloneReady', $this, 'flagClone');
    $this->addHookAfter('Pages::cloned', $this, 'clone');
    $this->addHookBefore('Page::changed(0:title)', $this, 'titleChanged');
    $this->addHookAfter('Pages::delete', $this, 'delete');
    $this->addHookAfter('Page(name=pg-template)::listable', $this, 'hideTemplatePage');
    $this->addHookAfter('Pages::added', $this, 'copyFromTemplate');
    $this->addHookAfter("Pages::save", $this, 'autoPuplish');
    $this->addHookAfter("ProcessPageEdit::buildForm", $this, 'openChildren');

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

    //interface classes
    foreach ($this->interface as $interfaceClass) {
      $theme->addBodyClass($interfaceClass);
    }

    //add active breakpoint classes
    $theme->addBodyClass('breakpoint-base');
  }

  // set clonePage var for page added hook, to disable copy from page template feature
  public function flagClone($event) {
    $this->clonePage = true;
  }

  // clone items of page if page gets cloned
  public function clone($event) {
    static $clonedIDs = array();

    $this->clonePage = true;

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

  //hide template pages for nonSuper users
  public function hideTemplatePage($event) {
    $page = $event->object;
    $user = $event->wire('user');
    if ($user->isSuperuser() == 0) {
      foreach ($page->fields as $f) {
        if ($f->type instanceof FieldtypePageGrid) {
          $event->return = false;
        }
      }
    }
  }

  // page template feature and auto publish
  public function copyFromTemplate($event) {
    $page = $event->arguments(0);
    $pages = $event->wire('pages');

    // auto puplish pagetable items or pages without title
    if (!$page->title) {
      $page->title = $page->name;
      $page->removeStatus('unpublished');
      $page->save();
    }
    // END auto puplish pagetable items or pages without title

    // COPY FROM TEMPLATE -------------------------------------------

    //if clone return
    // // // // bd($this->clonePage);

    if ($this->clonePage) {
      return false;
    }

    foreach ($page->fields as $f) {

      if ($f->type instanceof FieldtypePageGrid) {

        $pg = $f;
        $table_template_ids = $pg->template_id;
        $table_templates = $event->wire('templates')->find(['id' => $table_template_ids]);

        foreach ($table_templates as $table_template) {
          // return if added page is nested group to prevent endless loop
          if ($page->template->name == $table_template->name) {
            return false;
          }
        }

        // get the field in context of this template
        // $t = $page->template;
        // $f = $t->fieldgroup->getField($pg->name, $useFieldgroupContext = true);

        // if (isset($f->pageTemplate) && $f->pageTemplate !== '' && $f->pageTemplate != 0) {

        $childPages = $page->parent()->children('include=all');
        $pageTemplate = $childPages->findOne('name=pg-template, include=all');

        if ($pageTemplate && $pageTemplate->id) {
        } else {
          $pageTemplate = $pages->findOne('name=pg-template, include=all');
        }

        if ($pageTemplate && $pageTemplate->id) {

          $page1ID = $pageTemplate->id;
          $page1 = $pageTemplate;
          $page1Items = $pages->get('pg-' . $page1ID);
          $pageID = $page->id;
          $pageOldItems = $pages->get('pg-' . $pageID);

          //there is an old page already 
          if ($pageOldItems->id) {
            return false;
          }

          // $page1 is page containing pageGrid you want to copy from, defined in field settings
          if ($page1Items->id) {
            $cloneItemsParent = $pages->clone($page1Items);
            $cloneItemsParent->setAndSave('name', 'pg-' . $page->id);
            $cloneItemsParent->setAndSave('title', $page->title . ' items');

            foreach ($cloneItemsParent->find('') as $clone) {
              $newName = $clone->template->name . '-' . $clone->id;
              $clone->setAndSave('name', $newName);
              $clone->setAndSave('title', $newName);
            }
            $this->message('New page created based on: ' . $page1->name);
          }
        }
        // END COPY FROM TEMPLATE -------------------------------------------


        //break the loop when work is done
        break;
      }
    }
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
  public function openChildren($event) {
    if ($this->process != 'ProcessPageEdit') return;
    $this->ppe = $event->object;
    $this->form = $event->return;
    $page = $this->ppe->getPage();

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
    //hook PageFrontEdit to read ajax items for inline edit
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
