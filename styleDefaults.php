<?php

namespace ProcessWire;

$backend = $this->isBackend();

$defaultCss = '<style class="pg-style-defaults">';

//inline editor fix
if ($this->user->isLoggedin()) {
  $defaultCss .= '.tox.tox-tinymce-inline {z-index:99999!important;}';
  $defaultCss .= '.tox-tinymce-aux {z-index: 999999!important;}';
  $defaultCss .= '.ui-dialog {position:fixed!important; z-index: 999999!important;}';
  $defaultCss .= '.ui-dialog iframe {max-height: calc(100vh - 150px) !important;}';
  $defaultCss .= '.pw-AdminThemeCanvas .ui-dialog {filter: grayscale(100%);}';
  $defaultCss .= '.pw-AdminThemeCanvas .ui-button.ui-state-default, .pw-AdminThemeCanvas .ui-dialog .ui-dialog-titlebar {background-color:black;}';
  $defaultCss .= '.ui-widget-overlay {position:fixed!important; z-index: 999998!important;}';
  $defaultCss .= '.tox .tox-collection__item-label {line-height:1!important; letter-spacing:0.1px!important;}';
  $defaultCss .= '.tox .tox-collection__item-label * {color:#000!important; font-size:16px!important; letter-spacing:0.1px!important; line-height: 1.3!important; mix-blend-mode:unset!important;}';
  $defaultCss .= '.tox .tox-collection__item-label h1 {font-size:30px!important;}';
  $defaultCss .= '.tox .tox-collection__item-label h2 {font-size:27px!important;}';
  $defaultCss .= '.tox .tox-collection__item-label h3 {font-size:24px!important;}';
  $defaultCss .= '.tox .tox-collection__item-label h4 {font-size:21px!important;}';
  $defaultCss .= '.tox .tox-collection__item-label h5 {font-size:18px!important;}';
  $defaultCss .= '.tox .tox-collection__item-label h6 {font-size:15px!important;}';
  $defaultCss .= '.pw-edit-buttons {z-index:999999!important;}';
  $defaultCss .= '.pw-edit:has(.pw-edit-orig > br[data-mce-bogus]) {display: none;}'; // hide empty edit tags without placeholder
}

if ($backend) {
  $defaultCss .= 'pg-icon {display:none!important;}';
}

if ($this->user->hasPermission('PageFrontEdit')) {
  //change to inline to work with plain textarea inline elements
  $defaultCss .= 'body .pw-edit-InputfieldTextarea, .pw-edit-InputfieldTextarea .pw-edit-orig, .pw-edit-InputfieldTextarea .pw-edit-copy {display:inline-block;}';
}

if ($this->user->admin_theme == 'AdminThemeCanvas') {
  $defaultCss .= '.pw-edit-buttons .ui-button {background-color:black!important; border:none!important;} .pw-edit-cancel {opacity:0.5!important;}';
}

//load lazyload css to fade in images
if ($this->modules->get('FieldtypePageGrid')->plugins && $this->modules->get('FieldtypePageGrid')->plugins[0] === 'lazysizes') {
  $defaultCss .= '.lazyload,
  .lazyloading {
    opacity: 0;
  }
  
  .lazyloaded {
    opacity: 1;
    transition: opacity 600ms;
  }';
}

$defaultCss .= '*, *::before, *::after {
      box-sizing: border-box;
      -webkit-font-smoothing: antialiased;
      margin: 0;
      padding: 0;
    } ';

$defaultCss .= 'html, body {
    width:100%;
    } ';

$defaultCss .= 'p, h1, h2, h3, h4, h5, h6 {
  overflow-wrap: break-word;
  word-break: break-word;
  font-weight: normal;
    } ';

$defaultCss .= 'a {
   color: inherit;
   } ';

//    $defaultCss .= 'html, body {
//    height: 100%;
//    } ';

$defaultCss .= 'input, button, textarea, select {
      font: inherit;
    } ';

$defaultCss .= '.pg-main, .pg-group {
      display: grid;
      margin: 0 auto;
      list-style: none;
      column-gap: 30px;
      row-gap: 30px;
      grid-template-columns: repeat(12, 1fr);
      grid-template-rows: auto;
      grid-column-end: -1;
      grid-column-start: 1;
      grid-row-start: auto;
      width: 100%;
      height: auto;
      box-sizing: border-box;
      position: relative;
      overflow-wrap: break-word;
      word-break: break-word;
    } ';

if ($this->modules->get('FieldtypePageGrid')->fallbackFonts) {
  $defaultCss .= 'html {
    font-family:' . $this->modules->get('FieldtypePageGrid')->fallbackFonts . ';
}';
}

$defaultCss .= '.pg {
     grid-column-end: -1;
     grid-column-start: 1;
    } ';

$defaultCss .= '.pg-item {
      animation-fill-mode: forwards!important;
    } ';

// $defaultCss .= '.pg-image, .pg-video {
//     overflow: hidden;
//   } ';

$defaultCss .= '.pg-image img, .pg-video video, .pg-media-responsive, .pg-fileupload {
      display: block;
      width: 100%;
      max-width: 100%;
    } ';

$defaultCss .= '.pg-state-click {cursor:pointer;} ';

//make css var default to initial to stop inheritance
$defaultCss .= '* {--pg-animation: initial;}';

$defaultCss .= '.pg-event-inview {transition: all 600ms ease ;} ';

// Small devices default ( landscape phones, 576px and up )
$defaultCss .= ' @media (max-width: 640px) {
        html:root .pg-main,
        html:root .pg-group {
              column-gap: 15px;
              row-gap: 15px;
          }
          html:root .pg-item,
          html.breakpoint-s .pg-item {
            grid-column: 1/-1;
            /* grid-row-end: span 1;*/
            grid-row: auto;
            }
        } ';

//load TinyMCE styles
$tmceString = $this->modules->get('InputfieldTinyMCE')->styleFormatsCSS;
if ($tmceString) {
  $tmceStringFirst = mb_substr($tmceString, 0, 1);
  if ($tmceStringFirst == '#') {
    $defaultCss .= strstr($tmceString, " ");
  } else {
    $defaultCss .= $tmceString;
  }
}
//END load TinyMCE styles

$defaultCss .= strip_tags($this->ft->customStyles, '');

$defaultCss .= '</style>';

// custom fonts ---------------------------------------
$customFont = '';
$localFonts = $this->getFontNames();
$filePath = $this->getFontPath();
$fileUrl = $this->config->urls->templates . 'fonts/';

foreach ($localFonts as $font) {

  $fontExt = '.' . pathinfo($font, PATHINFO_EXTENSION);
  $fontName = str_replace($fontExt, '', $font);

  $fontFile = $filePath . $font;
  if (file_exists($fontFile)) {

    $customFont .= '<style class="pg-custom-fonts">';
    $customFont .= '@font-face {
    font-display: swap;
    font-family: "' . $fontName . '";
    src: url("' . $fileUrl . $font . '");
    font-weight: 400;
    font-style: normal;
    font-stretch: normal;
    }
    ;';
    $customFont .= '</style>';
  }
}

return  $customFont . $defaultCss;
