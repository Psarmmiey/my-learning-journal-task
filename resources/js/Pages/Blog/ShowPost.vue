<script setup>
import { Head } from '@inertiajs/vue3';
import BlogLayout from '@/Layouts/BlogLayout.vue';
import { formatDate } from '@/helpers/format.js';
import RecentPosts from '@/Components/BlogPost/RecentPosts.vue';

defineProps({
    post: {
        type: Object,
        required: true,
    },
    recentPosts: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head title="Article" />
    <BlogLayout>
        <!-- Title -->
        <div class="container mx-auto px-4 py-8">
            <div class="lg:max-w-1/2">
                <div>
                    <p class="mb-2 text-sm">
                        {{ formatDate(post.data.published_at) }}
                    </p>
                    <h2 class="mb-4 text-4xl font-bold">
                        {{ post.data.title }}
                    </h2>
                    <p class="text-lg lg:break-words">
                        {{ post.data.excerpt }}
                    </p>
                </div>
            </div>
        </div>

        <div class="relative mx-8 flex flex-row justify-center px-4">
            <img
                :src="post.data.image.original_url"
                alt="Banner"
                class="h-[488px] w-[979px] max-w-full rounded object-cover sm:w-full md:w-full" />
        </div>

        <!-- Blog Posts -->
        <div class="container mx-auto px-4 py-12">
            <div class="prose lg:prose-xl mx-auto">
                <section>
                    <p>
                        {{ post.data.body }}
                    </p>
                </section>
            </div>
        </div>
        <RecentPosts :recent-posts="recentPosts.data" />
    </BlogLayout>
</template>
