<script setup lang="ts">
    import { Link } from '@inertiajs/vue3'
    import { computed } from 'vue';

    interface ApplLinkProps
    {
        when: Boolean,
        href: string,
        prefetch: boolean,
    }

    
    const props = defineProps<ApplLinkProps>()

    const whenNot = computed(() => !props.when)
</script>
<template>
    <Link 
        :prefetch="props.prefetch"  
        :class="{'pointer-events-none opacity-50 text-white': whenNot, 'text-blue-700 dark:text-blue-500 hover:underline': when}"  
        :href="props.href">
        <slot></slot>
    </Link>
</template>