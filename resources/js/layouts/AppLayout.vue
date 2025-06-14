<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import type { BreadcrumbItemType } from '@/types';
import { usePage } from '@inertiajs/vue3'
import { onMounted } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

onMounted(() => {
    if(usePage().props?.flash?.error) {
        toast.error(usePage().props?.flash?.error + (usePage().props?.flash['request-id'] ? '\n\nRequest ID: ' + usePage().props?.flash['request-id'] : ''), {
            autoClose: 6000,
            closeOnClick: false,
            newestOnTop: true,
        })
    }
    if(usePage().props?.flash?.success) {
        toast.success(usePage().props?.flash?.error + (usePage().props?.flash['request-id'] ? '\n\nRequest ID: ' + usePage().props?.flash['request-id'] : ''), {
            autoClose: 6000,
            closeOnClick: false,
            newestOnTop: true,
        })
    }
    if(usePage().props?.flash?.info) {
        toast.info(usePage().props?.flash?.error + (usePage().props?.flash['request-id'] ? '\n\nRequest ID: ' + usePage().props?.flash['request-id'] : ''), {
            autoClose: 6000,
            closeOnClick: false,
            newestOnTop: true,
        })
    }
})

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
</template>
