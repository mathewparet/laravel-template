<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import type { BreadcrumbItemType } from '@/types';
import { usePage } from '@inertiajs/vue3'
import { onMounted } from 'vue';
import { computed, watch } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();

// Watch the entire flash object
const flash = computed(() => page.props.flash);

watch(flash, (newFlash) => {
    if(newFlash?.error) {
        toast.error(newFlash?.error + (newFlash?.requestId ? '\n\nRequest ID: ' + newFlash?.requestId : ''), {
            autoClose: 6000,
            closeOnClick: false,
            newestOnTop: true,
        })
    }
    if(newFlash?.success) {
        toast.success(newFlash?.error + (newFlash?.requestId ? '\n\nRequest ID: ' + newFlash?.requestId : ''), {
            autoClose: 6000,
            closeOnClick: false,
            newestOnTop: true,
        })
    }
    if(newFlash?.info) {
        toast.info(newFlash?.error + (newFlash?.requestId ? '\n\nRequest ID: ' + newFlash?.requestId : ''), {
            autoClose: 6000,
            closeOnClick: false,
            newestOnTop: true,
        })
    }
})

onMounted(() => {
    
})

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
</template>
