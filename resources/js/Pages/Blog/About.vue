<script setup>
import { Head } from '@inertiajs/vue3';
import BlogLayout from '@/Layouts/BlogLayout.vue';
import RecentPosts from '@/Components/BlogPost/RecentPosts.vue';

defineProps({
    recentPosts: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head title="Home" />
    <BlogLayout>
        <section class="mx-auto bg-white px-4 py-12">
            <div class="items-left container mx-auto flex flex-col md:flex-row items-center">
                <img
                    src="/images/bg-sample.jpg"
                    alt="Profile Picture"
                    class="mb-4 h-40 w-40 rounded-full object-cover md:mb-0 md:mr-6" />
                <div>
                    <h2 class="mb-2 text-4xl font-bold">
                        Hi there! My name is Roku and welcome to my learning
                        journal.
                    </h2>
                    <p class="text-lg text-gray-700">
                        After several months of learning in the Frontend
                        Developer Career Path, I've made the big jump over to
                        the Bootcamp to get expert code reviews of my Solo
                        Projects projects and meet like-minded peers.
                    </p>
                </div>
            </div>
        </section>

        <section class="mx-auto bg-white px-4 py-6">
            <div class="container mx-auto">
                <h2 class="mb-4 text-2xl font-bold">
                    How I stay committed to learning
                </h2>
                <p class="text-lg text-gray-700">
                    I like to think of myself as a lifelong learner. I used to
                    spend hours and hours learning, then try to create simple
                    projects using what I learned or work new techniques into
                    existing projects. While that was fun, I felt like it would
                    be helpful to share what I was learning and most things
                    about my journey with the world.
                </p>
            </div>
        </section>

        <section class="mx-auto bg-white px-4 py-6">
            <div class="container mx-auto">
                <h2 class="mb-4 text-2xl font-bold">How I got started</h2>
                <p class="text-lg text-gray-700">
                    I started simple and gradually grew my learning journal
                    site. I would take notes about what I was learning. After
                    each learning session, I'd use my notes to not only reflect
                    on what I learned but also write short summaries of what I
                    learned using my own words. That helped me grok what I was
                    learning, and I realized that posting my learning summaries
                    was also helping others learn and stay motivated.
                </p>
            </div>
        </section>

        <RecentPosts :recent-posts="recentPosts.data" />
    </BlogLayout>
</template>

<style scoped></style>
