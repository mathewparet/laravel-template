<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
// import { router } from '@inertiajs/vue3'
// import { onMounted } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

// onMounted(() => {
//     router.on('before', (event) => {
//         const currentRouteName = route().current()

//         if (!currentRouteName) return // Safety check

//         const url = new URL(event.detail.visit.url, window.location.origin)

//         const savedFilters = JSON.parse(localStorage.getItem('filter:' + route().current()) || '{}')
        
//         for (const [key, value] of Object.entries(savedFilters)) {
//             if (!url.searchParams.has(key) && value != null && value !== '') {
//                 url.searchParams.set(key, value)
//             }
//         }
        
//         console.log(url.toString());

//         event.detail.visit.url = url
//     })
// })

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
</template>
