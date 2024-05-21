<?php

namespace ProcessWire;

// STYLEPANEL saved as JSON
// prefix name with "data" to prevent save
// prefix data with "pg-" to save as non css 

//render custom tabs, just use uikit classes
$tabs = '
<ul uk-tab="" class="WireTabs uk-tab">
<li id="tab-styles" class="uk-active"><a href="#">Styles</a></li>
<li id="tab-interactions"><a href="#">Interactions</a></li>
</ul>';

// style wrapper
$wrapper = new InputfieldWrapper();
$wrapper->attr('name', 'gridStyleTab');
$wrapper->attr('title', 'Styles');
$wrapper->attr('class', 'WireTab');
$wrapper->addClass('WireTab');

// Add fields -> field name = css rule name
//show hidden elements
$field = $this->modules->get('InputfieldCheckbox');
$field->set('label', __('Show hidden blocks'));
$field->set('name', __('data-show'));
$field->set('value', 'none');
//$field->addClass( 'padding-top', 'wrapClass' );
$field->addClass('hide-label', 'headerClass');
$field->columnWidth = 100;
$wrapper->append($field);

// Selector
$fieldset = $this->modules->get('InputfieldFieldset');
$fieldset->label = 'Selector';
$fieldset->name = 'pg_settings_selector';
$fieldset->addClass('hide-label', 'headerClass');
$fieldset->addClass('pg-sticky', 'wrapClass');
$wrapper->append($fieldset);

// change tag name
$field = $this->modules->get('InputfieldSelect');
$field->name = "data-tagname";
$field->label = $this->_("Tag Name");
// $field->addOption("h1");
// $field->addOption("h2");
// $field->addOption("h3");
// $field->addOption("h4");
// $field->addOption("h5");
// $field->addOption("h6");
// $field->addOption("p");
// $field->set('value', 'h2');
$field->addClass('label-left', 'wrapClass');
//$field->addClass( 'hide-label', 'headerClass' );
$field->columnWidth = 100;
$fieldset->append($field);

// Classes
if ($this->modules->get('InputfieldTextTags')) {
  $field = $this->modules->get('InputfieldTextTags');
  $field->allowUserTags = true;
  $field->delimiter = 's';
  $field->set('label', __('Selector'));
  $field->addClass('label-left', 'wrapClass');
  $field->set('name', __('data-classes'));
  $field->attr('placeholder', 'Add class…');
  $field->columnWidth = 100;
  $fieldset->append($field);
}

// LAYOUT
$fieldset = $this->modules->get('InputfieldFieldset');
$fieldset->label = 'Layout';
$fieldset->name = 'layout';
$wrapper->append($fieldset);

//PARENT
// display
$field = $this->modules->get('InputfieldSelect');
$field->set('name', __('display'));
$field->label = $this->_("Display");
$field->addOption("block", "block");
$field->addOption("inline", "inline");
$field->addOption("inline-block", "inline-block");
$field->addOption("grid", "grid");
$field->addOption("flex", "flex");
$field->addOption("inline-flex", "inline-flex");
$field->addOption("none", "none");
$field->set('block', 'block');
//$field->addClass( 'pg-select-btn');
$field->addClass('label-left', 'wrapClass');
//$field->columnWidth = 100;
$fieldset->append($field);

// GRID PARENT
$fieldsetsub = $this->modules->get('InputfieldFieldset');
$fieldsetsub->label = ' ';
$fieldsetsub->name = 'pg_settings_grid';
$fieldsetsub->addClass('label-left', 'wrapClass');
$fieldsetsub->addClass('fieldsetsub', 'wrapClass');
$fieldset->append($fieldsetsub);

// Col
$field = $this->modules->get('InputfieldInteger');
$field->inputType = "number";
$field->set('notes', __('Columns'));
$field->set('name', __('grid-template-columns'));
$field->addClass('hide-label', 'headerClass');
$field->min = 1;
$field->columnWidth = 25;
$fieldsetsub->append($field);

$field = $this->modules->get('InputfieldInteger');
$field->inputType = "number";
$field->set('label', __('Gap'));
$field->set('notes',  $field->label);
$field->set('name', __('column-gap'));
$field->attr('unit', 'px');
$field->addClass('hide-label', 'headerClass');
$field->columnWidth = 25;
$fieldsetsub->append($field);

// Row
$field = $this->modules->get('InputfieldInteger');
$field->inputType = "number";
$field->set('label', __(' '));
$field->set('notes', __('Rows'));
$field->set('name', __('grid-template-rows'));
$field->addClass('hide-label', 'headerClass');
$field->columnWidth = 25;
$fieldsetsub->append($field);

$field = $this->modules->get('InputfieldInteger');
$field->inputType = "number";
$field->set('label', __('Gap'));
$field->set('notes',  $field->label);
$field->set('name', __('row-gap'));
$field->attr('unit', 'px');
$field->addClass('hide-label', 'headerClass');
$field->columnWidth = 25;
$fieldsetsub->append($field);

//grid justify-items (container)
$field = $this->modules->get('InputfieldSelect');
$field->name = "justify-items";
$field->label = $this->_("Align Children H");
$field->addOption("normal", "Normal");
$field->addOption("stretch", "Stretch");
$field->addOption("start", "Left");
$field->addOption("center", "Center");
$field->addOption("end", "Right");
$field->set('value', 'normal');
$field->set('notes',  $field->label);
$field->addClass('hide-label', 'headerClass');
$field->columnWidth = 50;
$fieldsetsub->append($field);

//grid align-items (container)
$field = $this->modules->get('InputfieldSelect');
$field->name = "align-items";
$field->label = $this->_("Align Children V");
$field->addOption("normal", "Normal");
$field->addOption("stretch", "Stretch");
$field->addOption("start", "Top");
$field->addOption("center", "Center");
$field->addOption("end", "Bottom");
$field->addOption("baseline", "Baseline");
$field->set('value', 'normal');
$field->set('notes',  $field->label);
$field->addClass('hide-label', 'headerClass');
$field->columnWidth = 50;
$fieldsetsub->append($field);

//grid align-items (container)
$field = $this->modules->get('InputfieldSelect');
$field->name = "pg-children-placement";
$field->label = "Children Placement";
$field->addOption("manual", "Manual");
$field->addOption("auto-row", "Auto Row");
$field->addOption("auto-column", "Auto Column");
$field->addOption("auto", "Auto");
$field->set('value', 'Manual');
$field->set('notes',  $field->label);
$field->addClass('hide-label', 'headerClass');
$field->columnWidth = 50;
$fieldsetsub->append($field);

