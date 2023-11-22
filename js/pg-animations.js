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
/******/ 	return __webpack_require__(__webpack_require__.s = 4);
/******/ })
/************************************************************************/
/******/ ({

/***/ "../../node_modules/basicscroll/dist/basicScroll.min.js":
/*!******************************************************************************************************************!*\
  !*** /Users/jploch/dev/pgrid-dev/src/modules/FieldtypePageGrid/node_modules/basicscroll/dist/basicScroll.min.js ***!
  \******************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var require;var require;!function(t){if(true)module.exports=t();else {}}((function(){return function t(n,o,e){function r(i,c){if(!o[i]){if(!n[i]){var f="function"==typeof require&&require;if(!c&&f)return require(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var s=o[i]={exports:{}};n[i][0].call(s.exports,(function(t){return r(n[i][1][t]||t)}),s,s.exports,t,n,o,e)}return o[i].exports}for(var u="function"==typeof require&&require,i=0;i<e.length;i++)r(e[i]);return r}({1:[function(t,n,o){n.exports=function(t){var n=2.5949095;return(t*=2)<1?t*t*((n+1)*t-n)*.5:.5*((t-=2)*t*((n+1)*t+n)+2)}},{}],2:[function(t,n,o){n.exports=function(t){var n=1.70158;return t*t*((n+1)*t-n)}},{}],3:[function(t,n,o){n.exports=function(t){var n=1.70158;return--t*t*((n+1)*t+n)+1}},{}],4:[function(t,n,o){var e=t("./bounce-out");n.exports=function(t){return t<.5?.5*(1-e(1-2*t)):.5*e(2*t-1)+.5}},{"./bounce-out":6}],5:[function(t,n,o){var e=t("./bounce-out");n.exports=function(t){return 1-e(1-t)}},{"./bounce-out":6}],6:[function(t,n,o){n.exports=function(t){var n=t*t;return t<4/11?7.5625*n:t<8/11?9.075*n-9.9*t+3.4:t<.9?4356/361*n-35442/1805*t+16061/1805:10.8*t*t-20.52*t+10.72}},{}],7:[function(t,n,o){n.exports=function(t){return(t*=2)<1?-.5*(Math.sqrt(1-t*t)-1):.5*(Math.sqrt(1-(t-=2)*t)+1)}},{}],8:[function(t,n,o){n.exports=function(t){return 1-Math.sqrt(1-t*t)}},{}],9:[function(t,n,o){n.exports=function(t){return Math.sqrt(1- --t*t)}},{}],10:[function(t,n,o){n.exports=function(t){return t<.5?4*t*t*t:.5*Math.pow(2*t-2,3)+1}},{}],11:[function(t,n,o){n.exports=function(t){return t*t*t}},{}],12:[function(t,n,o){n.exports=function(t){var n=t-1;return n*n*n+1}},{}],13:[function(t,n,o){n.exports=function(t){return t<.5?.5*Math.sin(13*Math.PI/2*2*t)*Math.pow(2,10*(2*t-1)):.5*Math.sin(-13*Math.PI/2*(2*t-1+1))*Math.pow(2,-10*(2*t-1))+1}},{}],14:[function(t,n,o){n.exports=function(t){return Math.sin(13*t*Math.PI/2)*Math.pow(2,10*(t-1))}},{}],15:[function(t,n,o){n.exports=function(t){return Math.sin(-13*(t+1)*Math.PI/2)*Math.pow(2,-10*t)+1}},{}],16:[function(t,n,o){n.exports=function(t){return 0===t||1===t?t:t<.5?.5*Math.pow(2,20*t-10):-.5*Math.pow(2,10-20*t)+1}},{}],17:[function(t,n,o){n.exports=function(t){return 0===t?t:Math.pow(2,10*(t-1))}},{}],18:[function(t,n,o){n.exports=function(t){return 1===t?t:1-Math.pow(2,-10*t)}},{}],19:[function(t,n,o){n.exports={backInOut:t("./back-in-out"),backIn:t("./back-in"),backOut:t("./back-out"),bounceInOut:t("./bounce-in-out"),bounceIn:t("./bounce-in"),bounceOut:t("./bounce-out"),circInOut:t("./circ-in-out"),circIn:t("./circ-in"),circOut:t("./circ-out"),cubicInOut:t("./cubic-in-out"),cubicIn:t("./cubic-in"),cubicOut:t("./cubic-out"),elasticInOut:t("./elastic-in-out"),elasticIn:t("./elastic-in"),elasticOut:t("./elastic-out"),expoInOut:t("./expo-in-out"),expoIn:t("./expo-in"),expoOut:t("./expo-out"),linear:t("./linear"),quadInOut:t("./quad-in-out"),quadIn:t("./quad-in"),quadOut:t("./quad-out"),quartInOut:t("./quart-in-out"),quartIn:t("./quart-in"),quartOut:t("./quart-out"),quintInOut:t("./quint-in-out"),quintIn:t("./quint-in"),quintOut:t("./quint-out"),sineInOut:t("./sine-in-out"),sineIn:t("./sine-in"),sineOut:t("./sine-out")}},{"./back-in":2,"./back-in-out":1,"./back-out":3,"./bounce-in":5,"./bounce-in-out":4,"./bounce-out":6,"./circ-in":8,"./circ-in-out":7,"./circ-out":9,"./cubic-in":11,"./cubic-in-out":10,"./cubic-out":12,"./elastic-in":14,"./elastic-in-out":13,"./elastic-out":15,"./expo-in":17,"./expo-in-out":16,"./expo-out":18,"./linear":20,"./quad-in":22,"./quad-in-out":21,"./quad-out":23,"./quart-in":25,"./quart-in-out":24,"./quart-out":26,"./quint-in":28,"./quint-in-out":27,"./quint-out":29,"./sine-in":31,"./sine-in-out":30,"./sine-out":32}],20:[function(t,n,o){n.exports=function(t){return t}},{}],21:[function(t,n,o){n.exports=function(t){return(t/=.5)<1?.5*t*t:-.5*(--t*(t-2)-1)}},{}],22:[function(t,n,o){n.exports=function(t){return t*t}},{}],23:[function(t,n,o){n.exports=function(t){return-t*(t-2)}},{}],24:[function(t,n,o){n.exports=function(t){return t<.5?8*Math.pow(t,4):-8*Math.pow(t-1,4)+1}},{}],25:[function(t,n,o){n.exports=function(t){return Math.pow(t,4)}},{}],26:[function(t,n,o){n.exports=function(t){return Math.pow(t-1,3)*(1-t)+1}},{}],27:[function(t,n,o){n.exports=function(t){return(t*=2)<1?.5*t*t*t*t*t:.5*((t-=2)*t*t*t*t+2)}},{}],28:[function(t,n,o){n.exports=function(t){return t*t*t*t*t}},{}],29:[function(t,n,o){n.exports=function(t){return--t*t*t*t*t+1}},{}],30:[function(t,n,o){n.exports=function(t){return-.5*(Math.cos(Math.PI*t)-1)}},{}],31:[function(t,n,o){n.exports=function(t){var n=Math.cos(t*Math.PI*.5);return Math.abs(n)<1e-14?1:1-n}},{}],32:[function(t,n,o){n.exports=function(t){return Math.sin(t*Math.PI/2)}},{}],33:[function(t,n,o){n.exports=function(t,n){n||(n=[0,""]),t=String(t);var o=parseFloat(t,10);return n[0]=o,n[1]=t.match(/[\d.\-\+]*\s*(.*)/)[1]||"",n}},{}],34:[function(t,n,o){"use strict";Object.defineProperty(o,"__esModule",{value:!0}),o.create=void 0;var e=u(t("parse-unit")),r=u(t("eases"));function u(t){return t&&t.__esModule?t:{default:t}}function i(t){return(i="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t})(t)}var c,f,a,s=[],p="undefined"!=typeof window,l=function(){return(document.scrollingElement||document.documentElement).scrollTop},d=function(){return window.innerHeight||window.outerHeight},m=function(t){return!1===isNaN((0,e.default)(t)[0])},b=function(t){var n=(0,e.default)(t);return{value:n[0],unit:n[1]}},h=function(t){return null!==String(t).match(/^[a-z]+-[a-z]+$/)},w=function(t,n){return!0===t?n.elem:t instanceof HTMLElement==!0?n.direct:n.global},y=function(t,n){var o=arguments.length>2&&void 0!==arguments[2]?arguments[2]:l(),e=arguments.length>3&&void 0!==arguments[3]?arguments[3]:d(),r=n.getBoundingClientRect(),u=t.match(/^[a-z]+/)[0],i=t.match(/[a-z]+$/)[0],c=0;return"top"===i&&(c-=0),"middle"===i&&(c-=e/2),"bottom"===i&&(c-=e),"top"===u&&(c+=r.top+o),"middle"===u&&(c+=r.top+o+r.height/2),"bottom"===u&&(c+=r.top+o+r.height),"".concat(c,"px")},v=function(t){var n=arguments.length>1&&void 0!==arguments[1]?arguments[1]:l(),o=t.getData(),e=o.to.value-o.from.value,r=n-o.from.value,u=r/(e/100),i=Math.min(Math.max(u,0),100),c=w(o.direct,{global:document.documentElement,elem:o.elem,direct:o.direct}),f=Object.keys(o.props).reduce((function(t,n){var e=o.props[n],r=e.from.unit||e.to.unit,u=e.from.value-e.to.value,c=e.timing(i/100),f=e.from.value-u*c,a=Math.round(1e4*f)/1e4;return t[n]=a+r,t}),{}),a=u>=0&&u<=100,s=u<0||u>100;return!0===a&&o.inside(t,u,f),!0===s&&o.outside(t,u,f),{elem:c,props:f}},x=function(t,n){Object.keys(n).forEach((function(o){return function(t,n){t.style.setProperty(n.key,n.value)}(t,{key:o,value:n[o]})}))};o.create=function(t){var n=null,o=!1,e={isActive:function(){return o},getData:function(){return n},calculate:function(){n=function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};if(null==(t=Object.assign({},t)).inside&&(t.inside=function(){}),null==t.outside&&(t.outside=function(){}),null==t.direct&&(t.direct=!1),null==t.track&&(t.track=!0),null==t.props&&(t.props={}),null==t.from)throw new Error("Missing property `from`");if(null==t.to)throw new Error("Missing property `to`");if("function"!=typeof t.inside)throw new Error("Property `inside` must be undefined or a function");if("function"!=typeof t.outside)throw new Error("Property `outside` must be undefined or a function");if("boolean"!=typeof t.direct&&t.direct instanceof HTMLElement==0)throw new Error("Property `direct` must be undefined, a boolean or a DOM element/node");if(!0===t.direct&&null==t.elem)throw new Error("Property `elem` is required when `direct` is true");if("boolean"!=typeof t.track)throw new Error("Property `track` must be undefined or a boolean");if("object"!==i(t.props))throw new Error("Property `props` must be undefined or an object");if(null==t.elem){if(!1===m(t.from))throw new Error("Property `from` must be a absolute value when no `elem` has been provided");if(!1===m(t.to))throw new Error("Property `to` must be a absolute value when no `elem` has been provided")}else!0===h(t.from)&&(t.from=y(t.from,t.elem)),!0===h(t.to)&&(t.to=y(t.to,t.elem));return t.from=b(t.from),t.to=b(t.to),t.props=Object.keys(t.props).reduce((function(n,o){var e=Object.assign({},t.props[o]);if(!1===m(e.from))throw new Error("Property `from` of prop must be a absolute value");if(!1===m(e.to))throw new Error("Property `from` of prop must be a absolute value");if(e.from=b(e.from),e.to=b(e.to),null==e.timing&&(e.timing=r.default.linear),"string"!=typeof e.timing&&"function"!=typeof e.timing)throw new Error("Property `timing` of prop must be undefined, a string or a function");if("string"==typeof e.timing&&null==r.default[e.timing])throw new Error("Unknown timing for property `timing` of prop");return"string"==typeof e.timing&&(e.timing=r.default[e.timing]),n[o]=e,n}),{}),t}(t)},update:function(){var t=v(e),n=t.elem,o=t.props;return x(n,o),o},start:function(){o=!0},stop:function(){o=!1},destroy:function(){s[u]=void 0}},u=s.push(e)-1;return e.calculate(),e},!0===p?(!function t(n,o){var e=function(){requestAnimationFrame((function(){return t(n,o)}))},r=function(t){return t.filter((function(t){return null!=t&&t.isActive()}))}(s);if(0===r.length)return e();var u=l();if(o===u)return e();o=u,r.map((function(t){return v(t,u)})).forEach((function(t){var n=t.elem,o=t.props;return x(n,o)})),e()}(),window.addEventListener("resize",(c=function(){(function(t){return t.filter((function(t){return null!=t&&t.getData().track}))})(s).forEach((function(t){t.calculate(),t.update()}))},f=50,a=null,function(){for(var t=arguments.length,n=new Array(t),o=0;o<t;o++)n[o]=arguments[o];clearTimeout(a),a=setTimeout((function(){return c.apply(void 0,n)}),f)}))):console.warn("basicScroll is not executing because you are using it in an environment without a `window` object")},{eases:19,"parse-unit":33}]},{},[34])(34)}));

/***/ }),

/***/ "./pg-animations.js":
/*!**************************!*\
  !*** ./pg-animations.js ***!
  \**************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var basicscroll__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! basicscroll */ "../../node_modules/basicscroll/dist/basicScroll.min.js");
