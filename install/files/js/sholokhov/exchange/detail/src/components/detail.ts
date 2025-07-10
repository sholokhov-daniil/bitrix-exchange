import {Options} from "../../@types/detail.d.ts";
import {Target} from "./target.ts";
import {General} from "./general.ts";
import {Source} from "./source.ts";
import {Http} from "../utils/http.ts";

/**
 * @since 1.2.0
 * @version 1.2.0
 */
export class Detail {
    /**
     * @since 1.2.0
     * @version 1.2.0
     */
    _options: Options;

    /**
     * @since 1.2.0
     * @version 1.2.0
     */
    _data: object;

    /**
     * @param data
     * @param options
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    constructor(data: object = {}, options: Options) {
        this._options = options;
        this._data = data;

        Http.signed = this._options?.signed || '';
    }

    /**
     * @since 1.2.0
     * @version 1.2.0
     */
    view(): void {
        if (this._options.container?.general) {
            (new General(this._options.container.general, this._data)).view();
        }

        if (this._options.container?.target) {
            (new Target(this._options.container.target, this._data)).view();
        }

        if (this._options.container?.source) {
            (new Source(this._options.container.source, this._data)).view();
        }

        const form = document.getElementById(this._options.container.form);

        if (!form) {
            throw 'Form not found';
        }

        form.onsubmit = (event) => this._saveAction(event);
    }

    /**
     * @param event
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    _saveAction(event) {
        event.preventDefault();
        event.stopImmediatePropagation();

        Http.send('save', {data: new FormData(event.target)})
            .then(response => {
                console.log(response);
            })
            .catch(response => {
                console.log(response);
            })
    }
}