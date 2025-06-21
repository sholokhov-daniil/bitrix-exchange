import { createApp } from 'vue'
import App from '@/App.vue'


if (!window.Sholokhov) {
    window.Sholokhov = {};
}

if (!window.Sholokhov.Exchange) {
    window.Sholokhov.Exchange = {};
}

if (!window.Sholokhov.Exchange.IBlockSelector) {
    window.Sholokhov.Exchange.IBlockSelector = {
        _app: null,

        mount(node, data) {
            this.unmount();
            this._app = createApp(App, data);
            this._app.mount(node);
        },

        unmount() {
            if (this._app) {
                this._app.unmount();
            }
        }
    };
}
