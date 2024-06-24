<script setup>
import { onMounted, reactive, ref, watch } from 'vue';
import PreviewPost from '@/Components/BlogPost/PreviewPost.vue';
import { Head, router } from '@inertiajs/vue3';
import BlogLayout from '@/Layouts/BlogLayout.vue';
import { formatDate } from '@/helpers/format.js';
import { Link } from '@inertiajs/vue3';

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

const fetchBlogPosts = () => {
    router.reload({
        data: {
            blogPosts: blogPostsState.meta.next_cursor,
        },
        preserveState: true,
        onSuccess: (page) => {
            blogPostsState.data.push(...page.props.blogPosts.data);
            blogPostsState.meta = page.props.blogPosts.meta;
        },
    });
};

watch(
    () => blogPostsState.meta.next_cursor,
    (newValue) => {
        if (newValue === null) {
            // loadMoreButton.value.remove()
        }
    },
);

onMounted(() => {
    if (props.blogPosts.meta.prev_cursor) {
        router.replace({
            data: {
                blogPosts: props.blogPosts.meta.prev_cursor,
            },
            preserveState: true,
            onSuccess: (page) => {
                blogPostsState.value = page.props.blogPosts;
                blogPostsState.meta = page.props.blogPosts.meta;
            },
        });
    }
});
</script>

<template>
    <Head title="Home" />
    <BlogLayout>
        <!-- Main Banner -->
        <Link :href="route('blog.show', featuredPost.data.slug)" class="cursor-pointer" v-if="featuredPost?.data">
                <div class="relative" >
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

<!--        <div class="relative" v-if="featuredPost?.data">-->
<!--            <img-->
<!--                :src="featuredPost.data.image"-->
<!--                alt="Banner"-->
<!--                class="h-96 w-full object-cover" />-->
<!--            <div-->
<!--                class="lg:max-w-1/2 absolute bottom-0 left-0 bg-black bg-opacity-20 p-4">-->
<!--                <div class="text-white lg:px-12">-->
<!--                    <p class="mb-2 text-sm">-->
<!--                        {{ formatDate(featuredPost.data.published_at) }}-->
<!--                    </p>-->
<!--                    <h2 class="mb-4 text-4xl font-bold">-->
<!--                        {{ featuredPost.data.title }}-->
<!--                    </h2>-->
<!--                    <p class="text-lg lg:break-words">-->
<!--                        {{ featuredPost.data.excerpt }}-->
<!--                    </p>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->

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
                    ref="loadMoreButton"
                    @click="fetchBlogPosts"
                    v-if="blogPostsState.meta.next_cursor"
                    class="cursor-pointer px-4 py-2 text-black underline hover:bg-gray-700 hover:text-white">
                    View More
                </button>
            </div>
        </div>
    </BlogLayout>
</template>
