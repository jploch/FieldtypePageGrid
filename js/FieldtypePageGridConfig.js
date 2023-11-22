/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "http://localhost:3335/site/modules/FieldtypePageGrid/js/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 2);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./FieldtypePageGridConfig.js":
/*!************************************!*\
  !*** ./FieldtypePageGridConfig.js ***!
  \************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  console.log('FieldtypePageGird js loaded'); //fix inputfield customStyles height

  var customStyles = $('#Inputfield_customStyles'); // console.log(url);
  //fix tables

  $('.pw-table-responsive').css('margin', '0'); //set height

  customStyles.css('min-height', customStyles.prop('scrollHeight') + 'px');
  console.log(customStyles);
  console.log(customStyles.scrollHeight + 'px'); //make header open more easy

  $('.Inputfield').addClass('InputfieldStateWasCollapsed'); //save collapsed states

  $(document).on('click', '.InputfieldHeader', function (e) {
    setTimeout(function () {
      console.log('click');
      var collapsed = $('.InputfieldStateCollapsed');
      var value = '';
      collapsed.each(function () {
        var name = $(this).attr('id');
        var name = name.replace("Inputfield_", "");
        var name = name.replace("wrap_", "");
        console.log(name);
        value += name + ',';
      });
      $('#Inputfield_collapsedState').val(value);
    }, 500);
  }); //font uploader

  if ($('.Inputfield_uploadFonts').length == 0) {
    return;
  } //add attribute to form to make it work with fileuploader inside wrapper


  $('form').attr('enctype', 'multipart/form-data'); //styling

  $('.Inputfield_uploadFonts .InputfieldHeader').hide();
  $('<style>.AdminThemeCanvas .Inputfield_customFontList tr{border:2px solid #fff;}</sytle>').appendTo($('body')); //hide false error

  var errors = $('.NoticeError');
  errors.each(function () {
    var error = $(this);
    var errorText = error.text();
    var hasString = "Unable to move uploaded file";

    if (errorText.includes(hasString)) {
      error.hide();
    }
  });
  $('#notices').show(); //make sure the delete field is empty

  $('#Inputfield_deleteFonts').val(''); //mark deleted

  $(document).on('click', '.Inputfield_customFontList .delete-file', function (e) {
    e.preventDefault();
    var row = $(this).closest('tr');
    var deleteField = $('#Inputfield_deleteFonts');
    row.toggleClass('InputfieldIsError pg-font-delete');
    var val = $('.pg-font-delete label').text();
    deleteField.val(val);
  });
});

/***/ }),

/***/ 2:
/*!******************************************!*\
  !*** multi ./FieldtypePageGridConfig.js ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! ./FieldtypePageGridConfig.js */"./FieldtypePageGridConfig.js");


