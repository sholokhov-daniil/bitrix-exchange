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
          var _this$_options, _this$_options$contai, _this$_options2, _this$_options2$conta, _this$_options3, _this$_options3$conta;
          if ((_this$_options = this._options) !== null && _this$_options !== void 0 && (_this$_options$contai = _this$_options.container) !== null && _this$_options$contai !== void 0 && _this$_options$contai.general) {
            new General(this._options.container.general, this._data).view();
          }
          if ((_this$_options2 = this._options) !== null && _this$_options2 !== void 0 && (_this$_options2$conta = _this$_options2.container) !== null && _this$_options2$conta !== void 0 && _this$_options2$conta.target) {
            new Target(this._options.container.target, this._data).view();
          }
          if ((_this$_options3 = this._options) !== null && _this$_options3 !== void 0 && (_this$_options3$conta = _this$_options3.container) !== null && _this$_options3$conta !== void 0 && _this$_options3$conta.source) {
            new Source(this._options.container.source, this._data).view();
          }
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
            BX.ajax.runAction('sholokhov:exchange.SecureController.generateHash');
            input.value = "OPA";
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