$field = $this->modules->get('InputfieldInteger');
$field->inputType = "number";
$field->name = "pg-children-column-end";
$field->attr('placeholder', 'auto');
$field->label = "Colspan";
$field->set('notes',  $field->label);
$field->addClass('hide-label', 'headerClass');
$field->columnWidth = 25;
$fieldsetsub->append($field);

$field = $this->modules->get('InputfieldInteger');
$field->inputType = "number";
$field->name = "pg-children-row-end";
$field->attr('placeholder', 'auto');
$field->label = "Rowspan";
$field->set('notes',  $field->label);
$field->addClass('hide-label', 'headerClass');
$field->columnWidth = 25;
$fieldsetsub->append($field);

// $field = $this->modules->get('InputfieldMarkup');
// $field->addClass('hide', 'wrapClass');
// $field->addClass('hide-label', 'headerClass');
// $field->name = "data-children-placement-button";
// $field->value = '<button class="data-children-placement-button-cancel ui-button ui-widget ui-corner-all ui-state-default ui-priority-secondary">Cancel</button>';
// $field->value .= '<button class="data-children-placement-button-submit ui-button ui-widget ui-corner-all ui-state-default">Convert children</button>';
// $field->attr('title', 'this will chnage the postion of all the children of this container!');
// $field->columnWidth = 100;
// $fieldsetsub->append($field);

// END Grid Parent

//FLEXBOX PARENT
$fieldsetsub = $this->modules->get('InputfieldFieldset');
$fieldsetsub->label = ' ';
$fieldsetsub->name = 'pg_settings_flex';
$fieldsetsub->addClass('label-left', 'wrapClass');
$fieldsetsub->addClass('fieldsetsub', 'wrapClass');
$fieldset->append($fieldsetsub);

//flex-direction
$field = $this->modules->get('InputfieldSelect');
$field->name = "flex-direction";
$field->label = $this->_("Direction");
$field->addOption("row");
$field->addOption("column");
$field->set('value', 'row');
$field->set('notes', $field->label);
$field->addClass('hide-label', 'headerClass');
$field->columnWidth = 50;
$fieldsetsub->append($field);

//flex-wrap
$field = $this->modules->get('InputfieldSelect');
$field->name = "flex-wrap";
$field->label = "Wrap Children";
$field->addOption("nowrap");
$field->addOption("wrap");
$field->addOption("wrap-reverse");
$field->set('value', 'nowrap');
$field->set('notes', $field->label);
$field->addClass('hide-label', 'headerClass');
$field->columnWidth = 50;
$fieldsetsub->append($field);

//flex align-items (container)
$field = $this->modules->get('InputfieldSelect');
$field->name = "align-items";
$field->label = $this->_("Align Children");
$field->addOption("normal", "Normal");
$field->addOption("stretch", "Stretch");
$field->addOption("flex-start", "Left");
$field->addOption("center", "Center");
$field->addOption("flex-end", "Right");
$field->set('value', 'normal');
$field->set('notes',  $field->label);
$field->addClass('hide-label', 'headerClass');
$field->columnWidth = 50;
$fieldsetsub->append($field);

//justify-content (container)
$field = $this->modules->get('InputfieldSelect');
$field->name = "justify-content";
$field->label = $this->_("Justify Children");
$field->addOption("normal");
$field->addOption("flex-start");
$field->addOption("center");
$field->addOption("flex-end");
$field->addOption("space-between");
$field->addOption("space-around");
$field->set('value', 'normal');
$field->set('notes',  $field->label);
$field->addClass('hide-label', 'headerClass');
$field->columnWidth = 50;
$fieldsetsub->append($field);

$field = $this->modules->get('InputfieldInteger');
$field->inputType = "number";
$field->set('label', __('Row Gap'));
$field->set('notes',  $field->label);
$field->set('name', __('row-gap'));
$field->attr('unit', 'px');
$field->addClass('hide-label', 'headerClass');
$field->columnWidth = 50;
$fieldsetsub->append($field);

$field = $this->modules->get('InputfieldInteger');
$field->inputType = "number";
$field->set('label', __('Column Gap'));
$field->set('notes',  $field->label);
$field->set('name', __('column-gap'));
$field->attr('unit', 'px');
$field->addClass('hide-label', 'headerClass');
$field->columnWidth = 50;
$fieldsetsub->append($field);

// END Flex

//GRID ITEM
$fieldsetsub = $this->modules->get('InputfieldFieldset');
$fieldsetsub->label = 'Layout';
$fieldsetsub->name = 'pg_settings_griditem';
$fieldsetsub->addClass('label-left', 'wrapClass');
$fieldsetsub->addClass('fieldsetsub', 'wrapClass');
$fieldset->append($fieldsetsub);

//placement
$field = $this->modules->get('InputfieldSelect');
$field->name = "data_item_placement";
$field->label = $this->_("Layout");
$field->addOption("Manual");
$field->addOption("Auto Row");
$field->addOption("Auto Column");
$field->addOption("Auto");
$field->set('value', 'Manual');
// $field->set('notes',  $field->label);
$field->addClass('hide-label', 'headerClass');
$field->columnWidth = 100;
$fieldsetsub->append($field);

// Col, Row
$field = $this->modules->get('InputfieldInteger');
$field->inputType = "number";
$field->set('label', __('Colspan'));
$field->set('name', __('grid-column-end'));
//       $field->set( 'icon', __( 'arrows-h' ) );
$field->set('notes',  $field->label);
$field->addClass('hide-label', 'headerClass');
$field->columnWidth = 25;
$fieldsetsub->append($field);

$field = $this->modules->get('InputfieldInteger');
$field->inputType = "number";
$field->set('label', __('Rowspan'));
$field->set('name', __('grid-row-end'));
//       $field->set( 'icon', __( 'arrows-v' ) );
$field->set('notes',  $field->label);
$field->addClass('hide-label', 'headerClass');
$field->columnWidth = 25;
$fieldsetsub->append($field);

$field = $this->modules->get('InputfieldInteger');
$field->inputType = "number";
$field->set('label', __('Column'));
$field->set('name', __('grid-column-start'));
$field->set('notes',  $field->label);
$field->addClass('hide-label', 'headerClass');
$field->min = 1;
//$field->max = 99;
//       $field->set( 'icon', __( 'long-arrow-right' ) );
$field->columnWidth = 25;
$fieldsetsub->append($field);

