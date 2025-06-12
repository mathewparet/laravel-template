export { default as SelectInput } from './SelectInput.vue';
import type { BaseFieldProps, Choice } from '../form/index';

export type SelectInputProps = BaseFieldProps & SelectInputDefinition

export interface SelectInputDefinition
{
    options: Array<Choice>
    optional?: boolean
    disabled?: boolean
}