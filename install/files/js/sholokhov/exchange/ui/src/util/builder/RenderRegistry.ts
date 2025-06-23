import {Registry} from "../container/index.ts";
import {Map} from '../../config/render';

export class RenderRegistry {
    static create(): Registry {
        return new Registry(Map);
    }
}