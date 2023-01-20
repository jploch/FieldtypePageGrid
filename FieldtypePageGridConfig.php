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
		$input->customStyles = strip_tags($input->customStyles, '');
		$event->arguments(0, $input);
	}

	public function setDefaults(HookEvent $event) {
		//set checkboxes to default
		if ($this->modules->get('FieldtypePageGrid')->interfaceDefault) {
			// // bd('set defaults');
			$data = $this->modules->getConfig('FieldtypePageGrid');
			$dataOld = $data;
			$data['interface'] = array('hideFieldTitle', 'hidePageHeadline', 'hideTitleField', 'hideTabs');
			$data['interfaceDefault'] = 0;

			if ($dataOld !== $data) {
				$this->modules->saveConfig('FieldtypePageGrid', $data);
			}
		}
	}

	//this function runs on module save to keep PageFrontEdit and FieldtypePageGrid config is sync
	public function saveConfig(HookEvent $event) {

		$classname = $event->arguments[0];
		$configCore = $this->modules->getConfig('PageFrontEdit');
		$config = $this->modules->getConfig('FieldtypePageGrid');

		 if( isset($config['inlineEditFields']) == 0 ) {
			$config['inlineEditFields'] = array();
		 }

		 if( isset($configCore['inlineEditFields']) == 0 ) {
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
			'inlineEditFieldsUpload' => array(), // file fields that are inline-editable
			'placeholderText' => 'Click twice to edit this text', // placeholder text
			'interfaceDefault' => 1,
			'interface' => array(
				'hideFieldTitle',
				'hidePageHeadline',
				'hideTitleField',
				'hideTabs',
			),
			'customStyles' => '',
			'fontColor' => '',
			'bgColor' => '',
			'fontPrivacy' => 1,
			'fallbackFonts' => 'Helvetica, Arial, sans-serif',
			'stylePanel' => 1,
		);
	}

	public function getInputfields() {

		//add js for font uploader and to save collapsed states
		$this->config->scripts->add($this->config->urls->FieldtypePageGrid . "FieldtypePageGridConfig.js'");

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
			$description = "Please buy a license before you launch your project. For test and staging domains you don’t need a license.";
		}

		if ($valid == 3) {
			$validateNote = "License is already in use!";
			$description = "Please buy a license before you launch your project.";
		}

		$f = $this('modules')->get('InputfieldText');
		$f->name = 'lKey';
		$f->label = $validateNote;
		$f->description = $description;
		$f->addClass($statusClass, 'wrapClass');
		$f->icon = 'key';
		$f->columnWidth('100');
		$f->collapsed = $collapsed;

		// don't show lkey in modal context
		if (isset($_GET['modal']) == 0) {
			$wrapper->add($f);
		}

		//-------------------------------------------------------
		//hidden field to store collapsed states as comma seperated string
		$collapsedString = $this->collapsedState ? $this->collapsedState : 'interface,stylePanelSettings,inlineSettings,customStyles';
		$collapsed = explode(',', $collapsedString);

		$f = $this('modules')->get('InputfieldHidden');
		$f->attr('name', 'collapsedState');
		$f->columnWidth('100');
		$f->attr('value', $this->collapsedState); // set value back to empty
		$wrapper->append($f);

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
		// $f->addOption('setDefault', 'Default'); //set on first run to have default checked
		$f->addClass('pg-table-auto', 'wrapClass');
		$f->addOption('hideFieldTitle', 'Hide Field Title | [span.detail] Hide field title in page editor. [/span]');
		$f->addOption('hidePageHeadline', 'Hide Page Headline | [span.detail] Hide page headline in page editor and add it to breadcrumb. [/span]');
		$f->addOption('hideTitleField', 'Hide Page Title Field | [span.detail] Hide page title field in page editor and add it to settings tab. [/span]');
		$f->addOption('hideTabs', 'Hide Tabs | [span.detail] Hide tabs and add a settings icon to show them. [/span]');
		// $f->val($this->interface);
		// $f->attr('checked', 'checked');
		$wrapper->append($f);

		// set interface on AdminThemeCanvas
		if ($this->input->requestMethod('POST')) {
			if ($this->modules->isInstalled('AdminThemeCanvas')) {
				$data = $this->modules->getConfig('AdminThemeCanvas');
				foreach ($this->interface as $interface) {
					if ($interface == 'hidePageHeadline') {
						$data['breadcrumb'] = 'breadcrumb-with-title';
					}
					if ($interface == 'hideTitleField') {
						$data['hide-title'] = 1;
					}
					$this->modules->saveConfig('AdminThemeCanvas', $data);
				}
			}
		}
		//-------------------------------------------------------
		// Field settings for convenience
		wire('modules')->get('JqueryUI')->use('modal');

		$f = $this->modules->get('InputfieldMarkup');
		$f->attr('id+name', 'fieldSettings');
		$f->collapsed = 1;
		$f->themeOffset = 1;
		$f->label = 'Field Settings';
		$f->icon = 'cube';
		$f->value = '<table class="AdminDataList pw-no-select uk-table uk-table-divider uk-table-small" style="margin-bottom:0;">';
		$f->value .= '<tr><th>Name</th><th>Templates</th></tr>';
		$fName = '';

		foreach ($this->fields as $pgf) {
			if ($pgf->type instanceof FieldtypePageGrid) {
				$fLink =  $pgf->editUrl() . '&modal=1&';
				$fName = $pgf->label ? $pgf->label : $pgf->name;
				$tName = '';
				foreach ($this->templates as $t) {
					if ($t->hasField($pgf)) {
						$tName .= $t->name . ' ';
					}
				}
				$f->value .= '<tr>';
				$f->value .= '<td><i class="fa fw fa-cog" title="fa-cog"></i>&nbsp; <a href="' . $fLink . '" data-href="' . $fLink . '" class="pw-modal">' . $fName . '</a></td>';
				$f->value .= '<td>' . $tName . '</td>';
				$f->value .= '</tr>';
			}
		}

		if ($fName == '') {
			$fLink = $this->config->urls->admin . 'setup/field/add?&modal=1&';
			$f->collapsed = 0;
			$f->description = 'To use PAGEGRID you need to create a field first.';
			$f->value = '<a href="' . $fLink . '" data-href="' . $fLink . '" class="pw-modal">Create Field</a>';
		}

		$f->value .= '</table>';
		$wrapper->add($f);

		//-------------------------------------------------------

		//STYLE PANEL
		$fieldset = $this->modules->get('InputfieldFieldset');
		$fieldset->attr('id+name', 'stylePanelSettings');
		$fieldset->label = $this->_('Style Panel');
		$fieldset->collapsed(in_array($fieldset->name, $collapsed) ? 1 : 0);
		$fieldset->icon = 'paint-brush';
		// $fieldset->themeOffset = 1;
		$wrapper->add($fieldset);

		// google font DVGO option
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
		$f->value .= '<style>html #notices {display:none;}</style>'; // hide false upload error and use js to show relevant ones
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
		$fieldset->description = 'These fields will become editable on the front-end/iframe, directly in the page, simply by checking the boxes below.';
		$fieldset->icon = 'edit';
		$fieldset->themeOffset = 1;
		$wrapper->add($fieldset);

		//get core front end edit config for text fields
		$coreInputfields = $this->modules->getModuleConfigInputfields('PageFrontEdit');
		$coreInputfields = $coreInputfields->inlineEditFields;
		$coreInputfields->label = 'Text fields';
		$coreInputfields->icon = 'font';
		$coreInputfields->description = '';
		$fieldset->add($coreInputfields);

		//image upload fields
		$fields = array();

		foreach ($this->wire('fields') as $field) {
			if (!$field->type instanceof FieldtypeFile) continue;
			$fields[$field->name] = $field;
		}

		$f = $this->wire('modules')->get('InputfieldCheckboxes');
		$f->name = 'inlineEditFieldsUpload';
		$f->icon = 'image';
		$f->label = $this->_('File fields');
		$f->optionColumns = 3;
		foreach ($fields as $field) {
			$label = $field->name;
			if ($label == 'title') $label .= ' ' . $this->_('(not recommended)');
			$f->addOption($field->id, $label);
		}

		$fieldset->add($f);

		//END image upload fields

		//default text for new items
		$f = $this('modules')->get('InputfieldText');
		$f->attr('name', 'placeholderText');
		$f->label = $this->_('Placeholder text for text fields');
		$f->description = 'Without a placeholder empty text fields will not work (default: "Click twice to edit this text")';
		$f->columnWidth('100');
		$fieldset->append($f);
		//END default text for new items

		// ------------------------------------------------------------------------------

		// CSS SETTINGS ---------------------------------------------------------------

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


		return $wrapper;
	}
}
