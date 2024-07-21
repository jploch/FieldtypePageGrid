<?php

namespace ProcessWire;

/**
 * Class PageGridFrontEditConfig
 * 
 * Handles configuration for PageGridFrontEdit module
 *
 */

class FieldtypePageGridConfig extends ModuleConfig {

	public function __construct() {
		$this->addHookAfter('Modules::saveConfig', $this, 'saveConfig');
		$this->addHookBefore('Page::render', $this, 'setDefaults');
		$this->addHookBefore('InputfieldTextarea::processInput', $this, 'sanitizeValue');
	}

	public function sanitizeValue(HookEvent $event) {
		$input = $event->arguments(0);
		if ($input->customStyles) $input->customStyles = strip_tags($input->customStyles, '');
		$event->arguments(0, $input);
	}

	public function setDefaults(HookEvent $event) {

		//return if not module settings page
		if (!isset($_GET['name'])) return;
		if ($_GET['name'] !== 'FieldtypePageGrid') return;

		//get config data
		$data = $this->modules->getConfig('FieldtypePageGrid');
		$dataOld = $data;

		//install selected block modules before render so they can change main module settings
		foreach ($this->fields->find('type=FieldtypePageGrid') as $pgf) {

			//populate field settings for older pg versions
			if (!isset($data['template_id_' . $pgf->id])) {
				if ($pgf->template_id && is_array($pgf->template_id) && count($pgf->template_id)) {
					$data['template_id_' . $pgf->id] = $pgf->template_id;
					$this->modules->saveConfig('FieldtypePageGrid', $data);
					$data = $this->modules->getConfig('FieldtypePageGrid');
					$dataOld = $data;
				}
			}
			//END populate field settings for older pg versions

			if (!isset($data['template_id_' . $pgf->id])) continue;

			$selectedBlocks = $data['template_id_' . $pgf->id];

			if (!is_array($selectedBlocks)) continue;
			foreach ($selectedBlocks as $key => $templateName) {
				if (is_numeric($templateName)) continue;
				$templateName = $this->sanitizer->filename($templateName);
				$className = str_replace('pg_', '', $templateName);
				$className = str_replace('_', '', ucwords($className, '_'));
				$className = 'Blocks' . $className;

				//install module if not installed
				if (!$this->modules->isInstalled($className) && in_array($templateName, $selectedBlocks)) {
					$this->modules->install($className);
					$data = $this->modules->getConfig('FieldtypePageGrid');
					$dataOld = $data;
				}

				//template_id: convert name values back to ids
				$t = $this->templates->get($templateName);
				$filename = wire('config')->paths->templates . 'blocks/' . $templateName . '.php';
				if ((!$t || !$t->id) && file_exists($filename)) {
					$t = $this->createTemplate($templateName);
				}

				if ($t && $t->id) {
					$data['template_id_' . $pgf->id][$key] = $t->id;
					$this->modules->saveConfig('FieldtypePageGrid', $data);
				}
				//END template_id: convert name values back to ids
			}
			// bd($data['template_id_' . $pgf->id]);
		}
		//END install selected block modules before render so they can change main module settings

		//set checkboxes to default
		if ($this->modules->get('FieldtypePageGrid')->interfaceDefault) {
			$data['interface'] = array('hideFieldTitle', 'hidePageHeadline', 'hideTitleField', 'hideTabs', 'hideSaveButton', 'showToolTips');
			$data['interfaceDefault'] = 0;
		}
		if ($this->modules->get('FieldtypePageGrid')->pluginsDefault) {
			$data['plugins'] = array('lazysizes');
			$data['pluginsDefault'] = 0;
		}

		//allways add pagegrid-page
		$pgT = $this->templates->get('pagegrid-page');
		if ($pgT && $pgT->id) {
			$pgf = $pgT->fields->get("type=FieldtypePageGrid");
			if ($pgf && $pgf->id) {
				if (!isset($data['addTemplate_' . $pgf->id])) $data['addTemplate_' . $pgf->id] = [];
				if (!in_array($pgT->id, $data['addTemplate_' . $pgf->id])) $data['addTemplate_' . $pgf->id][] = $pgT->id;
			}
		}


		if ($dataOld !== $data) {
			// bd($data);
			$this->modules->saveConfig('FieldtypePageGrid', $data);
		}

		//custom uninstall: delete field if option was checked
		if (isset($data['deleteFields']) && $data['deleteFields']) {
			foreach ($this->fields->find('type=FieldtypePageGrid') as $f) {
				//unset this option
				unset($data['deleteFields']);
				$this->modules->saveConfig('FieldtypePageGrid', $data);
				//remove field from all templates first
				foreach ($this->templates as $t) {
					if (!$t->hasField($f)) continue;
					$t->fields->remove($f);
					$t->fields->save();
					$t->fieldgroup->remove($f);
					$t->fieldgroup->save();
				}
				$this->fields->delete($f);
			}
		}
	}

