<script setup>
import PreviewPost from '@/Components/BlogPost/PreviewPost.vue';

defineProps({
    recentPosts: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <div class="container mx-auto bg-white px-4 py-12">
        <h2 class="mb-8 text-center text-2xl font-bold">Recent posts</h2>
        <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
            <PreviewPost
                v-for="(post, index) in recentPosts"
                :key="index"
                :post="post" />
        </div>
    </div>
</template>
