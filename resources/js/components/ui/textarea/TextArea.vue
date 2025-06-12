<script setup lang="ts">
import { cn } from '@/lib/utils';
import { useVModel } from '@vueuse/core';
import type { HTMLAttributes } from 'vue';
import { useForwardPropsEmits } from 'radix-vue'
import { TextAreaInputProps } from '.';

const props = defineProps<TextAreaInputProps>();

const emits = defineEmits<{
    (e: 'update:modelValue', payload: string | number): void;
}>();

const emitsAsProps = useForwardPropsEmits(props, emits)

const modelValue = useVModel(props, 'modelValue', emits, {
    passive: true,
    defaultValue: props.defaultValue,
});
</script>

<template>
    <textarea
        v-model="modelValue"
        :rows="props.rows"
        v-bind="useForwardPropsEmits"
        :disabled="props.disabled"
        :readonly="props.readonly"
        :class="
            cn(
                'flex w-full rounded-md border border-input bg-background px-3 py-2 text-base file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
                {
                    'ring-offset-background focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2': !props.readonly
                },
                props.class,
            )
        "
    />
</template>