	//this function runs on module save to keep PageFrontEdit and FieldtypePageGrid config is sync
	public function saveConfig(HookEvent $event) {

		if (!$this->modules->isInstalled('PageFrontEdit')) $this->modules->install('PageFrontEdit');

		$classname = $event->arguments[0];
		$configCore = $this->modules->getConfig('PageFrontEdit');
		$config = $this->modules->getConfig('FieldtypePageGrid');

		if (isset($config['inlineEditFields']) == 0) {
			$config['inlineEditFields'] = array();
		}

		if (isset($configCore['inlineEditFields']) == 0) {
			$configCore['inlineEditFields'] = array();
		}

		if ($config['inlineEditFields'] != $configCore['inlineEditFields']) {
			if ($classname == 'PageFrontEdit') {
				$config['inlineEditFields'] = $configCore['inlineEditFields'];
				$this->modules->saveConfig('FieldtypePageGrid', $config);
			}

			if ($classname == 'FieldtypePageGrid') {
				$configCore['inlineLimitPage'] = '0';
				$configCore['inlineEditFields'] = $config['inlineEditFields'];
				$this->modules->saveConfig('PageFrontEdit', $configCore);
			}
		}
	}

	public function getDefaults() {
		$configCore = $this->modules->getConfig('PageFrontEdit');
		$inlineEditFields = isset($configCore['inlineEditFields']) ? $configCore['inlineEditFields'] : array();

		return array(
			'lKey' => '',
			'lUrl' => '',
			'inlineEditFields' => $inlineEditFields, // core text fields that are inline-editable
			'inlineEditorFrontDisable' => 1,
			'interfaceDefault' => 1,
			'interface' => array(
				'hideFieldTitle',
				'hidePageHeadline',
				'hideTitleField',
				'hideTabs',
				'hideSaveButton',
				'showToolTips',
			),
			'pluginsDefault' => 1,
			'plugins' => array(
				'lazysizes',
			),
			'customStyles' => '',
			'customScript' => '',
			'fontColor' => '',
			'bgColor' => '',
			'fontPrivacy' => 1,
			'fallbackFonts' => 'Helvetica, Arial, sans-serif',
			'stylePanel' => 1,
		);
	}

