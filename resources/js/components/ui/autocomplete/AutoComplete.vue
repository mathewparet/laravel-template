<script setup lang="ts">
import { cn } from '@/lib/utils';
import { useDebounceFn, useVModel } from '@vueuse/core';
import type { HTMLAttributes } from 'vue';
import { computed, nextTick, onMounted, ref } from 'vue'
import { useForwardPropsEmits } from 'radix-vue'
import { AutocompleteProps } from '.';
import axios from 'axios';

const props = defineProps<AutocompleteProps>();

const emits = defineEmits<{
    (e: 'update:modelValue', payload: string | number): void;
    (e: 'change'): void;
}>();

const emitsAsProps = useForwardPropsEmits(props, emits)

const modelValue = useVModel(props, 'modelValue', emits, {
    passive: true,
    defaultValue: props.defaultValue,
});

const restultValue = ref(null)

const input = ref(null)

onMounted(() => {
    if(props.autoFocus) {
        input?.value?.focus();
    }

    if(props.modelValue) {
        results.value = [props.modelValue]
        console.log(results.value)
        clickSelect(0)
    }
})

const autoCompleteModel = ref(props.modelValue);

const results = ref({})

const showingResults = ref(false)

const activeSuggestionIndex = ref(-1);

const resultLength = computed<number>(() => results.value?.length)

defineExpose({ focus: () => input.value.focus(), blur: () => input.value.blur() });

const getDeepValue = (obj, path) => {
    return path.split('.').reduce((acc, part) => {
        if (acc && acc.hasOwnProperty(part)) {
            return acc[part];
        }
        return undefined;
    }, obj);
};


const populateAutoCompleteList = useDebounceFn(() => {
    let url = decodeURI(props.search);

    url = url.replace(/\[([^\]]+)]/g, (match, key) => {
        if (key === 'search') {
            return modelValue.value;
        }

        const value = getDeepValue(props.form, key);
        return value !== undefined ? value : '';
    });

    axios.get(url.replace('[search]', modelValue.value))
        .then(response => {
            results.value = response.data[props.dataKey].data;
            if(resultLength.value) {
                activeSuggestionIndex.value = 0;
            } else {
                activeSuggestionIndex.value = -1;
            }
            showingResults.value = true;
        })
}, 100);

const hideResults = () => {
    setTimeout(() => {
        showingResults.value = false;
    }, 200);
};

const onArrowDown = () => {
    if (activeSuggestionIndex.value < resultLength.value - 1) {
        activeSuggestionIndex.value++;
    } else {
        activeSuggestionIndex.value = 0;
    }
};

const onArrowUp = () => {
    if (activeSuggestionIndex.value > 0) {
        activeSuggestionIndex.value--;
    } else {
        activeSuggestionIndex.value = resultLength.value -1;
    }
};

const onEnter = () => {
    if (activeSuggestionIndex.value >= 0 && activeSuggestionIndex.value < resultLength.value) {
        clickSelect(activeSuggestionIndex.value);
        hideResults()
    }
};

const clickSelect = (result) => {
    autoCompleteModel.value = generateLabel(result)
    emits('update:modelValue', results.value[result])
    emits('change')
}

const generateLabel = (result) => props.format?.replace(/:(\w+(?:\.\w+)*):/g, (_, key) => getValue(results.value[result], key) ?? '')

const getValue = (obj: object, key: string) => key.split('.').reduce<any|string|number>((acc: Array<string>, part: string) => acc && acc[part], obj);

</script>

<template>
    <div class="relative mb-6">
        <div v-if="props.prefix?.length" class="absolute h-8 justify-center start-0 flex items-center ps-3.5 pointer-events-none">
            <div class="w-4 h-4 text-gray-500 dark:text-gray-400 uppercase font-mono">{{ props.prefix }}</div>
        </div>
        <input
            v-bind="emitsAsProps"
            v-model="autoCompleteModel"
            @input="populateAutoCompleteList"
            @blur="hideResults"
            @focus="() => showingResults = true"
            :disabled="props.disabled"
            @keydown.down.prevent="onArrowDown"
            @keydown.up.prevent="onArrowUp"
            @keydown.enter.prevent="onEnter"
            ref="input"
            :class="
                cn(
                    'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
                    props.class,
                    {
                        'ps-10 font-mono' : props.prefix,
                        'ring-offset-background focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2': !props.readonly
                    },
                )
            "
        />
        <div class="z-10 overflow-auto max-h-[400px] absolute mt-2  w-full px-1 bg-gray-50 border-0 ring-2 ring-gray-900 dark:border-gray-800 border:gray-200 text-gray-900 dark:bg-gray-900 dark:text-gray-300 rounded-lg text-sm " v-if="showingResults && resultLength > 0">
            <div v-for="(result, index) in Object.keys(results)" :key="result">
                <div @click="clickSelect(result)">
                    <div :class="{'dark:text-indigo-900 dark:bg-indigo-200 text-gray-600 bg-indigo-200': activeSuggestionIndex == index, 'dark:bg-gray-800 bg-gray-50': activeSuggestionIndex != index}" class="cursor-pointer text-gray-700 dark:text-gray-100 px-1 text-sm py-2 hover:dark:text-indigo-900 hover:dark:bg-indigo-200 hover:text-gray-600 hover:bg-indigo-200">
                        <img :src="result?.image" class="rounded size-6" v-if="result?.imaage"/>
                        <span>{{ generateLabel(result) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
