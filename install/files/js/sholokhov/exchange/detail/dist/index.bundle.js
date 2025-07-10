/* eslint-disable */
this.BX = this.BX || {};
this.BX.Sholokhov = this.BX.Sholokhov || {};
this.BX.Sholokhov.Exchange = this.BX.Sholokhov.Exchange || {};
(function (exports,sholokhov_exchange_ui) {
    'use strict';

    var Config = {
      fields: []
    };

    function normalizeTypeResponse(response) {
      if (!Array.isArray(response.data)) {
        return [];
      }
      response.data = response.data.map(function (field) {
        return {
          value: field.CODE,
          name: field.NAME
        };
      });
      return response;
    }

    /**
     * @since 1.2.0
     * @version 1.2.0
     */
    var Target = /*#__PURE__*/function () {
      /**
       * Контейнер UI
       *
       * @private
       *
       * @since 1.2.0
       * @version 1.2.0
       */

      /**
       * Контейнер хранения пользовательских полей
       *
       * @private
       *
       * @since 1.2.0
       * @version 1.2.0
       */

      /**
       * @param {Element|string} node Контейнер в который будет производиться отрисовка
       * @param {object} options Конфигурация отрисовки
       */
      function Target(node) {
        var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
        babelHelpers.classCallCheck(this, Target);
        babelHelpers.defineProperty(this, "_node", null);
        babelHelpers.defineProperty(this, "_customFieldNode", null);
        if (typeof node === 'string') {
          this._node = document.querySelector(node);
        } else if (node) {
          this._node = node;
        }
        if (!this._node) {
          throw 'Invalid target settings node';
        }
        this._options = options;
      }

      /**
       * Отрисовка контейнера UI настроек
       *
       * @return void
       *
       * @since 1.2.0
       * @version 1.2.0
       */
      babelHelpers.createClass(Target, [{
        key: "view",
        value: function view() {
          this._node.innerHTML = '';
          this._appendType();
          this._appendFields(this._node, Config.fields);
          this._appendCustomFields();
        }
        /**
         * Загрузка пользовательских полей
         *
         * @param target
         * @private
         *
         * @since 1.2.0
         * @version 1.2.0
         */
      }, {
        key: "_loadFields",
        value: function _loadFields(target) {
          var _this = this;
          BX.ajax.runAction('sholokhov:exchange.EntityController.getFields', {
            data: {
              code: target
            }
          }).then(function (response) {
            _this._customFieldNode.innerHTML = '';
            if (Array.isArray(response.data)) {
              _this._appendFields(_this._customFieldNode, response.data);
            }
          })["catch"](function (response) {
            return console.error(response);
          });
        }
        /**
         * Добавление списка доступных обменов
         *
         * @private
         * @return void
         *
         * @since 1.2.0
         * @version 1.2.0
         */
      }, {
        key: "_appendType",
        value: function _appendType() {
          var _this2 = this;
          var view;
          var options = {
            title: 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_TARGET_TITLE_FIELD_TYPE',
            attributes: {
              name: 'target[type]'
            },
            events: {
              onchange: function onchange(event) {
                return _this2._loadFields(event.target.value);
              }
            }
          };
          if (this._options.id) {
            view = sholokhov_exchange_ui.RenderType.Input;
            options.attributes.type = 'hidden';
          } else {
            view = sholokhov_exchange_ui.RenderType.Select;
            options.api = {
              action: 'sholokhov:exchange.EntityController.getByType',
              data: {
                code: 'target'
              },
              callback: normalizeTypeResponse
            };
          }
          this._node.append(sholokhov_exchange_ui.Factory.create(view, options).getContainer());
        }
        /**
         * Добавление контейнера с пользовательскими полями
         *
         * @private
         *
         * @since 1.2.0
         * @version 1.2.0
         */
      }, {
        key: "_appendCustomFields",
        value: function _appendCustomFields() {
          this._customFieldNode = document.createElement('div');
          this._node.append(this._customFieldNode);
        }
        /**
         * Отрисовка полей в контейнер
         *
         * @param node
         * @param iterator
         * @private
         *
         * @since 1.2.0
         * @version 1.2.0
         */
      }, {
        key: "_appendFields",
        value: function _appendFields(node, iterator) {
          iterator.forEach(function (field) {
            var element = sholokhov_exchange_ui.Factory.create(field.view, field.options);
            if (element) {
              node.append(element.getContainer());
            }
          });
        }
      }]);
      return Target;
    }();

    var Config$1 = {
      fields: [{
        view: sholokhov_exchange_ui.RenderType.Checkbox,
        options: {
          title: 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_GENERAL_TITLE_FIELD_ACTIVE',
          attributes: {
            name: 'target[active]'
          }
        }
      }, {
        view: 'hash',
        options: {
          title: 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_GENERAL_TITLE_FIELD_HASH',
          attributes: {
            name: 'target[hash]'
          }
        }
      }]
    };

    var General = /*#__PURE__*/function () {
      /**
       * Контейнер UI
       *
       * @private
       *
       * @since 1.2.0
       * @version 1.2.0
       */

      /**
       * @param node
       * @param options
       *
       * @since 1.2.0
       * @version 1.2.0
       */
      function General(node) {
        var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
        babelHelpers.classCallCheck(this, General);
        babelHelpers.defineProperty(this, "_node", null);
        if (typeof node === 'string') {
          this._node = document.querySelector(node);
        } else if (node) {
          this._node = node;
        }
        if (!this._node) {
          throw 'Invalid target settings node';
        }
        this._options = options;
      }

      /**
       * @since 1.2.0
       * @version 1.2.0
       */
      babelHelpers.createClass(General, [{
        key: "view",
        value: function view() {
          this._node.innerHTML = '';
          this._appendFields(this._node, Config$1.fields);
        }
        /**
         * Отрисовка полей в контейнер
         *
         * @param node
         * @param iterator
         * @private
         *
         * @since 1.2.0
         * @version 1.2.0
         */
      }, {
        key: "_appendFields",
        value: function _appendFields(node, iterator) {
          iterator.forEach(function (field) {
            var element = sholokhov_exchange_ui.Factory.create(field.view, field.options);
            if (element) {
              node.append(element.getContainer());
            }
          });
        }
      }]);
      return General;
    }();

    var Config$2 = {
      fields: []
    };

    var Source = /*#__PURE__*/function () {
      /**
       * Контейнер UI
       *
       * @private
       *
       * @since 1.2.0
       * @version 1.2.0
       */

      /**
       * Контейнер хранения пользовательских полей
       *
       * @private
       *
       * @since 1.2.0
       * @version 1.2.0
       */

      /**
       * @param {Element|string} node Контейнер в который будет производиться отрисовка
       * @param {object} options Конфигурация отрисовки
       */
      function Source(node) {
        var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
        babelHelpers.classCallCheck(this, Source);
        babelHelpers.defineProperty(this, "_node", null);
        babelHelpers.defineProperty(this, "_customFieldNode", null);
        if (typeof node === 'string') {
          this._node = document.querySelector(node);
        } else if (node) {
          this._node = node;
        }
        if (!this._node) {
          throw 'Invalid target settings node';
        }
        this._options = options;
      }

      /**
       * Отрисовка контейнера UI настроек
       *
       * @return void
       *
       * @since 1.2.0
       * @version 1.2.0
       */
      babelHelpers.createClass(Source, [{
        key: "view",
        value: function view() {
          this._node.innerHTML = '';
          this._appendTypeField();
          this._appendFields(this._node, Config$2.fields);
          this._appendCustomFields();
        }
        /**
         * Добавление контейнера с пользовательскими полями
         *
         * @private
         *
         * @since 1.2.0
         * @version 1.2.0
         */
      }, {
        key: "_appendCustomFields",
        value: function _appendCustomFields() {
          this._customFieldNode = document.createElement('div');
          this._node.append(this._customFieldNode);
        }
        /**
         * Добавление списка доступных источников
         *
         * @private
         * @return void
         *
         * @since 1.2.0
         * @version 1.2.0
         */
      }, {
        key: "_appendTypeField",
        value: function _appendTypeField() {
          var _this = this;
          var options = {
            title: 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_SOURCE_TITLE_FIELD_TYPE',
            attributes: {
              name: 'source[type]'
            },
            events: {
              onchange: function onchange(event) {
                return _this._loadFields(event.target.value);
              }
            },
            api: {
              action: 'sholokhov:exchange.EntityController.getByType',
              data: {
                code: 'source'
              },
              callback: normalizeTypeResponse
            }
          };
          this._node.append(sholokhov_exchange_ui.Factory.create(sholokhov_exchange_ui.RenderType.Select, options).getContainer());
        }
        /**
         * Отрисовка полей в контейнер
         *
         * @param node
         * @param iterator
         * @private
         *
         * @since 1.2.0
         * @version 1.2.0
         */
      }, {
        key: "_appendFields",
        value: function _appendFields(node, iterator) {
          iterator.forEach(function (field) {
            var element = sholokhov_exchange_ui.Factory.create(field.view, field.options);
            if (element) {
              node.append(element.getContainer());
            }
          });
        }
        /**
         * Загрузка пользовательских полей
         *
         * @param source {string}
         * @private
         *
         * @since 1.2.0
         * @version 1.2.0
         */
      }, {
        key: "_loadFields",
        value: function _loadFields(source) {
          var _this2 = this;
          BX.ajax.runAction('sholokhov:exchange.EntityController.getFields', {
            data: {
              code: source
            }
          }).then(function (response) {
            _this2._customFieldNode.innerHTML = '';
            if (Array.isArray(response.data)) {
              _this2._appendFields(_this2._customFieldNode, response.data);
            }
          })["catch"](function (response) {
            return console.error(response);
          });
        }
      }]);
      return Source;
    }();

    function _regeneratorRuntime() { /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return exports; }; var exports = {}, Op = Object.prototype, hasOwn = Op.hasOwnProperty, defineProperty = Object.defineProperty || function (obj, key, desc) { obj[key] = desc.value; }, $Symbol = "function" == typeof Symbol ? Symbol : {}, iteratorSymbol = $Symbol.iterator || "@@iterator", asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator", toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag"; function define(obj, key, value) { return Object.defineProperty(obj, key, { value: value, enumerable: !0, configurable: !0, writable: !0 }), obj[key]; } try { define({}, ""); } catch (err) { define = function define(obj, key, value) { return obj[key] = value; }; } function wrap(innerFn, outerFn, self, tryLocsList) { var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator, generator = Object.create(protoGenerator.prototype), context = new Context(tryLocsList || []); return defineProperty(generator, "_invoke", { value: makeInvokeMethod(innerFn, self, context) }), generator; } function tryCatch(fn, obj, arg) { try { return { type: "normal", arg: fn.call(obj, arg) }; } catch (err) { return { type: "throw", arg: err }; } } exports.wrap = wrap; var ContinueSentinel = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var IteratorPrototype = {}; define(IteratorPrototype, iteratorSymbol, function () { return this; }); var getProto = Object.getPrototypeOf, NativeIteratorPrototype = getProto && getProto(getProto(values([]))); NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol) && (IteratorPrototype = NativeIteratorPrototype); var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(IteratorPrototype); function defineIteratorMethods(prototype) { ["next", "throw", "return"].forEach(function (method) { define(prototype, method, function (arg) { return this._invoke(method, arg); }); }); } function AsyncIterator(generator, PromiseImpl) { function invoke(method, arg, resolve, reject) { var record = tryCatch(generator[method], generator, arg); if ("throw" !== record.type) { var result = record.arg, value = result.value; return value && "object" == babelHelpers["typeof"](value) && hasOwn.call(value, "__await") ? PromiseImpl.resolve(value.__await).then(function (value) { invoke("next", value, resolve, reject); }, function (err) { invoke("throw", err, resolve, reject); }) : PromiseImpl.resolve(value).then(function (unwrapped) { result.value = unwrapped, resolve(result); }, function (error) { return invoke("throw", error, resolve, reject); }); } reject(record.arg); } var previousPromise; defineProperty(this, "_invoke", { value: function value(method, arg) { function callInvokeWithMethodAndArg() { return new PromiseImpl(function (resolve, reject) { invoke(method, arg, resolve, reject); }); } return previousPromise = previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); } }); } function makeInvokeMethod(innerFn, self, context) { var state = "suspendedStart"; return function (method, arg) { if ("executing" === state) throw new Error("Generator is already running"); if ("completed" === state) { if ("throw" === method) throw arg; return doneResult(); } for (context.method = method, context.arg = arg;;) { var delegate = context.delegate; if (delegate) { var delegateResult = maybeInvokeDelegate(delegate, context); if (delegateResult) { if (delegateResult === ContinueSentinel) continue; return delegateResult; } } if ("next" === context.method) context.sent = context._sent = context.arg;else if ("throw" === context.method) { if ("suspendedStart" === state) throw state = "completed", context.arg; context.dispatchException(context.arg); } else "return" === context.method && context.abrupt("return", context.arg); state = "executing"; var record = tryCatch(innerFn, self, context); if ("normal" === record.type) { if (state = context.done ? "completed" : "suspendedYield", record.arg === ContinueSentinel) continue; return { value: record.arg, done: context.done }; } "throw" === record.type && (state = "completed", context.method = "throw", context.arg = record.arg); } }; } function maybeInvokeDelegate(delegate, context) { var methodName = context.method, method = delegate.iterator[methodName]; if (undefined === method) return context.delegate = null, "throw" === methodName && delegate.iterator["return"] && (context.method = "return", context.arg = undefined, maybeInvokeDelegate(delegate, context), "throw" === context.method) || "return" !== methodName && (context.method = "throw", context.arg = new TypeError("The iterator does not provide a '" + methodName + "' method")), ContinueSentinel; var record = tryCatch(method, delegate.iterator, context.arg); if ("throw" === record.type) return context.method = "throw", context.arg = record.arg, context.delegate = null, ContinueSentinel; var info = record.arg; return info ? info.done ? (context[delegate.resultName] = info.value, context.next = delegate.nextLoc, "return" !== context.method && (context.method = "next", context.arg = undefined), context.delegate = null, ContinueSentinel) : info : (context.method = "throw", context.arg = new TypeError("iterator result is not an object"), context.delegate = null, ContinueSentinel); } function pushTryEntry(locs) { var entry = { tryLoc: locs[0] }; 1 in locs && (entry.catchLoc = locs[1]), 2 in locs && (entry.finallyLoc = locs[2], entry.afterLoc = locs[3]), this.tryEntries.push(entry); } function resetTryEntry(entry) { var record = entry.completion || {}; record.type = "normal", delete record.arg, entry.completion = record; } function Context(tryLocsList) { this.tryEntries = [{ tryLoc: "root" }], tryLocsList.forEach(pushTryEntry, this), this.reset(!0); } function values(iterable) { if (iterable) { var iteratorMethod = iterable[iteratorSymbol]; if (iteratorMethod) return iteratorMethod.call(iterable); if ("function" == typeof iterable.next) return iterable; if (!isNaN(iterable.length)) { var i = -1, next = function next() { for (; ++i < iterable.length;) if (hasOwn.call(iterable, i)) return next.value = iterable[i], next.done = !1, next; return next.value = undefined, next.done = !0, next; }; return next.next = next; } } return { next: doneResult }; } function doneResult() { return { value: undefined, done: !0 }; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, defineProperty(Gp, "constructor", { value: GeneratorFunctionPrototype, configurable: !0 }), defineProperty(GeneratorFunctionPrototype, "constructor", { value: GeneratorFunction, configurable: !0 }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction"), exports.isGeneratorFunction = function (genFun) { var ctor = "function" == typeof genFun && genFun.constructor; return !!ctor && (ctor === GeneratorFunction || "GeneratorFunction" === (ctor.displayName || ctor.name)); }, exports.mark = function (genFun) { return Object.setPrototypeOf ? Object.setPrototypeOf(genFun, GeneratorFunctionPrototype) : (genFun.__proto__ = GeneratorFunctionPrototype, define(genFun, toStringTagSymbol, "GeneratorFunction")), genFun.prototype = Object.create(Gp), genFun; }, exports.awrap = function (arg) { return { __await: arg }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, asyncIteratorSymbol, function () { return this; }), exports.AsyncIterator = AsyncIterator, exports.async = function (innerFn, outerFn, self, tryLocsList, PromiseImpl) { void 0 === PromiseImpl && (PromiseImpl = Promise); var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl); return exports.isGeneratorFunction(outerFn) ? iter : iter.next().then(function (result) { return result.done ? result.value : iter.next(); }); }, defineIteratorMethods(Gp), define(Gp, toStringTagSymbol, "Generator"), define(Gp, iteratorSymbol, function () { return this; }), define(Gp, "toString", function () { return "[object Generator]"; }), exports.keys = function (val) { var object = Object(val), keys = []; for (var key in object) keys.push(key); return keys.reverse(), function next() { for (; keys.length;) { var key = keys.pop(); if (key in object) return next.value = key, next.done = !1, next; } return next.done = !0, next; }; }, exports.values = values, Context.prototype = { constructor: Context, reset: function reset(skipTempReset) { if (this.prev = 0, this.next = 0, this.sent = this._sent = undefined, this.done = !1, this.delegate = null, this.method = "next", this.arg = undefined, this.tryEntries.forEach(resetTryEntry), !skipTempReset) for (var name in this) "t" === name.charAt(0) && hasOwn.call(this, name) && !isNaN(+name.slice(1)) && (this[name] = undefined); }, stop: function stop() { this.done = !0; var rootRecord = this.tryEntries[0].completion; if ("throw" === rootRecord.type) throw rootRecord.arg; return this.rval; }, dispatchException: function dispatchException(exception) { if (this.done) throw exception; var context = this; function handle(loc, caught) { return record.type = "throw", record.arg = exception, context.next = loc, caught && (context.method = "next", context.arg = undefined), !!caught; } for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i], record = entry.completion; if ("root" === entry.tryLoc) return handle("end"); if (entry.tryLoc <= this.prev) { var hasCatch = hasOwn.call(entry, "catchLoc"), hasFinally = hasOwn.call(entry, "finallyLoc"); if (hasCatch && hasFinally) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } else if (hasCatch) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); } else { if (!hasFinally) throw new Error("try statement without catch or finally"); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } } } }, abrupt: function abrupt(type, arg) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) { var finallyEntry = entry; break; } } finallyEntry && ("break" === type || "continue" === type) && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc && (finallyEntry = null); var record = finallyEntry ? finallyEntry.completion : {}; return record.type = type, record.arg = arg, finallyEntry ? (this.method = "next", this.next = finallyEntry.finallyLoc, ContinueSentinel) : this.complete(record); }, complete: function complete(record, afterLoc) { if ("throw" === record.type) throw record.arg; return "break" === record.type || "continue" === record.type ? this.next = record.arg : "return" === record.type ? (this.rval = this.arg = record.arg, this.method = "return", this.next = "end") : "normal" === record.type && afterLoc && (this.next = afterLoc), ContinueSentinel; }, finish: function finish(finallyLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.finallyLoc === finallyLoc) return this.complete(entry.completion, entry.afterLoc), resetTryEntry(entry), ContinueSentinel; } }, "catch": function _catch(tryLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc === tryLoc) { var record = entry.completion; if ("throw" === record.type) { var thrown = record.arg; resetTryEntry(entry); } return thrown; } } throw new Error("illegal catch attempt"); }, delegateYield: function delegateYield(iterable, resultName, nextLoc) { return this.delegate = { iterator: values(iterable), resultName: resultName, nextLoc: nextLoc }, "next" === this.method && (this.arg = undefined), ContinueSentinel; } }, exports; }
    function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
    function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { babelHelpers.defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
    var Http = /*#__PURE__*/function () {
      function Http() {
        babelHelpers.classCallCheck(this, Http);
      }
      babelHelpers.createClass(Http, null, [{
        key: "send",
        /**
         * @param action {string}
         * @param parameters {HttpParameters}
         *
         * @since 1.2.0
         * @version 1.2.0
         */
        value: function () {
          var _send = babelHelpers.asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee(action) {
            var parameters,
              _args = arguments;
            return _regeneratorRuntime().wrap(function _callee$(_context) {
              while (1) switch (_context.prev = _context.next) {
                case 0:
                  parameters = _args.length > 1 && _args[1] !== undefined ? _args[1] : {};
                  return _context.abrupt("return", new Promise(function (resolve, reject) {
                    BX.ajax.runComponentAction('sholokhov:exchange.settings.detail', action, _objectSpread(_objectSpread({
                      method: 'POST'
                    }, parameters), {}, {
                      mode: 'class',
                      signedParameters: Http.signed
                    })).then(function (response) {
                      return resolve(response);
                    })["catch"](function (response) {
                      return reject(response);
                    });
                  }));
                case 2:
                case "end":
                  return _context.stop();
              }
            }, _callee);
          }));
          function send(_x) {
            return _send.apply(this, arguments);
          }
          return send;
        }()
      }]);
      return Http;
    }();
    babelHelpers.defineProperty(Http, "signed", '');

    /**
     * @since 1.2.0
     * @version 1.2.0
     */
    var Detail = /*#__PURE__*/function () {
      /**
       * @since 1.2.0
       * @version 1.2.0
       */

      /**
       * @since 1.2.0
       * @version 1.2.0
       */

      /**
       * @param data
       * @param options
       *
       * @since 1.2.0
       * @version 1.2.0
       */
      function Detail() {
        var _this$_options;
        var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
        var options = arguments.length > 1 ? arguments[1] : undefined;
        babelHelpers.classCallCheck(this, Detail);
        this._options = options;
        this._data = data;
        Http.signed = ((_this$_options = this._options) === null || _this$_options === void 0 ? void 0 : _this$_options.signed) || '';
      }

      /**
       * @since 1.2.0
       * @version 1.2.0
       */
      babelHelpers.createClass(Detail, [{
        key: "view",
        value: function view() {
          var _this$_options$contai,
            _this$_options$contai2,
            _this$_options$contai3,
            _this = this;
          if ((_this$_options$contai = this._options.container) !== null && _this$_options$contai !== void 0 && _this$_options$contai.general) {
            new General(this._options.container.general, this._data).view();
          }
          if ((_this$_options$contai2 = this._options.container) !== null && _this$_options$contai2 !== void 0 && _this$_options$contai2.target) {
            new Target(this._options.container.target, this._data).view();
          }
          if ((_this$_options$contai3 = this._options.container) !== null && _this$_options$contai3 !== void 0 && _this$_options$contai3.source) {
            new Source(this._options.container.source, this._data).view();
          }
          var form = document.getElementById(this._options.container.form);
          if (!form) {
            throw 'Form not found';
          }
          form.onsubmit = function (event) {
            return _this._saveAction(event);
          };
        }
        /**
         * @param event
         *
         * @since 1.2.0
         * @version 1.2.0
         */
      }, {
        key: "_saveAction",
        value: function _saveAction(event) {
          event.preventDefault();
          event.stopImmediatePropagation();
          Http.send('save', {
            data: new FormData(event.target)
          }).then(function (response) {
            console.log(response);
          })["catch"](function (response) {
            console.log(response);
          });
        }
      }]);
      return Detail;
    }();

    var Hash = /*#__PURE__*/function () {
      function Hash(options) {
        babelHelpers.classCallCheck(this, Hash);
        this._options = options;
      }
      babelHelpers.createClass(Hash, [{
        key: "getContainer",
        value: function getContainer() {
          var _this$_container;
          return (_this$_container = this._container) !== null && _this$_container !== void 0 ? _this$_container : this._container = this._create();
        }
      }, {
        key: "_create",
        value: function _create() {
          var input = sholokhov_exchange_ui.Factory.create(sholokhov_exchange_ui.RenderType.Input, this._options);
          var label = document.createElement('span');
          label.innerText = 'Сгенерировать';
          label.className = "hash-text-generator";
          label.onclick = function () {
            // Отправляем ajax запрос
            BX.ajax.runAction('sholokhov:exchange.SecureController.generateHash').then(function (response) {
              return input.value = response.data;
            })["catch"](function () {
              return alert('Ошибка генерации идентификатора');
            });
          };
          input.valueCell.style.display = 'flex';
          input.valueCell.append(label);
          return input.getContainer();
        }
      }]);
      return Hash;
    }();

    sholokhov_exchange_ui.Registry.set('hash', Hash);

    exports.Target = Target;
    exports.Detail = Detail;

}((this.BX.Sholokhov.Exchange.Detail = this.BX.Sholokhov.Exchange.Detail || {}),BX.Sholokhov.Exchange.UI));
//# sourceMappingURL=index.bundle.js.map
