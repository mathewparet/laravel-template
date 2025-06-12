export { default as AutoComplete } from './AutoComplete.vue';

import type { BaseFieldProps, Choice, FieldType } from '../form/index';

export type AutocompleteProps = BaseFieldProps & AutoCompleteDefinition & {
    form: any,
}

export interface AutoCompleteDefinition
{
    valueField: string,
    autoFocus?: boolean,
    format?: string|null,
    search: string,
    placeholder?: string|null,
    required?: boolean|false
    dataKey: string,
    disabled?: boolean
}