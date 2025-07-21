import { createApp } from 'vue'
import App from './App.vue'
import {ExternalRegistry} from "@/registry/external";

if (!window.Sholokhov) {
    window.Sholokhov = {};
}

if (!window.Sholokhov.Exchange) {
    window.Sholokhov.Exchange = {};
}

if (!window.Sholokhov.Exchange.Detail) {
    window.Sholokhov.Exchange.Detail = new class {
        _app;
        _externalRegistry;

        mounted(node, options) {
            console.log(BX.Loc.getMessage('LANGUAGE_ID'));
            this._app = createApp(App, options);
            this._app.mount(node);
        }

        unmount() {
            this._app?.unmount();
        }

        getExternalRegistry() {
            return this._externalRegistry ??= new ExternalRegistry;
        }
    }
}