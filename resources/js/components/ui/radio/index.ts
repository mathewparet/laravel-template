export { default as Radio } from './Radio.vue';

import type { BaseFieldProps, Choice, FieldType } from '../form/index';

export type RadioProps = BaseFieldProps & RadioInputDefinition

export interface RadioInputDefinition
{
    options: Array<Choice>
    optional?: boolean
    disabled?: boolean
}