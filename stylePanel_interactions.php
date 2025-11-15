<?php

namespace ProcessWire;

// prefix name with "data" to prevent save
// prefix data with "pg-" to save as non css 

// interaction wrapper
$interactions = new InputfieldWrapper();
$interactions->attr('name', 'interactions');
$interactions->attr('title', 'Interactions');
$interactions->attr('class', 'WireTab');
$interactions->addClass('WireTab');

$fieldset = $this->modules->get('InputfieldFieldset');
$fieldset->label = 'Interactions';
$fieldset->name = 'interactions';
$fieldset->addClass('hide-label', 'headerClass');
$interactions->append($fieldset);

// animations
if ($this->modules->get('InputfieldTextTags')) {
  $field = $this->modules->get('InputfieldTextTags');
  $field->allowUserTags = true;
  $field->delimiter = 'c';
  $field->set('label', __('Animation'));
  $field->addClass('label-left', 'wrapClass');
  $field->name = '--pg-animation';
  $field->columnWidth = 100;
  $field->attr('placeholder', 'Type to create an animation…');
  createTooltip($field, 'To <b>create an animation</b> type a name in the animation field and press the "return" key. <br><br>To <b>delete an animation</b>, right click on an animation and select "delete animation". <br><br>To <b>reuse an animation</b> add the animation to other elements.');
  $fieldset->append($field);

  $animationParent = $this->pages->get('name=pg-animations, template=pg_container');
  $field = $this->modules->get('InputfieldMarkup');
  $field->addClass('pg-show-classlist', 'wrapClass');
  $field->value = "<a href='#' class='pg-edit' data-url='./?id=$animationParent->id&modal=1&pgmodal=1&pgchildren=1&pghidesettings=1&pghidechildsorting=1&pgnoadd=1' data-title='Animations'><i class='fa fa-gear pw-nav-icon'></i></a>";
  createTooltip($field, "Open the animation manager. To manage all your animations in one place.", "bottom");
  $fieldset->append($field);
}

// Event
$field = $this->modules->get('InputfieldSelect');
$field->name = 'pg-animation-event';
$field->label = $this->_("Event");
$field->addOption("load", "on load");
$field->addOption("hover", "on hover");
$field->addOption("click", "on click/touch");
$field->addOption("inview", "in view");
$field->addOption("scroll", "on scroll");
$field->addClass('label-left', 'wrapClass');
//$field->columnWidth = 100;
createTooltip($field, 'Trigger event');
$fieldset->append($field);

//keyframes 
$field = $this->modules->get('InputfieldMarkup');
// $field->label = 'keyframes';
$field->label = ' ';
$field->icon = 'play';
$field->name = 'data-animation-keyframes';
$field->addClass('label-left', 'wrapClass');
$field->value = '<div class="range-multi range-add range-keyframes" data-unit="%" min="0" max="100"><output class="bubble" data-unit="%"></output></div>';
$field->columnWidth = 100;
createTooltip($field, 'Play animation');
$fieldset->append($field);

//options
$fieldset2 = $this->modules->get('InputfieldFieldset');
$fieldset2->label = 'More animation options';
$fieldset2->name = 'pg_settings_advanced';
$fieldset2->collapsed = 1;
$fieldset->append($fieldset2);

//trigger element
$field = $this->modules->get('InputfieldSelect');
$field->name = 'pg-animation-trigger';
$field->label = "Trigger";
$field->addClass('label-left', 'wrapClass');
$field->addOption("self", "Selected Element");
$field->addOption("parent", "Parent Element");
createTooltip($field, 'The element that triggers the animation.');
$field->columnWidth = 100;
$fieldset2->append($field);

//target element (allows for stagger animations)
$field = $this->modules->get('InputfieldSelect');
$field->name = 'pg-animation-target';
$field->label = "Target";
$field->addClass('label-left', 'wrapClass');
$field->addOption("self", "Selected Element");
$field->addOption("children", "Children (Stagger Animation)");
$field->addOption("words", "Words (Stagger Animation)");
$field->addOption("letters", "Letters (Stagger Animation)");
createTooltip($field, 'Apply the animation to the selected element or to multiple child elements (use the delay value to create a stagger animation).');
$field->columnWidth = 100;
$fieldset2->append($field);

//once
$field = $this->modules->get('InputfieldSelect');
$field->name = 'pg-animation-once';
$field->label = $this->_("Once");
$field->addClass('label-left', 'wrapClass');
$field->addOption("false", "false");
$field->addOption("true", "true");
createTooltip($field, 'Select true to play the animation only once.');
$field->columnWidth = 100;
$fieldset2->append($field);

