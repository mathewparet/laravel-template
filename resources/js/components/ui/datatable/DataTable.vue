<script setup lang="ts">
    import { useId } from 'radix-vue'
    import { computed } from 'vue';
    import { DateTime } from 'luxon'
    import { defineProps, defineEmits } from 'vue';
    import { ChevronUp, ChevronDown, ArrowDownUp } from 'lucide-vue-next';
    import { router, useForm } from '@inertiajs/vue3'
    import { type ResourceMetadataLink } from '@/types';
    import { Button } from '../button/index';
    import { Link } from '@inertiajs/vue3'
    import { DataFilter } from '../datafilter';
    import { getCurrentUrl, getFormattedFieldName } from '@/composables/useUtils';
    import { ColumnDefinition, ColumnType, type DataTableProps } from '.';
    import Paginator from '@/components/Paginator.vue';
    import { FieldDecoration } from '../form';

    const props = defineProps<DataTableProps<T>>();
    const emits = defineEmits(['sortBy', 'create']);

    const hasCaption = computed<boolean>(() => (props.caption?.title?.length || 0) + (props.caption?.description?.length || 0) > 0)

    const getColumnType = <ColumnType>(key: string, value?: string) => {
        let colType  = ColumnType.Default;

        if ( key.endsWith('_at') ) {
            colType = ColumnType.DateTime;
        } 
        if(value) {
            if ( typeof value === 'number' && !isNaN(value) ) {
                colType = ColumnType.Numeric;
            } 
            else if ( typeof value === 'string' && !isNaN(value.trim()) ) {
                colType = ColumnType.Numeric;
            }
            else if ( typeof value === 'string' && !isNaN(Date.parse(value)) ) {
                colType = ColumnType.Date;
            }
        }

        return colType;
    }

    const columns = computed(() => {
        let sortField = getUrlParam('sortBy');
        let sortOrder = getUrlParam('sortOrder');

        if(props.columns) {
            return props.columns.map((column: ColumnDefinition) => {
                return <ColumnDefinition>{
                    type: column?.type ?? getColumnType<ColumnType>(column.name),
                    name: column.name,
                    label: column?.label,
                    sortable: column.sortable ?? false,
                    sorted: column.sorted ?? (sortField == column.name ? sortOrder : ''),
                    prefix: column.prefix,
                    decimals: column.decimals,
                    decoration: column?.decoration ?? FieldDecoration.Normal,
                }
            })
        }

        let cols: Array<ColumnDefinition> = [];
        
        for(let key in props.data.data[0])
        {
            if(key == 'can') continue;

            let colType = getColumnType<ColumnType>(key, props.data.data[0])

            cols.push({
                name: key,
                type: colType,
                sortable: false,
                sorted: ''
            });
        }

        return cols;
    })

    const generatePrefix = (data, prefix) => prefix?.replace(/:(\w+(?:\.\w+)*):/g, (_, key) => getValue(data, key) ?? '')

    const getUrlParam = (param: string) => {
        const urlObj = getCurrentUrl(props.data)

        if(!urlObj) return ''

        const sortByValue = urlObj.searchParams.get(param);

        return sortByValue || '';
    }

    const sortBy = (column: ColumnDefinition) => {
        if(!column.sortable) return;

        if(column.sorted == 'asc') {
            router.visit(modifyOrCreateSortParam(column.name, 'desc'));
        } else {
            router.visit(modifyOrCreateSortParam(column.name, 'asc'));
        }
    }

    const modifyOrCreateSortParam = (column: string, order: string) => {
        const urlObj = getCurrentUrl(props.data)

        if(!urlObj) return '';

        urlObj.searchParams.set('sortBy', column);

        urlObj.searchParams.set('sortOrder', order);

        return urlObj.toString();
    }

    const form = useForm({})

    const applyFilter = (filter: Array<string>) => {
        const urlObj = getCurrentUrl(props.data)

        if(!urlObj) return '';

        for(let item in filter) {
            if(urlObj.searchParams.get(item)) {
                urlObj.searchParams.delete(item)
            }
            if(filter[item]?.length) {
                urlObj.searchParams.set(item, filter[item]);
            }
        }

        form.get(urlObj.toString(), {
            preserveScroll: true,
            preserveState: true
        })
    } 

    const getValue = (obj: object, key: string) => key.split('.').reduce<any|string|number>((acc: Array<string>, part: string) => acc && acc[part], obj);

    const filterApplied = (data) => {
        applyFilter(data);
    }

