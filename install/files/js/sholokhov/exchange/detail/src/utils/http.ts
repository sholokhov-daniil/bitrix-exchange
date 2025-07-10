import {HttpParameters, HttpResponse} from "../../@types/detail.d.ts";

export class Http {
    static signed = '';

    /**
     * @param action {string}
     * @param parameters {HttpParameters}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    static async send(action: string, parameters: HttpParameters = {}): Promise<HttpResponse> {
        return new Promise((resolve, reject) => {
            BX.ajax.runComponentAction(
                'sholokhov:exchange.settings.detail',
                action,
                {
                    method: 'POST',
                    ...parameters,
                    mode: 'class',
                    signedParameters: Http.signed,
                }
            )
                .then(response => resolve(response))
                .catch(response => reject(response))
        });
    }
}