$field = $this->modules->get('InputfieldInteger');
$field->inputType = "number";
$field->set('label', __('Row'));
$field->set('name', __('grid-row-start'));
//      $field->set( 'icon', __( 'long-arrow-down' ) );
$field->set('notes',  $field->label);
$field->addClass('hide-label', 'headerClass');
$field->columnWidth = 25;
$fieldsetsub->append($field);

//prevent colapsing
$field = $this->modules->get('InputfieldMarkup');
$field->label = ' ';
$field->set('name', __('pg-settings-clear'));
$field->addClass('hide-label', 'headerClass');
$field->value = '<div></div>';
$field->columnWidth = 100;
$fieldsetsub->append($field);

// align v
$field = $this->modules->get('InputfieldSelect');
$field->name = "align-self";
$field->label = $this->_("Align V");
$field->addOption("auto", "Auto");
$field->addOption("start", "Top");
$field->addOption("center", "Center");
$field->addOption("end", "Bottom");
$field->set('notes',  $field->label);
$field->addClass('hide-label', 'headerClass');
$field->columnWidth = 50;
$fieldsetsub->append($field);

// align h
$field = $this->modules->get('InputfieldSelect');
$field->name = "justify-self";
$field->label = $this->_("Align H");
$field->addOption("auto", "Auto");
$field->addOption("stretch", "stretch");
$field->addOption("start", "Left");
$field->addOption("center", "Center");
$field->addOption("end", "Right");
$field->set('notes',  $field->label);
$field->addClass('hide-label', 'headerClass');
$field->columnWidth = 50;
$fieldsetsub->append($field);
// END grid item

//FLEX ITEM
$fieldsetsub = $this->modules->get('InputfieldFieldset');
$fieldsetsub->label = 'Layout';
$fieldsetsub->name = 'pg_settings_flexitem';
$fieldsetsub->addClass('label-left', 'wrapClass');
$fieldsetsub->addClass('fieldsetsub', 'wrapClass');
$fieldset->append($fieldsetsub);

// align v
$field = $this->modules->get('InputfieldSelect');
$field->name = "align-self";
$field->label = $this->_("Align V");
$field->addOption("auto", "Auto");
$field->addOption("flex-start", "Top");
$field->addOption("center", "Center");
$field->addOption("flex-end", "Bottom");
$field->addOption("baseline", "Baseline");
$field->set('notes',  $field->label);
$field->addClass('hide-label', 'headerClass');
$field->columnWidth = 50;
$fieldsetsub->append($field);

// flex-grow / flex-shrink (grow|shrink|basis)
$field = $this->modules->get('InputfieldSelect');
$field->name = "flex";
$field->label = $this->_("Sizing");
$field->addOption("0 1 auto", "Shrink");
$field->addOption("1 0 auto", "Grow");
$field->addOption("0 0 auto", "None");
$field->set('notes',  $field->label);
$field->addClass('hide-label', 'headerClass');
$field->columnWidth = 50;
$fieldsetsub->append($field);

// z-index
$field = $this->modules->get('InputfieldInteger');
$field->inputType = "number";
$field->set('label', __('Z-Index'));
$field->set('name', __('z-index'));
$field->attr('placeholder', 'auto');
$field->addClass('label-left', 'wrapClass');
$field->columnWidth = 100;
$field->min = 0;
$field->max = 99;
$fieldset->append($field);

//Advanced
$fieldsetAdvanced = $this->modules->get('InputfieldFieldset');
$fieldsetAdvanced->label = 'More layout options';
$fieldsetAdvanced->name = 'pg_settings_advanced';
$fieldsetAdvanced->collapsed = 1;
$fieldset->append($fieldsetAdvanced);

// position
$field = $this->modules->get('InputfieldSelect');
$field->name = "position";
$field->label = $this->_("Position");
$field->addOption("relative");
$field->addOption("static");
$field->addOption("absolute");
$field->addOption("fixed");
$field->addOption("sticky");
$field->set('value', 'relative');
$field->addClass('label-left', 'wrapClass');
//$field->addClass( 'hide-label', 'headerClass' );
$field->columnWidth = 100;
$fieldsetAdvanced->append($field);

// position wrapper
$fieldsetSub = $this->modules->get('InputfieldFieldset');
$fieldsetSub->name = 'position-wrapper';
$fieldsetSub->set('label', '');
$fieldsetSub->addClass('hide-label', 'headerClass');
$fieldsetSub->addClass('combo-wrapper');
$fieldsetSub->columnWidth = 100;
$fieldsetAdvanced->append($fieldsetSub);

// position-left
$field = createUnitField('left', ' ', 50, 'chevron-left', 'auto');
$field->addClass('padding-bottom', 'wrapClass');
$fieldsetSub->append($field);

// position-right
$field = createUnitField('right', ' ', 50, 'chevron-right', 'auto');
$field->addClass('padding-bottom', 'wrapClass');
$fieldsetSub->append($field);

// position-top
$field = createUnitField('top', ' ', 50, 'chevron-up', 'auto');
$fieldsetSub->append($field);

// position-bottom
$field = createUnitField('bottom', ' ', 50, 'chevron-down', 'auto');
$fieldsetSub->append($field);

//float
$field = $this->modules->get('InputfieldSelect');
$field->name = "float";
$field->label = "Float";
$field->columnWidth = 50;
$field->addOption("none", "none");
$field->addOption("left", "left");
$field->addOption("right", "right");
$field->addClass('label-left', 'wrapClass');
$fieldsetAdvanced->append($field);

//shape-outside
$field = $this->modules->get('InputfieldSelect');
$field->name = "shape-outside";
$field->label = "Shape";
$field->columnWidth = 50;
$field->addOption("none", "none");
$field->addOption("circle()", "circle");
$field->addOption("ellipse()", "ellipse");
$field->addClass('label-left', 'wrapClass');
$fieldsetAdvanced->append($field);

//clear
$field = $this->modules->get('InputfieldSelect');
$field->name = "clear";
$field->label = "Clear";
$field->columnWidth = 100;
$field->addOption("none", "none");
$field->addOption("left", "left");
$field->addOption("right", "right");
$field->addOption("both", "both");
$field->addClass('label-left', 'wrapClass');
$fieldsetAdvanced->append($field);

