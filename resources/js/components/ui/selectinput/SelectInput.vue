<script setup lang="ts">
import { cn } from '@/lib/utils';
import { useVModel } from '@vueuse/core';
import { readonly, type HTMLAttributes } from 'vue';
import type { Choice } from '../form/index';
import { type SelectInputProps } from '.';

const props = defineProps<SelectInputProps>();

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
    <select
        v-model="modelValue"
        v-else
        :disabled="props.disabled"
        :class="
            cn(
                'flex w-full h-10 rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
                props.class,
            )
        "
    >
        <option value='' v-if="props.optional"></option>
        <option v-for="option in props.options" :key="option.value" :value="option.value">{{ option.label || option.value }}</option>
    </select>
</template>
