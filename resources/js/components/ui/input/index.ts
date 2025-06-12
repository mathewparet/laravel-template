export { default as Input } from './Input.vue';

import type { BaseFieldProps, Choice, FieldType } from '../form/index';

export type InputProps = BaseFieldProps & InputDefinition

export interface InputDefinition
{
    min?: number|null|string|undefined,
    max?: number|null|string|undefined,
    autoFocus?: boolean,
    prefix?: string|null,
    step?: number|string,
    type?: string|undefined|FieldType,
    placeholder?: string|null,
    required?: boolean|false
    disabled?: boolean
    accept?: string
}