//overflow-x
$field = $this->modules->get('InputfieldSelect');
$field->name = "overflow-x";
$field->label = "Overflow X";
$field->addOption("unset", "unset");
$field->addOption("visible", "visible");
$field->addOption("scroll", "scroll");
$field->addOption("hidden", "hidden");
$field->addOption("clip", "clip");
$field->addOption("auto", "auto");
$field->addClass('label-left', 'wrapClass');
$fieldsetAdvanced->append($field);

//overflow-y
$field = $this->modules->get('InputfieldSelect');
$field->name = "overflow-y";
$field->label = "Overflow Y";
$field->addOption("unset", "unset");
$field->addOption("visible", "visible");
$field->addOption("scroll", "scroll");
$field->addOption("hidden", "hidden");
$field->addOption("clip", "clip");
$field->addOption("auto", "auto");
$field->addClass('label-left', 'wrapClass');
$fieldsetAdvanced->append($field);

// END LAYOUT

//Typography
$fieldset = $this->modules->get('InputfieldFieldset');
$fieldset->label = 'Typography';
$fieldset->name = 'pg_settings_typography';
$fieldset->addClass('pg-global');
//$fieldset->collapsed = 1;
$wrapper->append($fieldset);

// font color
$colors = '';
$list = '';
if ($this->ft->fontColor !== '') {
  $defaults = str_replace(' ', '', $this->ft->fontColor);
  $defaults = explode(',', $defaults);
  $list = 'list="fontColor"';
  $colors = '<datalist id="fontColor">';
  foreach ($defaults as $default) {
    $colors .= '<option>' . $default . '</option>';
  }
  $colors .= '</datalist>';
}

$field = $this->modules->get('InputfieldMarkup');
$field->label = 'Color';
$field->set('name', __('color'));
$field->addClass('label-left', 'wrapClass');
$field->value = '<input class="uk-input" type="color" id="color" name="color" placeholder="#000000"' . $list . ' >' . $colors;
$field->columnWidth = 50;
$fieldset->append($field);

// opacity
$field = $this->modules->get('InputfieldMarkup');
$field->label = 'Opacity';
$field->set('name', __('data-color-opacity'));
$field->addClass('label-left', 'wrapClass');
$field->value = '<div class="range-wrap"><output class="bubble"></output><input name="data-color-opacity" data-type="color" type="range" min="0" max="1" step="0.1" placeholder="1" class="range"></div>';
$field->columnWidth = 50;
$fieldset->append($field);

//FONT SELECT

//get local fonts without extension
$localFonts = $this->getFontNames();
$filePath = $this->getFontPath();
$fontNames = '';

foreach ($localFonts as $font) {
  // // // bd($font);
  $fileExt = '.' . pathinfo($font, PATHINFO_EXTENSION);
  $font = str_replace($fileExt, '', $font);
  $fontNames .= $font . ',';
}

$field = $this->modules->get('InputfieldText');
$field->set('label', __('Font'));
$field->set('name', __('font-family'));
$field->addClass('label-left', 'wrapClass');
$field->addClass('fonts');

$field->attr('local-fonts', $fontNames);
$field->columnWidth = 100;
$fieldset->append($field);

// font weight
$field = $this->modules->get('InputfieldSelect');
$field->name = "font-weight";
$field->label = $this->_("Style");
$field->addOption("normal");
$field->addOption("100");
//$field->addOption( "200" );
//$field->addOption( "300" );
//$field->addOption( "400" );
//$field->addOption( "500" );
//$field->addOption( "600" );
//$field->addOption( "700" );
$field->set('value', 'normal');
$field->addClass('label-left', 'wrapClass');
//$field->addClass( 'hide-label', 'headerClass' );
$field->columnWidth = 100;
$fieldset->append($field);

// font-size
$field = createUnitField('font-size', 'Size', 60);
$fieldset->append($field);

// line-height
$field = $this->modules->get('InputfieldInteger');
$field->inputType = "number";
$field->label = 'Line Height';
$field->name = 'line-height';
$field->attr('step', '0.01');
$field->addClass('label-left', 'wrapClass');
$field->columnWidth = 40;
$fieldset->append($field);

// letter-spacing
$field = createUnitField('letter-spacing', 'Letter Spacing', 100);
$fieldset->append($field);

// text-align
$field = $this->modules->get('InputfieldSelect');
$field->name = "text-align";
$field->label = $this->_("Align");
$field->addOption("left");
$field->addOption("right");
$field->addOption("center");
$field->addOption("justify");
$field->set('value', 'left');
$field->addClass('label-left', 'wrapClass');
$field->columnWidth = 100;
$fieldset->append($field);

//Advanced
$fieldsetSub = $this->modules->get('InputfieldFieldset');
$fieldsetSub->label = 'More typography options';
$fieldsetSub->name = 'pg_settings_advanced';;
$fieldsetSub->collapsed = 1;
$fieldset->append($fieldsetSub);

// hyphens
$field = $this->modules->get('InputfieldSelect');
$field->name = "hyphens";
$field->label = $this->_("Hyphens");
$field->addOption("none");
$field->addOption("auto");
$field->set('value', 'none');
$field->addClass('label-left', 'wrapClass');
//$field->addClass( 'hide-label', 'headerClass' );
$field->columnWidth = 100;
$fieldsetSub->append($field);

// text-decoration
$field = $this->modules->get('InputfieldSelect');
$field->name = "text-transform";
$field->label = $this->_("Text transform");
$field->addOption("none");
$field->addOption("uppercase");
$field->addOption("lowercase");
$field->addOption("capitalize");
$field->set('value', 'none');
$field->addClass('label-left', 'wrapClass');
//$field->addClass( 'hide-label', 'headerClass' );
$field->columnWidth = 100;
$fieldsetSub->append($field);

// text-decoration
$field = $this->modules->get('InputfieldSelect');
$field->name = "text-decoration";
$field->label = $this->_("Under line");
$field->addOption("none");
$field->addOption("underline");
$field->addOption("underline wavy");
$field->addOption("overline");
$field->addOption("line-through");
$field->addOption("underline overline");
$field->set('value', 'none');
$field->addClass('label-left', 'wrapClass');
$field->columnWidth = 100;
//$field->addClass( 'hide-label', 'headerClass' );
// $field->columnWidth = 30;
$fieldsetSub->append($field);

