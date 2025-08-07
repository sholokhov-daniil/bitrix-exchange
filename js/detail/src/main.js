import { createApp } from 'vue'
import App from './App.vue'
import {EntityRegistry} from "@/registry/ui-registry";
import {sendBefore, events as initEvents} from '@/events/init';

if (!window.Sholokhov) {
    window.Sholokhov = {};
}

if (!window.Sholokhov.Exchange) {
    window.Sholokhov.Exchange = {};
}

if (!window.Sholokhov.Exchange.Detail) {
    window.Sholokhov.Exchange.Detail = new class {
        _app;
        _entityRegistry;

        mounted(node, options) {
            sendBefore({
                id: options?.id
            });

            this._app = createApp(App, options);
            this._app.mount(node);
        }

        unmount() {
            this._app?.unmount();
        }

        get events() {
            return {
                init: initEvents
            }
        }

        get entityRegistry() {
            return this._entityRegistry ??= new EntityRegistry;
        }
    }
}