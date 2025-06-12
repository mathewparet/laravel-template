<script setup lang="ts">
import { cn } from '@/lib/utils';
import { useVModel } from '@vueuse/core';
import { readonly, type HTMLAttributes } from 'vue';
import type { Choice } from '../form/index';
import { type RadioProps } from '.';
 import { Label } from '@/components/ui/label';

const props = defineProps<RadioProps>();

const emits = defineEmits<{
    (e: 'update:modelValue', payload: string | number): void;
}>();

const modelValue = useVModel(props, 'modelValue', emits, {
    passive: true,
    defaultValue: props.defaultValue,
});
</script>

<template>
    <div :class="cn(
                'flex w-full h-10 rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
                props.class,
            )"
        v-if="props.readonly"
    >
        {{ props.options.filter(option => option.value == props.modelValue)[0]?.label }}        
    </div>
    <div v-for="option in props.options" 
        :key="option.value" 
        v-else
        class="flex items-center ps-4 border border-gray-200 rounded-sm dark:border-gray-700 m-2"
    >
        <Label class="w-full py-4 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300 space-x-2">
            <input v-model="modelValue"
                type="radio"
                :value="option.value"
                :disabled="props.disabled"
                :class="cn(
                    'rounded-md border border-input bg-background text-base ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm'
                )"
            >
            <span><slot :name="'radio-' + option.value.toLowerCase().replace(' ','-')" v-bind="option">{{ option.label || option.value }}</slot></span>
        </Label>
    </div>
</template>
