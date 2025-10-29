<script setup>
defineProps({
    tags: {
        type: Array,
        default: () => []
    }
})
</script>

<template>
    <div v-if="tags && tags.length > 0" class="flex flex-wrap gap-2">
        <span
            v-for="tag in tags"
            :key="tag"
            class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800">
            #{{ tag }}
        </span>
    </div>
</template>