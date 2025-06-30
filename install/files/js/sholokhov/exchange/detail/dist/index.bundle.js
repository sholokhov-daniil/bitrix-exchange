/* eslint-disable */
this.BX = this.BX || {};
this.BX.Sholokhov = this.BX.Sholokhov || {};
this.BX.Sholokhov.Exchange = this.BX.Sholokhov.Exchange || {};
(function (exports,sholokhov_exchange_ui) {
    'use strict';

    var Config = {
      fields: []
    };

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
          title: 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_TARGET_TITLE_FIELD_ACTIVE',
          attributes: {
            name: 'target[active]'
          }
        }
      }, {
        view: sholokhov_exchange_ui.RenderType.Input,
        options: {
          title: 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_TARGET_TITLE_FIELD_HASH',
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
        var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
        var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
        babelHelpers.classCallCheck(this, Detail);
        this._options = options;
        this._data = data;
      }

      /**
       * @since 1.2.0
       * @version 1.2.0
       */
      babelHelpers.createClass(Detail, [{
        key: "view",
        value: function view() {
          var _this$_options, _this$_options$contai, _this$_options2, _this$_options2$conta;
          if ((_this$_options = this._options) !== null && _this$_options !== void 0 && (_this$_options$contai = _this$_options.container) !== null && _this$_options$contai !== void 0 && _this$_options$contai.general) {
            new General(this._options.container.general, this._data).view();
          }
          if ((_this$_options2 = this._options) !== null && _this$_options2 !== void 0 && (_this$_options2$conta = _this$_options2.container) !== null && _this$_options2$conta !== void 0 && _this$_options2$conta.target) {
            new Target(this._options.container.target, this._data).view();
          }
        }
      }]);
      return Detail;
    }();

    exports.Target = Target;
    exports.Detail = Detail;

}((this.BX.Sholokhov.Exchange.Detail = this.BX.Sholokhov.Exchange.Detail || {}),BX.Sholokhov.Exchange.UI));
//# sourceMappingURL=index.bundle.js.map
