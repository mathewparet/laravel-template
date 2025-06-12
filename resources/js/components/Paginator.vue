<script setup>
import { Link, router } from "@inertiajs/vue3";

const props = defineProps({
  meta: {
    type: Array,
    required: true,
  },
});

const onClick = (page) => {
  router.visit(props.meta.links.filter(link => parseInt(link.label) == page)[0].url)
}

const classes = "mr-1 px-4 py-3 text-sm leading-4 border rounded";
</script>

<template>
  <vue-awesome-paginate
    v-if="meta && meta.last_page > 1"
    :total-items="meta.total"
    :items-per-page="meta.per_page"
    :max-pages-shown="5"
    v-model="meta.current_page"
    @click="onClick"
    class="flex justify-center"
    paginateButtonsClass="mr-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-gray-500 dark:hover:bg-gray-200 text-gray-500 dark:text-gray-400 hover:text-gray-100 dark:hover:text-gray-800"
    type="link"
    disabledPaginateButtonsClass="cursor-not-allowed"
    backButtonClass="text-gray-400 dark:text-gray-60"
    nextButtonClass="text-gray-400 dark:text-gray-60"
    activePageClass="bg-gray-500 text-white dark:bg-gray-300 dark:text-gray-600"
    :link-url="meta.path+'?page=[page]'"
  >
    <template #prev-button>
      &laquo; Previous
    </template>
    <template #next-button>
      Next &raquo;
    </template>
  </vue-awesome-paginate>
</template>