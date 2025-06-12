<script setup lang="ts">
    import { Link } from '@inertiajs/vue3'
    import { computed } from 'vue';

    interface ApplLinkProps
    {
        when: Boolean,
        href: string,
        maxWidth: string,
    }

    
    const props = defineProps<ApplLinkProps>()

    const whenNot = computed(() => !props.when)
</script>
<template>
    <ModalLink 
        :max-width="props.maxWidth"
        :class="{'pointer-events-none opacity-50': whenNot, 'text-blue-700 dark:text-blue-500 hover:underline': when}"  
        :href="props.href"
    >
        <slot></slot>
    </ModalLink>
</template>