//text-decoration-thickness
$field = createUnitField('text-decoration-thickness', 'Underline thickness', 50, '', 'auto');
$fieldsetSub->append($field);

// text-underline-offset
$field = createUnitField('text-underline-offset', 'Underline offset', 50, '', 'auto');
$fieldsetSub->append($field);

// white-space
$field = $this->modules->get('InputfieldSelect');
$field->name = "white-space";
$field->label = $this->_("Line break");
$field->addOption("normal");
$field->addOption("nowrap", "no wrap");
$field->addClass('label-left', 'wrapClass');
$field->columnWidth = 100;
$fieldsetSub->append($field);

//Lists
$field = $this->modules->get('InputfieldSelect');
$field->name = "list-style-type";
$field->label = $this->_("List Style");
$field->addOption("disc");
$field->addOption("circle");
$field->addOption("square");
$field->addOption("decimal");
$field->addOption("decimal-leading-zero");
$field->addOption("upper-roman");
$field->addOption("none");
$field->set('value', 'disc');
$field->addClass('label-left', 'wrapClass');
$field->columnWidth = 100;
$fieldsetSub->append($field);

$field = $this->modules->get('InputfieldSelect');
$field->name = "list-style-position";
$field->label = $this->_("List Position");
$field->addOption("outside");
$field->addOption("inside");
$field->set('value', 'outside');
$field->addClass('label-left', 'wrapClass');
$field->columnWidth = 100;
$fieldsetSub->append($field);

//list image
$parentPageId = (int) wire('input')->get('id');
$field = $this->modules->get('InputfieldMarkup');
$field->label = 'List Image';
$field->addClass('label-left', 'wrapClass');
$field->value = '<div class="setting pg-file-picker pg-file-picker- pg-style-panel">
                      <div class="settings_wrap">
                        <div class="drop_target">
                          <div class="input_button"></div>
                            <input class="inputFile" type="file" data-field="" data-id="' . $parentPageId . '" data-type="upload" />
                            <input type="text" name="list-style-image" class="file-url"/>
                            <img src="" class="pg-fileupload" />
                            </div>
                        </div>
                    </div>';
$field->columnWidth = 100;
$fieldsetSub->append($field);
// END Typografie

//SPACING
$fieldset = $this->modules->get('InputfieldFieldset');
$fieldset->label = 'Spacing';
$fieldset->name = 'spacing';
$wrapper->append($fieldset);

// padding
$fieldsetSub = $this->modules->get('InputfieldFieldset');
$fieldsetSub->label = 'Padding';
$fieldsetSub->name = 'padding';
$fieldsetSub->addClass('combo-wrapper');
$fieldsetSub->columnWidth = 100;
$fieldset->append($fieldsetSub);

// padding-left
$field = createUnitField('padding-left', ' ', 50, 'chevron-left', '', 'none');
$field->addClass('padding-bottom', 'wrapClass');
$fieldsetSub->append($field);

// padding-right
$field = createUnitField('padding-right', ' ', 50, 'chevron-right', '', 'none');
$field->addClass('padding-bottom', 'wrapClass');
$fieldsetSub->append($field);

// padding-top
$field = createUnitField('padding-top', ' ', 50, 'chevron-up', '', 'none');
$fieldsetSub->append($field);

// padding-bottom
$field = createUnitField('padding-bottom', ' ', 50, 'chevron-down', '', 'none');
$fieldsetSub->append($field);

// END Padding

// margin
$fieldsetSub = $this->modules->get('InputfieldFieldset');
$fieldsetSub->label = 'Margin';
$fieldsetSub->name = 'margin';
$fieldsetSub->addClass('combo-wrapper');
$fieldsetSub->columnWidth = 100;
$fieldset->append($fieldsetSub);

// margin-left
$field = createUnitField('margin-left', ' ', 50, 'chevron-left', 'auto');
$field->addClass('padding-bottom', 'wrapClass');
$fieldsetSub->append($field);

// margin-right
$field = createUnitField('margin-right', ' ', 50, 'chevron-right', 'auto');
$field->addClass('padding-bottom', 'wrapClass');
$fieldsetSub->append($field);

// margin-top
$field = createUnitField('margin-top', ' ', 50, 'chevron-up', 'auto');
$fieldsetSub->append($field);

// margin-bottom
$field = createUnitField('margin-bottom', ' ', 50, 'chevron-down', 'auto');
$fieldsetSub->append($field);

//END SPACING

// SIZE

$fieldset = $this->modules->get('InputfieldFieldset');
$fieldset->label = 'Size';
$fieldset->name = 'size';
$wrapper->append($fieldset);

//width
$field = createUnitField('width', 'Width', 50, '', 'auto');
$fieldset->append($field);

//height
$field = createUnitField('height', 'Height', 50, '', 'auto');
$fieldset->append($field);

//min width
$field = createUnitField('min-width', 'Min W', 50, '', '', 'none');
$fieldset->append($field);

//min height
$field = createUnitField('min-height', 'Min H', 50, '', '', 'none');
$fieldset->append($field);

//max width
$field = createUnitField('max-width', 'Max W', 50, '', '', 'none');
$fieldset->append($field);

//max height
$field = createUnitField('max-height', 'Max H', 50, '', '', 'none');
$fieldset->append($field);

// object-fit
$field = $this->modules->get('InputfieldSelect');
$field->name = "object-fit";
$field->label = $this->_("Content");
$field->addOption("none", "none");
$field->addOption("cover", "cover");
$field->addOption("contain", "contain");
$field->addOption("fill", "fill");
$field->addOption("scale-down", "scale-down");
$field->addClass('label-left', 'wrapClass');
$field->columnWidth = 100;
$fieldset->append($field);

// BACKGROUND
// wrapper
$fieldset = $this->modules->get('InputfieldFieldset');
$fieldset->label = 'Background';
$fieldset->name = 'background';
$wrapper->append($fieldset);

// bg color
$colors = '';
$list = '';
if ($this->ft->bgColor !== '') {
  $defaults = str_replace(' ', '', $this->ft->bgColor);
  $defaults = explode(',', $defaults);
  $list = 'list="bgColor"';
  $colors = '<datalist id="bgColor">';
  foreach ($defaults as $default) {
    $colors .= '<option>' . $default . '</option>';
  }
  $colors .= '</datalist>';
}

