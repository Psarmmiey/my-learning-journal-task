<script setup>
import { Head } from '@inertiajs/vue3';
import BlogLayout from '@/Layouts/BlogLayout.vue';
import { formatDate } from '@/helpers/format.js';
import RecentPosts from '@/Components/BlogPost/RecentPosts.vue';
import CommentSection from '@/Components/Comments/CommentSection.vue';
import TagDisplay from '@/Components/TagDisplay.vue';

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
                    <div class="mb-4 text-lg lg:break-words prose prose-lg" v-html="post.data.excerpt_html || post.data.excerpt"></div>
                    <TagDisplay :tags="post.data.tags" />
                </div>
            </div>
        </div>

        <div class="relative mx-8 flex flex-row justify-center px-4">
            <img
                :src="post.data.image?.original_url"
                alt="Banner"
                class="h-[488px] w-[979px] max-w-full rounded object-cover sm:w-full md:w-full" />
        </div>

        <!-- Blog Posts -->
        <div class="container mx-auto px-4 py-12">
            <div class="prose lg:prose-xl mx-auto">
                <section v-html="post.data.body_html || post.data.body">
                </section>
            </div>
        </div>

        <!-- Comments Section -->
        <CommentSection 
            :blog-post-id="post.data.id" 
            :comments-count="post.data.comments_count" 
        />
        
        <RecentPosts :recent-posts="recentPosts.data" />
    </BlogLayout>
</template>
