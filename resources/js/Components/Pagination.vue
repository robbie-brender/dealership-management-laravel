<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: Array,
});
</script>

<template>
    <div v-if="links.length > 3">
        <div class="flex flex-wrap -mb-1">
            <template v-for="(link, key) in links" :key="key">
                <div v-if="link.url === null" 
                     class="mr-1 mb-1 px-4 py-2 text-sm border rounded text-gray-400" 
                     :class="{ 'ml-auto': link.label === 'Next &raquo;' }">
                    <span v-html="link.label"></span>
                </div>
                <Link v-else 
                      class="mr-1 mb-1 px-4 py-2 text-sm border rounded" 
                      :class="{ 
                          'bg-orange-500 text-white': link.active,
                          'text-gray-700 hover:bg-gray-100': !link.active,
                          'ml-auto': link.label === 'Next &raquo;'
                      }" 
                      :href="link.url">
                    <span v-html="link.label"></span>
                </Link>
            </template>
        </div>
    </div>
</template>
