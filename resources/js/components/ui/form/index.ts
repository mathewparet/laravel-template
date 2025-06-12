import { InertiaForm } from "@inertiajs/vue3"
import { SelectInputDefinition } from "../selectinput";
export { default as AppForm } from './Form.vue';
import { type HTMLAttributes } from 'vue';
import { TextAreaInputDefinition } from "../textarea";
import { InputDefinition } from "../input";
import { AutoCompleteDefinition } from "../autocomplete";

export interface FormProps
{
    formDefinition: Array<FieldDefinition>,
    formFields: FormField,
    title: string,
    description: string,
    cols?: number|undefined,
    readonly?: boolean|undefined
    canEdit?: boolean|false
    canDelete?: boolean|false
    editRoute?: string
    form?: object
    deleteRoute?: string
}

export interface FormField
{
    [key: string]: any
}

export interface BaseFieldProps
{
    defaultValue?: string | number;
    modelValue?: string | number;
    class?: HTMLAttributes['class'];
    readonly?: boolean
}

export interface FormDefinitions
{
    [key: string]: FieldDefinition,
}

export interface FieldDefinition extends DefaultFieldDefinition
{
    selectOptions?: SelectInputDefinition
    textAreaOptions?: TextAreaInputDefinition
    inputOptions?: InputDefinition
    autoCompleteOptions? :AutoCompleteDefinition
    readonly?: boolean,
    help?: string,
}

export interface DefaultFieldDefinition
{
    name: string,
    label?: string,
    value?: any,
    placeholder?: string,
    hasPlaceholder?: boolean,
    decoration?: FieldDecoration,
    colSpan?: number|undefined,
    required?: boolean,
    when?: FieldDisplayCondition,
    type: FieldType,
}

export interface FieldDisplayCondition
{
    field: string,
    value: any|Array<any>,
}

export enum FieldDecoration
{
    Uppercase,
    Lowercase,
    Capitalize,
    Normal,
}

export interface Choice
{
    label?: string,
    value: string|string|any,
    image?: string,
}

export enum FieldType
{
    Text,
    TextArea,
    Integer,
    Float,
    Checkbox,
    File,
    Select,
    Date,
    DateTime,
    Password,
    Autocomplete,
    Radio,
    ReccuranceInput,
}

export interface FormEmitions<T>
{
    save: [form: InertiaForm<FormDataType>],
    change: [field: FieldChangedEventAttributes<T>],
    updating: [field: FieldChangedEventAttributes<T>],
}

export interface FieldChangedEventAttributes<T>
{
    name: string,
    value: any,
    form: InertiaForm<T>,
}

export type FormDataType = any;