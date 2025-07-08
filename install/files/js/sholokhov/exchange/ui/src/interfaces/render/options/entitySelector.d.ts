import {Options} from './options.d.ts';
import {TagSelectorOptions} from 'ui.tag-selector';

export interface EntitySelectorOptions extends Options {
    selector: TagSelectorOptions;
}