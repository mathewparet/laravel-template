<script setup lang="ts">
import { cn } from '@/lib/utils';
import { useVModel } from '@vueuse/core';
import type { HTMLAttributes } from 'vue';
import { onMounted, ref } from 'vue'
import { useForwardPropsEmits } from 'radix-vue'
import { InputProps } from '.';

const props = defineProps<InputProps>();

const emits = defineEmits<{
    (e: 'update:modelValue', payload: string | number): void;
}>();

const emitsAsProps = useForwardPropsEmits(props, emits)

const modelValue = useVModel(props, 'modelValue', emits, {
    passive: true,
    defaultValue: props.defaultValue,
});

const input = ref(null)

onMounted(() => {
    if(props.autoFocus) {
        input?.value?.focus();
    }
})

defineExpose({ focus: () => input.value.focus(), blur: () => input.value.blur() });

</script>

<template>
    <div class="relative mb-6">
        <div v-if="props.prefix?.length" class="absolute h-8 justify-center start-0 flex items-center ps-3.5 pointer-events-none">
            <div class="w-4 h-4 text-gray-500 dark:text-gray-400 uppercase font-mono">{{ props.prefix }}</div>
        </div>
        <input
            v-bind="emitsAsProps"
            v-model="modelValue"
            ref="input"
            :disabled="props.disabled"
            :class="
                cn(
                    'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
                    props.class,
                    {
                        'ps-[62px]' : props.prefix?.length == 5,
                        'ps-[52px]' : props.prefix?.length == 4,
                        'ps-[42px]' : props.prefix?.length == 3,
                        'ps-[32px]' : props.prefix?.length == 2,
                        'ps-[22px]' : props.prefix?.length == 1,
                        'font-mono' : props.prefix,
                        'ring-offset-background focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2': !props.readonly
                    },
                )
            "
        />
    </div>
</template>
