/* eslint-disable */
this.BX = this.BX || {};
this.BX.Sholokhov = this.BX.Sholokhov || {};
this.BX.Sholokhov.Exchange = this.BX.Sholokhov.Exchange || {};
(function (exports,sholokhov_exchange_ui) {
    'use strict';

    var Config = {
      fields: [{
        view: sholokhov_exchange_ui.RenderType.Input,
        options: {
          title: 'ID импорта:',
          attributes: {
            name: 'target[hash]'
          }
        }
      }, {
        view: sholokhov_exchange_ui.RenderType.Input,
        options: {
          title: 'Активность:',
          attributes: {
            type: 'checkbox',
            name: 'target[active]'
          }
        }
      }, {
        view: sholokhov_exchange_ui.RenderType.Input,
        options: {
          title: 'Деактивировать элементы, которые не пришли в импорте:',
          attributes: {
            type: 'checkbox',
            name: 'target[deactivate]'
          }
        }
      }]
    };

    function _classPrivateMethodInitSpec(obj, privateSet) { _checkPrivateRedeclaration(obj, privateSet); privateSet.add(obj); }
    function _classPrivateFieldInitSpec(obj, privateMap, value) { _checkPrivateRedeclaration(obj, privateMap); privateMap.set(obj, value); }
    function _checkPrivateRedeclaration(obj, privateCollection) { if (privateCollection.has(obj)) { throw new TypeError("Cannot initialize the same private elements twice on an object"); } }
    function _classPrivateMethodGet(receiver, privateSet, fn) { if (!privateSet.has(receiver)) { throw new TypeError("attempted to get private field on non-instance"); } return fn; }

    /**
     * @since 1.2.0
     * @version 1.2.0
     */
    var _node = /*#__PURE__*/new WeakMap();
    var _customFieldNode = /*#__PURE__*/new WeakMap();
    var _options = /*#__PURE__*/new WeakMap();
    var _loadFields = /*#__PURE__*/new WeakSet();
    var _appendType = /*#__PURE__*/new WeakSet();
    var _appendCustomFields = /*#__PURE__*/new WeakSet();
    var _appendFields = /*#__PURE__*/new WeakSet();
    var TargetSettings = /*#__PURE__*/function () {
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
       * Конфигурация отрисовки
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
      function TargetSettings(_node2) {
        var _options2 = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
        babelHelpers.classCallCheck(this, TargetSettings);
        _classPrivateMethodInitSpec(this, _appendFields);
        _classPrivateMethodInitSpec(this, _appendCustomFields);
        _classPrivateMethodInitSpec(this, _appendType);
        _classPrivateMethodInitSpec(this, _loadFields);
        _classPrivateFieldInitSpec(this, _node, {
          writable: true,
          value: null
        });
        _classPrivateFieldInitSpec(this, _customFieldNode, {
          writable: true,
          value: null
        });
        _classPrivateFieldInitSpec(this, _options, {
          writable: true,
          value: void 0
        });
        if (typeof _node2 === 'string') {
          babelHelpers.classPrivateFieldSet(this, _node, document.querySelector(_node2));
        } else if (_node2) {
          babelHelpers.classPrivateFieldSet(this, _node, _node2);
        }
        if (!babelHelpers.classPrivateFieldGet(this, _node)) {
          throw 'Invalid target settings node';
        }
        babelHelpers.classPrivateFieldSet(this, _options, _options2);
      }

      /**
       * Отрисовка контейнера UI настроек
       *
       * @return void
       *
       * @since 1.2.0
       * @version 1.2.0
       */
      babelHelpers.createClass(TargetSettings, [{
        key: "view",
        value: function view() {
          babelHelpers.classPrivateFieldGet(this, _node).innerHTML = '';
          _classPrivateMethodGet(this, _appendType, _appendType2).call(this);
          _classPrivateMethodGet(this, _appendFields, _appendFields2).call(this, babelHelpers.classPrivateFieldGet(this, _node), Config.fields);
          _classPrivateMethodGet(this, _appendCustomFields, _appendCustomFields2).call(this);
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
      }]);
      return TargetSettings;
    }();
    function _loadFields2(target) {
      var _this = this;
      BX.ajax.runAction('sholokhov:exchange.EntityController.getFields', {
        data: {
          code: target
        }
      }).then(function (response) {
        babelHelpers.classPrivateFieldGet(_this, _customFieldNode).innerHTML = '';
        if (Array.isArray(response.data)) {
          _classPrivateMethodGet(_this, _appendFields, _appendFields2).call(_this, babelHelpers.classPrivateFieldGet(_this, _customFieldNode), response.data);
        }
      })["catch"](function (response) {
        return console.error(response);
      });
    }
    function _appendType2() {
      var _this2 = this;
      var view;
      var options = {
        title: 'Тип обмена:',
        attributes: {
          name: 'target[type]'
        },
        events: {
          onchange: function onchange(event) {
            return _classPrivateMethodGet(_this2, _loadFields, _loadFields2).call(_this2, event.target.value);
          }
        }
      };
      if (babelHelpers.classPrivateFieldGet(this, _options).id) {
        view = sholokhov_exchange_ui.RenderType.Input;
        options.attributes.type = 'hidden';
      } else {
        view = sholokhov_exchange_ui.RenderType.Select;
        options.api = {
          action: 'sholokhov:exchange.EntityController.getByType',
          data: {
            code: 'target'
          },
          callback: function callback(response) {
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
        };
      }
      babelHelpers.classPrivateFieldGet(this, _node).append(sholokhov_exchange_ui.Factory.create(view, options));
    }
    function _appendCustomFields2() {
      babelHelpers.classPrivateFieldSet(this, _customFieldNode, document.createElement('div'));
      babelHelpers.classPrivateFieldGet(this, _node).append(babelHelpers.classPrivateFieldGet(this, _customFieldNode));
    }
    function _appendFields2(node, iterator) {
      iterator.forEach(function (field) {
        var element = sholokhov_exchange_ui.Factory.create(field.view, field.options);
        if (element) {
          node.append(element);
        }
      });
    }

    exports.TargetSettings = TargetSettings;

}((this.BX.Sholokhov.Exchange.Detail = this.BX.Sholokhov.Exchange.Detail || {}),BX.Sholokhov.Exchange.UI));
//# sourceMappingURL=index.bundle.js.map