	public function getInputfields() {

		//add js for font uploader and to save collapsed states
		$this->config->scripts->add($this->config->urls->FieldtypePageGrid . "js/FieldtypePageGridConfig.js'");

		$wrapper = new InputfieldWrapper();
		$valid = $this->modules->get('FieldtypePageGrid')->setup();
		$validateNote = "License is invalid " . '🙁';
		$collapsed = 0;
		$statusClass = 'InputfieldIsError';
		$description = $this->_('Please enter your PageGrid License Key.');

		if ($valid == 1) {
			$validateNote = "License is valid!";
			$collapsed = 8;
			$statusClass = 'InputfieldIsSuccess';
			$description = '';
		}

		if ($valid == 2) {
			$validateNote = "License is valid for testing!";
			$collapsed = 8;
			$statusClass = 'InputfieldIsWarning';
			$description = "Please [buy a license](https://page-grid.com/buy/) before you launch your project. For test and staging domains you don’t need a license.";
		}

		if ($valid == 3) {
			$validateNote = "License is already in use!";
			$description = "Please [buy a license](https://page-grid.com/buy/) before you launch your project.";
		}

		$f = $this('modules')->get('InputfieldText');
		$f->name = 'lKey';
		$f->label = $validateNote;
		$f->description = $description;
		$f->addClass($statusClass, 'wrapClass');
		$f->icon = 'key';
		$f->columnWidth('100');
		if (count($this->fields->find('type=FieldtypePageGrid'))) $f->themeOffset = 1;
		$f->collapsed = $collapsed;

		// don't show lkey in modal context
		if (isset($_GET['modal']) == 0) {
			$wrapper->add($f);
		}

		//-------------------------------------------------------
		//hidden field to store collapsed states as comma seperated string
		$collapsedString = $this->collapsedState ? $this->collapsedState : 'plugins,interface,stylePanelSettings,inlineSettings,customStyles';
		$collapsed = explode(',', $collapsedString);

		$f = $this('modules')->get('InputfieldHidden');
		$f->attr('name', 'collapsedState');
		$f->columnWidth('100');
		$f->attr('value', $this->collapsedState); // set value back to empty
		$wrapper->append($f);

		//-------------------------------------------------------
		// Field settings for convenience
		wire('modules')->get('JqueryUI')->use('modal');

		//create field if none is found and get var is set
		if (isset($_GET['createField']) && !$this->fields->get('name=grid')) {
			$fpg = new Field;
			$fpg->label = 'Grid';
			$fpg->name = 'grid';
			$fpg->type = $this->modules->get('FieldtypePageGrid');
			$fpg->tags = 'PageGrid';
			$fpg->save();
		}

		// return early and hide other settings if no field created
		if (!count($this->fields->find('type=FieldtypePageGrid'))) {
			$fLink = $this->config->urls->admin . 'module/edit?name=FieldtypePageGrid&collapse_info=1&createField';
			$f = $this->modules->get('InputfieldMarkup');
			$f->attr('id+name', 'fieldSettings');
			$f->label = 'Field Settings';
			$f->collapsed = 0;
			$f->themeOffset = 1;
			$f->value = "<p class='description'><i class='fa fw fa-bell-o'></i> Looks like you haven't created a field yet.</p>";
			$f->value .= '<a href="' . $fLink . '" href="' . $fLink . '">Create field</a><br>';
			$wrapper->add($f);
			return $wrapper;
		}


		//render field settings for convinience
		foreach ($this->fields->find('type=FieldtypePageGrid') as $pgf) {
			// if (!$pgf->type instanceof FieldtypePageGrid) continue;

			//see it field is added to template
			$pgfId = $pgf->id;
			$pgfName = $pgf->name;
			$fieldTemplates = $this['addTemplate_' . $pgfId] ? $this['addTemplate_' . $pgfId] : [];

			$fieldset = $this->modules->get('InputfieldFieldset');
			$fieldset->attr('id+name', 'fieldSettings_' . $pgfId);
			$fieldset->label = $this->_('Field Settings (' . $pgfName . ')');
			$fieldset->collapsed(in_array($fieldset->name, $collapsed) ? 1 : 0);
			$fieldset->icon = 'cog';
			$wrapper->add($fieldset);

			//blocks setting
			$f = $this->getBlockSettings($pgf);
			$f->name = 'template_id_' . $pgfId;
			$f->label = 'Blocks';
			// $f->columnWidth('50');
			$value = $this['template_id_' . $pgfId] ? $this['template_id_' . $pgfId] : [];

			//save field value
			$pgf->template_id = $value;
			$pgf->save();
			$fieldset->add($f);

			//add template to field
			$f = $this->wire('modules')->get('InputfieldAsmSelect');
			$f->attr('name', 'addTemplate_' . $pgfId);
			$f->collapsed = 1;
			$f->icon = 'cube';
			// $f->columnWidth('50');
			$f->label = 'Add field to templates';
			$f->description = 'The template files must be placed in **site/templates/** folder. [Learn more](https://page-grid.com/docs/#/developer/functions)';

			foreach ($this->templates as $t) {
				if ($t->name == 'admin') continue;
				if ($t->flags & Template::flagSystem) continue;
				$filename = wire('config')->paths->templates . $t->name . '.php';
				if (!file_exists($filename)) continue;
				$f->addOption($t->id, $t->name);
				if ($t->name === 'pagegrid-page') continue;
				if ($t->fields->get('type=FieldtypePageGrid') && $t->fields->get('type=FieldtypePageGrid')->id) continue;

				if (in_array($t->id, $fieldTemplates) !== false) {
					$t->fieldgroup->add($pgf);
					$t->fieldgroup->save();
				} else {
					$t->fieldgroup->remove($pgf);
					$t->fieldgroup->save();
				}
			}

			$fieldset->add($f);
		}

		//-------------------------------------------------------
		//interface

		// checkbox to set defaults
		$f = $this('modules')->get('InputfieldCheckbox');
		$f->collapsed = 4; //set to hidden
		$f->attr('name', 'interfaceDefault');
		$f->label = ' ';
		$f->checkboxLabel = 'Defaults';
		$f->attr('value', $this->interfaceDefault);
		if ($this->interfaceDefault) {
			$f->attr('checked', 'checked');
		}
		$wrapper->append($f);

		$f = $this->modules->get('InputfieldCheckboxes');
		$f->name = 'interface';
		$f->icon = 'eye-slash';
		$f->label = $this->_('Interface');
		$f->table = true;
		$f->collapsed(in_array($f->name, $collapsed) ? 1 : 0);
		$f->themeOffset = 1;
		$f->textFormat = Inputfield::textFormatBasic;
		if (!$this->modules->isInstalled('AdminThemeCanvas')) $f->notes = 'If you prefer a more neutral look for your backend, you can also use [AdminThemeCanvas](https://processwire.com/modules/admin-theme-canvas/).';
		// $f->addOption('setDefault', 'Default'); //set on first run to have default checked
		$f->addClass('pg-table-auto', 'wrapClass');
		$f->addOption('hideFieldTitle', 'Hide Field Label | [span.detail] Hide field label in page editor if there is only one PAGEGRID field.[/span]');
		$f->addOption('hidePageHeadline', 'Hide Page Headline | [span.detail] Hide page headline in page editor and add it to breadcrumb. [/span]');
		$f->addOption('hideTitleField', 'Hide Page Title Field | [span.detail] Hide page title field in page editor and add it to settings tab. [/span]');
		$f->addOption('hideTabs', 'Hide Tabs | [span.detail] Hide tabs and add a settings icon to show them. [/span]');
		$f->addOption('hideSaveButton', 'Hide Save Button | [span.detail] Hide save button (and use automatic ajax save) if there are no other fields than PAGEGRID on the content tab. [/span]');
		$f->addOption('showToolTips', 'Show Tooltips | [span.detail] Display helpful tooltips when you move the mouse pointer over field labels in the style panel. [/span]');
		// $f->val($this->interface);
		// $f->attr('checked', 'checked');
		$wrapper->append($f);

		//-------------------------------------------------------
		//pluins
		// checkbox to set defaults
		$f = $this('modules')->get('InputfieldCheckbox');
		$f->collapsed = 4; //set to hidden
		$f->attr('name', 'pluginsDefault');
		$f->label = ' ';
		$f->checkboxLabel = 'Defaults';
		$f->attr('value', $this->pluginsDefault);
		if ($this->pluginsDefault) {
			$f->attr('checked', 'checked');
		}
		$wrapper->append($f);

		// will load files with the same name as option automatically
		$f = $this->modules->get('InputfieldCheckboxes');
		$f->name = 'plugins';
		$f->icon = 'plug';
		$f->label = $this->_('Plugins');
		$f->table = true;
		$f->collapsed = 1;
		$f->themeOffset = 1;
		$f->textFormat = Inputfield::textFormatBasic;
		$f->description = 'Vanilla javascript plugins you want to load when using PAGEGRID’s script function.';
		// $f->addOption('setDefault', 'Default'); //set on first run to have default checked
		$f->addClass('pg-table-auto', 'wrapClass');
		$f->addOption('lazysizes', 'lazysizes | [span.detail] Lazyloader for images and videos. [learn more](https://github.com/aFarkas/lazysizes) [/span]');
		$wrapper->append($f);

		//STYLE PANEL
		$fieldset = $this->modules->get('InputfieldFieldset');
		$fieldset->attr('id+name', 'stylePanelSettings');
		$fieldset->label = $this->_('Style Panel');
		$fieldset->collapsed(in_array($fieldset->name, $collapsed) ? 1 : 0);
		$fieldset->icon = 'paint-brush';
		// $fieldset->themeOffset = 1;
		$wrapper->add($fieldset);

		// enable/disable
		$f = $this('modules')->get('InputfieldCheckbox');
		$f->attr('name', 'stylePanel');
		$f->label = ' ';
		$f->checkboxLabel = 'Enable Style Panel';
		$f->attr('value', $this->stylePanel);
		if ($this->stylePanel) {
			$f->attr('checked', 'checked');
		}
		$fieldset->append($f);

		//------------------------------------------

		// FONTS
		$fieldset1 = $this->modules->get('InputfieldFieldset');
		$fieldset1->attr('id+name', 'fonts');
		$fieldset1->label = $this->_('Fonts');
		// $fieldset1->collapsed(1);
		$fieldset1->icon = 'font';
		// $fieldset1->themeOffset = 1;
		$fieldset->add($fieldset1);

		// google font DVGO option
		$f = $this('modules')->get('InputfieldCheckbox');
		$f->attr('name', 'fontPrivacy');
		$f->label = $this->_('Google Fonts');
		$f->checkboxLabel = $this->_('Privacy friendly Google Fonts');
		$f->description = $this->_("Check this option to request Google Fonts from BunnyCDN instead of the Google server. BunnyCDN has logging disabled, and will not log any user information. [More information](https://github.com/coollabsio/fonts)");
		$f->attr('value', $this->fontPrivacy);
		$f->icon = 'google';
		if ($this->fontPrivacy) {
			$f->attr('checked', 'checked');
		}
		$fieldset1->append($f);

		//custom font
		//list files
		$files = $this->modules->get('InputfieldPageGrid')->getFontNames();
		$filePath = $this->modules->get('InputfieldPageGrid')->getFontPath();

		$f = $this->modules->get('InputfieldMarkup');
		$f->name = 'customFontList';
		$f->label = $this->_('Custom Fonts');
		$f->icon = 'text-width';
		$f->value = '<table class="AdminDataList pw-no-select uk-table uk-table-divider uk-table-small">';
		$f->value .= '<tr></tr>';
		foreach ($files as $file) {
			$ext = pathinfo($filePath . $file, PATHINFO_EXTENSION);
			$size = filesize($filePath . $file);

			if ($ext == 'woff' || $ext == 'woff2') {
				if ($size < 1024) {
					$size = "{$size} bytes";
				} elseif ($size < 1048576) {
					$size = round($size / 1024) . 'KB';
				} else {
					$size = round($size / 1048576, 1) . 'MB';
				}
				$f->value .= '<tr class="InputfieldIsSecondary">';
				$f->value .= '<td class="InputfieldContent"><i class="fa fw fa-file-o"></i>&nbsp;&nbsp;<label class="filename">' . $file . ' </label><span class="notes">(' . $size . ')</span></td>';
				$f->value .= '<td class="InputfieldContent uk-text-right"><a href="#" class="delete-file"><i class="fa fw fa-trash-o"></i></a></td>';
				$f->value .= '</tr>';
			}
		}
		$f->value .= '</table>';
		$f->value .= '<style>.asmSelect {min-width:200px;} html #notices {display:none;}</style>'; // hide false upload error and use js to show relevant ones
		if (!count($files)) {
			$f->description = 'No font files found. You can upload font files using the button below. Fonts are stored under site/templates/fonts/.';
			$f->value = '';
		}
		$fieldset1->append($f);

		//hidden field to store deleted
		$f = $this('modules')->get('InputfieldHidden');
		$f->attr('name', 'deleteFonts');
		$f->columnWidth('100');
		$f->attr('value', ''); // set value back to empty
		$fieldset1->append($f);

		//font uploader
		$f = $this->modules->get('InputfieldFile');
		$f->attr('id+name', "uploadFonts");
		$f->label = '';
		$f->extensions = "woff woff2";
		$f->maxFiles = 0;
		$f->descriptionRows = 0;
		$f->destinationPath = $filePath;
		// $f->noAjax = true; // uncomment if necessary
		$fieldset1->append($f);

		// when the form is submitted:
		if ($this->input->requestMethod('POST')) {

			/** @var WireUpload $ul */
			$ul = $this->wire(new WireUpload($f->name));
			// $ul = new WireUpload($f->name);
			$ul->setValidExtensions(explode(" ", $f->extensions));
			$ul->setMaxFiles($f->maxFiles);
			$ul->setOverwrite(true);
			$ul->setDestinationPath($f->destinationPath);
			$ul->setExtractArchives(false);
			$ul->setLowercase(false);

			$files = $ul->execute();

			if (count($files)) {
				// $file = $f->destinationPath . reset($files);
				$this->message("Font uploaded to templates/fonts");
			}

			//delete marked fonts
			$wrapper->processInput($this->input->post);
			$files = $wrapper->get('deleteFonts')->value;
			$filesArray = array_filter(explode(" ", $files));

			foreach ($filesArray as $file) {
				if (file_exists($filePath . $file)) {
					// // // bd('delete');
					// // // bd($filePath . $file);
					unlink($filePath . $file);
					$this->message($file . ' deleted');
				}
			}
		}

		//fallback fonts
		$f = $this('modules')->get('InputfieldText');
		$f->attr('name', 'fallbackFonts');
		$f->label = $this->_('Fallback Fonts');
		$f->icon = 'font';
		$f->description = "Comma-separated list of font names to be used when a font isn't available";
		$f->columnWidth('100');
		$fieldset1->append($f);

		//------------------------------------------

		// colors
		$fieldset1 = $this->modules->get('InputfieldFieldset');
		$fieldset1->label = $this->_('Colors');
		$fieldset1->description = $this->_('Comma seperated list of hex values, eg. #FFFFFF, #000000');
		// $fieldset1->collapsed(1);
		$fieldset1->icon = 'adjust'; //or tint?
		// $fieldset1->themeOffset = 1;

		$f = $this('modules')->get('InputfieldText');
		$f->attr('name', 'fontColor');
		$f->attr('value', $this->fontColor);
		$f->label = $this->_('Default Font Colors');
		$f->columnWidth('50');
		$fieldset1->append($f);

		$f = $this('modules')->get('InputfieldText');
		$f->attr('name', 'bgColor');
		$f->attr('value', $this->bgColor);
		$f->label = $this->_('Default Background Colors');
		$f->columnWidth('50');
		$fieldset1->append($f);
		$fieldset->append($fieldset1);

		$wrapper->append($fieldset);

		// ------------------------------------------------------------
		//INLINE EDITOR
		$fieldset = $this->wire('modules')->get('InputfieldFieldset');
		$fieldset->label = $this->_('Inline Editor');
		$fieldset->attr('id+name', 'inlineSettings');
		$fieldset->collapsed(in_array($fieldset->name, $collapsed) ? 1 : 0);
		$fieldset->icon = 'edit';
		$fieldset->themeOffset = 1;
		$wrapper->add($fieldset);

		// enable/disable frontend
		$f = $this('modules')->get('InputfieldCheckbox');
		$f->attr('name', 'inlineEditorFrontDisable');
		$f->label = ' ';
		$f->checkboxLabel = 'Disable inline editor for the frontend';
		$f->attr('value', $this->inlineEditorFrontDisable);
		if ($this->inlineEditorFrontDisable) {
			$f->attr('checked', 'checked');
		}
		$fieldset->append($f);

		//get core front end edit config for text fields
		$coreInputfields = $this->modules->getModuleConfigInputfields('PageFrontEdit');
		$coreInputfields = $coreInputfields->inlineEditFields;
		$coreInputfields->label = 'Text fields';
		$coreInputfields->icon = 'font';
		$coreInputfields->description = 'These fields will become editable on the front-end/iframe, directly in the page, simply by checking the boxes below.';
		$fieldset->add($coreInputfields);

		//NEW placeholder text per field
		$PageFrontEditFields = $this->modules->getConfig('PageFrontEdit')['inlineEditFields'];
		if ($PageFrontEditFields && count($PageFrontEditFields)) {
			$fieldsetSub = $this->wire('modules')->get('InputfieldFieldset');
			$fieldsetSub->label = $this->_('Placeholder text for empty text fields');
			$fieldsetSub->description = 'Without a placeholder empty text fields will not be visible';
			$fieldsetSub->attr('id+name', 'placeholderTextWrapper');
			$fieldsetSub->collapsed = 3;
			$fieldset->add($fieldsetSub);

			foreach ($PageFrontEditFields as $fId) {
				$PageFrontEditField = $this->fields->get($fId);
				if (!$PageFrontEditField) continue;
				$f = $this('modules')->get('InputfieldText');
				$f->attr('name', 'placeholderText_' . $fId);
				$f->label = $PageFrontEditField->name;
				$f->columnWidth('33');
				$fieldsetSub->append($f);
			}
		}

		//file fields API call help
		$preStyle = " style='padding:10px;border:1px dashed #ccc'";
		$sanitizer = $this->wire('sanitizer');
		$f = $this->modules->get('InputfieldMarkup');
		$f->collapsed = true;
		$f->label = $this->_('File fields');
		$f->icon = 'image';
		$f->description = 'For the default blocks the inline file uploader is enabled by default. To enable the uploader for your templates, place an image or video inside a <pg-edit> tag.';
		$f->description .= " [More information](https://page-grid.com/docs/)";
		$f->notes = 'Only single value image/file fields are supported.';
		$f->value = "<pre$preStyle>" . $sanitizer->entities(
			"<pg-edit page='2145' field='image'>\n <img src='url/example.jpg'>\n</pg-edit>"
		) . "</pre>";
		$f->value .= '<style>.asmSelect {min-width:200px;}</style>'; // fix all asmSelects added here as a lazy fix.
		$fieldset->append($f);

		//custom styles
		$f = $this('modules')->get('InputfieldTextArea');
		$f->attr('name', 'customStyles');
		$f->label = $this->_('CSS Code');
		$f->description = $this->_('You can add custom CSS code here. If your page has a PAGEGRID field, it will be loaded automatically');
		$f->rows = 4;
		$f->collapsed(in_array($f->name, $collapsed) || $this->customStyles == '' ? 1 : 0);
		$f->attr("onclick", "this.style.height = ''; this.style.height = this.scrollHeight +'px'"); // auto resize height based on content
		$f->attr("oninput", "this.style.height = ''; this.style.height = this.scrollHeight +'px'"); // auto resize height based on content
		$f->attr('style', 'font-family: monospace;');
		$f->columnWidth('100');
		$f->themeOffset = 1;
		$f->icon = 'code';
		$f->value($this->sanitizer->text($this->customStyles));
		$wrapper->append($f);

		//custom js
		$f = $this('modules')->get('InputfieldTextArea');
		$f->attr('name', 'customScript');
		$f->label = $this->_('Javascript Code');
		$f->description = $this->_('You can add custom Javascript code here. If your page has a PAGEGRID field, it will be loaded on the frontend automatically. If you need to add Javascript code to the backend as well you have to load it inside your template file.');
		$f->rows = 4;
		$f->collapsed(in_array($f->name, $collapsed) || $this->customScript == '' ? 1 : 0);
		$f->attr("onclick", "this.style.height = ''; this.style.height = this.scrollHeight +'px'"); // auto resize height based on content
		$f->attr("oninput", "this.style.height = ''; this.style.height = this.scrollHeight +'px'"); // auto resize height based on content
		$f->attr('style', 'font-family: monospace;');
		$f->columnWidth('100');
		$f->themeOffset = 1;
		$f->icon = 'code';
		$f->value($this->sanitizer->text($this->customScript));
		$wrapper->append($f);

		//custom uninstall even if field still exists
		$f = $this->fields->get('type=FieldtypePageGrid');

		if ($f && $f->id) {
			$f = $this('modules')->get('InputfieldCheckbox');
			$f->attr('name', 'deleteFields');
			$f->label = $this->_('Uninstall');
			$f->checkboxLabel = 'Delete Fields';
			$f->icon('times-circle');
			$f->collapsed = 1;
			$f->description = $this->_('Before you can uninstall the module you have to delete all PAGEGRID fields.');
			$wrapper->append($f);
		}

		//new remove fields for non-superusers if LimitedModuleEdit is installed
		if (!$this->user->isSuperuser()) {
			$wrapper->remove('lKey');
			$wrapper->remove('interface');
			$wrapper->remove('plugins');
			$wrapper->remove('inlineSettings');
			$wrapper->remove('deleteFields');

			foreach ($this->fields->find('type=FieldtypePageGrid') as $pgf) {
				$wrapper->remove('fieldSettings_' . $pgfId);
			}

			//hide module info
			$f = $this->modules->get('InputfieldMarkup');
			$f->collapsed = 3; //hidden
			$f->value = '<style>#ModuleInfo{display:none!important;}</style>';
			$wrapper->append($f);
		}
		//END new remove fields for non-superusers if LimitedModuleEdit is installed

		return $wrapper;
	}

