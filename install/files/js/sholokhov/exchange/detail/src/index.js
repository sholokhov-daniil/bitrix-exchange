import {Target} from "./components/target.ts";
import {Detail} from "./components/detail.ts";
import {Hash} from "./render/hash.ts";
import {Registry} from 'sholokhov.exchange.ui';

Registry.set('hash', Hash);

export {Target, Detail};