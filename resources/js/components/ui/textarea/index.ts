export { default as TextArea } from './TextArea.vue';
import type { BaseFieldProps, Choice } from '../form/index';

export type TextAreaInputProps = BaseFieldProps & TextAreaInputDefinition

export interface TextAreaInputDefinition
{
    rows?: number
    disabled?: boolean
}