$field = $this->modules->get('InputfieldMarkup');
$field->label = 'Color';
$field->set('name', __('background-color'));
$field->addClass('label-left', 'wrapClass');
$field->value = '<input class="uk-input" type="color" id="background-color" name="background-color" placeholder="#FFFFFF"' . $list . ' >' . $colors;
$field->columnWidth = 50;
$fieldset->append($field);

// opacity
$field = $this->modules->get('InputfieldMarkup');
$field->label = 'Opacity';
$field->set('name', __('data-bg-opacity'));
$field->addClass('label-left', 'wrapClass');
$field->value = '<div class="range-wrap"><output class="bubble"></output><input name="data-color-opacity" data-type="background-color" type="range" min="0" max="1" step="0.1" placeholder="1" class="range"></div>';
$field->columnWidth = 50;
$fieldset->append($field);

//Advanced
$fieldsetSub = $this->modules->get('InputfieldFieldset');
$fieldsetSub->label = 'Background Image';
$fieldsetSub->name = 'pg_settings_advanced';;
$fieldsetSub->collapsed = 1;
$fieldset->append($fieldsetSub);

//bg image
$parentPageId = (int) wire('input')->get('id');
$field = $this->modules->get('InputfieldMarkup');
$field->label = 'Image';
$field->addClass('label-left', 'wrapClass');
$field->value = '<div class="setting pg-file-picker pg-file-picker- pg-style-panel">
                      <div class="settings_wrap">
                        <div class="drop_target">
                          <div class="input_button"></div>
                            <input class="inputFile" type="file" data-field="" data-id="' . $parentPageId . '" data-type="upload"/>
                            <input type="text" name="background-image" class="file-url"/>
                            <img src="" class="pg-fileupload" />
                            </div>
                        </div>
                    </div>';
$field->columnWidth = 100;
$fieldsetSub->append($field);

// Background-position
$field = $this->modules->get('InputfieldSelect');
$field->name = "background-repeat";
$field->label = $this->_("Repeat");
$field->addClass('label-left', 'wrapClass');
$field->addOption("repeat", "repeat");
$field->addOption("repeat-x", "repeat-x");
$field->addOption("repeat-y", "repeat-y");
$field->addOption("no-repeat", "no-repeat");
$field->columnWidth = 100;
$fieldsetSub->append($field);

//background-size
$field = $this->modules->get('InputfieldText');
$field->set('label', __('Size'));
$field->set('name', __('background-size'));
$field->columnWidth = 100;
$field->attr('placeholder', 'none');
$field->addClass('label-left', 'wrapClass');
$fieldsetSub->append($field);

//background-size
$field = $this->modules->get('InputfieldText');
$field->set('label', __('Position'));
$field->set('name', __('background-position'));
$field->columnWidth = 100;
$field->attr('placeholder', 'none');
$field->addClass('label-left', 'wrapClass');
$fieldsetSub->append($field);

// BORDERS
$fieldset = $this->modules->get('InputfieldFieldset');
$fieldset->label = 'Borders';
$fieldset->name = 'borders';
$wrapper->append($fieldset);

$field = $this->modules->get('InputfieldSelect');
$field->name = "border-style";
$field->label = $this->_("Style");
$field->addClass('label-left', 'wrapClass');
$field->addOption("none", "none");
$field->addOption("solid solid solid solid", "all");
$field->addOption("solid none none none", "top");
$field->addOption("none solid none none", "right");
$field->addOption("none none solid none", "bottom");
$field->addOption("none none none solid", "left");
$field->addOption("none solid none solid", "left and right");
$field->addOption("solid none solid none", "top and bottom");
$field->addOption("solid solid none none", "top and right");
$field->addOption("solid none none solid", "top and left");
$field->addOption("none solid solid none", "bottom and right");
$field->addOption("none none solid solid", "bottom and left");
$field->addOption("dotted dotted dotted dotted", "all-dottet");
$field->addOption("dotted none none none", "top-dotted");
$field->addOption("none dotted none none", "right-dotted");
$field->addOption("none none dotted none", "bottom-dotted");
$field->addOption("none none none dotted", "left-dotted");
$field->addOption("dashed dashed dashed dashed", "all-dashed");
$field->addOption("dashed none none none", "top-dashed");
$field->addOption("none dashed none none", "right-dashed");
$field->addOption("none none dashed none", "bottom-dashed");
$field->addOption("none none none dashed", "left-dashed");
$field->columnWidth = 100;
$fieldset->append($field);

//$field = $this->modules->get( 'InputfieldSelect' );
//$field->name = "border-style";
//$field->label = $this->_( "Style" );
////$field->addClass( 'label-left', 'wrapClass' );
//$field->addClass( 'hide-label', 'headerClass' );
//$field->addOption( "none", "none" );
//$field->addOption( "solid", "solid" );
//$field->addOption( "dotted", "dotted" );
//$field->addOption( "dashed", "dashed" );
//$field->addOption( "double", "double" );
//$field->addOption( "groove", "groove" );
//$field->addOption( "ridge", "ridge" );
//$field->addOption( "inset", "inset" );
//$field->addOption( "outset", "outset" );
//$field->addOption( "outset", "outset" );
//$field->addClass( 'hidden', 'hidden' );
//$field->columnWidth = 50;
//$fieldset->append( $field );

//border-width
$field = createUnitField('border-width', 'Width', 50);
$fieldset->append($field);

//border-radius
$field = createUnitField('border-radius', 'Radius', 50);
$fieldset->append($field);

$field = $this->modules->get('InputfieldMarkup');
$field->label = 'Color';
$field->set('name', __('border-color'));
$field->addClass('label-left', 'wrapClass');
$field->value = '<input class="uk-input" type="color" id="border-color" name="border-color" placeholder="#000000"' . $list . ' >' . $colors;
$field->columnWidth = 50;
$fieldset->append($field);

// opacity
$field = $this->modules->get('InputfieldMarkup');
$field->label = 'Opacity';
$field->set('name', __('data-border-opacity'));
$field->addClass('label-left', 'wrapClass');
$field->value = '<div class="range-wrap"><output class="bubble"></output><input name="data-color-opacity" data-type="border-color" type="range" min="0" max="1" step="0.1" placeholder="1" class="range"></div>';
$field->columnWidth = 50;
$fieldset->append($field);

//END Background

// EFFECTS
$fieldset = $this->modules->get('InputfieldFieldset');
$fieldset->label = 'Effects';
$fieldset->name = 'effects-wrapper';
$wrapper->append($fieldset);

