<script setup lang="ts">
defineProps<{
    message?: string;
}>();
</script>

<template>
    <div v-show="message || $slots.default">
        <p class="text-sm text-gray-600 dark:text-gray-500 italic">
            <slot>{{ message }}</slot>
        </p>
    </div>
</template>