/* harmony import */ var basicscroll__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(basicscroll__WEBPACK_IMPORTED_MODULE_0__);
function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _iterableToArrayLimit(arr, i) { if (typeof Symbol === "undefined" || !(Symbol.iterator in Object(arr))) return; var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

//needs basicscroll to work 
 // access pg data through var pgAnimations set via php
//** var pgAnimations"

var scrollAnimations = [],
    inviewTriggerClass = 'pg-event-trigger-inview'; //init all animations found in var pgAnimationsSelectors

function init() {
  //if no data found return
  if (pgAnimations === undefined) return;
  if (pgAnimationsSelectors === undefined) return;
  console.log('pg-animations.js loaded');
  console.log(pgAnimationsSelectors); //set empty array to be able to reinit scrollanimations

  scrollAnimations = []; //init for found selectors

  for (var _i = 0, _Object$entries = Object.entries(pgAnimationsSelectors); _i < _Object$entries.length; _i++) {
    var _Object$entries$_i = _slicedToArray(_Object$entries[_i], 2),
        key = _Object$entries$_i[0],
        selector = _Object$entries$_i[1];

    //get items and also event classes to be able to init items via clases from code
    var items = document.querySelectorAll(selector);
    items.forEach(function (el) {
      var animationNames = getComputedStyle(el).getPropertyValue('--pg-animation') || '';
      animationNames = animationNames.split(',');
      animationNames.forEach(function (animationName) {
        if (!animationName || animationName === '0') return;
        var animationData = pgAnimations[animationName][animationName];
        if (animationData === undefined || animationData === null) return;
        var type = animationData['animation-event'] || 'load';
        var triggerSelector = animationData['animation-trigger'] || 'self';
        var trigger = el;

        if (triggerSelector === 'parent') {
          trigger = el.parentNode;
        }

        if (triggerSelector !== 'self' && triggerSelector !== 'parent') {
          trigger = getClosest(el, triggerSelector);
        }

        if (trigger === undefined || trigger === null) {
          trigger = el;
        }

        animationData['animation-name'] = animationName;
        el.classList.add('pg-event-' + type);
        el.classList.add('pg-animation-' + animationName); // //make animation stop at end

        el.style.animationFillMode = 'forwards'; //add first keyframe if exists

        if (pgAnimations[animationName]['keyframes']['0'] !== undefined) {
          el.classList.add(pgAnimations[animationName]['keyframes']['0']['id']);
        } //add classes if animation is running


        el.addEventListener("animationstart", function (ani) {
          if (ani.animationName !== animationName) return;

          if (el.classList.contains('pg-event-trigger-inview') && el.classList.contains('pg-event-hover') && el.classList.contains('pg-event-hover-active')) {
            if (ani.elapsedTime !== 1) {
              //if animation starts from reverse remove state
              el.classList.remove(pgAnimations[animationName]['keyframes']['100']['id']);
            }
          }

          el.classList.add('pg-animation-is-running');

          if (ani.elapsedTime !== 1) {
            //if animation starts from reverse remove state
            el.classList.remove(pgAnimations[animationName]['keyframes']['100']['id']);
          } // trigger a DOM reflow to reinit animation
          // void el.offsetWidth;

        });
        el.addEventListener("animationcancel", function (ani) {
          if (ani.animationName !== animationName) return;
          el.classList.remove('pg-animation-is-running');

          if (ani.elapsedTime !== 1) {
            //if animation starts from reverse remove state
            el.classList.remove(pgAnimations[animationName]['keyframes']['100']['id']);
          }
        }); //remove animation when it's finished

        el.addEventListener("animationend", function (ani) {
          if (ani.animationName !== animationName) return;

          if (el.classList.contains('pg-event-trigger-inview') && el.classList.contains('pg-event-hover') && el.classList.contains('pg-event-hover-active')) {
            if (ani.elapsedTime !== 1) {
              //if animation starts from reverse remove state
              el.classList.remove(pgAnimations[animationName]['keyframes']['100']['id']);
            }
          }

          el.classList.remove('pg-animation-is-running'); // console.log('remove animation');

          if (ani.elapsedTime > 0) {
            //add last keyframe class to preserve state
            el.classList.add(pgAnimations[animationName]['keyframes']['100']['id']);
          } else {
            el.classList.remove(pgAnimations[animationName]['keyframes']['100']['id']);
            el.classList.remove('pg-event-hover-active');
          }

          el.classList.remove('pg-event-trigger-' + type);
          el.classList.remove('pg-animation-' + animationName);
        });

        if (type === 'load') {
          initEventLoad(el, trigger, animationData);
        }

        if (type === 'hover') {
          initEventHover(el, trigger, animationData);
        }

        if (type === 'click') {
          initEventClick(el, trigger, animationData);
        }

        if (type === 'scroll' || type === 'inview') {
          initEventScroll(el, trigger, animationData);
        }
      });
    });
  }

  ; // start animations

  scrollAnimations.forEach(function (animation) {
    return animation.start();
  });
}

function initEventLoad(el, trigger, animationData) {
  el.classList.add('pg-event-trigger-load');
}

function initEventHover(el, trigger, animationData) {
  var animationName = animationData['animation-name'];
  var reverse = animationData['animation-reverse'] || 'true'; //mouse enter

  trigger.addEventListener('mouseenter', function (e) {
    if (e.target !== e.currentTarget) return;
    if (el.classList.contains('pg-animation-is-running')) return;
    if (el.classList.contains('pg-event-hover-once')) return;
    el.classList.add('pg-event-hover-active');
    el.classList.add('pg-animation-' + animationName);
    el.classList.add("pg-event-trigger-hover");

    if (reverse === 'false') {
      el.classList.add('pg-event-hover-once');
    }
  }, {
    passive: true
  }); //mouse mouseout

  trigger.addEventListener('mouseleave', function (e) {
    if (e.target !== e.currentTarget) return;
    if (!el.classList.contains('pg-event-hover-active')) return;
    el.classList.remove('pg-event-hover-active'); //trigger if not playing

    if (!el.classList.contains('pg-animation-is-running')) {
      el.classList.add('pg-animation-' + animationName);
      el.classList.add("pg-event-trigger-hover");
    } //play reverse


    if (reverse === 'true') {
      playAnimation(el, animationName, true);
    }
  }, false);
}

function initEventClick(el, trigger, animationData) {
  var animationName = animationData['animation-name'];
  var reverse = animationData['animation-reverse'] || 'true'; //add click and touch events

  ['click', 'touchstart'].forEach(function (eventType) {
    trigger.addEventListener(eventType, function (e) {
      if (trigger == e.currentTarget) {
        // console.log('click');
        //trigger if not playing
        if (!el.classList.contains('pg-animation-is-running')) {
          el.classList.add('pg-animation-' + animationName);
          el.classList.add("pg-event-trigger-click");
        } //play reverse


        if (reverse === 'true' && el.classList.contains('pg-event-click-active')) {
          playAnimation(el, animationName, true);
        }

        el.classList.toggle('pg-event-click-active');
      }
    }, {
      passive: true
    });
  });
}

function initEventScroll(el, trigger, animationData) {
  var animationName = animationData['animation-name'];
  var keyframesData = pgAnimations[animationName]['keyframes'];
  var keyPrev = '0';
  var keyframeDataPrev = {}; //get options

  var type = animationData['animation-event'] || 'load';
  var pin = animationData['animation-pin'] || 'false';
  var once = animationData['animation-once'] || 'false';
  var timing = animationData['animation-timing'] || 'linear';
  var distance = parseInt(animationData['animation-distance']) || 100;
  var offset = parseInt(animationData['animation-start']) || 0;
  var scrollProps = {};
  var debug = false;
  var url = window.location.href;

  if (url.includes('?debug')) {
    debug = true;
  }

  if (type !== 'scroll' && type !== 'inview') {
    return;
  }

  if (pin === 'false') {
    pin = 0;
  }

  if (once === 'false') {
    once = 0;
  }

  if (debug) {
    var debugStart = '<div class="pg-animation-debug" style="bottom:' + offset + 'vh; position:fixed; height:1px; width:100vw; background-color:green; z-index:999; pointer-events:none;">' + animationName + ' start</div>';
    var debugEnd = '<div class="pg-animation-debug" style="bottom:' + (distance + offset) + 'vh; position:fixed; height:1px; width:100vw; background-color:red;  z-index:999; pointer-events:none;">' + animationName + ' end</div>';
    document.body.insertAdjacentHTML('beforeend', debugStart);
    document.body.insertAdjacentHTML('beforeend', debugEnd);
  } // console.log('distance: ' + distance);
  //create pin


  if (pin) {
    console.log('create pin');
    var pinStartPixel = window.innerHeight / 100 * (100 - offset);
    var pinEndPixel = window.innerHeight / 100 * (distance + offset);
    createPin(pin, trigger, pinStartPixel, pinEndPixel);
  } //for type inview create one animation


  if (type === 'inview') {
    // let start = Object.keys(keyframesData)[0];
    var prop = '--pg-animation-dummy-' + animationName; //create single dummy property

    scrollProps[prop] = {
      from: '0',
      to: '100'
    };
    createAnimation(el, trigger, offset, distance, type, pin, once, distance, offset, scrollProps, 1, 1, animationName, debug);
    return;
  } //for type scroll create animation per keyframe
  //sort keyframes


  Object.keys(keyframesData).sort().reduce(function (a, c) {
    return a[c] = keyframesData[c], a;
  }, {}); // console.log(keyframesData);

  var KeyframesTotal = Object.keys(keyframesData).length;
  var KeyframeCount = 0;
  var i = 0;

  for (var key in keyframesData) {
    //only get keyframes
    if (!keyframesData[key].hasOwnProperty('keyframe')) {
      continue;
    }

    KeyframeCount++; // get props of keyframes

    scrollProps = {};
    var keyframeData = keyframesData[key]['breakpoints']['base']['css']; //get prev value

    if (keyframesData[keyPrev] && keyframesData[keyPrev]['breakpoints']) {
      keyframeDataPrev = keyframesData[keyPrev]['breakpoints']['base']['css'];
    } // console.log('keyframe: ' + key);
    // set scrollProps for basicScroll


    var _loop = function _loop(_prop) {
      i++;
      var from = keyframeDataPrev[_prop] || '0';
      var to = keyframeData[_prop] || '0';
      var property = '--pg-' + _prop + i; // console.log(prop);
      // convert to absolute values we can passs to basicScroll eg. "20px" or "10%"
      // get css value fallback if prop with no unit and from not defined

      if (_prop === 'scale' || _prop === 'color' || _prop === 'background-color' || _prop === 'opacity') {
        if (keyframeDataPrev[_prop] === undefined) {
          from = getComputedStyle(el).getPropertyValue(_prop) || '1';
          if (from === 'none') from = '1';
        }
      } //convert transform string to individuall props


      if (_prop === 'transform') {
        //remove first empty space
        from = from.replace(/^ /, '');
        to = to.replace(/^ /, ''); //split string after space

        var transformFrom = from.split(' ');
        var transformTo = to.split(' ');
        transformFrom.forEach(function (key, index) {
          var property2 = property + index;
          var subProp = String(transformFrom[index]).split('(')[0];
          var from2 = String(transformFrom[index]).split('(').pop().split(')')[0];
          var to2 = String(transformTo[index]).split('(').pop().split(')')[0];
          if (transformFrom[index] === undefined) from2 = '0';
          if (transformTo[index] === undefined) to2 = '0'; // rename property2 for clarity

          if (subProp === 'translateY') {
            property2 = '--pg-' + subProp + index;
          } // console.log('prop:' + subProp + ' from2:' + from2 + ' to2:' + to2);


          scrollProps[property2] = {
            from: "".concat(from2),
            to: "".concat(to2),
            timing: "".concat(timing),
            prop: "".concat(_prop),
            subProp: "".concat(subProp)
          };
        });
        return "continue";
      } // if colors create multiple properties and css vars for ech RGB channel


      if (_prop === 'color' || _prop === 'background-color') {
        //convert values to arrays
        var rgbFrom = from.split(',');
        var rgbTo = to.split(','); //always force rgba value

        if (rgbFrom.length == 3) {
          rgbFrom[3] = '1';
        }

        if (rgbTo.length == 3) {
          rgbTo[3] = '1';
        }

        rgbFrom.forEach(function (color, index) {
          var property2 = String(property + index);
          var fromColor = String(rgbFrom[index]).replace(/[^\d.]/g, '');
          var toColor = String(rgbTo[index]).replace(/[^\d.]/g, ''); // console.log('prop2:' + property2 + ' fromColor:' + fromColor + ' toColor:' + toColor);

          scrollProps[property2] = {
            from: "".concat(fromColor),
            to: "".concat(toColor),
            timing: "".concat(timing),
            prop: "".concat(_prop)
          };
        });
        return "continue";
      } //single property


      scrollProps[property] = {
        from: "".concat(from),
        to: "".concat(to),
        timing: "".concat(timing),
        prop: "".concat(_prop)
      };
    };

    for (var _prop in keyframeData) {
      var _ret = _loop(_prop);

      if (_ret === "continue") continue;
    } //create one animation per keyframe


    createAnimation(el, trigger, keyPrev, key, type, pin, once, distance, offset, scrollProps, KeyframeCount, KeyframesTotal, animationName, debug); //set keyPrev to get from value

    keyPrev = key;
  }
}

function createAnimation(el, trigger, scrollStart, scrollEnd, type, pin, once, distance, offset, scrollProps, KeyframeCount, KeyframesTotal, animationName, debug) {
  // if (!prop || prop.startsWith('data-') || el.classList.contains('pg-scroll-wrapper') || el.classList.contains('pg-scroll-spacer')) {
  //   return;
  // }
  if (scrollEnd == scrollStart) {
    return;
  } //set origin
  // el.style.transformOrigin = '0 0';
  //add smoothing


  if (type === 'scroll') {
    el.style.transition = 'all 0.1s linear';
  } //set body overflow to hidden


  document.body.style.overflowX = 'hidden'; //disable lazyload for item to get right height

  if (el.querySelectorAll('.lazyload')[0]) {
    el.querySelectorAll('.lazyload')[0].srcset = el.querySelectorAll('.lazyload')[0].getAttribute('data-srcset');
    el.querySelectorAll('.lazyload')[0].classList.remove('lazyload');
  } //get start/end values in vh


  scrollStart = 100 - offset - distance / 100 * scrollStart;
  scrollEnd = 100 - offset - distance / 100 * scrollEnd;

  if (type === 'inview') {
    scrollStart = 100 - offset - distance / 100 * 1;
    scrollEnd = 100 - offset - distance / 100 * 100;
  } // console.log('scrollStart-scrollEnd 👻');
  // console.log(scrollStart + ' : ' + scrollEnd);
  // console.log(scrollProps);
  //get start/end in px


  var scrollStartPixel = window.innerHeight / 100 * scrollStart;
  var scrollEndPixel = window.innerHeight / 100 * scrollEnd;
  var start = Math.round(trigger.getBoundingClientRect().top - scrollStartPixel + window.scrollY) + "px";
  var end = Math.round(trigger.getBoundingClientRect().top - scrollEndPixel + window.scrollY) + "px"; // console.log(start + ' : ' + end);

  if (debug) {
    var keyframe = scrollStart;
    var debugStart = '<div class="pg-animation-debug" style="top:' + scrollStart + 'vh; position:fixed; height:1px; width:100vw; background-color:blue; color:blue; z-index:1;">' + keyframe + '%</div>';
    var debugEnd = '<div class="pg-animation-debug" style="top:' + scrollEnd + 'vh; position:fixed; height:1px; width:100vw; background-color:blue; color:blue; z-index:1;">' + keyframe + '%</div>';
    document.body.insertAdjacentHTML('beforeend', debugStart); // document.body.insertAdjacentHTML('beforeend', debugEnd);
  } //init basicScroll
  // Create an instance for the current element and store the instance in an array.
  // We start the animation later using the instances from the array.


  scrollAnimations.push(basicscroll__WEBPACK_IMPORTED_MODULE_0__["create"]({
    elem: el,
    from: start,
    to: end,
    direct: true,
    props: scrollProps,
    inside: function inside(instance, percentage, props) {
      //callback
      inView(el, percentage, props, type, once, scrollProps, animationName);
    },
    outside: function outside(instance, percentage, props) {
      //callback
      outView(el, percentage, props, type, once, scrollEndPixel, KeyframeCount, KeyframesTotal, scrollAnimations, animationName);
    }
  }));
} //basicScroll supports recalculating start/end values automatically, might be needed later for improved responsivness
// window.onresize = function () {
//   scrollAnimations.forEach((animation) => {
//     animation.destroy();
//     // animation.calculate();
//     // animation.update();
//   });
//   init();
// }


function createPin(pin, el, scrollStartPixel, scrollEndPixel) {
  // create wrapper container
  var wrapper = el.cloneNode(false); // let wrapper = document.createelent('div');

  wrapper.style.height = scrollEndPixel + 'px'; // wrapper.style.width = el.offsetWidth + 'px';

  wrapper.style.display = 'block';
  wrapper.style.position = 'relative';
  wrapper.style.backgroundColor = 'transparent';
  wrapper.style.padding = '0';
  wrapper.style.margin = '0';
  wrapper.classList.add('pg-scroll-wrapper');

  if (pin !== 'pin-spacing') {
    var spacer = el.cloneNode(false);
    spacer.classList.add('pg-scroll-spacer');
    spacer.style.height = el.offsetHeight + 'px';
    spacer.style.width = el.offsetWidth + 'px';
    spacer.style.backgroundColor = 'transparent';
    el.parentNode.insertBefore(spacer, el);
    wrapper.style.position = 'absolute';

    if (window.getComputedStyle(el).getPropertyValue('z-index') === 'auto') {
      wrapper.style.zIndex = '11';
    }
  }

  el.parentNode.insertBefore(wrapper, el);
  wrapper.appendChild(el);
  el.style.position = "sticky";
  el.style.top = scrollStartPixel + 'px';
  el.style.alignSelf = 'start';
} //in view


function inView(el, percentage, props, type, once, scrollProps, animationName) {
  // console.log('in view');
  // console.log(animationName);
  el.classList.add('pg-item-is-inview');

  if (type === 'inview') {
    if (!el.classList.contains('pg-animation-is-running')) {
      if (!el.classList.contains('pg-animation-persist')) {
        el.classList.add('pg-animation-' + animationName);
        el.classList.add(inviewTriggerClass);
        el.classList.add('pg-animation-persist');
      }
    }

    return;
  }

  if (once && el.classList.contains('pg-state-scrolling-once')) {
    return;
  } //set value


  var keys = Object.keys(props);
  var translateX = '0 ';
  var translateY = '0';

  var _loop2 = function _loop2(_key) {
    _key = keys[_key];
    var prop = scrollProps[_key].prop;
    var subProp = scrollProps[_key].subProp || '';
    var cssVars = 'var(' + _key + ')'; //multi prop

    if (prop === 'color' || prop === 'background-color') {
      var matched = Object.keys(scrollProps).filter(function (key) {
        key = _key;
        return scrollProps[key].prop === prop;
      });
      cssVars = 'rgba(var(' + matched[0] + '), var(' + matched[1] + '), var(' + matched[2] + '), var(' + matched[3] + '))';
    }

    if (prop === 'translate' && subProp !== 'translateY') {
      translateX = 'var(' + _key + ') ';
      cssVars = translateX + translateY;
    }

    if (subProp === 'translateY') {
      translateY = 'var(' + _key + ') ';
      cssVars = translateX + translateY;
      prop = 'translate';
    }

    if (prop === 'transform') {
      cssVars = '';

      for (var index in scrollProps) {
        if (scrollProps[index].prop === prop && scrollProps[index].subProp !== 'translateY') {
          cssVars += scrollProps[index].subProp + '(var(' + _key + ')) ';
        }
      }
    }

    el.style.setProperty(prop, cssVars);
    key = _key;
  };

  for (var key in keys) {
    _loop2(key);
  }
}

function outView(el, percentage, props, type, once, scrollEndPixel, KeyframeCount, KeyframesTotal, scrollAnimations, animationName) {
  // console.log('Out of view');
  el.classList.remove('pg-item-is-inview'); //play scroll animation once and keep position

  if (type === 'scroll' && once && percentage > 100 && KeyframeCount === KeyframesTotal) {
    console.log('OUT');
    el.classList.add('pg-state-scrolling-once'); // if (pin) {
    // el.style.top = scrollEndPixel + 'px';
    // }
    //destroy animation on item if run once

    scrollAnimations.forEach(function (animation) {
      if (el === animation.getData().elem) {
        // console.log('🧨 detroy:');
        animation.destroy();
      }
    });
    return;
  } //play animation reverse if end is reached


  if (type === 'inview' && !once) {
    if (!el.classList.contains('pg-animation-is-running')) {
      if (el.classList.contains('pg-animation-persist')) {
        // el.classList.remove(inviewTriggerClass);
        void el.offsetWidth;
        el.classList.add(inviewTriggerClass);
        el.classList.add('pg-animation-' + animationName);
        el.classList.remove('pg-animation-persist');
        playAnimation(el, animationName, true);
      }
    } // if (!el.classList.contains('pg-animation-is-running')) {
    //   el.classList.remove(inviewTriggerClass);
    // }

  }
} //function to seamlessly play and reverse animations


function playAnimation(el, animationName) {
  var reverse = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
  var animationen = el.getAnimations();
  animationen.forEach(function (ani) {
    if (!ani) {
      return;
    } // console.log(ani.animationName + ' === ' + animationName);


    if (ani.animationName == animationName) {
      if (reverse) {
        ani.playbackRate = -1;
      } else {
        ani.playbackRate = 1;
      }

      ani.play();
    }
  });
} // Finding closest DOM elements in any direction


function getClosest(el, selector) {
  var currentElement = el;
  var returnElement;

  while (currentElement.parentNode && !returnElement) {
    currentElement = currentElement.parentNode;
    returnElement = currentElement.querySelector(selector) || currentElement.closest(selector);
  }

  return returnElement;
} // init when document is fully loaded


window.addEventListener('load', function () {
  // setTimeout(init, 300);
  init();
});

/***/ }),

/***/ 4:
/*!********************************!*\
  !*** multi ./pg-animations.js ***!
  \********************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! ./pg-animations.js */"./pg-animations.js");