	public function getBlockSettings($field) {

		$info = $this->modules->getModuleInfoVerbose('PageGridBlocks');
		$downloaded = $this->modules->get('PageGridBlocks') ? 1 : 0;
		$installed = $this->modules->isInstalled('PageGridBlocks');
		$downloadLink = $this->config->urls->admin . 'module/edit?name=FieldtypePageGrid&collapse_info=1&downloadBlocks';

		//download block module if get var is set
		if (isset($_GET['downloadBlocks'])) {
			$this->downloadModule('PageGridBlocks');
			$downloaded = 1;
			// if (!$installed) $this->modules->install('PageGridBlocks');
		}

		$value = $this['template_id_' . $field->id];
		if (!is_array($value)) $value = $value ? array($value) : array();

		/** @var InputfieldAsmSelect $f */
		$f = $this->wire('modules')->get('InputfieldAsmSelect');
		$f->attr('name', 'template_id');
		$f->label = $this->_('Block templates');
		$f->icon = 'cubes';
		if (!$installed && !$downloaded) $f->addClass('links-target-self', 'wrapClass');

		// $f->required = true;
		$f->description = $this->_('The block template files must be placed in **site/templates/blocks/** folder. [Learn more](https://page-grid.com/docs/#/developer/blocks?id=create-a-new-block)'); // Templates selection description

		if (!$installed && !$downloaded) $f->notes .= 'Alternatively you can also download our [block modules](' . $downloadLink . ')';

		$path = wire('config')->paths->templates . 'blocks/';
		$files = glob($path . '*.php');

		foreach ($files as $file) {
			$fileName = str_replace($path, '', $file);
			$templateName = str_replace('.php', '', $fileName);
			$templateName = $this->sanitizer->filename($templateName);
			$t = $this->templates->get($templateName);
			$attrs = [];

			if ((!$t || !$t->id) && in_array($templateName, $value)) {
				$t = $this->createTemplate($templateName);
			}

			if ($t && $t->flags & Template::flagSystem) continue;
			if ($t && $t->label) $attrs['data-desc'] = $t->label;
			if ($t && $t->icon) $attrs['data-handle'] = wireIconMarkup($t->icon, 'fw');

			$templateId = $templateName;
			if ($t && $t->id) $templateId = $t->id;
			$f->addOption($templateId, $templateName, $attrs);
		}

		//add module blocks
		$path = wire('config')->paths->siteModules . 'PageGridBlocks/blocks/';
		$files = glob($path . '*.php');

		//set section 
		if ($downloaded) {
			$f->addOption('--------- Modules ---------');
			$f->addOptionAttributes('--------- Modules ---------', ['disabled' => 'disabled']);
		}

		foreach ($files as $file) {
			$fileName = str_replace($path, '', $file);
			$templateName = str_replace('.php', '', $fileName);
			$templateName = $this->sanitizer->filename($templateName);
			$className = str_replace('pg_', '', $templateName);
			$className = str_replace('_', '', ucwords($className, '_'));
			$className = 'Blocks' . $className;
			$installedBlock = $this->modules->isInstalled($className);
			$info = $this->modules->getModuleInfoVerbose($className);

			//check if module exists
			if (!$info['name']) continue;

			//check if modules are installed
			if (!$installedBlock && in_array($templateName, $value)) $this->modules->install($className);

			$attrs = [];
			$attrs['data-desc'] = $info['title'];
			$attrs['data-handle'] = wireIconMarkup($info['icon'], 'fw');
			$templateId = $templateName;
			if ($this->templates->get($templateName)) $templateId = $this->templates->get($templateName)->id;
			$f->addOption($templateId, $templateName, $attrs);
		}

		// $this['template_id_' . $field->id] = $value;
		$f->attr('value', $value);
		return $f;
	}

