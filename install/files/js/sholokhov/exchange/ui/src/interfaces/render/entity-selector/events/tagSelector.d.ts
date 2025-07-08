import {TagItem, TagSelector} from 'ui.tag-selector';

export interface TagEvent {
    data: {
        tag: TagItem
    },
    target: TagSelector,
    type: string,
    defaultPrevented: boolean
}