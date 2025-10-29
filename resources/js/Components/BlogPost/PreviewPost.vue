<script setup>
import { Link } from '@inertiajs/vue3';
import { formatDate } from '@/helpers/format.js';

defineProps({
    /**
     * The blog post to display.
     */
    post: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Link :href="route('blog.show', post.slug)">
        <div class="overflow-hidden rounded">
            <img
                :src="post.image"
                alt="Blog post image"
                class="h-52 w-full rounded object-cover" />
            <div class="pt-2">
                <p class="text-sm text-gray-600">
                    {{ formatDate(post.published_at) }}
                </p>
                <h3 class="my-2 text-xl font-bold">{{ post.title }}</h3>
                <p class="text-sm text-gray-700">{{ post.excerpt }}</p>
                <div
                    v-if="post.tags && post.tags.length > 0"
                    class="mt-2 flex flex-wrap gap-1">
                    <span
                        v-for="(tag, index) in post.tags"
                        :key="index"
                        class="inline-block rounded-full bg-gray-200 px-2 py-1 text-xs text-gray-700">
                        {{ tag }}
                    </span>
                </div>
            </div>
        </div>
    </Link>
</template>