	public function createTemplate($templateName = '') {
		if (!$templateName) return 0;
		if ($this->templates->get($templateName)) return 0;

		//create template
		$titleField = $this->fields->get('title');

		// fieldgroup for template
		$fg = new Fieldgroup();
		$fg->name = $templateName;
		$fg->add($titleField);
		$fg->save();

		$t = new Template();
		$t->name = $templateName;
		$t->fieldgroup = $fg; // add the field group
		// $t->icon = 'th';
		$t->tags = 'Blocks';
		$t->save();

		return $t;
	}

	//helper to download and install missing modules
	public function downloadModule($name, $update = false) {

		if ($this->modules->get($name)) return;

		// if ($this->session->download_modules != '1') return false;

		$name = $this->wire('sanitizer')->name($name);
		// $info = self::getModuleInfo();

		$redirectURL = $update ? "./edit?name=$name" : "./";
		$className = $name;
		$url = trim($this->wire('config')->moduleServiceURL, '/') . "/$className/?apikey=" . $this->wire('sanitizer')->name($this->wire('config')->moduleServiceKey);

		$http = new WireHttp();
		$data = $http->get($url);

		if (empty($data)) {
			$this->error($this->_('Error retrieving data from web service URL') . ' - ' . $http->getError());
			return $this->session->redirect($redirectURL);
		}
		$data = json_decode($data, true);
		if (empty($data)) {
			$this->error($this->_('Error decoding JSON from web service'));
			return $this->session->redirect($redirectURL);
		}
		if ($data['status'] == 'success') {

			$installed = $this->modules->isInstalled($className) ? $this->modules->getModuleInfoVerbose($className) : null;

			$destinationDir = $this->wire('config')->paths->siteModules . $className . '/';
			require_once(wire('config')->paths->modules . 'Process/ProcessModule/ProcessModuleInstall.php');
			$install = new ProcessModuleInstall();

			$completedDir = $install->downloadModule($data['download_url'], $destinationDir);
			if ($completedDir) {
				return true;
			}
		}
	}
}