</script>
<template>
    <div class="max-w-sm lg:max-w-7xl shadow-md sm:rounded-lg">
        <div class="relative max-w-sm lg:max-w-7xl shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <caption v-if="hasCaption || props.canCreate || props.filters?.length || $slots.captionTitle" class="p-5 text-lg font-semibold text-left rtl:text-right text-gray-900 bg-white dark:text-white dark:bg-neutral-100/20">
                    <div class="lg:flex justify-between items-center space-y-2 mb-2">
                        <div>
                            <slot name="caption-title">{{ props.caption?.title }}</slot>
                            <p class="mt-1 text-sm font-normal text-gray-500 dark:text-gray-400">
                                <slot name="caption-description">{{ props.caption?.description }}</slot>
                            </p>
                        </div>
                        <div v-if="props.canCreate">
                            <slot name="buttons">
                                <Link :href="props.createRoute" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-10 rounded-md px-8">Create</Link>
                            </slot>
                        </div>
                    </div>
                    <div class="flex flex-wrap justify-between space-x-2 pt-2">
                        <DataFilter  :filters="props.filters" @filter="filterApplied" :data="props.data"/>
                    </div>
                </caption>
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-neutral-100/30 dark:text-gray-400">
                    <tr>
                        <th v-for="column in columns" :key="column.name" scope="col" class="px-6 py-3" :class="{'text-right': column.type === ColumnType.Numeric}">
                            <div :class="{'flex space-x-1 cursor-pointer': column.sortable, 'float-right': column.type === ColumnType.Numeric}" @click.prevent="sortBy(column)">
                                <div>{{ getFormattedFieldName(column) }}</div>
                                <div v-if="column.sortable">
                                    <div v-if="column.sorted == 'asc'"><ChevronUp class="size-4"/></div>
                                    <div v-else-if="column.sorted == 'desc'"><ChevronDown class="size-4"/></div>
                                    <div v-else><ArrowDownUp class="size-4"/></div>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="data in props.data.data" :key="useId()" class="bg-white border-b dark:bg-neutral-100/10 dark:border-gray-700 border-gray-200">
                        <td v-for="column in columns" :key="useId()" scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white" :class="{'text-right font-mono': column.type === ColumnType.Numeric, 'uppercase': column?.decoration === FieldDecoration.Uppercase, 'capitalize': column?.decoration === FieldDecoration.Capitalize, 'lowercase': column?.decoration === FieldDecoration.Lowercase}">
                            <slot name="cell" :data="data" :column="column.name">
                                <span v-if="column.prefix">{{ generatePrefix(data, column.prefix) }}</span>
                                <template 
                                    v-if="column.type === ColumnType.Date && getValue(data, column.name)"
                                >
                                    {{ DateTime.fromISO(getValue(data, column.name)).toLocaleString(DateTime.DATE_MED) }}
                                </template>
                                <template 
                                    v-else-if="column.type === ColumnType.DateTime && getValue(data, column.name)"
                                >
                                    {{ DateTime.fromISO(getValue(data, column.name)).toLocaleString(DateTime.DATETIME_MED) }}
                                </template>
                                <template v-else-if="column.type == ColumnType.Numeric">{{ column.decimals ? getValue(data, column.name)?.toLocaleString(undefined, {minimumFractionDigits: column.decimals, maximumFractionDigits: column.decimals}) : getValue(data, column.name)?.toLocaleString() }}</template>
                                <template v-else>{{ getValue(data, column.name) }}</template>
                            </slot>
                        </td>
                    </tr>
                    <tr v-if="props.data.data.length === 0">
                        <td :colspan="columns.length" class="justify-center text-center p-2 italic">Wow, such empty!</td>
                    </tr>
                </tbody>
            </table>
            <Paginator v-if="props.data?.meta" :meta="props.data.meta" class="p-4"/>
        </div>
    </div>
</template>