/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vLy9Vc2Vycy9qcGxvY2gvZGV2L3BncmlkLWRldi9zcmMvbW9kdWxlcy9GaWVsZHR5cGVQYWdlR3JpZC9ub2RlX21vZHVsZXMvYmFzaWNzY3JvbGwvZGlzdC9iYXNpY1Njcm9sbC5taW4uanMiLCJ3ZWJwYWNrOi8vLy4vcGctYW5pbWF0aW9ucy5qcyJdLCJuYW1lcyI6WyJzY3JvbGxBbmltYXRpb25zIiwiaW52aWV3VHJpZ2dlckNsYXNzIiwiaW5pdCIsInBnQW5pbWF0aW9ucyIsInVuZGVmaW5lZCIsInBnQW5pbWF0aW9uc1NlbGVjdG9ycyIsImNvbnNvbGUiLCJsb2ciLCJPYmplY3QiLCJlbnRyaWVzIiwia2V5Iiwic2VsZWN0b3IiLCJpdGVtcyIsImRvY3VtZW50IiwicXVlcnlTZWxlY3RvckFsbCIsImZvckVhY2giLCJlbCIsImFuaW1hdGlvbk5hbWVzIiwiZ2V0Q29tcHV0ZWRTdHlsZSIsImdldFByb3BlcnR5VmFsdWUiLCJzcGxpdCIsImFuaW1hdGlvbk5hbWUiLCJhbmltYXRpb25EYXRhIiwidHlwZSIsInRyaWdnZXJTZWxlY3RvciIsInRyaWdnZXIiLCJwYXJlbnROb2RlIiwiZ2V0Q2xvc2VzdCIsImNsYXNzTGlzdCIsImFkZCIsInN0eWxlIiwiYW5pbWF0aW9uRmlsbE1vZGUiLCJhZGRFdmVudExpc3RlbmVyIiwiYW5pIiwiY29udGFpbnMiLCJlbGFwc2VkVGltZSIsInJlbW92ZSIsImluaXRFdmVudExvYWQiLCJpbml0RXZlbnRIb3ZlciIsImluaXRFdmVudENsaWNrIiwiaW5pdEV2ZW50U2Nyb2xsIiwiYW5pbWF0aW9uIiwic3RhcnQiLCJyZXZlcnNlIiwiZSIsInRhcmdldCIsImN1cnJlbnRUYXJnZXQiLCJwYXNzaXZlIiwicGxheUFuaW1hdGlvbiIsImV2ZW50VHlwZSIsInRvZ2dsZSIsImtleWZyYW1lc0RhdGEiLCJrZXlQcmV2Iiwia2V5ZnJhbWVEYXRhUHJldiIsInBpbiIsIm9uY2UiLCJ0aW1pbmciLCJkaXN0YW5jZSIsInBhcnNlSW50Iiwib2Zmc2V0Iiwic2Nyb2xsUHJvcHMiLCJkZWJ1ZyIsInVybCIsIndpbmRvdyIsImxvY2F0aW9uIiwiaHJlZiIsImluY2x1ZGVzIiwiZGVidWdTdGFydCIsImRlYnVnRW5kIiwiYm9keSIsImluc2VydEFkamFjZW50SFRNTCIsInBpblN0YXJ0UGl4ZWwiLCJpbm5lckhlaWdodCIsInBpbkVuZFBpeGVsIiwiY3JlYXRlUGluIiwicHJvcCIsImZyb20iLCJ0byIsImNyZWF0ZUFuaW1hdGlvbiIsImtleXMiLCJzb3J0IiwicmVkdWNlIiwiYSIsImMiLCJLZXlmcmFtZXNUb3RhbCIsImxlbmd0aCIsIktleWZyYW1lQ291bnQiLCJpIiwiaGFzT3duUHJvcGVydHkiLCJrZXlmcmFtZURhdGEiLCJwcm9wZXJ0eSIsInJlcGxhY2UiLCJ0cmFuc2Zvcm1Gcm9tIiwidHJhbnNmb3JtVG8iLCJpbmRleCIsInByb3BlcnR5MiIsInN1YlByb3AiLCJTdHJpbmciLCJmcm9tMiIsInBvcCIsInRvMiIsInJnYkZyb20iLCJyZ2JUbyIsImNvbG9yIiwiZnJvbUNvbG9yIiwidG9Db2xvciIsInNjcm9sbFN0YXJ0Iiwic2Nyb2xsRW5kIiwidHJhbnNpdGlvbiIsIm92ZXJmbG93WCIsInNyY3NldCIsImdldEF0dHJpYnV0ZSIsInNjcm9sbFN0YXJ0UGl4ZWwiLCJzY3JvbGxFbmRQaXhlbCIsIk1hdGgiLCJyb3VuZCIsImdldEJvdW5kaW5nQ2xpZW50UmVjdCIsInRvcCIsInNjcm9sbFkiLCJlbmQiLCJrZXlmcmFtZSIsInB1c2giLCJiYXNpY1Njcm9sbCIsImVsZW0iLCJkaXJlY3QiLCJwcm9wcyIsImluc2lkZSIsImluc3RhbmNlIiwicGVyY2VudGFnZSIsImluVmlldyIsIm91dHNpZGUiLCJvdXRWaWV3Iiwid3JhcHBlciIsImNsb25lTm9kZSIsImhlaWdodCIsImRpc3BsYXkiLCJwb3NpdGlvbiIsImJhY2tncm91bmRDb2xvciIsInBhZGRpbmciLCJtYXJnaW4iLCJzcGFjZXIiLCJvZmZzZXRIZWlnaHQiLCJ3aWR0aCIsIm9mZnNldFdpZHRoIiwiaW5zZXJ0QmVmb3JlIiwiekluZGV4IiwiYXBwZW5kQ2hpbGQiLCJhbGlnblNlbGYiLCJ0cmFuc2xhdGVYIiwidHJhbnNsYXRlWSIsImNzc1ZhcnMiLCJtYXRjaGVkIiwiZmlsdGVyIiwic2V0UHJvcGVydHkiLCJnZXREYXRhIiwiZGVzdHJveSIsImFuaW1hdGlvbmVuIiwiZ2V0QW5pbWF0aW9ucyIsInBsYXliYWNrUmF0ZSIsInBsYXkiLCJjdXJyZW50RWxlbWVudCIsInJldHVybkVsZW1lbnQiLCJxdWVyeVNlbGVjdG9yIiwiY2xvc2VzdCJdLCJtYXBwaW5ncyI6IjtRQUFBO1FBQ0E7O1FBRUE7UUFDQTs7UUFFQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTs7UUFFQTtRQUNBOztRQUVBO1FBQ0E7O1FBRUE7UUFDQTtRQUNBOzs7UUFHQTtRQUNBOztRQUVBO1FBQ0E7O1FBRUE7UUFDQTtRQUNBO1FBQ0EsMENBQTBDLGdDQUFnQztRQUMxRTtRQUNBOztRQUVBO1FBQ0E7UUFDQTtRQUNBLHdEQUF3RCxrQkFBa0I7UUFDMUU7UUFDQSxpREFBaUQsY0FBYztRQUMvRDs7UUFFQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0EseUNBQXlDLGlDQUFpQztRQUMxRSxnSEFBZ0gsbUJBQW1CLEVBQUU7UUFDckk7UUFDQTs7UUFFQTtRQUNBO1FBQ0E7UUFDQSwyQkFBMkIsMEJBQTBCLEVBQUU7UUFDdkQsaUNBQWlDLGVBQWU7UUFDaEQ7UUFDQTtRQUNBOztRQUVBO1FBQ0Esc0RBQXNELCtEQUErRDs7UUFFckg7UUFDQTs7O1FBR0E7UUFDQTs7Ozs7Ozs7Ozs7O0FDbEZBLHFDQUFhLEdBQUcsSUFBb0Qsb0JBQW9CLEtBQUssRUFBb0wsQ0FBQyxhQUFhLHlCQUF5QixnQkFBZ0IsVUFBVSxVQUFVLDBDQUEwQyxnQkFBZ0IsT0FB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QVU7QUFDUjtBQUNELEtBSjBCLENBTTNCOzs7QUFFQSxRQUFJQSxHQUFHLENBQUNaLGFBQUosSUFBcUJBLGFBQXpCLEVBQXdDO0FBRXRDLFVBQUlzQixPQUFKLEVBQWE7QUFDWFYsV0FBRyxDQUFDNkgsWUFBSixHQUFtQixDQUFDLENBQXBCO0FBQ0QsT0FGRCxNQUVPO0FBQ0w3SCxXQUFHLENBQUM2SCxZQUFKLEdBQW1CLENBQW5CO0FBQ0Q7O0FBRUQ3SCxTQUFHLENBQUM4SCxJQUFKO0FBQ0Q7QUFFRixHQW5CRDtBQW9CRCxDLENBRUQ7OztBQUNBLFNBQVNwSSxVQUFULENBQW9CWCxFQUFwQixFQUF3QkwsUUFBeEIsRUFBa0M7QUFDaEMsTUFBSXFKLGNBQWMsR0FBR2hKLEVBQXJCO0FBQ0EsTUFBSWlKLGFBQUo7O0FBRUEsU0FBT0QsY0FBYyxDQUFDdEksVUFBZixJQUE2QixDQUFDdUksYUFBckMsRUFBb0Q7QUFDbERELGtCQUFjLEdBQUdBLGNBQWMsQ0FBQ3RJLFVBQWhDO0FBQ0F1SSxpQkFBYSxHQUFHRCxjQUFjLENBQUNFLGFBQWYsQ0FBNkJ2SixRQUE3QixLQUEwQ3FKLGNBQWMsQ0FBQ0csT0FBZixDQUF1QnhKLFFBQXZCLENBQTFEO0FBQ0Q7O0FBRUQsU0FBT3NKLGFBQVA7QUFDRCxDLENBRUQ7OztBQUNBbEcsTUFBTSxDQUFDL0IsZ0JBQVAsQ0FBd0IsTUFBeEIsRUFBZ0MsWUFBWTtBQUMxQztBQUNBOUIsTUFBSTtBQUNMLENBSEQsRSIsImZpbGUiOiJwZy1hbmltYXRpb25zLmpzIiwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7IGVudW1lcmFibGU6IHRydWUsIGdldDogZ2V0dGVyIH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBkZWZpbmUgX19lc01vZHVsZSBvbiBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnIgPSBmdW5jdGlvbihleHBvcnRzKSB7XG4gXHRcdGlmKHR5cGVvZiBTeW1ib2wgIT09ICd1bmRlZmluZWQnICYmIFN5bWJvbC50b1N0cmluZ1RhZykge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBTeW1ib2wudG9TdHJpbmdUYWcsIHsgdmFsdWU6ICdNb2R1bGUnIH0pO1xuIFx0XHR9XG4gXHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCAnX19lc01vZHVsZScsIHsgdmFsdWU6IHRydWUgfSk7XG4gXHR9O1xuXG4gXHQvLyBjcmVhdGUgYSBmYWtlIG5hbWVzcGFjZSBvYmplY3RcbiBcdC8vIG1vZGUgJiAxOiB2YWx1ZSBpcyBhIG1vZHVsZSBpZCwgcmVxdWlyZSBpdFxuIFx0Ly8gbW9kZSAmIDI6IG1lcmdlIGFsbCBwcm9wZXJ0aWVzIG9mIHZhbHVlIGludG8gdGhlIG5zXG4gXHQvLyBtb2RlICYgNDogcmV0dXJuIHZhbHVlIHdoZW4gYWxyZWFkeSBucyBvYmplY3RcbiBcdC8vIG1vZGUgJiA4fDE6IGJlaGF2ZSBsaWtlIHJlcXVpcmVcbiBcdF9fd2VicGFja19yZXF1aXJlX18udCA9IGZ1bmN0aW9uKHZhbHVlLCBtb2RlKSB7XG4gXHRcdGlmKG1vZGUgJiAxKSB2YWx1ZSA9IF9fd2VicGFja19yZXF1aXJlX18odmFsdWUpO1xuIFx0XHRpZihtb2RlICYgOCkgcmV0dXJuIHZhbHVlO1xuIFx0XHRpZigobW9kZSAmIDQpICYmIHR5cGVvZiB2YWx1ZSA9PT0gJ29iamVjdCcgJiYgdmFsdWUgJiYgdmFsdWUuX19lc01vZHVsZSkgcmV0dXJuIHZhbHVlO1xuIFx0XHR2YXIgbnMgPSBPYmplY3QuY3JlYXRlKG51bGwpO1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLnIobnMpO1xuIFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkobnMsICdkZWZhdWx0JywgeyBlbnVtZXJhYmxlOiB0cnVlLCB2YWx1ZTogdmFsdWUgfSk7XG4gXHRcdGlmKG1vZGUgJiAyICYmIHR5cGVvZiB2YWx1ZSAhPSAnc3RyaW5nJykgZm9yKHZhciBrZXkgaW4gdmFsdWUpIF9fd2VicGFja19yZXF1aXJlX18uZChucywga2V5LCBmdW5jdGlvbihrZXkpIHsgcmV0dXJuIHZhbHVlW2tleV07IH0uYmluZChudWxsLCBrZXkpKTtcbiBcdFx0cmV0dXJuIG5zO1xuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCJodHRwOi8vbG9jYWxob3N0OjMzMzUvc2l0ZS9tb2R1bGVzL0ZpZWxkdHlwZVBhZ2VHcmlkL2pzL1wiO1xuXG5cbiBcdC8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuIFx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oX193ZWJwYWNrX3JlcXVpcmVfXy5zID0gNCk7XG4iLCIhZnVuY3Rpb24odCl7aWYoXCJvYmplY3RcIj09dHlwZW9mIGV4cG9ydHMmJlwidW5kZWZpbmVkXCIhPXR5cGVvZiBtb2R1bGUpbW9kdWxlLmV4cG9ydHM9dCgpO2Vsc2UgaWYoXCJmdW5jdGlvblwiPT10eXBlb2YgZGVmaW5lJiZkZWZpbmUuYW1kKWRlZmluZShbXSx0KTtlbHNleyhcInVuZGVmaW5lZFwiIT10eXBlb2Ygd2luZG93P3dpbmRvdzpcInVuZGVmaW5lZFwiIT10eXBlb2YgZ2xvYmFsP2dsb2JhbDpcInVuZGVmaW5lZFwiIT10eXBlb2Ygc2VsZj9zZWxmOnRoaXMpLmJhc2ljU2Nyb2xsPXQoKX19KChmdW5jdGlvbigpe3JldHVybiBmdW5jdGlvbiB0KG4sbyxlKXtmdW5jdGlvbiByKGksYyl7aWYoIW9baV0pe2lmKCFuW2ldKXt2YXIgZj1cImZ1bmN0aW9uXCI9PXR5cGVvZiByZXF1aXJlJiZyZXF1aXJlO2lmKCFjJiZmKXJldHVybiBmKGksITApO2lmKHUpcmV0dXJuIHUoaSwhMCk7dmFyIGE9bmV3IEVycm9yKFwiQ2Fubm90IGZpbmQgbW9kdWxlICdcIitpK1wiJ1wiKTt0aHJvdyBhLmNvZGU9XCJNT0RVTEVfTk9UX0ZPVU5EXCIsYX12YXIgcz1vW2ldPXtleHBvcnRzOnt9fTtuW2ldWzBdLmNhbGwocy5leHBvcnRzLChmdW5jdGlvbih0KXtyZXR1cm4gcihuW2ldWzFdW3RdfHx0KX0pLHMscy5leHBvcnRzLHQsbixvLGUpfXJldHVybiBvW2ldLmV4cG9ydHN9Zm9yKHZhciB1PVwiZnVuY3Rpb25cIj09dHlwZW9mIHJlcXVpcmUmJnJlcXVpcmUsaT0wO2k8ZS5sZW5ndGg7aSsrKXIoZVtpXSk7cmV0dXJuIHJ9KHsxOltmdW5jdGlvbih0LG4sbyl7bi5leHBvcnRzPWZ1bmN0aW9uKHQpe3ZhciBuPTIuNTk0OTA5NTtyZXR1cm4odCo9Mik8MT90KnQqKChuKzEpKnQtbikqLjU6LjUqKCh0LT0yKSp0KigobisxKSp0K24pKzIpfX0se31dLDI6W2Z1bmN0aW9uKHQsbixvKXtuLmV4cG9ydHM9ZnVuY3Rpb24odCl7dmFyIG49MS43MDE1ODtyZXR1cm4gdCp0KigobisxKSp0LW4pfX0se31dLDM6W2Z1bmN0aW9uKHQsbixvKXtuLmV4cG9ydHM9ZnVuY3Rpb24odCl7dmFyIG49MS43MDE1ODtyZXR1cm4tLXQqdCooKG4rMSkqdCtuKSsxfX0se31dLDQ6W2Z1bmN0aW9uKHQsbixvKXt2YXIgZT10KFwiLi9ib3VuY2Utb3V0XCIpO24uZXhwb3J0cz1mdW5jdGlvbih0KXtyZXR1cm4gdDwuNT8uNSooMS1lKDEtMip0KSk6LjUqZSgyKnQtMSkrLjV9fSx7XCIuL2JvdW5jZS1vdXRcIjo2fV0sNTpbZnVuY3Rpb24odCxuLG8pe3ZhciBlPXQoXCIuL2JvdW5jZS1vdXRcIik7bi5leHBvcnRzPWZ1bmN0aW9uKHQpe3JldHVybiAxLWUoMS10KX19LHtcIi4vYm91bmNlLW91dFwiOjZ9XSw2OltmdW5jdGlvbih0LG4sbyl7bi5leHBvcnRzPWZ1bmN0aW9uKHQpe3ZhciBuPXQqdDtyZXR1cm4gdDw0LzExPzcuNTYyNSpuOnQ8OC8xMT85LjA3NSpuLTkuOSp0KzMuNDp0PC45PzQzNTYvMzYxKm4tMzU0NDIvMTgwNSp0KzE2MDYxLzE4MDU6MTAuOCp0KnQtMjAuNTIqdCsxMC43Mn19LHt9XSw3OltmdW5jdGlvbih0LG4sbyl7bi5leHBvcnRzPWZ1bmN0aW9uKHQpe3JldHVybih0Kj0yKTwxPy0uNSooTWF0aC5zcXJ0KDEtdCp0KS0xKTouNSooTWF0aC5zcXJ0KDEtKHQtPTIpKnQpKzEpfX0se31dLDg6W2Z1bmN0aW9uKHQsbixvKXtuLmV4cG9ydHM9ZnVuY3Rpb24odCl7cmV0dXJuIDEtTWF0aC5zcXJ0KDEtdCp0KX19LHt9XSw5OltmdW5jdGlvbih0LG4sbyl7bi5leHBvcnRzPWZ1bmN0aW9uKHQpe3JldHVybiBNYXRoLnNxcnQoMS0gLS10KnQpfX0se31dLDEwOltmdW5jdGlvbih0LG4sbyl7bi5leHBvcnRzPWZ1bmN0aW9uKHQpe3JldHVybiB0PC41PzQqdCp0KnQ6LjUqTWF0aC5wb3coMip0LTIsMykrMX19LHt9XSwxMTpbZnVuY3Rpb24odCxuLG8pe24uZXhwb3J0cz1mdW5jdGlvbih0KXtyZXR1cm4gdCp0KnR9fSx7fV0sMTI6W2Z1bmN0aW9uKHQsbixvKXtuLmV4cG9ydHM9ZnVuY3Rpb24odCl7dmFyIG49dC0xO3JldHVybiBuKm4qbisxfX0se31dLDEzOltmdW5jdGlvbih0LG4sbyl7bi5leHBvcnRzPWZ1bmN0aW9uKHQpe3JldHVybiB0PC41Py41Kk1hdGguc2luKDEzKk1hdGguUEkvMioyKnQpKk1hdGgucG93KDIsMTAqKDIqdC0xKSk6LjUqTWF0aC5zaW4oLTEzKk1hdGguUEkvMiooMip0LTErMSkpKk1hdGgucG93KDIsLTEwKigyKnQtMSkpKzF9fSx7fV0sMTQ6W2Z1bmN0aW9uKHQsbixvKXtuLmV4cG9ydHM9ZnVuY3Rpb24odCl7cmV0dXJuIE1hdGguc2luKDEzKnQqTWF0aC5QSS8yKSpNYXRoLnBvdygyLDEwKih0LTEpKX19LHt9XSwxNTpbZnVuY3Rpb24odCxuLG8pe24uZXhwb3J0cz1mdW5jdGlvbih0KXtyZXR1cm4gTWF0aC5zaW4oLTEzKih0KzEpKk1hdGguUEkvMikqTWF0aC5wb3coMiwtMTAqdCkrMX19LHt9XSwxNjpbZnVuY3Rpb24odCxuLG8pe24uZXhwb3J0cz1mdW5jdGlvbih0KXtyZXR1cm4gMD09PXR8fDE9PT10P3Q6dDwuNT8uNSpNYXRoLnBvdygyLDIwKnQtMTApOi0uNSpNYXRoLnBvdygyLDEwLTIwKnQpKzF9fSx7fV0sMTc6W2Z1bmN0aW9uKHQsbixvKXtuLmV4cG9ydHM9ZnVuY3Rpb24odCl7cmV0dXJuIDA9PT10P3Q6TWF0aC5wb3coMiwxMCoodC0xKSl9fSx7fV0sMTg6W2Z1bmN0aW9uKHQsbixvKXtuLmV4cG9ydHM9ZnVuY3Rpb24odCl7cmV0dXJuIDE9PT10P3Q6MS1NYXRoLnBvdygyLC0xMCp0KX19LHt9XSwxOTpbZnVuY3Rpb24odCxuLG8pe24uZXhwb3J0cz17YmFja0luT3V0OnQoXCIuL2JhY2staW4tb3V0XCIpLGJhY2tJbjp0KFwiLi9iYWNrLWluXCIpLGJhY2tPdXQ6dChcIi4vYmFjay1vdXRcIiksYm91bmNlSW5PdXQ6dChcIi4vYm91bmNlLWluLW91dFwiKSxib3VuY2VJbjp0KFwiLi9ib3VuY2UtaW5cIiksYm91bmNlT3V0OnQoXCIuL2JvdW5jZS1vdXRcIiksY2lyY0luT3V0OnQoXCIuL2NpcmMtaW4tb3V0XCIpLGNpcmNJbjp0KFwiLi9jaXJjLWluXCIpLGNpcmNPdXQ6dChcIi4vY2lyYy1vdXRcIiksY3ViaWNJbk91dDp0KFwiLi9jdWJpYy1pbi1vdXRcIiksY3ViaWNJbjp0KFwiLi9jdWJpYy1pblwiKSxjdWJpY091dDp0KFwiLi9jdWJpYy1vdXRcIiksZWxhc3RpY0luT3V0OnQoXCIuL2VsYXN0aWMtaW4tb3V0XCIpLGVsYXN0aWNJbjp0KFwiLi9lbGFzdGljLWluXCIpLGVsYXN0aWNPdXQ6dChcIi4vZWxhc3RpYy1vdXRcIiksZXhwb0luT3V0OnQoXCIuL2V4cG8taW4tb3V0XCIpLGV4cG9Jbjp0KFwiLi9leHBvLWluXCIpLGV4cG9PdXQ6dChcIi4vZXhwby1vdXRcIiksbGluZWFyOnQoXCIuL2xpbmVhclwiKSxxdWFkSW5PdXQ6dChcIi4vcXVhZC1pbi1vdXRcIikscXVhZEluOnQoXCIuL3F1YWQtaW5cIikscXVhZE91dDp0KFwiLi9xdWFkLW91dFwiKSxxdWFydEluT3V0OnQoXCIuL3F1YXJ0LWluLW91dFwiKSxxdWFydEluOnQoXCIuL3F1YXJ0LWluXCIpLHF1YXJ0T3V0OnQoXCIuL3F1YXJ0LW91dFwiKSxxdWludEluT3V0OnQoXCIuL3F1aW50LWluLW91dFwiKSxxdWludEluOnQoXCIuL3F1aW50LWluXCIpLHF1aW50T3V0OnQoXCIuL3F1aW50LW91dFwiKSxzaW5lSW5PdXQ6dChcIi4vc2luZS1pbi1vdXRcIiksc2luZUluOnQoXCIuL3NpbmUtaW5cIiksc2luZU91dDp0KFwiLi9zaW5lLW91dFwiKX19LHtcIi4vYmFjay1pblwiOjIsXCIuL2JhY2staW4tb3V0XCI6MSxcIi4vYmFjay1vdXRcIjozLFwiLi9ib3VuY2UtaW5cIjo1LFwiLi9ib3VuY2UtaW4tb3V0XCI6NCxcIi4vYm91bmNlLW91dFwiOjYsXCIuL2NpcmMtaW5cIjo4LFwiLi9jaXJjLWluLW91dFwiOjcsXCIuL2NpcmMtb3V0XCI6OSxcIi4vY3ViaWMtaW5cIjoxMSxcIi4vY3ViaWMtaW4tb3V0XCI6MTAsXCIuL2N1YmljLW91dFwiOjEyLFwiLi9lbGFzdGljLWluXCI6MTQsXCIuL2VsYXN0aWMtaW4tb3V0XCI6MTMsXCIuL2VsYXN0aWMtb3V0XCI6MTUsXCIuL2V4cG8taW5cIjoxNyxcIi4vZXhwby1pbi1vdXRcIjoxNixcIi4vZXhwby1vdXRcIjoxOCxcIi4vbGluZWFyXCI6MjAsXCIuL3F1YWQtaW5cIjoyMixcIi4vcXVhZC1pbi1vdXRcIjoyMSxcIi4vcXVhZC1vdXRcIjoyMyxcIi4vcXVhcnQtaW5cIjoyNSxcIi4vcXVhcnQtaW4tb3V0XCI6MjQsXCIuL3F1YXJ0LW91dFwiOjI2LFwiLi9xdWludC1pblwiOjI4LFwiLi9xdWludC1pbi1vdXRcIjoyNyxcIi4vcXVpbnQtb3V0XCI6MjksXCIuL3NpbmUtaW5cIjozMSxcIi4vc2luZS1pbi1vdXRcIjozMCxcIi4vc2luZS1vdXRcIjozMn1dLDIwOltmdW5jdGlvbih0LG4sbyl7bi5leHBvcnRzPWZ1bmN0aW9uKHQpe3JldHVybiB0fX0se31dLDIxOltmdW5jdGlvbih0LG4sbyl7bi5leHBvcnRzPWZ1bmN0aW9uKHQpe3JldHVybih0Lz0uNSk8MT8uNSp0KnQ6LS41KigtLXQqKHQtMiktMSl9fSx7fV0sMjI6W2Z1bmN0aW9uKHQsbixvKXtuLmV4cG9ydHM9ZnVuY3Rpb24odCl7cmV0dXJuIHQqdH19LHt9XSwyMzpbZnVuY3Rpb24odCxuLG8pe24uZXhwb3J0cz1mdW5jdGlvbih0KXtyZXR1cm4tdCoodC0yKX19LHt9XSwyNDpbZnVuY3Rpb24odCxuLG8pe24uZXhwb3J0cz1mdW5jdGlvbih0KXtyZXR1cm4gdDwuNT84Kk1hdGgucG93KHQsNCk6LTgqTWF0aC5wb3codC0xLDQpKzF9fSx7fV0sMjU6W2Z1bmN0aW9uKHQsbixvKXtuLmV4cG9ydHM9ZnVuY3Rpb24odCl7cmV0dXJuIE1hdGgucG93KHQsNCl9fSx7fV0sMjY6W2Z1bmN0aW9uKHQsbixvKXtuLmV4cG9ydHM9ZnVuY3Rpb24odCl7cmV0dXJuIE1hdGgucG93KHQtMSwzKSooMS10KSsxfX0se31dLDI3OltmdW5jdGlvbih0LG4sbyl7bi5leHBvcnRzPWZ1bmN0aW9uKHQpe3JldHVybih0Kj0yKTwxPy41KnQqdCp0KnQqdDouNSooKHQtPTIpKnQqdCp0KnQrMil9fSx7fV0sMjg6W2Z1bmN0aW9uKHQsbixvKXtuLmV4cG9ydHM9ZnVuY3Rpb24odCl7cmV0dXJuIHQqdCp0KnQqdH19LHt9XSwyOTpbZnVuY3Rpb24odCxuLG8pe24uZXhwb3J0cz1mdW5jdGlvbih0KXtyZXR1cm4tLXQqdCp0KnQqdCsxfX0se31dLDMwOltmdW5jdGlvbih0LG4sbyl7bi5leHBvcnRzPWZ1bmN0aW9uKHQpe3JldHVybi0uNSooTWF0aC5jb3MoTWF0aC5QSSp0KS0xKX19LHt9XSwzMTpbZnVuY3Rpb24odCxuLG8pe24uZXhwb3J0cz1mdW5jdGlvbih0KXt2YXIgbj1NYXRoLmNvcyh0Kk1hdGguUEkqLjUpO3JldHVybiBNYXRoLmFicyhuKTwxZS0xND8xOjEtbn19LHt9XSwzMjpbZnVuY3Rpb24odCxuLG8pe24uZXhwb3J0cz1mdW5jdGlvbih0KXtyZXR1cm4gTWF0aC5zaW4odCpNYXRoLlBJLzIpfX0se31dLDMzOltmdW5jdGlvbih0LG4sbyl7bi5leHBvcnRzPWZ1bmN0aW9uKHQsbil7bnx8KG49WzAsXCJcIl0pLHQ9U3RyaW5nKHQpO3ZhciBvPXBhcnNlRmxvYXQodCwxMCk7cmV0dXJuIG5bMF09byxuWzFdPXQubWF0Y2goL1tcXGQuXFwtXFwrXSpcXHMqKC4qKS8pWzFdfHxcIlwiLG59fSx7fV0sMzQ6W2Z1bmN0aW9uKHQsbixvKXtcInVzZSBzdHJpY3RcIjtPYmplY3QuZGVmaW5lUHJvcGVydHkobyxcIl9fZXNNb2R1bGVcIix7dmFsdWU6ITB9KSxvLmNyZWF0ZT12b2lkIDA7dmFyIGU9dSh0KFwicGFyc2UtdW5pdFwiKSkscj11KHQoXCJlYXNlc1wiKSk7ZnVuY3Rpb24gdSh0KXtyZXR1cm4gdCYmdC5fX2VzTW9kdWxlP3Q6e2RlZmF1bHQ6dH19ZnVuY3Rpb24gaSh0KXtyZXR1cm4oaT1cImZ1bmN0aW9uXCI9PXR5cGVvZiBTeW1ib2wmJlwic3ltYm9sXCI9PXR5cGVvZiBTeW1ib2wuaXRlcmF0b3I/ZnVuY3Rpb24odCl7cmV0dXJuIHR5cGVvZiB0fTpmdW5jdGlvbih0KXtyZXR1cm4gdCYmXCJmdW5jdGlvblwiPT10eXBlb2YgU3ltYm9sJiZ0LmNvbnN0cnVjdG9yPT09U3ltYm9sJiZ0IT09U3ltYm9sLnByb3RvdHlwZT9cInN5bWJvbFwiOnR5cGVvZiB0fSkodCl9dmFyIGMsZixhLHM9W10scD1cInVuZGVmaW5lZFwiIT10eXBlb2Ygd2luZG93LGw9ZnVuY3Rpb24oKXtyZXR1cm4oZG9jdW1lbnQuc2Nyb2xsaW5nRWxlbWVudHx8ZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50KS5zY3JvbGxUb3B9LGQ9ZnVuY3Rpb24oKXtyZXR1cm4gd2luZG93LmlubmVySGVpZ2h0fHx3aW5kb3cub3V0ZXJIZWlnaHR9LG09ZnVuY3Rpb24odCl7cmV0dXJuITE9PT1pc05hTigoMCxlLmRlZmF1bHQpKHQpWzBdKX0sYj1mdW5jdGlvbih0KXt2YXIgbj0oMCxlLmRlZmF1bHQpKHQpO3JldHVybnt2YWx1ZTpuWzBdLHVuaXQ6blsxXX19LGg9ZnVuY3Rpb24odCl7cmV0dXJuIG51bGwhPT1TdHJpbmcodCkubWF0Y2goL15bYS16XSstW2Etel0rJC8pfSx3PWZ1bmN0aW9uKHQsbil7cmV0dXJuITA9PT10P24uZWxlbTp0IGluc3RhbmNlb2YgSFRNTEVsZW1lbnQ9PSEwP24uZGlyZWN0Om4uZ2xvYmFsfSx5PWZ1bmN0aW9uKHQsbil7dmFyIG89YXJndW1lbnRzLmxlbmd0aD4yJiZ2b2lkIDAhPT1hcmd1bWVudHNbMl0/YXJndW1lbnRzWzJdOmwoKSxlPWFyZ3VtZW50cy5sZW5ndGg+MyYmdm9pZCAwIT09YXJndW1lbnRzWzNdP2FyZ3VtZW50c1szXTpkKCkscj1uLmdldEJvdW5kaW5nQ2xpZW50UmVjdCgpLHU9dC5tYXRjaCgvXlthLXpdKy8pWzBdLGk9dC5tYXRjaCgvW2Etel0rJC8pWzBdLGM9MDtyZXR1cm5cInRvcFwiPT09aSYmKGMtPTApLFwibWlkZGxlXCI9PT1pJiYoYy09ZS8yKSxcImJvdHRvbVwiPT09aSYmKGMtPWUpLFwidG9wXCI9PT11JiYoYys9ci50b3ArbyksXCJtaWRkbGVcIj09PXUmJihjKz1yLnRvcCtvK3IuaGVpZ2h0LzIpLFwiYm90dG9tXCI9PT11JiYoYys9ci50b3ArbytyLmhlaWdodCksXCJcIi5jb25jYXQoYyxcInB4XCIpfSx2PWZ1bmN0aW9uKHQpe3ZhciBuPWFyZ3VtZW50cy5sZW5ndGg+MSYmdm9pZCAwIT09YXJndW1lbnRzWzFdP2FyZ3VtZW50c1sxXTpsKCksbz10LmdldERhdGEoKSxlPW8udG8udmFsdWUtby5mcm9tLnZhbHVlLHI9bi1vLmZyb20udmFsdWUsdT1yLyhlLzEwMCksaT1NYXRoLm1pbihNYXRoLm1heCh1LDApLDEwMCksYz13KG8uZGlyZWN0LHtnbG9iYWw6ZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50LGVsZW06by5lbGVtLGRpcmVjdDpvLmRpcmVjdH0pLGY9T2JqZWN0LmtleXMoby5wcm9wcykucmVkdWNlKChmdW5jdGlvbih0LG4pe3ZhciBlPW8ucHJvcHNbbl0scj1lLmZyb20udW5pdHx8ZS50by51bml0LHU9ZS5mcm9tLnZhbHVlLWUudG8udmFsdWUsYz1lLnRpbWluZyhpLzEwMCksZj1lLmZyb20udmFsdWUtdSpjLGE9TWF0aC5yb3VuZCgxZTQqZikvMWU0O3JldHVybiB0W25dPWErcix0fSkse30pLGE9dT49MCYmdTw9MTAwLHM9dTwwfHx1PjEwMDtyZXR1cm4hMD09PWEmJm8uaW5zaWRlKHQsdSxmKSwhMD09PXMmJm8ub3V0c2lkZSh0LHUsZikse2VsZW06Yyxwcm9wczpmfX0seD1mdW5jdGlvbih0LG4pe09iamVjdC5rZXlzKG4pLmZvckVhY2goKGZ1bmN0aW9uKG8pe3JldHVybiBmdW5jdGlvbih0LG4pe3Quc3R5bGUuc2V0UHJvcGVydHkobi5rZXksbi52YWx1ZSl9KHQse2tleTpvLHZhbHVlOm5bb119KX0pKX07by5jcmVhdGU9ZnVuY3Rpb24odCl7dmFyIG49bnVsbCxvPSExLGU9e2lzQWN0aXZlOmZ1bmN0aW9uKCl7cmV0dXJuIG99LGdldERhdGE6ZnVuY3Rpb24oKXtyZXR1cm4gbn0sY2FsY3VsYXRlOmZ1bmN0aW9uKCl7bj1mdW5jdGlvbigpe3ZhciB0PWFyZ3VtZW50cy5sZW5ndGg+MCYmdm9pZCAwIT09YXJndW1lbnRzWzBdP2FyZ3VtZW50c1swXTp7fTtpZihudWxsPT0odD1PYmplY3QuYXNzaWduKHt9LHQpKS5pbnNpZGUmJih0Lmluc2lkZT1mdW5jdGlvbigpe30pLG51bGw9PXQub3V0c2lkZSYmKHQub3V0c2lkZT1mdW5jdGlvbigpe30pLG51bGw9PXQuZGlyZWN0JiYodC5kaXJlY3Q9ITEpLG51bGw9PXQudHJhY2smJih0LnRyYWNrPSEwKSxudWxsPT10LnByb3BzJiYodC5wcm9wcz17fSksbnVsbD09dC5mcm9tKXRocm93IG5ldyBFcnJvcihcIk1pc3NpbmcgcHJvcGVydHkgYGZyb21gXCIpO2lmKG51bGw9PXQudG8pdGhyb3cgbmV3IEVycm9yKFwiTWlzc2luZyBwcm9wZXJ0eSBgdG9gXCIpO2lmKFwiZnVuY3Rpb25cIiE9dHlwZW9mIHQuaW5zaWRlKXRocm93IG5ldyBFcnJvcihcIlByb3BlcnR5IGBpbnNpZGVgIG11c3QgYmUgdW5kZWZpbmVkIG9yIGEgZnVuY3Rpb25cIik7aWYoXCJmdW5jdGlvblwiIT10eXBlb2YgdC5vdXRzaWRlKXRocm93IG5ldyBFcnJvcihcIlByb3BlcnR5IGBvdXRzaWRlYCBtdXN0IGJlIHVuZGVmaW5lZCBvciBhIGZ1bmN0aW9uXCIpO2lmKFwiYm9vbGVhblwiIT10eXBlb2YgdC5kaXJlY3QmJnQuZGlyZWN0IGluc3RhbmNlb2YgSFRNTEVsZW1lbnQ9PTApdGhyb3cgbmV3IEVycm9yKFwiUHJvcGVydHkgYGRpcmVjdGAgbXVzdCBiZSB1bmRlZmluZWQsIGEgYm9vbGVhbiBvciBhIERPTSBlbGVtZW50L25vZGVcIik7aWYoITA9PT10LmRpcmVjdCYmbnVsbD09dC5lbGVtKXRocm93IG5ldyBFcnJvcihcIlByb3BlcnR5IGBlbGVtYCBpcyByZXF1aXJlZCB3aGVuIGBkaXJlY3RgIGlzIHRydWVcIik7aWYoXCJib29sZWFuXCIhPXR5cGVvZiB0LnRyYWNrKXRocm93IG5ldyBFcnJvcihcIlByb3BlcnR5IGB0cmFja2AgbXVzdCBiZSB1bmRlZmluZWQgb3IgYSBib29sZWFuXCIpO2lmKFwib2JqZWN0XCIhPT1pKHQucHJvcHMpKXRocm93IG5ldyBFcnJvcihcIlByb3BlcnR5IGBwcm9wc2AgbXVzdCBiZSB1bmRlZmluZWQgb3IgYW4gb2JqZWN0XCIpO2lmKG51bGw9PXQuZWxlbSl7aWYoITE9PT1tKHQuZnJvbSkpdGhyb3cgbmV3IEVycm9yKFwiUHJvcGVydHkgYGZyb21gIG11c3QgYmUgYSBhYnNvbHV0ZSB2YWx1ZSB3aGVuIG5vIGBlbGVtYCBoYXMgYmVlbiBwcm92aWRlZFwiKTtpZighMT09PW0odC50bykpdGhyb3cgbmV3IEVycm9yKFwiUHJvcGVydHkgYHRvYCBtdXN0IGJlIGEgYWJzb2x1dGUgdmFsdWUgd2hlbiBubyBgZWxlbWAgaGFzIGJlZW4gcHJvdmlkZWRcIil9ZWxzZSEwPT09aCh0LmZyb20pJiYodC5mcm9tPXkodC5mcm9tLHQuZWxlbSkpLCEwPT09aCh0LnRvKSYmKHQudG89eSh0LnRvLHQuZWxlbSkpO3JldHVybiB0LmZyb209Yih0LmZyb20pLHQudG89Yih0LnRvKSx0LnByb3BzPU9iamVjdC5rZXlzKHQucHJvcHMpLnJlZHVjZSgoZnVuY3Rpb24obixvKXt2YXIgZT1PYmplY3QuYXNzaWduKHt9LHQucHJvcHNbb10pO2lmKCExPT09bShlLmZyb20pKXRocm93IG5ldyBFcnJvcihcIlByb3BlcnR5IGBmcm9tYCBvZiBwcm9wIG11c3QgYmUgYSBhYnNvbHV0ZSB2YWx1ZVwiKTtpZighMT09PW0oZS50bykpdGhyb3cgbmV3IEVycm9yKFwiUHJvcGVydHkgYGZyb21gIG9mIHByb3AgbXVzdCBiZSBhIGFic29sdXRlIHZhbHVlXCIpO2lmKGUuZnJvbT1iKGUuZnJvbSksZS50bz1iKGUudG8pLG51bGw9PWUudGltaW5nJiYoZS50aW1pbmc9ci5kZWZhdWx0LmxpbmVhciksXCJzdHJpbmdcIiE9dHlwZW9mIGUudGltaW5nJiZcImZ1bmN0aW9uXCIhPXR5cGVvZiBlLnRpbWluZyl0aHJvdyBuZXcgRXJyb3IoXCJQcm9wZXJ0eSBgdGltaW5nYCBvZiBwcm9wIG11c3QgYmUgdW5kZWZpbmVkLCBhIHN0cmluZyBvciBhIGZ1bmN0aW9uXCIpO2lmKFwic3RyaW5nXCI9PXR5cGVvZiBlLnRpbWluZyYmbnVsbD09ci5kZWZhdWx0W2UudGltaW5nXSl0aHJvdyBuZXcgRXJyb3IoXCJVbmtub3duIHRpbWluZyBmb3IgcHJvcGVydHkgYHRpbWluZ2Agb2YgcHJvcFwiKTtyZXR1cm5cInN0cmluZ1wiPT10eXBlb2YgZS50aW1pbmcmJihlLnRpbWluZz1yLmRlZmF1bHRbZS50aW1pbmddKSxuW29dPWUsbn0pLHt9KSx0fSh0KX0sdXBkYXRlOmZ1bmN0aW9uKCl7dmFyIHQ9dihlKSxuPXQuZWxlbSxvPXQucHJvcHM7cmV0dXJuIHgobixvKSxvfSxzdGFydDpmdW5jdGlvbigpe289ITB9LHN0b3A6ZnVuY3Rpb24oKXtvPSExfSxkZXN0cm95OmZ1bmN0aW9uKCl7c1t1XT12b2lkIDB9fSx1PXMucHVzaChlKS0xO3JldHVybiBlLmNhbGN1bGF0ZSgpLGV9LCEwPT09cD8oIWZ1bmN0aW9uIHQobixvKXt2YXIgZT1mdW5jdGlvbigpe3JlcXVlc3RBbmltYXRpb25GcmFtZSgoZnVuY3Rpb24oKXtyZXR1cm4gdChuLG8pfSkpfSxyPWZ1bmN0aW9uKHQpe3JldHVybiB0LmZpbHRlcigoZnVuY3Rpb24odCl7cmV0dXJuIG51bGwhPXQmJnQuaXNBY3RpdmUoKX0pKX0ocyk7aWYoMD09PXIubGVuZ3RoKXJldHVybiBlKCk7dmFyIHU9bCgpO2lmKG89PT11KXJldHVybiBlKCk7bz11LHIubWFwKChmdW5jdGlvbih0KXtyZXR1cm4gdih0LHUpfSkpLmZvckVhY2goKGZ1bmN0aW9uKHQpe3ZhciBuPXQuZWxlbSxvPXQucHJvcHM7cmV0dXJuIHgobixvKX0pKSxlKCl9KCksd2luZG93LmFkZEV2ZW50TGlzdGVuZXIoXCJyZXNpemVcIiwoYz1mdW5jdGlvbigpeyhmdW5jdGlvbih0KXtyZXR1cm4gdC5maWx0ZXIoKGZ1bmN0aW9uKHQpe3JldHVybiBudWxsIT10JiZ0LmdldERhdGEoKS50cmFja30pKX0pKHMpLmZvckVhY2goKGZ1bmN0aW9uKHQpe3QuY2FsY3VsYXRlKCksdC51cGRhdGUoKX0pKX0sZj01MCxhPW51bGwsZnVuY3Rpb24oKXtmb3IodmFyIHQ9YXJndW1lbnRzLmxlbmd0aCxuPW5ldyBBcnJheSh0KSxvPTA7bzx0O28rKyluW29dPWFyZ3VtZW50c1tvXTtjbGVhclRpbWVvdXQoYSksYT1zZXRUaW1lb3V0KChmdW5jdGlvbigpe3JldHVybiBjLmFwcGx5KHZvaWQgMCxuKX0pLGYpfSkpKTpjb25zb2xlLndhcm4oXCJiYXNpY1Njcm9sbCBpcyBub3QgZXhlY3V0aW5nIGJlY2F1c2UgeW91IGFyZSB1c2luZyBpdCBpbiBhbiBlbnZpcm9ubWVudCB3aXRob3V0IGEgYHdpbmRvd2Agb2JqZWN0XCIpfSx7ZWFzZXM6MTksXCJwYXJzZS11bml0XCI6MzN9XX0se30sWzM0XSkoMzQpfSkpOyIsIi8vbmVlZHMgYmFzaWNzY3JvbGwgdG8gd29yayBcbmltcG9ydCAqIGFzIGJhc2ljU2Nyb2xsIGZyb20gJ2Jhc2ljc2Nyb2xsJztcblxuLy8gYWNjZXNzIHBnIGRhdGEgdGhyb3VnaCB2YXIgcGdBbmltYXRpb25zIHNldCB2aWEgcGhwXG4vLyoqIHZhciBwZ0FuaW1hdGlvbnNcIlxudmFyIHNjcm9sbEFuaW1hdGlvbnMgPSBbXSxcbiAgaW52aWV3VHJpZ2dlckNsYXNzID0gJ3BnLWV2ZW50LXRyaWdnZXItaW52aWV3JztcblxuLy9pbml0IGFsbCBhbmltYXRpb25zIGZvdW5kIGluIHZhciBwZ0FuaW1hdGlvbnNTZWxlY3RvcnNcbmZ1bmN0aW9uIGluaXQoKSB7XG5cbiAgLy9pZiBubyBkYXRhIGZvdW5kIHJldHVyblxuICBpZiAocGdBbmltYXRpb25zID09PSB1bmRlZmluZWQpIHJldHVybjtcbiAgaWYgKHBnQW5pbWF0aW9uc1NlbGVjdG9ycyA9PT0gdW5kZWZpbmVkKSByZXR1cm47XG5cbiAgY29uc29sZS5sb2coJ3BnLWFuaW1hdGlvbnMuanMgbG9hZGVkJyk7XG4gIGNvbnNvbGUubG9nKHBnQW5pbWF0aW9uc1NlbGVjdG9ycyk7XG5cbiAgLy9zZXQgZW1wdHkgYXJyYXkgdG8gYmUgYWJsZSB0byByZWluaXQgc2Nyb2xsYW5pbWF0aW9uc1xuICBzY3JvbGxBbmltYXRpb25zID0gW107XG5cbiAgLy9pbml0IGZvciBmb3VuZCBzZWxlY3RvcnNcbiAgZm9yIChjb25zdCBba2V5LCBzZWxlY3Rvcl0gb2YgT2JqZWN0LmVudHJpZXMocGdBbmltYXRpb25zU2VsZWN0b3JzKSkge1xuICAgIC8vZ2V0IGl0ZW1zIGFuZCBhbHNvIGV2ZW50IGNsYXNzZXMgdG8gYmUgYWJsZSB0byBpbml0IGl0ZW1zIHZpYSBjbGFzZXMgZnJvbSBjb2RlXG4gICAgbGV0IGl0ZW1zID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbChzZWxlY3Rvcik7XG5cbiAgICBpdGVtcy5mb3JFYWNoKChlbCkgPT4ge1xuICAgICAgbGV0IGFuaW1hdGlvbk5hbWVzID0gZ2V0Q29tcHV0ZWRTdHlsZShlbCkuZ2V0UHJvcGVydHlWYWx1ZSgnLS1wZy1hbmltYXRpb24nKSB8fCAnJztcbiAgICAgIGFuaW1hdGlvbk5hbWVzID0gYW5pbWF0aW9uTmFtZXMuc3BsaXQoJywnKTtcblxuICAgICAgYW5pbWF0aW9uTmFtZXMuZm9yRWFjaChmdW5jdGlvbiAoYW5pbWF0aW9uTmFtZSkge1xuICAgICAgICBpZiAoIWFuaW1hdGlvbk5hbWUgfHwgYW5pbWF0aW9uTmFtZSA9PT0gJzAnKSByZXR1cm47XG4gICAgICAgIGxldCBhbmltYXRpb25EYXRhID0gcGdBbmltYXRpb25zW2FuaW1hdGlvbk5hbWVdW2FuaW1hdGlvbk5hbWVdO1xuICAgICAgICBpZiAoYW5pbWF0aW9uRGF0YSA9PT0gdW5kZWZpbmVkIHx8IGFuaW1hdGlvbkRhdGEgPT09IG51bGwpIHJldHVybjtcblxuICAgICAgICBsZXQgdHlwZSA9IGFuaW1hdGlvbkRhdGFbJ2FuaW1hdGlvbi1ldmVudCddIHx8ICdsb2FkJztcbiAgICAgICAgbGV0IHRyaWdnZXJTZWxlY3RvciA9IGFuaW1hdGlvbkRhdGFbJ2FuaW1hdGlvbi10cmlnZ2VyJ10gfHwgJ3NlbGYnO1xuICAgICAgICBsZXQgdHJpZ2dlciA9IGVsO1xuXG4gICAgICAgIGlmICh0cmlnZ2VyU2VsZWN0b3IgPT09ICdwYXJlbnQnKSB7XG4gICAgICAgICAgdHJpZ2dlciA9IGVsLnBhcmVudE5vZGU7XG4gICAgICAgIH1cblxuICAgICAgICBpZiAodHJpZ2dlclNlbGVjdG9yICE9PSAnc2VsZicgJiYgdHJpZ2dlclNlbGVjdG9yICE9PSAncGFyZW50Jykge1xuICAgICAgICAgIHRyaWdnZXIgPSBnZXRDbG9zZXN0KGVsLCB0cmlnZ2VyU2VsZWN0b3IpO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKHRyaWdnZXIgPT09IHVuZGVmaW5lZCB8fCB0cmlnZ2VyID09PSBudWxsKSB7XG4gICAgICAgICAgdHJpZ2dlciA9IGVsO1xuICAgICAgICB9XG5cbiAgICAgICAgYW5pbWF0aW9uRGF0YVsnYW5pbWF0aW9uLW5hbWUnXSA9IGFuaW1hdGlvbk5hbWU7XG4gICAgICAgIGVsLmNsYXNzTGlzdC5hZGQoJ3BnLWV2ZW50LScgKyB0eXBlKTtcbiAgICAgICAgZWwuY2xhc3NMaXN0LmFkZCgncGctYW5pbWF0aW9uLScgKyBhbmltYXRpb25OYW1lKTtcblxuICAgICAgICAvLyAvL21ha2UgYW5pbWF0aW9uIHN0b3AgYXQgZW5kXG4gICAgICAgIGVsLnN0eWxlLmFuaW1hdGlvbkZpbGxNb2RlID0gJ2ZvcndhcmRzJztcblxuICAgICAgICAvL2FkZCBmaXJzdCBrZXlmcmFtZSBpZiBleGlzdHNcbiAgICAgICAgaWYgKHBnQW5pbWF0aW9uc1thbmltYXRpb25OYW1lXVsna2V5ZnJhbWVzJ11bJzAnXSAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICAgICAgZWwuY2xhc3NMaXN0LmFkZChwZ0FuaW1hdGlvbnNbYW5pbWF0aW9uTmFtZV1bJ2tleWZyYW1lcyddWycwJ11bJ2lkJ10pO1xuICAgICAgICB9XG5cbiAgICAgICAgLy9hZGQgY2xhc3NlcyBpZiBhbmltYXRpb24gaXMgcnVubmluZ1xuICAgICAgICBlbC5hZGRFdmVudExpc3RlbmVyKFwiYW5pbWF0aW9uc3RhcnRcIiwgKGFuaSkgPT4ge1xuXG4gICAgICAgICAgaWYgKGFuaS5hbmltYXRpb25OYW1lICE9PSBhbmltYXRpb25OYW1lKSByZXR1cm47XG5cbiAgICAgICAgICBpZiAoZWwuY2xhc3NMaXN0LmNvbnRhaW5zKCdwZy1ldmVudC10cmlnZ2VyLWludmlldycpICYmIGVsLmNsYXNzTGlzdC5jb250YWlucygncGctZXZlbnQtaG92ZXInKSAmJiBlbC5jbGFzc0xpc3QuY29udGFpbnMoJ3BnLWV2ZW50LWhvdmVyLWFjdGl2ZScpKSB7XG4gICAgICAgICAgICBpZiAoYW5pLmVsYXBzZWRUaW1lICE9PSAxKSB7XG4gICAgICAgICAgICAgIC8vaWYgYW5pbWF0aW9uIHN0YXJ0cyBmcm9tIHJldmVyc2UgcmVtb3ZlIHN0YXRlXG4gICAgICAgICAgICAgIGVsLmNsYXNzTGlzdC5yZW1vdmUocGdBbmltYXRpb25zW2FuaW1hdGlvbk5hbWVdWydrZXlmcmFtZXMnXVsnMTAwJ11bJ2lkJ10pO1xuICAgICAgICAgICAgfVxuICAgICAgICAgIH1cblxuICAgICAgICAgIGVsLmNsYXNzTGlzdC5hZGQoJ3BnLWFuaW1hdGlvbi1pcy1ydW5uaW5nJyk7XG5cbiAgICAgICAgICBpZiAoYW5pLmVsYXBzZWRUaW1lICE9PSAxKSB7XG4gICAgICAgICAgICAvL2lmIGFuaW1hdGlvbiBzdGFydHMgZnJvbSByZXZlcnNlIHJlbW92ZSBzdGF0ZVxuICAgICAgICAgICAgZWwuY2xhc3NMaXN0LnJlbW92ZShwZ0FuaW1hdGlvbnNbYW5pbWF0aW9uTmFtZV1bJ2tleWZyYW1lcyddWycxMDAnXVsnaWQnXSk7XG4gICAgICAgICAgfVxuXG4gICAgICAgICAgLy8gdHJpZ2dlciBhIERPTSByZWZsb3cgdG8gcmVpbml0IGFuaW1hdGlvblxuICAgICAgICAgIC8vIHZvaWQgZWwub2Zmc2V0V2lkdGg7XG5cbiAgICAgICAgfSk7XG5cbiAgICAgICAgZWwuYWRkRXZlbnRMaXN0ZW5lcihcImFuaW1hdGlvbmNhbmNlbFwiLCAoYW5pKSA9PiB7XG4gICAgICAgICAgaWYgKGFuaS5hbmltYXRpb25OYW1lICE9PSBhbmltYXRpb25OYW1lKSByZXR1cm47XG4gICAgICAgICAgZWwuY2xhc3NMaXN0LnJlbW92ZSgncGctYW5pbWF0aW9uLWlzLXJ1bm5pbmcnKTtcbiAgICAgICAgICBpZiAoYW5pLmVsYXBzZWRUaW1lICE9PSAxKSB7XG4gICAgICAgICAgICAvL2lmIGFuaW1hdGlvbiBzdGFydHMgZnJvbSByZXZlcnNlIHJlbW92ZSBzdGF0ZVxuICAgICAgICAgICAgZWwuY2xhc3NMaXN0LnJlbW92ZShwZ0FuaW1hdGlvbnNbYW5pbWF0aW9uTmFtZV1bJ2tleWZyYW1lcyddWycxMDAnXVsnaWQnXSk7XG4gICAgICAgICAgfVxuICAgICAgICB9KTtcblxuICAgICAgICAvL3JlbW92ZSBhbmltYXRpb24gd2hlbiBpdCdzIGZpbmlzaGVkXG4gICAgICAgIGVsLmFkZEV2ZW50TGlzdGVuZXIoXCJhbmltYXRpb25lbmRcIiwgKGFuaSkgPT4ge1xuICAgICAgICAgIGlmIChhbmkuYW5pbWF0aW9uTmFtZSAhPT0gYW5pbWF0aW9uTmFtZSkgcmV0dXJuO1xuXG4gICAgICAgICAgaWYgKGVsLmNsYXNzTGlzdC5jb250YWlucygncGctZXZlbnQtdHJpZ2dlci1pbnZpZXcnKSAmJiBlbC5jbGFzc0xpc3QuY29udGFpbnMoJ3BnLWV2ZW50LWhvdmVyJykgJiYgZWwuY2xhc3NMaXN0LmNvbnRhaW5zKCdwZy1ldmVudC1ob3Zlci1hY3RpdmUnKSkge1xuICAgICAgICAgICAgaWYgKGFuaS5lbGFwc2VkVGltZSAhPT0gMSkge1xuICAgICAgICAgICAgICAvL2lmIGFuaW1hdGlvbiBzdGFydHMgZnJvbSByZXZlcnNlIHJlbW92ZSBzdGF0ZVxuICAgICAgICAgICAgICBlbC5jbGFzc0xpc3QucmVtb3ZlKHBnQW5pbWF0aW9uc1thbmltYXRpb25OYW1lXVsna2V5ZnJhbWVzJ11bJzEwMCddWydpZCddKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9XG5cbiAgICAgICAgICBlbC5jbGFzc0xpc3QucmVtb3ZlKCdwZy1hbmltYXRpb24taXMtcnVubmluZycpO1xuICAgICAgICAgIC8vIGNvbnNvbGUubG9nKCdyZW1vdmUgYW5pbWF0aW9uJyk7XG5cbiAgICAgICAgICBpZiAoYW5pLmVsYXBzZWRUaW1lID4gMCkge1xuICAgICAgICAgICAgLy9hZGQgbGFzdCBrZXlmcmFtZSBjbGFzcyB0byBwcmVzZXJ2ZSBzdGF0ZVxuICAgICAgICAgICAgZWwuY2xhc3NMaXN0LmFkZChwZ0FuaW1hdGlvbnNbYW5pbWF0aW9uTmFtZV1bJ2tleWZyYW1lcyddWycxMDAnXVsnaWQnXSk7XG4gICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIGVsLmNsYXNzTGlzdC5yZW1vdmUocGdBbmltYXRpb25zW2FuaW1hdGlvbk5hbWVdWydrZXlmcmFtZXMnXVsnMTAwJ11bJ2lkJ10pO1xuICAgICAgICAgICAgZWwuY2xhc3NMaXN0LnJlbW92ZSgncGctZXZlbnQtaG92ZXItYWN0aXZlJyk7XG4gICAgICAgICAgfVxuXG4gICAgICAgICAgZWwuY2xhc3NMaXN0LnJlbW92ZSgncGctZXZlbnQtdHJpZ2dlci0nICsgdHlwZSk7XG4gICAgICAgICAgZWwuY2xhc3NMaXN0LnJlbW92ZSgncGctYW5pbWF0aW9uLScgKyBhbmltYXRpb25OYW1lKTtcbiAgICAgICAgfSk7XG5cblxuICAgICAgICBpZiAodHlwZSA9PT0gJ2xvYWQnKSB7XG4gICAgICAgICAgaW5pdEV2ZW50TG9hZChlbCwgdHJpZ2dlciwgYW5pbWF0aW9uRGF0YSk7XG4gICAgICAgIH1cblxuICAgICAgICBpZiAodHlwZSA9PT0gJ2hvdmVyJykge1xuICAgICAgICAgIGluaXRFdmVudEhvdmVyKGVsLCB0cmlnZ2VyLCBhbmltYXRpb25EYXRhKTtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmICh0eXBlID09PSAnY2xpY2snKSB7XG4gICAgICAgICAgaW5pdEV2ZW50Q2xpY2soZWwsIHRyaWdnZXIsIGFuaW1hdGlvbkRhdGEpO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKHR5cGUgPT09ICdzY3JvbGwnIHx8IHR5cGUgPT09ICdpbnZpZXcnKSB7XG4gICAgICAgICAgaW5pdEV2ZW50U2Nyb2xsKGVsLCB0cmlnZ2VyLCBhbmltYXRpb25EYXRhKTtcbiAgICAgICAgfVxuXG4gICAgICB9KTtcbiAgICB9KTtcbiAgfTtcblxuICAvLyBzdGFydCBhbmltYXRpb25zXG4gIHNjcm9sbEFuaW1hdGlvbnMuZm9yRWFjaCgoYW5pbWF0aW9uKSA9PiBhbmltYXRpb24uc3RhcnQoKSk7XG59XG5cbmZ1bmN0aW9uIGluaXRFdmVudExvYWQoZWwsIHRyaWdnZXIsIGFuaW1hdGlvbkRhdGEpIHtcbiAgZWwuY2xhc3NMaXN0LmFkZCgncGctZXZlbnQtdHJpZ2dlci1sb2FkJyk7XG59XG5cbmZ1bmN0aW9uIGluaXRFdmVudEhvdmVyKGVsLCB0cmlnZ2VyLCBhbmltYXRpb25EYXRhKSB7XG4gIGxldCBhbmltYXRpb25OYW1lID0gYW5pbWF0aW9uRGF0YVsnYW5pbWF0aW9uLW5hbWUnXTtcbiAgbGV0IHJldmVyc2UgPSBhbmltYXRpb25EYXRhWydhbmltYXRpb24tcmV2ZXJzZSddIHx8ICd0cnVlJztcblxuICAvL21vdXNlIGVudGVyXG4gIHRyaWdnZXIuYWRkRXZlbnRMaXN0ZW5lcignbW91c2VlbnRlcicsIGZ1bmN0aW9uIChlKSB7XG5cbiAgICBpZiAoZS50YXJnZXQgIT09IGUuY3VycmVudFRhcmdldCkgcmV0dXJuO1xuICAgIGlmIChlbC5jbGFzc0xpc3QuY29udGFpbnMoJ3BnLWFuaW1hdGlvbi1pcy1ydW5uaW5nJykpIHJldHVybjtcbiAgICBpZiAoZWwuY2xhc3NMaXN0LmNvbnRhaW5zKCdwZy1ldmVudC1ob3Zlci1vbmNlJykpIHJldHVybjtcblxuICAgIGVsLmNsYXNzTGlzdC5hZGQoJ3BnLWV2ZW50LWhvdmVyLWFjdGl2ZScpO1xuICAgIGVsLmNsYXNzTGlzdC5hZGQoJ3BnLWFuaW1hdGlvbi0nICsgYW5pbWF0aW9uTmFtZSk7XG4gICAgZWwuY2xhc3NMaXN0LmFkZChcInBnLWV2ZW50LXRyaWdnZXItaG92ZXJcIik7XG5cbiAgICBpZiAocmV2ZXJzZSA9PT0gJ2ZhbHNlJykge1xuICAgICAgZWwuY2xhc3NMaXN0LmFkZCgncGctZXZlbnQtaG92ZXItb25jZScpO1xuICAgIH1cblxuICB9LCB7IHBhc3NpdmU6IHRydWUgfSk7XG5cbiAgLy9tb3VzZSBtb3VzZW91dFxuICB0cmlnZ2VyLmFkZEV2ZW50TGlzdGVuZXIoJ21vdXNlbGVhdmUnLCBmdW5jdGlvbiAoZSkge1xuXG4gICAgaWYgKGUudGFyZ2V0ICE9PSBlLmN1cnJlbnRUYXJnZXQpIHJldHVybjtcbiAgICBpZiAoIWVsLmNsYXNzTGlzdC5jb250YWlucygncGctZXZlbnQtaG92ZXItYWN0aXZlJykpIHJldHVybjtcblxuICAgIGVsLmNsYXNzTGlzdC5yZW1vdmUoJ3BnLWV2ZW50LWhvdmVyLWFjdGl2ZScpO1xuXG4gICAgLy90cmlnZ2VyIGlmIG5vdCBwbGF5aW5nXG4gICAgaWYgKCFlbC5jbGFzc0xpc3QuY29udGFpbnMoJ3BnLWFuaW1hdGlvbi1pcy1ydW5uaW5nJykpIHtcbiAgICAgIGVsLmNsYXNzTGlzdC5hZGQoJ3BnLWFuaW1hdGlvbi0nICsgYW5pbWF0aW9uTmFtZSk7XG4gICAgICBlbC5jbGFzc0xpc3QuYWRkKFwicGctZXZlbnQtdHJpZ2dlci1ob3ZlclwiKTtcbiAgICB9XG5cbiAgICAvL3BsYXkgcmV2ZXJzZVxuICAgIGlmIChyZXZlcnNlID09PSAndHJ1ZScpIHtcbiAgICAgIHBsYXlBbmltYXRpb24oZWwsIGFuaW1hdGlvbk5hbWUsIHRydWUpO1xuICAgIH1cblxuXG4gIH0sIGZhbHNlKTtcblxufVxuXG5mdW5jdGlvbiBpbml0RXZlbnRDbGljayhlbCwgdHJpZ2dlciwgYW5pbWF0aW9uRGF0YSkge1xuICBsZXQgYW5pbWF0aW9uTmFtZSA9IGFuaW1hdGlvbkRhdGFbJ2FuaW1hdGlvbi1uYW1lJ107XG4gIGxldCByZXZlcnNlID0gYW5pbWF0aW9uRGF0YVsnYW5pbWF0aW9uLXJldmVyc2UnXSB8fCAndHJ1ZSc7XG5cbiAgLy9hZGQgY2xpY2sgYW5kIHRvdWNoIGV2ZW50c1xuICBbJ2NsaWNrJywgJ3RvdWNoc3RhcnQnXS5mb3JFYWNoKGZ1bmN0aW9uIChldmVudFR5cGUpIHtcbiAgICB0cmlnZ2VyLmFkZEV2ZW50TGlzdGVuZXIoZXZlbnRUeXBlLCBmdW5jdGlvbiAoZSkge1xuICAgICAgaWYgKHRyaWdnZXIgPT0gZS5jdXJyZW50VGFyZ2V0KSB7XG4gICAgICAgIC8vIGNvbnNvbGUubG9nKCdjbGljaycpO1xuXG4gICAgICAgIC8vdHJpZ2dlciBpZiBub3QgcGxheWluZ1xuICAgICAgICBpZiAoIWVsLmNsYXNzTGlzdC5jb250YWlucygncGctYW5pbWF0aW9uLWlzLXJ1bm5pbmcnKSkge1xuICAgICAgICAgIGVsLmNsYXNzTGlzdC5hZGQoJ3BnLWFuaW1hdGlvbi0nICsgYW5pbWF0aW9uTmFtZSk7XG4gICAgICAgICAgZWwuY2xhc3NMaXN0LmFkZChcInBnLWV2ZW50LXRyaWdnZXItY2xpY2tcIik7XG4gICAgICAgIH1cblxuICAgICAgICAvL3BsYXkgcmV2ZXJzZVxuICAgICAgICBpZiAocmV2ZXJzZSA9PT0gJ3RydWUnICYmIGVsLmNsYXNzTGlzdC5jb250YWlucygncGctZXZlbnQtY2xpY2stYWN0aXZlJykpIHtcbiAgICAgICAgICBwbGF5QW5pbWF0aW9uKGVsLCBhbmltYXRpb25OYW1lLCB0cnVlKTtcbiAgICAgICAgfVxuXG4gICAgICAgIGVsLmNsYXNzTGlzdC50b2dnbGUoJ3BnLWV2ZW50LWNsaWNrLWFjdGl2ZScpO1xuICAgICAgfVxuICAgIH0sIHsgcGFzc2l2ZTogdHJ1ZSB9KTtcbiAgfSk7XG59XG5cbmZ1bmN0aW9uIGluaXRFdmVudFNjcm9sbChlbCwgdHJpZ2dlciwgYW5pbWF0aW9uRGF0YSkge1xuXG4gIGxldCBhbmltYXRpb25OYW1lID0gYW5pbWF0aW9uRGF0YVsnYW5pbWF0aW9uLW5hbWUnXTtcbiAgbGV0IGtleWZyYW1lc0RhdGEgPSBwZ0FuaW1hdGlvbnNbYW5pbWF0aW9uTmFtZV1bJ2tleWZyYW1lcyddO1xuICBsZXQga2V5UHJldiA9ICcwJztcbiAgbGV0IGtleWZyYW1lRGF0YVByZXYgPSB7fTtcblxuICAvL2dldCBvcHRpb25zXG4gIGxldCB0eXBlID0gYW5pbWF0aW9uRGF0YVsnYW5pbWF0aW9uLWV2ZW50J10gfHwgJ2xvYWQnO1xuICBsZXQgcGluID0gYW5pbWF0aW9uRGF0YVsnYW5pbWF0aW9uLXBpbiddIHx8ICdmYWxzZSc7XG4gIGxldCBvbmNlID0gYW5pbWF0aW9uRGF0YVsnYW5pbWF0aW9uLW9uY2UnXSB8fCAnZmFsc2UnO1xuICBsZXQgdGltaW5nID0gYW5pbWF0aW9uRGF0YVsnYW5pbWF0aW9uLXRpbWluZyddIHx8ICdsaW5lYXInO1xuICBsZXQgZGlzdGFuY2UgPSBwYXJzZUludChhbmltYXRpb25EYXRhWydhbmltYXRpb24tZGlzdGFuY2UnXSkgfHwgMTAwO1xuICBsZXQgb2Zmc2V0ID0gcGFyc2VJbnQoYW5pbWF0aW9uRGF0YVsnYW5pbWF0aW9uLXN0YXJ0J10pIHx8IDA7XG4gIGxldCBzY3JvbGxQcm9wcyA9IHt9O1xuICBsZXQgZGVidWcgPSBmYWxzZTtcblxuICBsZXQgdXJsID0gd2luZG93LmxvY2F0aW9uLmhyZWY7XG4gIGlmICh1cmwuaW5jbHVkZXMoJz9kZWJ1ZycpKSB7XG4gICAgZGVidWcgPSB0cnVlO1xuICB9XG5cbiAgaWYgKHR5cGUgIT09ICdzY3JvbGwnICYmIHR5cGUgIT09ICdpbnZpZXcnKSB7XG4gICAgcmV0dXJuO1xuICB9XG5cbiAgaWYgKHBpbiA9PT0gJ2ZhbHNlJykge1xuICAgIHBpbiA9IDA7XG4gIH1cblxuICBpZiAob25jZSA9PT0gJ2ZhbHNlJykge1xuICAgIG9uY2UgPSAwO1xuICB9XG5cbiAgaWYgKGRlYnVnKSB7XG4gICAgbGV0IGRlYnVnU3RhcnQgPSAnPGRpdiBjbGFzcz1cInBnLWFuaW1hdGlvbi1kZWJ1Z1wiIHN0eWxlPVwiYm90dG9tOicgKyBvZmZzZXQgKyAndmg7IHBvc2l0aW9uOmZpeGVkOyBoZWlnaHQ6MXB4OyB3aWR0aDoxMDB2dzsgYmFja2dyb3VuZC1jb2xvcjpncmVlbjsgei1pbmRleDo5OTk7IHBvaW50ZXItZXZlbnRzOm5vbmU7XCI+JyArIGFuaW1hdGlvbk5hbWUgKyAnIHN0YXJ0PC9kaXY+JztcbiAgICBsZXQgZGVidWdFbmQgPSAnPGRpdiBjbGFzcz1cInBnLWFuaW1hdGlvbi1kZWJ1Z1wiIHN0eWxlPVwiYm90dG9tOicgKyAoZGlzdGFuY2UgKyBvZmZzZXQpICsgJ3ZoOyBwb3NpdGlvbjpmaXhlZDsgaGVpZ2h0OjFweDsgd2lkdGg6MTAwdnc7IGJhY2tncm91bmQtY29sb3I6cmVkOyAgei1pbmRleDo5OTk7IHBvaW50ZXItZXZlbnRzOm5vbmU7XCI+JyArIGFuaW1hdGlvbk5hbWUgKyAnIGVuZDwvZGl2Pic7XG4gICAgZG9jdW1lbnQuYm9keS5pbnNlcnRBZGphY2VudEhUTUwoJ2JlZm9yZWVuZCcsIGRlYnVnU3RhcnQpO1xuICAgIGRvY3VtZW50LmJvZHkuaW5zZXJ0QWRqYWNlbnRIVE1MKCdiZWZvcmVlbmQnLCBkZWJ1Z0VuZCk7XG4gIH1cblxuICAvLyBjb25zb2xlLmxvZygnZGlzdGFuY2U6ICcgKyBkaXN0YW5jZSk7XG5cbiAgLy9jcmVhdGUgcGluXG4gIGlmIChwaW4pIHtcbiAgICBjb25zb2xlLmxvZygnY3JlYXRlIHBpbicpO1xuICAgIGxldCBwaW5TdGFydFBpeGVsID0gKHdpbmRvdy5pbm5lckhlaWdodCAvIDEwMCkgKiAoMTAwIC0gb2Zmc2V0KTtcbiAgICBsZXQgcGluRW5kUGl4ZWwgPSAod2luZG93LmlubmVySGVpZ2h0IC8gMTAwKSAqIChkaXN0YW5jZSArIG9mZnNldCk7XG4gICAgY3JlYXRlUGluKHBpbiwgdHJpZ2dlciwgcGluU3RhcnRQaXhlbCwgcGluRW5kUGl4ZWwpO1xuICB9XG5cbiAgLy9mb3IgdHlwZSBpbnZpZXcgY3JlYXRlIG9uZSBhbmltYXRpb25cbiAgaWYgKHR5cGUgPT09ICdpbnZpZXcnKSB7XG4gICAgLy8gbGV0IHN0YXJ0ID0gT2JqZWN0LmtleXMoa2V5ZnJhbWVzRGF0YSlbMF07XG4gICAgbGV0IHByb3AgPSAnLS1wZy1hbmltYXRpb24tZHVtbXktJyArIGFuaW1hdGlvbk5hbWU7XG5cbiAgICAvL2NyZWF0ZSBzaW5nbGUgZHVtbXkgcHJvcGVydHlcbiAgICBzY3JvbGxQcm9wc1twcm9wXSA9IHtcbiAgICAgIGZyb206ICcwJyxcbiAgICAgIHRvOiAnMTAwJ1xuICAgIH07XG5cbiAgICBjcmVhdGVBbmltYXRpb24oZWwsIHRyaWdnZXIsIG9mZnNldCwgZGlzdGFuY2UsIHR5cGUsIHBpbiwgb25jZSwgZGlzdGFuY2UsIG9mZnNldCwgc2Nyb2xsUHJvcHMsIDEsIDEsIGFuaW1hdGlvbk5hbWUsIGRlYnVnKTtcbiAgICByZXR1cm47XG4gIH1cblxuICAvL2ZvciB0eXBlIHNjcm9sbCBjcmVhdGUgYW5pbWF0aW9uIHBlciBrZXlmcmFtZVxuICAvL3NvcnQga2V5ZnJhbWVzXG4gIE9iamVjdC5rZXlzKGtleWZyYW1lc0RhdGEpLnNvcnQoKS5yZWR1Y2UoKGEsIGMpID0+IChhW2NdID0ga2V5ZnJhbWVzRGF0YVtjXSwgYSksIHt9KTtcbiAgLy8gY29uc29sZS5sb2coa2V5ZnJhbWVzRGF0YSk7XG5cbiAgbGV0IEtleWZyYW1lc1RvdGFsID0gT2JqZWN0LmtleXMoa2V5ZnJhbWVzRGF0YSkubGVuZ3RoO1xuICBsZXQgS2V5ZnJhbWVDb3VudCA9IDA7XG4gIGxldCBpID0gMDtcbiAgZm9yIChjb25zdCBrZXkgaW4ga2V5ZnJhbWVzRGF0YSkge1xuXG4gICAgLy9vbmx5IGdldCBrZXlmcmFtZXNcbiAgICBpZiAoIWtleWZyYW1lc0RhdGFba2V5XS5oYXNPd25Qcm9wZXJ0eSgna2V5ZnJhbWUnKSkge1xuICAgICAgY29udGludWU7XG4gICAgfVxuXG4gICAgS2V5ZnJhbWVDb3VudCsrO1xuXG4gICAgLy8gZ2V0IHByb3BzIG9mIGtleWZyYW1lc1xuICAgIHNjcm9sbFByb3BzID0ge307XG4gICAgbGV0IGtleWZyYW1lRGF0YSA9IGtleWZyYW1lc0RhdGFba2V5XVsnYnJlYWtwb2ludHMnXVsnYmFzZSddWydjc3MnXTtcblxuICAgIC8vZ2V0IHByZXYgdmFsdWVcbiAgICBpZiAoa2V5ZnJhbWVzRGF0YVtrZXlQcmV2XSAmJiBrZXlmcmFtZXNEYXRhW2tleVByZXZdWydicmVha3BvaW50cyddKSB7XG4gICAgICBrZXlmcmFtZURhdGFQcmV2ID0ga2V5ZnJhbWVzRGF0YVtrZXlQcmV2XVsnYnJlYWtwb2ludHMnXVsnYmFzZSddWydjc3MnXTtcbiAgICB9XG5cbiAgICAvLyBjb25zb2xlLmxvZygna2V5ZnJhbWU6ICcgKyBrZXkpO1xuXG5cbiAgICAvLyBzZXQgc2Nyb2xsUHJvcHMgZm9yIGJhc2ljU2Nyb2xsXG4gICAgZm9yIChsZXQgcHJvcCBpbiBrZXlmcmFtZURhdGEpIHtcblxuICAgICAgaSsrO1xuXG4gICAgICBsZXQgZnJvbSA9IGtleWZyYW1lRGF0YVByZXZbcHJvcF0gfHwgJzAnO1xuICAgICAgbGV0IHRvID0ga2V5ZnJhbWVEYXRhW3Byb3BdIHx8ICcwJztcbiAgICAgIGxldCBwcm9wZXJ0eSA9ICctLXBnLScgKyBwcm9wICsgaTtcblxuICAgICAgLy8gY29uc29sZS5sb2cocHJvcCk7XG5cbiAgICAgIC8vIGNvbnZlcnQgdG8gYWJzb2x1dGUgdmFsdWVzIHdlIGNhbiBwYXNzcyB0byBiYXNpY1Njcm9sbCBlZy4gXCIyMHB4XCIgb3IgXCIxMCVcIlxuXG4gICAgICAvLyBnZXQgY3NzIHZhbHVlIGZhbGxiYWNrIGlmIHByb3Agd2l0aCBubyB1bml0IGFuZCBmcm9tIG5vdCBkZWZpbmVkXG4gICAgICBpZiAocHJvcCA9PT0gJ3NjYWxlJyB8fCBwcm9wID09PSAnY29sb3InIHx8IHByb3AgPT09ICdiYWNrZ3JvdW5kLWNvbG9yJyB8fCBwcm9wID09PSAnb3BhY2l0eScpIHtcbiAgICAgICAgaWYgKGtleWZyYW1lRGF0YVByZXZbcHJvcF0gPT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgIGZyb20gPSBnZXRDb21wdXRlZFN0eWxlKGVsKS5nZXRQcm9wZXJ0eVZhbHVlKHByb3ApIHx8ICcxJztcbiAgICAgICAgICBpZiAoZnJvbSA9PT0gJ25vbmUnKSBmcm9tID0gJzEnO1xuICAgICAgICB9XG4gICAgICB9XG5cbiAgICAgIC8vY29udmVydCB0cmFuc2Zvcm0gc3RyaW5nIHRvIGluZGl2aWR1YWxsIHByb3BzXG4gICAgICBpZiAocHJvcCA9PT0gJ3RyYW5zZm9ybScpIHtcbiAgICAgICAgLy9yZW1vdmUgZmlyc3QgZW1wdHkgc3BhY2VcbiAgICAgICAgZnJvbSA9IGZyb20ucmVwbGFjZSgvXiAvLCAnJyk7XG4gICAgICAgIHRvID0gdG8ucmVwbGFjZSgvXiAvLCAnJyk7XG4gICAgICAgIC8vc3BsaXQgc3RyaW5nIGFmdGVyIHNwYWNlXG4gICAgICAgIGxldCB0cmFuc2Zvcm1Gcm9tID0gZnJvbS5zcGxpdCgnICcpO1xuICAgICAgICBsZXQgdHJhbnNmb3JtVG8gPSB0by5zcGxpdCgnICcpO1xuXG4gICAgICAgIHRyYW5zZm9ybUZyb20uZm9yRWFjaChmdW5jdGlvbiAoa2V5LCBpbmRleCkge1xuICAgICAgICAgIGxldCBwcm9wZXJ0eTIgPSBwcm9wZXJ0eSArIGluZGV4O1xuICAgICAgICAgIGxldCBzdWJQcm9wID0gU3RyaW5nKHRyYW5zZm9ybUZyb21baW5kZXhdKS5zcGxpdCgnKCcpWzBdO1xuICAgICAgICAgIGxldCBmcm9tMiA9IFN0cmluZyh0cmFuc2Zvcm1Gcm9tW2luZGV4XSkuc3BsaXQoJygnKS5wb3AoKS5zcGxpdCgnKScpWzBdO1xuICAgICAgICAgIGxldCB0bzIgPSBTdHJpbmcodHJhbnNmb3JtVG9baW5kZXhdKS5zcGxpdCgnKCcpLnBvcCgpLnNwbGl0KCcpJylbMF07XG5cbiAgICAgICAgICBpZiAodHJhbnNmb3JtRnJvbVtpbmRleF0gPT09IHVuZGVmaW5lZCkgZnJvbTIgPSAnMCc7XG4gICAgICAgICAgaWYgKHRyYW5zZm9ybVRvW2luZGV4XSA9PT0gdW5kZWZpbmVkKSB0bzIgPSAnMCc7XG5cbiAgICAgICAgICAvLyByZW5hbWUgcHJvcGVydHkyIGZvciBjbGFyaXR5XG4gICAgICAgICAgaWYgKHN1YlByb3AgPT09ICd0cmFuc2xhdGVZJykge1xuICAgICAgICAgICAgcHJvcGVydHkyID0gJy0tcGctJyArIHN1YlByb3AgKyBpbmRleDtcbiAgICAgICAgICB9XG5cbiAgICAgICAgICAvLyBjb25zb2xlLmxvZygncHJvcDonICsgc3ViUHJvcCArICcgZnJvbTI6JyArIGZyb20yICsgJyB0bzI6JyArIHRvMik7XG5cbiAgICAgICAgICBzY3JvbGxQcm9wc1twcm9wZXJ0eTJdID0ge1xuICAgICAgICAgICAgZnJvbTogYCR7ZnJvbTJ9YCxcbiAgICAgICAgICAgIHRvOiBgJHt0bzJ9YCxcbiAgICAgICAgICAgIHRpbWluZzogYCR7dGltaW5nfWAsXG4gICAgICAgICAgICBwcm9wOiBgJHtwcm9wfWAsXG4gICAgICAgICAgICBzdWJQcm9wOiBgJHtzdWJQcm9wfWBcbiAgICAgICAgICB9O1xuXG4gICAgICAgIH0pO1xuXG4gICAgICAgIGNvbnRpbnVlO1xuICAgICAgfVxuXG4gICAgICAvLyBpZiBjb2xvcnMgY3JlYXRlIG11bHRpcGxlIHByb3BlcnRpZXMgYW5kIGNzcyB2YXJzIGZvciBlY2ggUkdCIGNoYW5uZWxcbiAgICAgIGlmIChwcm9wID09PSAnY29sb3InIHx8IHByb3AgPT09ICdiYWNrZ3JvdW5kLWNvbG9yJykge1xuXG4gICAgICAgIC8vY29udmVydCB2YWx1ZXMgdG8gYXJyYXlzXG4gICAgICAgIGxldCByZ2JGcm9tID0gZnJvbS5zcGxpdCgnLCcpO1xuICAgICAgICBsZXQgcmdiVG8gPSB0by5zcGxpdCgnLCcpO1xuXG4gICAgICAgIC8vYWx3YXlzIGZvcmNlIHJnYmEgdmFsdWVcbiAgICAgICAgaWYgKHJnYkZyb20ubGVuZ3RoID09IDMpIHtcbiAgICAgICAgICByZ2JGcm9tWzNdID0gJzEnO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKHJnYlRvLmxlbmd0aCA9PSAzKSB7XG4gICAgICAgICAgcmdiVG9bM10gPSAnMSc7XG4gICAgICAgIH1cblxuICAgICAgICByZ2JGcm9tLmZvckVhY2goZnVuY3Rpb24gKGNvbG9yLCBpbmRleCkge1xuICAgICAgICAgIGxldCBwcm9wZXJ0eTIgPSBTdHJpbmcocHJvcGVydHkgKyBpbmRleCk7XG4gICAgICAgICAgbGV0IGZyb21Db2xvciA9IFN0cmluZyhyZ2JGcm9tW2luZGV4XSkucmVwbGFjZSgvW15cXGQuXS9nLCAnJyk7XG4gICAgICAgICAgbGV0IHRvQ29sb3IgPSBTdHJpbmcocmdiVG9baW5kZXhdKS5yZXBsYWNlKC9bXlxcZC5dL2csICcnKTtcblxuICAgICAgICAgIC8vIGNvbnNvbGUubG9nKCdwcm9wMjonICsgcHJvcGVydHkyICsgJyBmcm9tQ29sb3I6JyArIGZyb21Db2xvciArICcgdG9Db2xvcjonICsgdG9Db2xvcik7XG5cbiAgICAgICAgICBzY3JvbGxQcm9wc1twcm9wZXJ0eTJdID0ge1xuICAgICAgICAgICAgZnJvbTogYCR7ZnJvbUNvbG9yfWAsXG4gICAgICAgICAgICB0bzogYCR7dG9Db2xvcn1gLFxuICAgICAgICAgICAgdGltaW5nOiBgJHt0aW1pbmd9YCxcbiAgICAgICAgICAgIHByb3A6IGAke3Byb3B9YFxuICAgICAgICAgIH07XG5cbiAgICAgICAgfSk7XG5cbiAgICAgICAgY29udGludWU7XG5cbiAgICAgIH1cblxuICAgICAgLy9zaW5nbGUgcHJvcGVydHlcbiAgICAgIHNjcm9sbFByb3BzW3Byb3BlcnR5XSA9IHtcbiAgICAgICAgZnJvbTogYCR7ZnJvbX1gLFxuICAgICAgICB0bzogYCR7dG99YCxcbiAgICAgICAgdGltaW5nOiBgJHt0aW1pbmd9YCxcbiAgICAgICAgcHJvcDogYCR7cHJvcH1gXG4gICAgICB9O1xuXG4gICAgfVxuXG4gICAgLy9jcmVhdGUgb25lIGFuaW1hdGlvbiBwZXIga2V5ZnJhbWVcbiAgICBjcmVhdGVBbmltYXRpb24oZWwsIHRyaWdnZXIsIGtleVByZXYsIGtleSwgdHlwZSwgcGluLCBvbmNlLCBkaXN0YW5jZSwgb2Zmc2V0LCBzY3JvbGxQcm9wcywgS2V5ZnJhbWVDb3VudCwgS2V5ZnJhbWVzVG90YWwsIGFuaW1hdGlvbk5hbWUsIGRlYnVnKTtcblxuICAgIC8vc2V0IGtleVByZXYgdG8gZ2V0IGZyb20gdmFsdWVcbiAgICBrZXlQcmV2ID0ga2V5O1xuXG4gIH1cbn1cblxuZnVuY3Rpb24gY3JlYXRlQW5pbWF0aW9uKGVsLCB0cmlnZ2VyLCBzY3JvbGxTdGFydCwgc2Nyb2xsRW5kLCB0eXBlLCBwaW4sIG9uY2UsIGRpc3RhbmNlLCBvZmZzZXQsIHNjcm9sbFByb3BzLCBLZXlmcmFtZUNvdW50LCBLZXlmcmFtZXNUb3RhbCwgYW5pbWF0aW9uTmFtZSwgZGVidWcpIHtcblxuICAvLyBpZiAoIXByb3AgfHwgcHJvcC5zdGFydHNXaXRoKCdkYXRhLScpIHx8IGVsLmNsYXNzTGlzdC5jb250YWlucygncGctc2Nyb2xsLXdyYXBwZXInKSB8fCBlbC5jbGFzc0xpc3QuY29udGFpbnMoJ3BnLXNjcm9sbC1zcGFjZXInKSkge1xuICAvLyAgIHJldHVybjtcbiAgLy8gfVxuXG4gIGlmIChzY3JvbGxFbmQgPT0gc2Nyb2xsU3RhcnQpIHtcbiAgICByZXR1cm47XG4gIH1cblxuICAvL3NldCBvcmlnaW5cbiAgLy8gZWwuc3R5bGUudHJhbnNmb3JtT3JpZ2luID0gJzAgMCc7XG5cbiAgLy9hZGQgc21vb3RoaW5nXG4gIGlmICh0eXBlID09PSAnc2Nyb2xsJykge1xuICAgIGVsLnN0eWxlLnRyYW5zaXRpb24gPSAnYWxsIDAuMXMgbGluZWFyJztcbiAgfVxuXG4gIC8vc2V0IGJvZHkgb3ZlcmZsb3cgdG8gaGlkZGVuXG4gIGRvY3VtZW50LmJvZHkuc3R5bGUub3ZlcmZsb3dYID0gJ2hpZGRlbic7XG5cbiAgLy9kaXNhYmxlIGxhenlsb2FkIGZvciBpdGVtIHRvIGdldCByaWdodCBoZWlnaHRcbiAgaWYgKGVsLnF1ZXJ5U2VsZWN0b3JBbGwoJy5sYXp5bG9hZCcpWzBdKSB7XG4gICAgZWwucXVlcnlTZWxlY3RvckFsbCgnLmxhenlsb2FkJylbMF0uc3Jjc2V0ID0gZWwucXVlcnlTZWxlY3RvckFsbCgnLmxhenlsb2FkJylbMF0uZ2V0QXR0cmlidXRlKCdkYXRhLXNyY3NldCcpO1xuICAgIGVsLnF1ZXJ5U2VsZWN0b3JBbGwoJy5sYXp5bG9hZCcpWzBdLmNsYXNzTGlzdC5yZW1vdmUoJ2xhenlsb2FkJyk7XG4gIH1cblxuICAvL2dldCBzdGFydC9lbmQgdmFsdWVzIGluIHZoXG4gIHNjcm9sbFN0YXJ0ID0gMTAwIC0gb2Zmc2V0IC0gKChkaXN0YW5jZSAvIDEwMCkgKiBzY3JvbGxTdGFydCk7XG4gIHNjcm9sbEVuZCA9IDEwMCAtIG9mZnNldCAtICgoZGlzdGFuY2UgLyAxMDApICogc2Nyb2xsRW5kKTtcblxuICBpZiAodHlwZSA9PT0gJ2ludmlldycpIHtcbiAgICBzY3JvbGxTdGFydCA9IDEwMCAtIG9mZnNldCAtICgoZGlzdGFuY2UgLyAxMDApICogMSk7XG4gICAgc2Nyb2xsRW5kID0gMTAwIC0gb2Zmc2V0IC0gKChkaXN0YW5jZSAvIDEwMCkgKiAxMDApO1xuICB9XG5cbiAgLy8gY29uc29sZS5sb2coJ3Njcm9sbFN0YXJ0LXNjcm9sbEVuZCDwn5G7Jyk7XG4gIC8vIGNvbnNvbGUubG9nKHNjcm9sbFN0YXJ0ICsgJyA6ICcgKyBzY3JvbGxFbmQpO1xuICAvLyBjb25zb2xlLmxvZyhzY3JvbGxQcm9wcyk7XG5cbiAgLy9nZXQgc3RhcnQvZW5kIGluIHB4XG4gIGxldCBzY3JvbGxTdGFydFBpeGVsID0gKHdpbmRvdy5pbm5lckhlaWdodCAvIDEwMCkgKiBzY3JvbGxTdGFydDtcbiAgbGV0IHNjcm9sbEVuZFBpeGVsID0gKHdpbmRvdy5pbm5lckhlaWdodCAvIDEwMCkgKiBzY3JvbGxFbmQ7XG4gIGxldCBzdGFydCA9IE1hdGgucm91bmQodHJpZ2dlci5nZXRCb3VuZGluZ0NsaWVudFJlY3QoKS50b3AgLSBzY3JvbGxTdGFydFBpeGVsICsgd2luZG93LnNjcm9sbFkpICsgXCJweFwiO1xuICBsZXQgZW5kID0gTWF0aC5yb3VuZCh0cmlnZ2VyLmdldEJvdW5kaW5nQ2xpZW50UmVjdCgpLnRvcCAtIHNjcm9sbEVuZFBpeGVsICsgd2luZG93LnNjcm9sbFkpICsgXCJweFwiO1xuXG4gIC8vIGNvbnNvbGUubG9nKHN0YXJ0ICsgJyA6ICcgKyBlbmQpO1xuXG4gIGlmIChkZWJ1Zykge1xuICAgIGxldCBrZXlmcmFtZSA9IHNjcm9sbFN0YXJ0O1xuICAgIGxldCBkZWJ1Z1N0YXJ0ID0gJzxkaXYgY2xhc3M9XCJwZy1hbmltYXRpb24tZGVidWdcIiBzdHlsZT1cInRvcDonICsgc2Nyb2xsU3RhcnQgKyAndmg7IHBvc2l0aW9uOmZpeGVkOyBoZWlnaHQ6MXB4OyB3aWR0aDoxMDB2dzsgYmFja2dyb3VuZC1jb2xvcjpibHVlOyBjb2xvcjpibHVlOyB6LWluZGV4OjE7XCI+JyArIGtleWZyYW1lICsgJyU8L2Rpdj4nO1xuICAgIGxldCBkZWJ1Z0VuZCA9ICc8ZGl2IGNsYXNzPVwicGctYW5pbWF0aW9uLWRlYnVnXCIgc3R5bGU9XCJ0b3A6JyArIHNjcm9sbEVuZCArICd2aDsgcG9zaXRpb246Zml4ZWQ7IGhlaWdodDoxcHg7IHdpZHRoOjEwMHZ3OyBiYWNrZ3JvdW5kLWNvbG9yOmJsdWU7IGNvbG9yOmJsdWU7IHotaW5kZXg6MTtcIj4nICsga2V5ZnJhbWUgKyAnJTwvZGl2Pic7XG4gICAgZG9jdW1lbnQuYm9keS5pbnNlcnRBZGphY2VudEhUTUwoJ2JlZm9yZWVuZCcsIGRlYnVnU3RhcnQpO1xuICAgIC8vIGRvY3VtZW50LmJvZHkuaW5zZXJ0QWRqYWNlbnRIVE1MKCdiZWZvcmVlbmQnLCBkZWJ1Z0VuZCk7XG4gIH1cblxuICAvL2luaXQgYmFzaWNTY3JvbGxcbiAgLy8gQ3JlYXRlIGFuIGluc3RhbmNlIGZvciB0aGUgY3VycmVudCBlbGVtZW50IGFuZCBzdG9yZSB0aGUgaW5zdGFuY2UgaW4gYW4gYXJyYXkuXG4gIC8vIFdlIHN0YXJ0IHRoZSBhbmltYXRpb24gbGF0ZXIgdXNpbmcgdGhlIGluc3RhbmNlcyBmcm9tIHRoZSBhcnJheS5cbiAgc2Nyb2xsQW5pbWF0aW9ucy5wdXNoKGJhc2ljU2Nyb2xsLmNyZWF0ZSh7XG4gICAgZWxlbTogZWwsXG4gICAgZnJvbTogc3RhcnQsXG4gICAgdG86IGVuZCxcbiAgICBkaXJlY3Q6IHRydWUsXG4gICAgcHJvcHM6IHNjcm9sbFByb3BzLFxuICAgIGluc2lkZTogKGluc3RhbmNlLCBwZXJjZW50YWdlLCBwcm9wcykgPT4ge1xuICAgICAgLy9jYWxsYmFja1xuICAgICAgaW5WaWV3KGVsLCBwZXJjZW50YWdlLCBwcm9wcywgdHlwZSwgb25jZSwgc2Nyb2xsUHJvcHMsIGFuaW1hdGlvbk5hbWUpO1xuICAgIH0sXG4gICAgb3V0c2lkZTogKGluc3RhbmNlLCBwZXJjZW50YWdlLCBwcm9wcykgPT4ge1xuICAgICAgLy9jYWxsYmFja1xuICAgICAgb3V0VmlldyhlbCwgcGVyY2VudGFnZSwgcHJvcHMsIHR5cGUsIG9uY2UsIHNjcm9sbEVuZFBpeGVsLCBLZXlmcmFtZUNvdW50LCBLZXlmcmFtZXNUb3RhbCwgc2Nyb2xsQW5pbWF0aW9ucywgYW5pbWF0aW9uTmFtZSk7XG5cbiAgICB9XG4gIH0pKVxuXG59XG5cbi8vYmFzaWNTY3JvbGwgc3VwcG9ydHMgcmVjYWxjdWxhdGluZyBzdGFydC9lbmQgdmFsdWVzIGF1dG9tYXRpY2FsbHksIG1pZ2h0IGJlIG5lZWRlZCBsYXRlciBmb3IgaW1wcm92ZWQgcmVzcG9uc2l2bmVzc1xuLy8gd2luZG93Lm9ucmVzaXplID0gZnVuY3Rpb24gKCkge1xuLy8gICBzY3JvbGxBbmltYXRpb25zLmZvckVhY2goKGFuaW1hdGlvbikgPT4ge1xuLy8gICAgIGFuaW1hdGlvbi5kZXN0cm95KCk7XG4vLyAgICAgLy8gYW5pbWF0aW9uLmNhbGN1bGF0ZSgpO1xuLy8gICAgIC8vIGFuaW1hdGlvbi51cGRhdGUoKTtcbi8vICAgfSk7XG4vLyAgIGluaXQoKTtcbi8vIH1cblxuXG5mdW5jdGlvbiBjcmVhdGVQaW4ocGluLCBlbCwgc2Nyb2xsU3RhcnRQaXhlbCwgc2Nyb2xsRW5kUGl4ZWwpIHtcblxuICAvLyBjcmVhdGUgd3JhcHBlciBjb250YWluZXJcbiAgbGV0IHdyYXBwZXIgPSBlbC5jbG9uZU5vZGUoZmFsc2UpO1xuICAvLyBsZXQgd3JhcHBlciA9IGRvY3VtZW50LmNyZWF0ZWVsZW50KCdkaXYnKTtcbiAgd3JhcHBlci5zdHlsZS5oZWlnaHQgPSBzY3JvbGxFbmRQaXhlbCArICdweCc7XG4gIC8vIHdyYXBwZXIuc3R5bGUud2lkdGggPSBlbC5vZmZzZXRXaWR0aCArICdweCc7XG4gIHdyYXBwZXIuc3R5bGUuZGlzcGxheSA9ICdibG9jayc7XG4gIHdyYXBwZXIuc3R5bGUucG9zaXRpb24gPSAncmVsYXRpdmUnO1xuICB3cmFwcGVyLnN0eWxlLmJhY2tncm91bmRDb2xvciA9ICd0cmFuc3BhcmVudCc7XG4gIHdyYXBwZXIuc3R5bGUucGFkZGluZyA9ICcwJztcbiAgd3JhcHBlci5zdHlsZS5tYXJnaW4gPSAnMCc7XG4gIHdyYXBwZXIuY2xhc3NMaXN0LmFkZCgncGctc2Nyb2xsLXdyYXBwZXInKTtcblxuICBpZiAocGluICE9PSAncGluLXNwYWNpbmcnKSB7XG4gICAgbGV0IHNwYWNlciA9IGVsLmNsb25lTm9kZShmYWxzZSk7XG4gICAgc3BhY2VyLmNsYXNzTGlzdC5hZGQoJ3BnLXNjcm9sbC1zcGFjZXInKTtcbiAgICBzcGFjZXIuc3R5bGUuaGVpZ2h0ID0gZWwub2Zmc2V0SGVpZ2h0ICsgJ3B4JztcbiAgICBzcGFjZXIuc3R5bGUud2lkdGggPSBlbC5vZmZzZXRXaWR0aCArICdweCc7XG4gICAgc3BhY2VyLnN0eWxlLmJhY2tncm91bmRDb2xvciA9ICd0cmFuc3BhcmVudCc7XG4gICAgZWwucGFyZW50Tm9kZS5pbnNlcnRCZWZvcmUoc3BhY2VyLCBlbCk7XG4gICAgd3JhcHBlci5zdHlsZS5wb3NpdGlvbiA9ICdhYnNvbHV0ZSc7XG5cbiAgICBpZiAod2luZG93LmdldENvbXB1dGVkU3R5bGUoZWwpLmdldFByb3BlcnR5VmFsdWUoJ3otaW5kZXgnKSA9PT0gJ2F1dG8nKSB7XG4gICAgICB3cmFwcGVyLnN0eWxlLnpJbmRleCA9ICcxMSc7XG4gICAgfVxuICB9XG5cbiAgZWwucGFyZW50Tm9kZS5pbnNlcnRCZWZvcmUod3JhcHBlciwgZWwpO1xuICB3cmFwcGVyLmFwcGVuZENoaWxkKGVsKTtcblxuICBlbC5zdHlsZS5wb3NpdGlvbiA9IFwic3RpY2t5XCI7XG4gIGVsLnN0eWxlLnRvcCA9IHNjcm9sbFN0YXJ0UGl4ZWwgKyAncHgnO1xuICBlbC5zdHlsZS5hbGlnblNlbGYgPSAnc3RhcnQnO1xufVxuXG4vL2luIHZpZXdcbmZ1bmN0aW9uIGluVmlldyhlbCwgcGVyY2VudGFnZSwgcHJvcHMsIHR5cGUsIG9uY2UsIHNjcm9sbFByb3BzLCBhbmltYXRpb25OYW1lKSB7XG5cbiAgLy8gY29uc29sZS5sb2coJ2luIHZpZXcnKTtcbiAgLy8gY29uc29sZS5sb2coYW5pbWF0aW9uTmFtZSk7XG5cbiAgZWwuY2xhc3NMaXN0LmFkZCgncGctaXRlbS1pcy1pbnZpZXcnKTtcblxuICBpZiAodHlwZSA9PT0gJ2ludmlldycpIHtcbiAgICBpZiAoIWVsLmNsYXNzTGlzdC5jb250YWlucygncGctYW5pbWF0aW9uLWlzLXJ1bm5pbmcnKSkge1xuICAgICAgaWYgKCFlbC5jbGFzc0xpc3QuY29udGFpbnMoJ3BnLWFuaW1hdGlvbi1wZXJzaXN0JykpIHtcbiAgICAgICAgZWwuY2xhc3NMaXN0LmFkZCgncGctYW5pbWF0aW9uLScgKyBhbmltYXRpb25OYW1lKTtcbiAgICAgICAgZWwuY2xhc3NMaXN0LmFkZChpbnZpZXdUcmlnZ2VyQ2xhc3MpO1xuICAgICAgICBlbC5jbGFzc0xpc3QuYWRkKCdwZy1hbmltYXRpb24tcGVyc2lzdCcpO1xuICAgICAgfVxuICAgIH1cbiAgICByZXR1cm47XG4gIH1cblxuICBpZiAob25jZSAmJiBlbC5jbGFzc0xpc3QuY29udGFpbnMoJ3BnLXN0YXRlLXNjcm9sbGluZy1vbmNlJykpIHtcbiAgICByZXR1cm47XG4gIH1cblxuICAvL3NldCB2YWx1ZVxuICBsZXQga2V5cyA9IE9iamVjdC5rZXlzKHByb3BzKTtcbiAgbGV0IHRyYW5zbGF0ZVggPSAnMCAnO1xuICBsZXQgdHJhbnNsYXRlWSA9ICcwJztcblxuICBmb3IgKGxldCBrZXkgaW4ga2V5cykge1xuXG4gICAga2V5ID0ga2V5c1trZXldO1xuICAgIGxldCBwcm9wID0gc2Nyb2xsUHJvcHNba2V5XS5wcm9wO1xuICAgIGxldCBzdWJQcm9wID0gc2Nyb2xsUHJvcHNba2V5XS5zdWJQcm9wIHx8ICcnO1xuICAgIGxldCBjc3NWYXJzID0gJ3ZhcignICsga2V5ICsgJyknO1xuXG4gICAgLy9tdWx0aSBwcm9wXG4gICAgaWYgKHByb3AgPT09ICdjb2xvcicgfHwgcHJvcCA9PT0gJ2JhY2tncm91bmQtY29sb3InKSB7XG4gICAgICBsZXQgbWF0Y2hlZCA9IE9iamVjdC5rZXlzKHNjcm9sbFByb3BzKS5maWx0ZXIoZnVuY3Rpb24gKGtleSkge1xuICAgICAgICByZXR1cm4gc2Nyb2xsUHJvcHNba2V5XS5wcm9wID09PSBwcm9wO1xuICAgICAgfSk7XG4gICAgICBjc3NWYXJzID0gJ3JnYmEodmFyKCcgKyBtYXRjaGVkWzBdICsgJyksIHZhcignICsgbWF0Y2hlZFsxXSArICcpLCB2YXIoJyArIG1hdGNoZWRbMl0gKyAnKSwgdmFyKCcgKyBtYXRjaGVkWzNdICsgJykpJztcbiAgICB9XG5cbiAgICBpZiAocHJvcCA9PT0gJ3RyYW5zbGF0ZScgJiYgc3ViUHJvcCAhPT0gJ3RyYW5zbGF0ZVknKSB7XG4gICAgICB0cmFuc2xhdGVYID0gJ3ZhcignICsga2V5ICsgJykgJztcbiAgICAgIGNzc1ZhcnMgPSB0cmFuc2xhdGVYICsgdHJhbnNsYXRlWTtcbiAgICB9XG5cbiAgICBpZiAoc3ViUHJvcCA9PT0gJ3RyYW5zbGF0ZVknKSB7XG4gICAgICB0cmFuc2xhdGVZID0gJ3ZhcignICsga2V5ICsgJykgJztcbiAgICAgIGNzc1ZhcnMgPSB0cmFuc2xhdGVYICsgdHJhbnNsYXRlWTtcbiAgICAgIHByb3AgPSAndHJhbnNsYXRlJ1xuICAgIH1cblxuICAgIGlmIChwcm9wID09PSAndHJhbnNmb3JtJykge1xuICAgICAgY3NzVmFycyA9ICcnO1xuICAgICAgZm9yIChsZXQgaW5kZXggaW4gc2Nyb2xsUHJvcHMpIHtcbiAgICAgICAgaWYgKHNjcm9sbFByb3BzW2luZGV4XS5wcm9wID09PSBwcm9wICYmIHNjcm9sbFByb3BzW2luZGV4XS5zdWJQcm9wICE9PSAndHJhbnNsYXRlWScpIHtcbiAgICAgICAgICBjc3NWYXJzICs9IHNjcm9sbFByb3BzW2luZGV4XS5zdWJQcm9wICsgJyh2YXIoJyArIGtleSArICcpKSAnO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgfVxuXG4gICAgZWwuc3R5bGUuc2V0UHJvcGVydHkocHJvcCwgY3NzVmFycyk7XG4gIH1cbn1cblxuZnVuY3Rpb24gb3V0VmlldyhlbCwgcGVyY2VudGFnZSwgcHJvcHMsIHR5cGUsIG9uY2UsIHNjcm9sbEVuZFBpeGVsLCBLZXlmcmFtZUNvdW50LCBLZXlmcmFtZXNUb3RhbCwgc2Nyb2xsQW5pbWF0aW9ucywgYW5pbWF0aW9uTmFtZSkge1xuXG4gIC8vIGNvbnNvbGUubG9nKCdPdXQgb2YgdmlldycpO1xuXG4gIGVsLmNsYXNzTGlzdC5yZW1vdmUoJ3BnLWl0ZW0taXMtaW52aWV3Jyk7XG5cbiAgLy9wbGF5IHNjcm9sbCBhbmltYXRpb24gb25jZSBhbmQga2VlcCBwb3NpdGlvblxuICBpZiAodHlwZSA9PT0gJ3Njcm9sbCcgJiYgb25jZSAmJiBwZXJjZW50YWdlID4gMTAwICYmIEtleWZyYW1lQ291bnQgPT09IEtleWZyYW1lc1RvdGFsKSB7XG4gICAgY29uc29sZS5sb2coJ09VVCcpO1xuICAgIGVsLmNsYXNzTGlzdC5hZGQoJ3BnLXN0YXRlLXNjcm9sbGluZy1vbmNlJyk7XG5cbiAgICAvLyBpZiAocGluKSB7XG4gICAgLy8gZWwuc3R5bGUudG9wID0gc2Nyb2xsRW5kUGl4ZWwgKyAncHgnO1xuICAgIC8vIH1cblxuICAgIC8vZGVzdHJveSBhbmltYXRpb24gb24gaXRlbSBpZiBydW4gb25jZVxuICAgIHNjcm9sbEFuaW1hdGlvbnMuZm9yRWFjaCgoYW5pbWF0aW9uKSA9PiB7XG4gICAgICBpZiAoZWwgPT09IGFuaW1hdGlvbi5nZXREYXRhKCkuZWxlbSkge1xuICAgICAgICAvLyBjb25zb2xlLmxvZygn8J+nqCBkZXRyb3k6Jyk7XG4gICAgICAgIGFuaW1hdGlvbi5kZXN0cm95KCk7XG4gICAgICB9XG4gICAgfSk7XG5cbiAgICByZXR1cm47XG4gIH1cbiAgLy9wbGF5IGFuaW1hdGlvbiByZXZlcnNlIGlmIGVuZCBpcyByZWFjaGVkXG4gIGlmICh0eXBlID09PSAnaW52aWV3JyAmJiAhb25jZSkge1xuICAgIGlmICghZWwuY2xhc3NMaXN0LmNvbnRhaW5zKCdwZy1hbmltYXRpb24taXMtcnVubmluZycpKSB7XG4gICAgICBpZiAoZWwuY2xhc3NMaXN0LmNvbnRhaW5zKCdwZy1hbmltYXRpb24tcGVyc2lzdCcpKSB7XG4gICAgICAgIC8vIGVsLmNsYXNzTGlzdC5yZW1vdmUoaW52aWV3VHJpZ2dlckNsYXNzKTtcbiAgICAgICAgdm9pZCBlbC5vZmZzZXRXaWR0aDtcbiAgICAgICAgZWwuY2xhc3NMaXN0LmFkZChpbnZpZXdUcmlnZ2VyQ2xhc3MpO1xuICAgICAgICBlbC5jbGFzc0xpc3QuYWRkKCdwZy1hbmltYXRpb24tJyArIGFuaW1hdGlvbk5hbWUpO1xuICAgICAgICBlbC5jbGFzc0xpc3QucmVtb3ZlKCdwZy1hbmltYXRpb24tcGVyc2lzdCcpO1xuICAgICAgICBwbGF5QW5pbWF0aW9uKGVsLCBhbmltYXRpb25OYW1lLCB0cnVlKTtcbiAgICAgIH1cbiAgICB9XG5cblxuICAgIC8vIGlmICghZWwuY2xhc3NMaXN0LmNvbnRhaW5zKCdwZy1hbmltYXRpb24taXMtcnVubmluZycpKSB7XG4gICAgLy8gICBlbC5jbGFzc0xpc3QucmVtb3ZlKGludmlld1RyaWdnZXJDbGFzcyk7XG4gICAgLy8gfVxuICB9XG5cbn1cblxuLy9mdW5jdGlvbiB0byBzZWFtbGVzc2x5IHBsYXkgYW5kIHJldmVyc2UgYW5pbWF0aW9uc1xuZnVuY3Rpb24gcGxheUFuaW1hdGlvbihlbCwgYW5pbWF0aW9uTmFtZSwgcmV2ZXJzZSA9IGZhbHNlKSB7XG5cbiAgbGV0IGFuaW1hdGlvbmVuID0gZWwuZ2V0QW5pbWF0aW9ucygpO1xuXG4gIGFuaW1hdGlvbmVuLmZvckVhY2goKGFuaSkgPT4ge1xuXG4gICAgaWYgKCFhbmkpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICAvLyBjb25zb2xlLmxvZyhhbmkuYW5pbWF0aW9uTmFtZSArICcgPT09ICcgKyBhbmltYXRpb25OYW1lKTtcblxuICAgIGlmIChhbmkuYW5pbWF0aW9uTmFtZSA9PSBhbmltYXRpb25OYW1lKSB7XG5cbiAgICAgIGlmIChyZXZlcnNlKSB7XG4gICAgICAgIGFuaS5wbGF5YmFja1JhdGUgPSAtMTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIGFuaS5wbGF5YmFja1JhdGUgPSAxO1xuICAgICAgfVxuXG4gICAgICBhbmkucGxheSgpO1xuICAgIH1cblxuICB9KTtcbn1cblxuLy8gRmluZGluZyBjbG9zZXN0IERPTSBlbGVtZW50cyBpbiBhbnkgZGlyZWN0aW9uXG5mdW5jdGlvbiBnZXRDbG9zZXN0KGVsLCBzZWxlY3Rvcikge1xuICBsZXQgY3VycmVudEVsZW1lbnQgPSBlbDtcbiAgbGV0IHJldHVybkVsZW1lbnQ7XG5cbiAgd2hpbGUgKGN1cnJlbnRFbGVtZW50LnBhcmVudE5vZGUgJiYgIXJldHVybkVsZW1lbnQpIHtcbiAgICBjdXJyZW50RWxlbWVudCA9IGN1cnJlbnRFbGVtZW50LnBhcmVudE5vZGU7XG4gICAgcmV0dXJuRWxlbWVudCA9IGN1cnJlbnRFbGVtZW50LnF1ZXJ5U2VsZWN0b3Ioc2VsZWN0b3IpIHx8IGN1cnJlbnRFbGVtZW50LmNsb3Nlc3Qoc2VsZWN0b3IpO1xuICB9XG5cbiAgcmV0dXJuIHJldHVybkVsZW1lbnQ7XG59XG5cbi8vIGluaXQgd2hlbiBkb2N1bWVudCBpcyBmdWxseSBsb2FkZWRcbndpbmRvdy5hZGRFdmVudExpc3RlbmVyKCdsb2FkJywgZnVuY3Rpb24gKCkge1xuICAvLyBzZXRUaW1lb3V0KGluaXQsIDMwMCk7XG4gIGluaXQoKTtcbn0pOyJdLCJzb3VyY2VSb290IjoiIn0=