<script setup lang="ts">
    import { AlertProps, AlertType } from '.';

    const props = defineProps<AlertProps>();
</script>
<template>
    <div class="flex p-4 mb-4 text-sm rounded-lg" :class="{
        'text-blue-800 bg-blue-100 dark:bg-blue-100 dark:text-blue-700' : props.type == AlertType.info,
        'text-red-800 bg-red-100 dark:bg-red-100 dark:text-red-700' : props.type == AlertType.danger,
        'text-green-800 bg-green-100 dark:bg-green-100 dark:text-green-700' : props.type == AlertType.success,
        'text-yellow-800 bg-yellow-100 dark:bg-yellow-100 dark:text-yellow-700' : props.type == AlertType.warning,
    }" role="alert">
        <svg class="shrink-0 inline w-4 h-4 me-3 mt-[2px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
        </svg>
        <span class="sr-only">{{ props.type }}</span>
        <div>
            <slot>{{ props.message }}</slot>
        </div>
    </div>
</template>