//box-shadow
$field = $this->modules->get('InputfieldText');
$field->set('label', __('Shadow'));
$field->set('name', __('box-shadow'));
$field->columnWidth = 100;
$field->addClass('label-left', 'wrapClass');
$fieldset->append($field);

//mix-blend-mode
$field = $this->modules->get('InputfieldSelect');
$field->name = "mix-blend-mode";
$field->label = "Blend Mode";
$field->addOption("normal", "normal");
$field->addOption("multiply", "multiply");
$field->addOption("screen", "screen");
$field->addOption("overlay", "overlay");
$field->addOption("darken", "darken");
$field->addOption("lighten", "lighten");
$field->addOption("color-dodge", "color-dodge");
$field->addOption("color-burn", "color-burn");
$field->addOption("hard-light", "hard-light");
$field->addOption("soft-light", "soft-light");
$field->addOption("difference", "difference");
$field->addOption("exclusion", "exclusion");
$field->addOption("hue", "hue");
$field->addOption("saturation", "saturation");
$field->addOption("color", "color");
$field->addOption("color", "color");
$field->addOption('luminosity', 'luminosity');
$field->addClass('label-left', 'wrapClass');
$field->columnWidth = 100;
$fieldset->append($field);

//opacity
$field = $this->modules->get('InputfieldMarkup');
$field->label = 'Opacity';
$field->set('name', __('opacity'));
$field->addClass('label-left', 'wrapClass');
$field->value = '<div class="range-wrap"><output class="bubble"></output><input name="opacity" type="range" min="0" max="1" step="0.1" placeholder="1" class="range"></div>';
$field->columnWidth = 100;
$fieldset->append($field);

//cursor
$field = $this->modules->get('InputfieldSelect');
$field->name = "cursor";
$field->label = "Cursor";
$field->addClass('label-left', 'wrapClass');
$field->addOption("auto", "auto");
$field->addOption("default", "default");
$field->addOption("context-menu", "context-menu");
$field->addOption("help", "help");
$field->addOption("pointer", "pointer");
$field->addOption("progress", "progress");
$field->addOption("wait", "wait");
$field->addOption("cell", "cell");
$field->addOption("crosshair", "crosshair");
$field->addOption("text", "text");
$field->addOption("vertical-text", "vertical-text");
$field->addOption("alias", "alias");
$field->addOption("copy", "copy");
$field->addOption("move", "move");
$field->addOption("no-drop", "no-drop");
$field->addOption("not-allowed", "not-allowed");
$field->addOption("grab", "grab");
$field->addOption("grabbing", "grabbing");
$field->addOption("all-scroll", "all-scroll");
$field->addOption("col-resize", "col-resize");
$field->addOption("row-resize", "row-resize");
$field->addOption("ew-resize", "ew-resize");
$field->addOption("ns-resize", "ns-resize");
$field->addOption("nesw-resize", "nesw-resize");
$field->addOption("nwse-resize", "nwse-resize");
$field->addOption("zoom-in", "zoom-in");
$field->addOption("zoom-out", "zoom-out");
$field->columnWidth = 100;
$fieldset->append($field);

// TRANSFORM
$fieldset = $this->modules->get('InputfieldFieldset');
$fieldset->label = 'Transform';
$fieldset->name = 'transform-wrapper';
$wrapper->append($fieldset);

//translate X
$fieldsetSubWrapper = $this->modules->get('InputfieldFieldset');
$fieldsetSubWrapper->label = 'Move X';
$fieldsetSubWrapper->name = 'translate-wrapper';
$fieldsetSubWrapper->addClass('label-left', 'wrapClass');
$fieldsetSubWrapper->addClass('padding-none');
$fieldset->append($fieldsetSubWrapper);

$field = $this->modules->get('InputfieldMarkup');
$field->name = 'translate';
$field->value = '<div class="range-wrap"><output class="bubble"></output><input name="translate" type="range" min="-100" max="100" step="1" placeholder="0" class="range"></div>';
$field->columnWidth = 72;
$field->addClass('hide-label', 'headerClass');
$fieldsetSubWrapper->append($field);

$field = createUnitField('translate', '', 28, '', '', '0');
$field->addClass('hide-label', 'headerClass');
$fieldsetSubWrapper->append($field);

//translate Y
$fieldsetSubWrapper = $this->modules->get('InputfieldFieldset');
$fieldsetSubWrapper->label = 'Move Y';
$fieldsetSubWrapper->name = 'translateY-wrapper';
$fieldsetSubWrapper->addClass('label-left', 'wrapClass');
$fieldsetSubWrapper->addClass('padding-none');
$fieldset->append($fieldsetSubWrapper);

$field = $this->modules->get('InputfieldMarkup');
$field->label = 'Move Y';
$field->name = 'transform';
$field->attr('name-add', 'translateY');
$field->addClass('hide-label', 'headerClass');
$field->value = '<div class="range-wrap"><output class="bubble"></output><input name="transform" name-add="translateY" type="range" min="-100" max="100" step="1" placeholder="0" class="range"></div>';
$field->columnWidth = 72;
$fieldsetSubWrapper->append($field);

$field = createUnitField('transform', '', 28, '', '', '0', 'translateY');
$field->addClass('hide-label', 'headerClass');
$fieldsetSubWrapper->append($field);

//scale
$fieldsetSubWrapper = $this->modules->get('InputfieldFieldset');
$fieldsetSubWrapper->label = 'Scale';
$fieldsetSubWrapper->name = 'scale-wrapper';
$fieldsetSubWrapper->addClass('label-left', 'wrapClass');
$fieldsetSubWrapper->addClass('padding-none');
$fieldset->append($fieldsetSubWrapper);

$field = $this->modules->get('InputfieldMarkup');
$field->name = 'scale';
$field->attr('unit', '');
$field->addClass('hide-label', 'headerClass');
$field->value = '<div class="range-wrap"><output class="bubble"></output><input name="scale" unit="" type="range" min="0.1" max="1.9" step="0.01" placeholder="1" class="range"></div>';
$field->columnWidth = 72;
$fieldsetSubWrapper->append($field);

$field = $this->modules->get('InputfieldInteger');
$field->inputType = "number";
$field->name = 'scale';
$field->addClass('hide-label', 'headerClass');
$field->attr('unit', '');
$field->attr('placeholder', '0');
$field->columnWidth = 28;
$fieldsetSubWrapper->append($field);

