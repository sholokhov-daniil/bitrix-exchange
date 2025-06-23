import RenderFactory from "./util/factory/render.ts";
import {Type as RenderType} from './config/render';
import {RenderRegistry} from "./util/builder/RenderRegistry.ts";

const Factory = new RenderFactory;
const Registry = RenderRegistry.create();

export {Factory, Registry, RenderType}