//pin
$field = $this->modules->get('InputfieldSelect');
$field->name = 'pg-animation-pin';
$field->label = $this->_("Pin");
$field->addClass('label-left', 'wrapClass');
$field->addOption("false", "false");
$field->addOption("true", "pin");
$field->addOption("pin-spacing", "pin-spacing");
$field->columnWidth = 100;
createTooltip($field, 'Pin the animation to the screen during scrolling.');
$fieldset2->append($field);;

//duration
$field = $this->modules->get('InputfieldInteger');
$field->inputType = "number";
$field->set('label', __('Duration'));
$field->name = 'animation-duration';
$field->attr('placeholder', '0.6');
$field->attr('unit', 's');
$field->attr('step', '0.1');
$field->addClass('label-left', 'wrapClass');
$field->min = 0;
$field->max = 99;
// $field->columnWidth = 50;
createTooltip($field, 'Duration of the animation in seconds.');
$fieldset2->append($field);

//scroll duration
$field = $this->modules->get('InputfieldInteger');
$field->inputType = "number";
$field->set('label', __('Distance'));
$field->name = 'pg-animation-distance';
$field->attr('placeholder', '100');
$field->attr('show-unit', 'vh');
$field->attr('step', '1');
$field->addClass('label-left', 'wrapClass');
createTooltip($field, 'Scroll distance relative to the screen height.');
$field->min = 0;
$field->max = 999999;
// $field->columnWidth = 50;
$fieldset2->append($field);

//scroll offset start
$field = $this->modules->get('InputfieldMarkup');
$field->label = 'Offset';
$field->set('name', __('pg-animation-start'));
$field->addClass('label-left', 'wrapClass');
$field->value = '<div class="range-wrap"><output class="bubble" data-unit="vh"></output><input name="pg-animation-start" type="range" min="0" max="100" step="1" placeholder="0" class="range"></div>';
$field->columnWidth = 100;
createTooltip($field, 'Start of the scroll animation relative to the bottom edge of the screen.');
$fieldset2->append($field);

//scroll offset end
// $field = $this->modules->get('InputfieldMarkup');
// $field->label = 'End';
// $field->set('name', __('pg-animation-end'));
// $field->addClass('label-left', 'wrapClass');
// $field->value = '<div class="range-wrap"><output class="bubble" data-unit="%"></output><input name="pg-animation-end" type="range" min="0" max="1000" step="1" placeholder="100" class="range"></div>';
// $field->columnWidth = 100;
// $fieldset->append($field);
// –––––

//delay
$field = $this->modules->get('InputfieldInteger');
$field->inputType = "number";
$field->set('label', __('Delay'));
$field->name = 'animation-delay';
$field->attr('placeholder', '0');
$field->attr('unit', 's');
$field->attr('step', '0.1');
$field->addClass('label-left', 'wrapClass');
$field->min = 0;
$field->max = 99;
createTooltip($field, 'Delay of the animation in seconds. If you choose multiple targets you can use the delay value to create a stagger animation.');
// $field->columnWidth = 50;
$fieldset2->append($field);

//animation-iteration-count
$field = $this->modules->get('InputfieldSelect');
$field->name = 'animation-iteration-count';
$field->label = 'Repeat';
$field->addClass('label-left', 'wrapClass');
$field->addOption("1", "1");
$field->addOption("2", "2");
$field->addOption("3", "3");
$field->addOption("4", "4");
$field->addOption("5", "5");
$field->addOption("infinite", "infinite");
createTooltip($field, 'Controls how often the animation is played.');
$field->columnWidth = 100;
$fieldset2->append($field);

//reverse (hover)
$field = $this->modules->get('InputfieldSelect');
$field->name = 'pg-animation-reverse';
$field->label = $this->_("Reverse");
$field->addClass('label-left', 'wrapClass');
$field->addOption("true", "true");
$field->addOption("false", "false");
$field->columnWidth = 100;
createTooltip($field, 'If this is set to true the animation will be played backwards on the second interaction.');
$fieldset2->append($field);

//prevent default (click)
$field = $this->modules->get('InputfieldSelect');
$field->name = 'pg-animation-prevent';
$field->label = $this->_("Prevent default event");
$field->addClass('label-left', 'wrapClass');
$field->addOption("true", "true");
$field->addOption("false", "false");
createTooltip($field, 'Prevent the browser to load link urls on the selected element, when clicked/touched.');
$field->columnWidth = 100;
$fieldset2->append($field);

