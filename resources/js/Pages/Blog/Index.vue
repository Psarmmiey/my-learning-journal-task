<script setup>
import { onMounted, reactive, ref } from 'vue';
import PreviewPost from '@/Components/BlogPost/PreviewPost.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import BlogLayout from '@/Layouts/BlogLayout.vue';
import { formatDate } from '@/helpers/format.js';

const props = defineProps({
    blogPosts: {
        type: Object,
        required: true,
    },
    featuredPost: {
        type: Object,
        required: true,
    },
});

const blogPostsState = reactive(props.blogPosts);
const loadMoreButton = ref(null);

/**
 * Fetch blog posts
 * typedef {Function} fetchBlogPosts
 */

/**
 * @typedef {Object} blogPostsState
 * @property {Array} data
 * @property {Object} meta
 * @property {string} meta.next_cursor
 * @property {string} meta.prev_cursor
 */

/**
 * define blogPosts
 * @type {(function(): *)|*} blogPosts
 * @property {Array} blogPosts.data
 * @property {Object} blogPosts.meta
 * @property {string} blogPosts.meta.next_cursor
 * @property {string} blogPosts.meta.prev_cursor
 */
const fetchBlogPosts = () => {
    router.reload({
        data: {
            blogPosts: blogPostsState.meta.next_cursor,
        },
        onSuccess: () => {
            blogPostsState.data.push(...props.blogPosts.data);
            blogPostsState.meta = props.blogPosts.meta;
        },
    });
};

onMounted(() => {
    if (props.blogPosts.meta.prev_cursor) {
        router.reload({
            data: {
                blogPosts: null,
            },
            preserveState: true,
            onSuccess: () => {
                blogPostsState.data = props.blogPosts.data;
                blogPostsState.meta = props.blogPosts.meta;
            },
        });
    }
});

defineOptions({
    layout: BlogLayout,
    inheritAttrs: false,
});
</script>

<template>
    <Head title="Home" />
    <!-- Main Banner -->
    <Link
        v-if="featuredPost?.data"
        :href="route('blog.show', featuredPost.data.slug)"
        class="cursor-pointer">
        <div class="relative">
            <img
                :src="featuredPost.data.image"
                alt="Banner"
                class="h-96 w-full object-cover" />
            <div
                class="lg:max-w-1/2 absolute bottom-0 left-0 bg-black bg-opacity-20 p-4">
                <div class="text-white lg:px-12">
                    <p class="mb-2 text-sm">
                        {{ formatDate(featuredPost.data.published_at) }}
                    </p>
                    <h2 class="mb-4 text-4xl font-bold">
                        {{ featuredPost.data.title }}
                    </h2>
                    <p class="text-lg lg:break-words">
                        {{ featuredPost.data.excerpt }}
                    </p>
                </div>
            </div>
        </div>
    </Link>

    <!-- Blog Posts -->
    <div class="container mx-auto px-4 py-12">
        <div v-if="blogPostsState.data.length === 0" class="text-center">
            <p>No posts found.</p>
        </div>
        <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
            <PreviewPost
                v-for="(post, index) in blogPostsState.data"
                :key="index"
                :post="post" />
        </div>
        <div class="mt-8 text-center">
            <button
                v-if="blogPostsState.meta.next_cursor"
                ref="loadMoreButton"
                class="cursor-pointer px-4 py-2 text-black underline hover:bg-gray-700 hover:text-white"
                @click="fetchBlogPosts">
                View More
            </button>
        </div>
    </div>
</template>
