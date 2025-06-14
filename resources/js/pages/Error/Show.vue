<script setup lang="ts">
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    
];

interface ErrorProps {
    message: string,
    code: number,
    requestId: string,
}

const props = defineProps<ErrorProps>();

</script>

<template>
    <Head title="Uh oh!" />

    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
        <h1>Error {{ props.code }}</h1>
        <p>{{ props.message }}</p>
        <p><strong>Request ID:</strong> {{ props.requestId }}</p>
    </div>
</template>