//rotate
$fieldsetSubWrapper = $this->modules->get('InputfieldFieldset');
$fieldsetSubWrapper->label = 'Rotate';
$fieldsetSubWrapper->name = 'rotate-wrapper';
$fieldsetSubWrapper->addClass('label-left', 'wrapClass');
$fieldsetSubWrapper->addClass('padding-none');
$fieldset->append($fieldsetSubWrapper);

$field = $this->modules->get('InputfieldMarkup');
$field->name = 'rotate';
$field->attr('unit', 'deg');
$field->addClass('hide-label', 'headerClass');
$field->value = '<div class="range-wrap"><output class="bubble"></output><input name="rotate" unit="deg" type="range" min="-360" max="360" step="1" placeholder="0" class="range"></div>';
$field->columnWidth = 72;
$fieldsetSubWrapper->append($field);

$field = $this->modules->get('InputfieldInteger');
$field->inputType = "number";
$field->name = 'rotate';
$field->addClass('hide-label', 'headerClass');
$field->attr('unit', 'deg');
$field->attr('placeholder', '0');
$field->columnWidth = 28;
$fieldsetSubWrapper->append($field);

//rotateX
$fieldsetSubWrapper = $this->modules->get('InputfieldFieldset');
$fieldsetSubWrapper->label = 'Rotate X';
$fieldsetSubWrapper->name = 'rotateX-wrapper';
$fieldsetSubWrapper->addClass('label-left', 'wrapClass');
$fieldsetSubWrapper->addClass('padding-none');
$fieldset->append($fieldsetSubWrapper);

$field = $this->modules->get('InputfieldMarkup');
$field->label = 'Rotate X';
$field->name = 'transform';
$field->attr('name-add', 'rotateX');
$field->attr('unit', 'deg');
$field->addClass('hide-label', 'headerClass');
$field->value = '<div class="range-wrap"><output class="bubble"></output><input name="transform" name-add="rotateX" unit="deg" type="range" min="-360" max="360" step="1" placeholder="0" class="range"></div>';
$field->columnWidth = 72;
$fieldsetSubWrapper->append($field);

$field = $this->modules->get('InputfieldInteger');
$field->inputType = "number";
$field->name = 'transform';
$field->attr('name-add', 'rotateX');
$field->addClass('hide-label', 'headerClass');
$field->attr('unit', 'deg');
$field->attr('placeholder', '0');
$field->columnWidth = 28;
$fieldsetSubWrapper->append($field);

//rotateY
$fieldsetSubWrapper = $this->modules->get('InputfieldFieldset');
$fieldsetSubWrapper->label = 'Rotate Y';
$fieldsetSubWrapper->name = 'rotateY-wrapper';
$fieldsetSubWrapper->addClass('label-left', 'wrapClass');
$fieldsetSubWrapper->addClass('padding-none');
$fieldset->append($fieldsetSubWrapper);

$field = $this->modules->get('InputfieldMarkup');
$field->name = 'transform';
$field->attr('name-add', 'rotateY');
$field->attr('unit', 'deg');
$field->addClass('hide-label', 'headerClass');
$field->value = '<div class="range-wrap"><output class="bubble"></output><input name="transform" name-add="rotateY" unit="deg" type="range" min="-360" max="360" step="1" placeholder="0" class="range"></div>';
$field->columnWidth = 72;
$fieldsetSubWrapper->append($field);

$field = $this->modules->get('InputfieldInteger');
$field->inputType = "number";
$field->name = 'transform';
$field->attr('name-add', 'rotateY');
$field->addClass('hide-label', 'headerClass');
$field->attr('unit', 'deg');
$field->attr('placeholder', '0');
$field->columnWidth = 28;
$fieldsetSubWrapper->append($field);

//transform-origin
$field = $this->modules->get('InputfieldText');
$field->set('label', __('Origin'));
$field->set('name', __('transform-origin'));
$field->columnWidth = 100;
$field->attr('placeholder', 'center');
$field->addClass('label-left', 'wrapClass');
$fieldset->append($field);

// END TRANSFORMS
//attributes (frontend only)
$field = $this->modules->get('InputfieldText');
$field->set('label', __('Attributes'));
$field->set('name', __('data-attributes'));
$field->columnWidth = 100;
//$field->addClass( 'hide-label', 'headerClass' );
$wrapper->append($field);

// ITEM STYLE SETTINGS END


//animations NEW
include('stylePanel_interactions.php');
//END animations NEW

$settings = $tabs . '<div class="pg-sticky pg-interactions-wrapper">' . $interactions->render() . '</div>' . $wrapper->render();

// END SETTINGS ------

function createUnitField($name, $label = '', $width = 100, $icon = '', $lastUnit = '', $placeholder = '', $nameAdd = '') {

  if(!$label) $label = $name;
  if($lastUnit && !$placeholder) $placeholder = $lastUnit; 

  $fieldsetSub = wire('modules')->get('InputfieldFieldset');
  $fieldsetSub->label = $label;
  $fieldsetSub->name = $name;
  $fieldsetSub->columnWidth = $width;
  if($icon) {
    $fieldsetSub->icon = $icon;
    $fieldsetSub->addClass('label-icon', 'wrapClass');
  }
  $fieldsetSub->addClass('label-left', 'wrapClass');
  $fieldsetSub->addClass('combo-wrapper');

  $field = wire('modules')->get('InputfieldInteger');
  $field->inputType = "number";
  $field->label = $label;
  $field->name = $name;
  $field->attr('placeholder', $placeholder);
  if($nameAdd) $field->attr('name-add', $nameAdd); // used to add transform property name
  $field->addClass('hide-label', 'headerClass');
  $fieldsetSub->append($field);
  
  $field = wire('modules')->get('InputfieldSelect');
  $field->name = $name;
  if($nameAdd) $field->name = $nameAdd;
  $field->label = " ";
  $field->addOption("px");
  $field->addOption("%");
  $field->addOption("vh");
  $field->addOption("vw");
  if($lastUnit) $field->addOption($lastUnit);
  $field->addClass('unit');
  $field->addClass('hide-label', 'headerClass');
  $field->addClass('field-combo', 'wrapClass');
  $field->addClass('padding-right', 'wrapClass');
  $fieldsetSub->append($field);

  return $fieldsetSub;
}
