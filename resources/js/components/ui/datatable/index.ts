export { default as DataTable } from './DataTable.vue';
import { type Resource } from '@/types';
import { Filter } from '../datafilter';
import { FieldDecoration } from '../form';

export interface DataTableCaption
{
    title?: string|null,
    description?: string|null,
}

export enum ColumnType
{
    Numeric,
    Default,
    Date,
    DateTime,
}

export interface ColumnDefinition
{
    name: string,
    label?: string|null,
    type?: ColumnType,
    sortable?: Boolean,
    sorted?: string,
    prefix?: string,
    decimals?: number|null,
    decoration?: FieldDecoration,
}

export interface DataTableProps<T>
{
    caption?: DataTableCaption,
    data: Resource<T>|undefined,
    columns: Array<ColumnDefinition>,
    canCreate?: boolean,
    createRoute?: string,
    filters?: Array<Filter>,
}

export interface DataTableSlotProps<T>
{
    data: T,
    column: string
}