<script setup lang="ts">
    import { useId } from 'radix-vue'
    import { computed, onMounted, onUnmounted, reactive, ref } from 'vue';
    import { DateTime } from 'luxon'
    import { defineProps, defineEmits } from 'vue';
    import { ChevronUp, ChevronDown, ArrowDownUp } from 'lucide-vue-next';
    import { router } from '@inertiajs/vue3'
    import { type ResourceMetadataLink } from '@/types';
    import { Button } from '../button/index';
    import { Link } from '@inertiajs/vue3'
    import { ListFilter, X, ListRestart } from 'lucide-vue-next';
    import { Input } from '../input';
    import { SelectInput } from '@/components/ui/selectinput';
    import { onKeyStroke } from '@vueuse/core'
    import { type DataFilterEmits, DataFilterProps, Filter } from '.';
    import { useDebounceFn } from '@vueuse/core';
    import { FieldType, FieldDecoration} from '../form';
    import { Label } from '@/components/ui/label';
    import { getCurrentUrl, getFormattedFieldName, getFieldTypeName } from '@/composables/useUtils';
    import { useStorage } from '@vueuse/core'

    const props = defineProps<DataFilterProps<T>>();

    const searchInput = ref(null)

    const emit = defineEmits<DataFilterEmits>();

    let storedFilter = useStorage('filter:' + route().current(), {}, sessionStorage, {mergeDefaults: false});

    const applyFilter = () => {
        storedFilter.value = form;
        emit('filter', form)
    }

    const filterChanged = useDebounceFn(() => {
        applyFilter()
    }, 100)
    
    const form = reactive<Object>({})

    const resetFilters = () => {
        props.filters.forEach((filter: Filter) => {
            form[filter.name] = filter?.default || ''
        })
        applyFilter()
    }

    const findFilter = (filterName: string) => props.filters.filter(f => f.name == filterName);

    onMounted(() => {
        const urlObj = getCurrentUrl(props.data)

        if(!urlObj) return '';

        props.filters?.forEach((filter: Filter) => {
            form[filter.name] = Object.keys(storedFilter.value).length ? storedFilter.value[filter.name] : urlObj.searchParams.get(filter.name) || '';
            
            if(filter.type == FieldType.Select) {
                if(filter.default?.length) {
                    if(form[filter.name] == '') {
                        form[filter.name] = filter.default
                    }
                }
            }
        });

        if(storedFilter.value) {
            props.filters?.forEach((filter: Filter) => {
                if(filter?.default) {
                    if(filter?.default != storedFilter.value[filter.name]) {
                        filterChanged()
                    }
                }
                else if(storedFilter.value[filter.name] != null && storedFilter.value[filter.name] != undefined && storedFilter.value[filter.name] != '') {
                    filterChanged()
                }
            });
        }
    })

    const showFilters = ref(false)

    const numFiltersApplied = computed(() => Object.values(form).filter(data => data != '' && data != null && data!=undefined).length)

    onKeyStroke('/', (e) => {
        e.preventDefault();
        searchInput.value.focus()
    })
    
    onKeyStroke('Escape', (e) => {
        e.preventDefault();
        searchInput.value.blur()
    })

    const removeFilter = (filter: Filter) => {
        form[filter.name] = null
        applyFilter()
    }

</script>
<template>
    <div>
        <div class="flex justify-between space-x-2" v-if="props.filters?.length">
            <div class="flex flex-wrap space-x-2">
                <div class="relative">
                    <Button variant="outline" size="lg" @click.prevent="showFilters = !showFilters">
                        <ListFilter class="w-4 h-4"/>
                        <div v-if="numFiltersApplied > 0" class="absolute inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full -top-2 -end-2 dark:border-gray-900">{{ numFiltersApplied }}</div>
                    </Button>
                    <div class="z-50 overflow-visible absolute top-12 w-64 -left-2 rounded-lg bg-white dark:text-white dark:bg-neutral-900 border-2 border-gray-200 dark:border-gray-600 p-4" v-if="showFilters">
                        <div class="grid grid-cols-1 gap-2" 
                            v-for="(field, index) in props.filters" 
                            :key="field.name"
                        >
                            <span v-if="field.name != 'search'">
                                <Label :for="field.name">{{ getFormattedFieldName(field) }}</Label>
                                <Input
                                    v-if="[FieldType.File, FieldType.Text, FieldType.Password, FieldType.Integer, FieldType.Float, FieldType.Date, FieldType.DateTime].includes(field.type)"
                                    :id="field.name"
                                    v-model="form[field.name]"
                                    :type="getFieldTypeName(field)"
                                    class="mt-1 block w-full"
                                    v-bind="field.inputOptions"
                                    :min="field.inputOptions?.min !== undefined ? field.inputOptions?.min : null"
                                    :max="field.inputOptions?.max !== undefined ? field.inputOptions?.max : null"
                                    :auto-focus="index==0"
                                    :accept="field.inputOptions?.accept"
                                    @input="filterChanged"
                                    :placeholder="field.hasPlaceholder || field.placeholder?.length ? field.placeholder || getFormattedFieldName(field) : ''"
                                />
                                <SelectInput
                                    v-if="field.type == FieldType.Select"
                                    :id="field.name"
                                    v-model="form[field.name]"
                                    class="mt-1 block w-full"
                                    @input="filterChanged"
                                    :options="[]"
                                    v-bind="field.selectOptions"
                                    :placeholder="field.hasPlaceholder || field.placeholder?.length ? field.placeholder || getFormattedFieldName(field) : ''"
                                />
                            </span>
                        </div>
                    </div>
                </div>
                <Button variant="outline" size="lg" @click.prevent="resetFilters">
                    <ListRestart class="w-4 h-4"/>
                </Button>
                <Input placeholder="Press / to search" @input="filterChanged" ref="searchInput" v-model="form['search']" type="text" @search="filterChanged"/>
            </div>
            <div class="flex flex-wrap overflow-auto" v-if="numFiltersApplied">
                <div v-for="filter in props.filters" :key="filter.name">
                    <span v-if="form[filter.name] && filter.name != 'search'" class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-gray-700 dark:text-gray-300">
                        {{ getFormattedFieldName(filter) }}: 
                        {{ 
                            findFilter(filter.name)[0].type == 6 
                                ? findFilter(filter.name)[0].selectOptions.options.filter(f => f.value == form[filter.name])[0].label 
                                : form[filter.name] 
                        }}
                        <Link class="ml-1" as="button" @click.prevent="removeFilter(filter)"><X class="w-3 h-3"/></Link></span>
                </div>
            </div>
        </div>
    </div>
</template>