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
            </div>
        </div>
    </Link>
</template>
