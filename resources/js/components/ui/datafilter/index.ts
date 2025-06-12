export { default as DataFilter } from './DataFilter.vue';
import { type Resource } from '@/types';
import { FieldDefinition } from '../form';

export interface DataFilterEmits
{
    filter: [data: any],
}

export interface DataFilterProps<T>
{
    filters: Array<Filter>
    data: Resource<T>,
}

export interface Filter extends FieldDefinition
{
    default?: any,
}