// animation-timing-function
$field = $this->modules->get('InputfieldSelect');
$field->name = 'animation-timing-function';
$field->label = $this->_("Easing");
$field->addClass('label-left', 'wrapClass');
addEasingOptions($field);
createTooltip($field, 'Apply easing to the entire animation.');
$fieldset2->append($field);

//keyframe animation-timing-function (easing)
$field = $this->modules->get('InputfieldSelect');
$field->name = 'keyframe-animation-timing-function';
$field->label = 'Keyframe Easing';
$field->addClass('label-left', 'wrapClass');
addEasingOptions($field);
createTooltip($field, 'Apply easing to the selected keyframe segment.');
$fieldset2->append($field);

//scroll timing
$field = $this->modules->get('InputfieldSelect');
$field->name = 'pg-animation-timing';
$field->label = $this->_("Easing");
$field->addClass('label-left', 'wrapClass');
$field->addOption("linear", "linear");

$field->addOption("cubicIn", "ease in cubic");
$field->addOption("cubicOut", "ease out cubic");
$field->addOption("cubicInOut", "ease in/out cubic");

$field->addOption("quartIn", "ease in quart");
$field->addOption("quartOut", "ease out quart");
$field->addOption("quartInOut", "ease in/out quart");

$field->addOption("quintIn", "ease in quint");
$field->addOption("quintOut", "ease out quint");
$field->addOption("quintInOut", "ease in/out quint");

$field->addOption("expoIn", "ease in expo");
$field->addOption("expoOut", "ease out expo");
$field->addOption("expoInOut", "ease in/out expo");

$field->addOption("circIn", "ease in circ");
$field->addOption("circOut", "ease out circ");
$field->addOption("circInOut", "ease in/out circ");

createTooltip($field, 'Apply easing to the scroll animation.');
$fieldset2->append($field);

//scroll offset start
// $field = $this->modules->get('InputfieldMarkup');
// $field->label = 'Start';
// $field->name = '--pg-animation-start';
// $field->addClass('label-left', 'wrapClass');
// $field->value = '<div class="range-wrap"><output class="bubble" data-unit="%"></output><input name="data-scroll-start" type="range" min="0" max="100" step="1" placeholder="0" class="range"></div>';
// $field->columnWidth = 100;
// $fieldset->append($field);

//scroll offset end
// $field = $this->modules->get('InputfieldMarkup');
// $field->label = 'End';
// $field->name = '--pg-animation-end';
// $field->addClass('label-left', 'wrapClass');
// $field->value = '<div class="range-wrap"><output class="bubble" data-unit="%"></output><input name="data-scroll-end" type="range" min="0" max="1000" step="1" placeholder="100" class="range"></div>';
// $field->columnWidth = 100;
// $fieldset->append($field);

function addEasingOptions($field) {
  $field->addOption("linear", "linear");
  $field->addOption("ease", "ease");
  $field->addOption("ease-in", "ease in");
  $field->addOption("ease-out", "ease out");

  $field->addOption("cubic-bezier(0.32, 0, 0.67, 0)", "ease in cubic");
  $field->addOption("cubic-bezier(0.33, 1, 0.68, 1)", "ease out cubic");
  $field->addOption("cubic-bezier(0.65, 0, 0.35, 1)", "ease in/out cubic");

  $field->addOption("cubic-bezier(0.5, 0, 0.75, 0)", "ease in quart");
  $field->addOption("cubic-bezier(0.25, 1, 0.5, 1)", "ease out quart");
  $field->addOption("cubic-bezier(0.76, 0, 0.24, 1)", "ease in/out quart");

  $field->addOption("cubic-bezier(0.64, 0, 0.78, 0)", "ease in quint");
  $field->addOption("cubic-bezier(0.22, 1, 0.36, 1)", "ease out quint");
  $field->addOption("cubic-bezier(0.83, 0, 0.17, 1)", "ease in/out quint");

  $field->addOption("cubic-bezier(0.7, 0, 0.84, 0)", "ease in expo");
  $field->addOption("cubic-bezier(0.16, 1, 0.3, 1)", "ease out expo");
  $field->addOption("cubic-bezier(0.87, 0, 0.13, 1)", "ease in/out expo");

  $field->addOption("cubic-bezier(0.55, 0, 1, 0.45)", "ease in circ");
  $field->addOption("cubic-bezier(0, 0.55, 0.45, 1)", "ease out circ");
  $field->addOption("cubic-bezier(0.85, 0, 0.15, 1)", "ease in/out circ");
}