/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vLy4vRmllbGR0eXBlUGFnZUdyaWRDb25maWcuanMiXSwibmFtZXMiOlsiJCIsImRvY3VtZW50IiwicmVhZHkiLCJjb25zb2xlIiwibG9nIiwiY3VzdG9tU3R5bGVzIiwiY3NzIiwicHJvcCIsInNjcm9sbEhlaWdodCIsImFkZENsYXNzIiwib24iLCJlIiwic2V0VGltZW91dCIsImNvbGxhcHNlZCIsInZhbHVlIiwiZWFjaCIsIm5hbWUiLCJhdHRyIiwicmVwbGFjZSIsInZhbCIsImxlbmd0aCIsImhpZGUiLCJhcHBlbmRUbyIsImVycm9ycyIsImVycm9yIiwiZXJyb3JUZXh0IiwidGV4dCIsImhhc1N0cmluZyIsImluY2x1ZGVzIiwic2hvdyIsInByZXZlbnREZWZhdWx0Iiwicm93IiwiY2xvc2VzdCIsImRlbGV0ZUZpZWxkIiwidG9nZ2xlQ2xhc3MiXSwibWFwcGluZ3MiOiI7UUFBQTtRQUNBOztRQUVBO1FBQ0E7O1FBRUE7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7O1FBRUE7UUFDQTs7UUFFQTtRQUNBOztRQUVBO1FBQ0E7UUFDQTs7O1FBR0E7UUFDQTs7UUFFQTtRQUNBOztRQUVBO1FBQ0E7UUFDQTtRQUNBLDBDQUEwQyxnQ0FBZ0M7UUFDMUU7UUFDQTs7UUFFQTtRQUNBO1FBQ0E7UUFDQSx3REFBd0Qsa0JBQWtCO1FBQzFFO1FBQ0EsaURBQWlELGNBQWM7UUFDL0Q7O1FBRUE7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBLHlDQUF5QyxpQ0FBaUM7UUFDMUUsZ0hBQWdILG1CQUFtQixFQUFFO1FBQ3JJO1FBQ0E7O1FBRUE7UUFDQTtRQUNBO1FBQ0EsMkJBQTJCLDBCQUEwQixFQUFFO1FBQ3ZELGlDQUFpQyxlQUFlO1FBQ2hEO1FBQ0E7UUFDQTs7UUFFQTtRQUNBLHNEQUFzRCwrREFBK0Q7O1FBRXJIO1FBQ0E7OztRQUdBO1FBQ0E7Ozs7Ozs7Ozs7OztBQ2xGQUEsQ0FBQyxDQUFDQyxRQUFELENBQUQsQ0FBWUMsS0FBWixDQUFrQixZQUFZO0FBQzVCQyxTQUFPLENBQUNDLEdBQVIsQ0FBWSw2QkFBWixFQUQ0QixDQUc1Qjs7QUFDQSxNQUFJQyxZQUFZLEdBQUdMLENBQUMsQ0FBQywwQkFBRCxDQUFwQixDQUo0QixDQUs1QjtBQUVBOztBQUNBQSxHQUFDLENBQUMsc0JBQUQsQ0FBRCxDQUEwQk0sR0FBMUIsQ0FBOEIsUUFBOUIsRUFBd0MsR0FBeEMsRUFSNEIsQ0FVNUI7O0FBQ0FELGNBQVksQ0FBQ0MsR0FBYixDQUFpQixZQUFqQixFQUErQkQsWUFBWSxDQUFDRSxJQUFiLENBQWtCLGNBQWxCLElBQW9DLElBQW5FO0FBQ0FKLFNBQU8sQ0FBQ0MsR0FBUixDQUFZQyxZQUFaO0FBQ0FGLFNBQU8sQ0FBQ0MsR0FBUixDQUFZQyxZQUFZLENBQUNHLFlBQWIsR0FBNEIsSUFBeEMsRUFiNEIsQ0FlNUI7O0FBQ0FSLEdBQUMsQ0FBQyxhQUFELENBQUQsQ0FBaUJTLFFBQWpCLENBQTBCLDZCQUExQixFQWhCNEIsQ0FrQjVCOztBQUNBVCxHQUFDLENBQUNDLFFBQUQsQ0FBRCxDQUFZUyxFQUFaLENBQWUsT0FBZixFQUF3QixtQkFBeEIsRUFBNkMsVUFBVUMsQ0FBVixFQUFhO0FBRXhEQyxjQUFVLENBQUMsWUFBTTtBQUNmVCxhQUFPLENBQUNDLEdBQVIsQ0FBWSxPQUFaO0FBQ0EsVUFBSVMsU0FBUyxHQUFHYixDQUFDLENBQUMsMkJBQUQsQ0FBakI7QUFDQSxVQUFJYyxLQUFLLEdBQUcsRUFBWjtBQUNBRCxlQUFTLENBQUNFLElBQVYsQ0FBZSxZQUFZO0FBQ3pCLFlBQUlDLElBQUksR0FBR2hCLENBQUMsQ0FBQyxJQUFELENBQUQsQ0FBUWlCLElBQVIsQ0FBYSxJQUFiLENBQVg7QUFDQSxZQUFJRCxJQUFJLEdBQUdBLElBQUksQ0FBQ0UsT0FBTCxDQUFhLGFBQWIsRUFBNEIsRUFBNUIsQ0FBWDtBQUNBLFlBQUlGLElBQUksR0FBR0EsSUFBSSxDQUFDRSxPQUFMLENBQWEsT0FBYixFQUFzQixFQUF0QixDQUFYO0FBQ0FmLGVBQU8sQ0FBQ0MsR0FBUixDQUFZWSxJQUFaO0FBQ0FGLGFBQUssSUFBSUUsSUFBSSxHQUFHLEdBQWhCO0FBQ0QsT0FORDtBQVFBaEIsT0FBQyxDQUFDLDRCQUFELENBQUQsQ0FBZ0NtQixHQUFoQyxDQUFvQ0wsS0FBcEM7QUFDRCxLQWJTLEVBYVAsR0FiTyxDQUFWO0FBZUQsR0FqQkQsRUFuQjRCLENBc0M1Qjs7QUFDQSxNQUFJZCxDQUFDLENBQUMseUJBQUQsQ0FBRCxDQUE2Qm9CLE1BQTdCLElBQXVDLENBQTNDLEVBQThDO0FBQzVDO0FBQ0QsR0F6QzJCLENBMkM1Qjs7O0FBQ0FwQixHQUFDLENBQUMsTUFBRCxDQUFELENBQVVpQixJQUFWLENBQWUsU0FBZixFQUEwQixxQkFBMUIsRUE1QzRCLENBOEM1Qjs7QUFDQWpCLEdBQUMsQ0FBQywyQ0FBRCxDQUFELENBQStDcUIsSUFBL0M7QUFDQXJCLEdBQUMsQ0FBQyx3RkFBRCxDQUFELENBQTRGc0IsUUFBNUYsQ0FBcUd0QixDQUFDLENBQUMsTUFBRCxDQUF0RyxFQWhENEIsQ0FrRDVCOztBQUNBLE1BQUl1QixNQUFNLEdBQUd2QixDQUFDLENBQUMsY0FBRCxDQUFkO0FBQ0F1QixRQUFNLENBQUNSLElBQVAsQ0FBWSxZQUFZO0FBQ3RCLFFBQUlTLEtBQUssR0FBR3hCLENBQUMsQ0FBQyxJQUFELENBQWI7QUFDQSxRQUFJeUIsU0FBUyxHQUFHRCxLQUFLLENBQUNFLElBQU4sRUFBaEI7QUFDQSxRQUFJQyxTQUFTLEdBQUcsOEJBQWhCOztBQUNBLFFBQUlGLFNBQVMsQ0FBQ0csUUFBVixDQUFtQkQsU0FBbkIsQ0FBSixFQUFtQztBQUNqQ0gsV0FBSyxDQUFDSCxJQUFOO0FBQ0Q7QUFDRixHQVBEO0FBU0FyQixHQUFDLENBQUMsVUFBRCxDQUFELENBQWM2QixJQUFkLEdBN0Q0QixDQStENUI7O0FBQ0E3QixHQUFDLENBQUMseUJBQUQsQ0FBRCxDQUE2Qm1CLEdBQTdCLENBQWlDLEVBQWpDLEVBaEU0QixDQWtFNUI7O0FBQ0FuQixHQUFDLENBQUNDLFFBQUQsQ0FBRCxDQUFZUyxFQUFaLENBQWUsT0FBZixFQUF3Qix5Q0FBeEIsRUFBbUUsVUFBVUMsQ0FBVixFQUFhO0FBQzlFQSxLQUFDLENBQUNtQixjQUFGO0FBQ0EsUUFBSUMsR0FBRyxHQUFHL0IsQ0FBQyxDQUFDLElBQUQsQ0FBRCxDQUFRZ0MsT0FBUixDQUFnQixJQUFoQixDQUFWO0FBQ0EsUUFBSUMsV0FBVyxHQUFHakMsQ0FBQyxDQUFDLHlCQUFELENBQW5CO0FBQ0ErQixPQUFHLENBQUNHLFdBQUosQ0FBZ0Isa0NBQWhCO0FBRUEsUUFBSWYsR0FBRyxHQUFHbkIsQ0FBQyxDQUFDLHVCQUFELENBQUQsQ0FBMkIwQixJQUEzQixFQUFWO0FBRUFPLGVBQVcsQ0FBQ2QsR0FBWixDQUFnQkEsR0FBaEI7QUFFRCxHQVZEO0FBWUQsQ0EvRUQsRSIsImZpbGUiOiJGaWVsZHR5cGVQYWdlR3JpZENvbmZpZy5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwgeyBlbnVtZXJhYmxlOiB0cnVlLCBnZXQ6IGdldHRlciB9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZGVmaW5lIF9fZXNNb2R1bGUgb24gZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5yID0gZnVuY3Rpb24oZXhwb3J0cykge1xuIFx0XHRpZih0eXBlb2YgU3ltYm9sICE9PSAndW5kZWZpbmVkJyAmJiBTeW1ib2wudG9TdHJpbmdUYWcpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgU3ltYm9sLnRvU3RyaW5nVGFnLCB7IHZhbHVlOiAnTW9kdWxlJyB9KTtcbiBcdFx0fVxuIFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgJ19fZXNNb2R1bGUnLCB7IHZhbHVlOiB0cnVlIH0pO1xuIFx0fTtcblxuIFx0Ly8gY3JlYXRlIGEgZmFrZSBuYW1lc3BhY2Ugb2JqZWN0XG4gXHQvLyBtb2RlICYgMTogdmFsdWUgaXMgYSBtb2R1bGUgaWQsIHJlcXVpcmUgaXRcbiBcdC8vIG1vZGUgJiAyOiBtZXJnZSBhbGwgcHJvcGVydGllcyBvZiB2YWx1ZSBpbnRvIHRoZSBuc1xuIFx0Ly8gbW9kZSAmIDQ6IHJldHVybiB2YWx1ZSB3aGVuIGFscmVhZHkgbnMgb2JqZWN0XG4gXHQvLyBtb2RlICYgOHwxOiBiZWhhdmUgbGlrZSByZXF1aXJlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnQgPSBmdW5jdGlvbih2YWx1ZSwgbW9kZSkge1xuIFx0XHRpZihtb2RlICYgMSkgdmFsdWUgPSBfX3dlYnBhY2tfcmVxdWlyZV9fKHZhbHVlKTtcbiBcdFx0aWYobW9kZSAmIDgpIHJldHVybiB2YWx1ZTtcbiBcdFx0aWYoKG1vZGUgJiA0KSAmJiB0eXBlb2YgdmFsdWUgPT09ICdvYmplY3QnICYmIHZhbHVlICYmIHZhbHVlLl9fZXNNb2R1bGUpIHJldHVybiB2YWx1ZTtcbiBcdFx0dmFyIG5zID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5yKG5zKTtcbiBcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KG5zLCAnZGVmYXVsdCcsIHsgZW51bWVyYWJsZTogdHJ1ZSwgdmFsdWU6IHZhbHVlIH0pO1xuIFx0XHRpZihtb2RlICYgMiAmJiB0eXBlb2YgdmFsdWUgIT0gJ3N0cmluZycpIGZvcih2YXIga2V5IGluIHZhbHVlKSBfX3dlYnBhY2tfcmVxdWlyZV9fLmQobnMsIGtleSwgZnVuY3Rpb24oa2V5KSB7IHJldHVybiB2YWx1ZVtrZXldOyB9LmJpbmQobnVsbCwga2V5KSk7XG4gXHRcdHJldHVybiBucztcbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiaHR0cDovL2xvY2FsaG9zdDozMzM1L3NpdGUvbW9kdWxlcy9GaWVsZHR5cGVQYWdlR3JpZC9qcy9cIjtcblxuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDIpO1xuIiwiJChkb2N1bWVudCkucmVhZHkoZnVuY3Rpb24gKCkge1xuICBjb25zb2xlLmxvZygnRmllbGR0eXBlUGFnZUdpcmQganMgbG9hZGVkJyk7XG5cbiAgLy9maXggaW5wdXRmaWVsZCBjdXN0b21TdHlsZXMgaGVpZ2h0XG4gIHZhciBjdXN0b21TdHlsZXMgPSAkKCcjSW5wdXRmaWVsZF9jdXN0b21TdHlsZXMnKTtcbiAgLy8gY29uc29sZS5sb2codXJsKTtcblxuICAvL2ZpeCB0YWJsZXNcbiAgJCgnLnB3LXRhYmxlLXJlc3BvbnNpdmUnKS5jc3MoJ21hcmdpbicsICcwJyk7XG5cbiAgLy9zZXQgaGVpZ2h0XG4gIGN1c3RvbVN0eWxlcy5jc3MoJ21pbi1oZWlnaHQnLCBjdXN0b21TdHlsZXMucHJvcCgnc2Nyb2xsSGVpZ2h0JykgKyAncHgnKTtcbiAgY29uc29sZS5sb2coY3VzdG9tU3R5bGVzKTtcbiAgY29uc29sZS5sb2coY3VzdG9tU3R5bGVzLnNjcm9sbEhlaWdodCArICdweCcpO1xuXG4gIC8vbWFrZSBoZWFkZXIgb3BlbiBtb3JlIGVhc3lcbiAgJCgnLklucHV0ZmllbGQnKS5hZGRDbGFzcygnSW5wdXRmaWVsZFN0YXRlV2FzQ29sbGFwc2VkJyk7XG5cbiAgLy9zYXZlIGNvbGxhcHNlZCBzdGF0ZXNcbiAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgJy5JbnB1dGZpZWxkSGVhZGVyJywgZnVuY3Rpb24gKGUpIHtcblxuICAgIHNldFRpbWVvdXQoKCkgPT4ge1xuICAgICAgY29uc29sZS5sb2coJ2NsaWNrJyk7XG4gICAgICB2YXIgY29sbGFwc2VkID0gJCgnLklucHV0ZmllbGRTdGF0ZUNvbGxhcHNlZCcpO1xuICAgICAgdmFyIHZhbHVlID0gJyc7XG4gICAgICBjb2xsYXBzZWQuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICAgIHZhciBuYW1lID0gJCh0aGlzKS5hdHRyKCdpZCcpO1xuICAgICAgICB2YXIgbmFtZSA9IG5hbWUucmVwbGFjZShcIklucHV0ZmllbGRfXCIsIFwiXCIpO1xuICAgICAgICB2YXIgbmFtZSA9IG5hbWUucmVwbGFjZShcIndyYXBfXCIsIFwiXCIpO1xuICAgICAgICBjb25zb2xlLmxvZyhuYW1lKTtcbiAgICAgICAgdmFsdWUgKz0gbmFtZSArICcsJztcbiAgICAgIH0pO1xuXG4gICAgICAkKCcjSW5wdXRmaWVsZF9jb2xsYXBzZWRTdGF0ZScpLnZhbCh2YWx1ZSk7XG4gICAgfSwgNTAwKVxuXG4gIH0pO1xuXG4gIC8vZm9udCB1cGxvYWRlclxuICBpZiAoJCgnLklucHV0ZmllbGRfdXBsb2FkRm9udHMnKS5sZW5ndGggPT0gMCkge1xuICAgIHJldHVybjtcbiAgfVxuXG4gIC8vYWRkIGF0dHJpYnV0ZSB0byBmb3JtIHRvIG1ha2UgaXQgd29yayB3aXRoIGZpbGV1cGxvYWRlciBpbnNpZGUgd3JhcHBlclxuICAkKCdmb3JtJykuYXR0cignZW5jdHlwZScsICdtdWx0aXBhcnQvZm9ybS1kYXRhJyk7XG5cbiAgLy9zdHlsaW5nXG4gICQoJy5JbnB1dGZpZWxkX3VwbG9hZEZvbnRzIC5JbnB1dGZpZWxkSGVhZGVyJykuaGlkZSgpO1xuICAkKCc8c3R5bGU+LkFkbWluVGhlbWVDYW52YXMgLklucHV0ZmllbGRfY3VzdG9tRm9udExpc3QgdHJ7Ym9yZGVyOjJweCBzb2xpZCAjZmZmO308L3N5dGxlPicpLmFwcGVuZFRvKCQoJ2JvZHknKSk7XG5cbiAgLy9oaWRlIGZhbHNlIGVycm9yXG4gIHZhciBlcnJvcnMgPSAkKCcuTm90aWNlRXJyb3InKTtcbiAgZXJyb3JzLmVhY2goZnVuY3Rpb24gKCkge1xuICAgIHZhciBlcnJvciA9ICQodGhpcyk7XG4gICAgdmFyIGVycm9yVGV4dCA9IGVycm9yLnRleHQoKTtcbiAgICB2YXIgaGFzU3RyaW5nID0gXCJVbmFibGUgdG8gbW92ZSB1cGxvYWRlZCBmaWxlXCI7XG4gICAgaWYgKGVycm9yVGV4dC5pbmNsdWRlcyhoYXNTdHJpbmcpKSB7XG4gICAgICBlcnJvci5oaWRlKCk7XG4gICAgfVxuICB9KTtcblxuICAkKCcjbm90aWNlcycpLnNob3coKTtcblxuICAvL21ha2Ugc3VyZSB0aGUgZGVsZXRlIGZpZWxkIGlzIGVtcHR5XG4gICQoJyNJbnB1dGZpZWxkX2RlbGV0ZUZvbnRzJykudmFsKCcnKTtcblxuICAvL21hcmsgZGVsZXRlZFxuICAkKGRvY3VtZW50KS5vbignY2xpY2snLCAnLklucHV0ZmllbGRfY3VzdG9tRm9udExpc3QgLmRlbGV0ZS1maWxlJywgZnVuY3Rpb24gKGUpIHtcbiAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgdmFyIHJvdyA9ICQodGhpcykuY2xvc2VzdCgndHInKTtcbiAgICB2YXIgZGVsZXRlRmllbGQgPSAkKCcjSW5wdXRmaWVsZF9kZWxldGVGb250cycpO1xuICAgIHJvdy50b2dnbGVDbGFzcygnSW5wdXRmaWVsZElzRXJyb3IgcGctZm9udC1kZWxldGUnKTtcblxuICAgIHZhciB2YWwgPSAkKCcucGctZm9udC1kZWxldGUgbGFiZWwnKS50ZXh0KCk7XG5cbiAgICBkZWxldGVGaWVsZC52YWwodmFsKTtcblxuICB9KTtcblxufSk7XG4iXSwic291cmNlUm9vdCI6IiJ9