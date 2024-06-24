<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import { formatDate } from '@/helpers/format.js';
import BlogLayout from '@/Layouts/BlogLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Dropdown from '@/Components/Dropdown.vue';
import Options from '@/Components/Options.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { reactive, ref, watch } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { useRoute } from '@/helpers/ziggy.js';

const props = defineProps({
    blogPosts: {
        type: Object,
        required: true,
    },
});

/**
 * @typedef {object} Post
 * @property {number} id
 * @property {string} title
 * @property {string} excerpt
 * @property {string} body
 * @property {boolean} is_published
 * @property {boolean} is_featured
 * @property {string} published_at
 * @property {string} created_at
 * @property {string} updated_at
 * @property {Image} image
 */

/**
 * @typedef {object} Image
 * @property {string} uuid
 * @property {string} original_url
 * @property {string} preview_url
 */

/**
 * @typedef {object} PostsState
 * @property {Post[]} data
 * @property {Object} meta
 * @property {string} filter
 */

/**
 * @typedef {object} props
 * @property {PostsState} blogPosts
 */

const openCreatePost = ref(false);
const openViewPost = ref(false);
const selectedPost = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);
const postToEdit = ref(null);
const isEditModalOpen = ref(false);
const imagePreview = ref('');
const filter = ref('all');
const { route } = useRoute();

const postsState = reactive({
    data: props.blogPosts.data,
    meta: props.blogPosts.meta,
    filter: 'all',
});

const form = useForm({
    title: '',
    excerpt: '',
    body: '',
    is_published: false,
    is_featured: false,
    photo: null,
});

const updatePostForm = useForm({
    title: '',
    excerpt: '',
    body: '',
    is_published: false,
    is_featured: false,
    photo: null,
});

const statusClass = (status) => ({
    'bg-green-500': status === 'published',
    'bg-yellow-500': status === 'draft',
});

const showCreateTaskModal = () => {
    openCreatePost.value = true;
};

const showViewModal = (post) => {
    selectedPost.value = post;
    openViewPost.value = true;
};

const isPublished = (status) => (status ? 'published' : 'draft');

const loadMore = (page) => {
    router.get(route('blog.my-posts', { blogPosts: page }));
};

const openEditPostModal = (post) => {
    isEditModalOpen.value = true;
    postToEdit.value = post;
    updatePostForm.title = post.title;
    updatePostForm.excerpt = post.excerpt;
    updatePostForm.body = post.body;
    updatePostForm.is_published = post.is_published;
    updatePostForm.photo = post.image?.uuid;
    imagePreview.value = post.image.preview_url;
};

const closeCreatePostModal = () => {
    openCreatePost.value = false;
    form.reset('photo', 'title', 'excerpt', 'body', 'is_published');
};

const closeEditPostModal = () => {
    isEditModalOpen.value = false;
    postToEdit.value = null;
    updatePostForm.reset('title', 'excerpt', 'body', 'is_published');
};

const createBlog = () => {
    form.photo = photoInput.value.files[0];
    form.post(route('blog.store'), {
        preserveScroll: true,
        errorBag: 'createPost',
        onSuccess: (page) => {
            page.props.banner = {
                style: 'success',
                message: 'Post created successfully!',
            };
            closeCreatePostModal();
            postsState.data = page.props.blogPosts.data;
            form.reset('photo', 'title', 'excerpt', 'body', 'is_published');
        },
    });
};

const updateBlog = () => {
    console.log(updatePostForm);
    if (photoInput.value) {
        updatePostForm.photo = photoInput.value.files[0];
    }
    console.log(updatePostForm);
    updatePostForm.post(route('blog.update', postToEdit.value.id), {
        preserveScroll: true,
        errorBag: 'updatePost',
        onSuccess: (page) => {
            page.props.banner = {
                style: 'success',
                message: 'Post updated successfully!',
            };
            closeEditPostModal();
            postsState.data = page.props.blogPosts.data;
            updatePostForm.reset('title', 'excerpt', 'body', 'is_published');
        },
    });
};

const selectNewPhoto = () => {
    photoInput.value.click();
};

/**
 * Update the photo preview when a new photo is selected
 */
const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];
    if (!photo) return;
    const reader = new FileReader();
    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };
    reader.readAsDataURL(photo);
};

const getPaginationPageNumber = (url) => {
    return url.split('blogPosts=')[1];
};

const deletePost = (postId) => {
    if (confirm('Are you sure you want to delete this post?')) {
        router.delete(route('blog.destroy', postId), {
            preserveScroll: true,
            onSuccess: (page) => {
                postsState.data = page.props.blogPosts.data;
            },
        });
    }
};

watch(filter, (value) => {
    router.reload({
        data: {
            filter: value,
            blogPosts: null,
        },
        preserveState: true,
        onSuccess: (page) => {
            postsState.data = page.props.blogPosts.data;
            postsState.meta = page.props.blogPosts.meta;
        },
    });
});
</script>

<template>
    <Head title="My Posts" />
    <BlogLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-6 flex items-center justify-between">
                    <select
                        id="filter"
                        v-model="filter"
                        class="mt-1 block w-1/4 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="all">All</option>
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                    </select>
                    <PrimaryButton @click="showCreateTaskModal()"
                        >New Post</PrimaryButton
                    >
                </div>
                <ul>
                    <li
                        v-for="post in postsState.data"
                        :key="post.id"
                        class="mb-4 cursor-pointer">
                        <div
                            class="flex items-center justify-between rounded-lg bg-gray-100 p-3">
                            <div>
                                <a
                                    class="mb-4"
                                    :href="route('blog.show', post)"
                                    @click="showViewModal(post)">
                                    <div
                                        class="mb-4 flex items-center space-x-4">
                                        <div>
                                            <img
                                                :src="post.image.preview_url"
                                                alt="Post Image"
                                                class="h-24 w-32 rounded-lg object-cover" />
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-medium">
                                                {{ post.title }}
                                            </h3>
                                            <div class="text-gray-500">
                                                {{ post.excerpt }}
                                            </div>
                                            <div class="text-gray-500">
                                                <span
                                                    v-if="post.is_published"
                                                    class="text-sm text-gray-500">
                                                    Published:
                                                    {{
                                                        formatDate(
                                                            post.published_at,
                                                        )
                                                    }}</span
                                                >
                                                <span
                                                    v-else
                                                    class="text-sm text-gray-500">
                                                    Drafted:
                                                    {{
                                                        formatDate(
                                                            post.created_at,
                                                        )
                                                    }}</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <div class="flex items-center">
                                    <div
                                        :class="
                                            statusClass(
                                                isPublished(post.is_published),
                                            )
                                        "
                                        class="rounded-full px-3 py-1 text-xs uppercase text-white">
                                        {{ isPublished(post.is_published) }}
                                    </div>

                                    <!--add a . separator between the date and the status-->
                                    <div
                                        v-if="post.is_featured"
                                        class="ml-4 rounded-full bg-blue-500 px-3 py-1 text-xs uppercase text-white">
                                        Featured
                                    </div>
                                </div>
                            </div>

                            <!--Post Management Popup Menu-->
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button
                                        class="self-center text-gray-500 hover:text-gray-700">
                                        <Options />
                                    </button>
                                </template>

                                <template #content>
                                    <!-- Account Management -->
                                    <div
                                        class="block px-4 py-2 text-xs text-gray-400">
                                        Manage Post
                                    </div>
                                    <DropdownLink
                                        as="button"
                                        @click="openEditPostModal(post)">
                                        Edit
                                    </DropdownLink>
                                    <div class="border-t border-gray-200" />
                                    <DropdownLink
                                        as="button"
                                        @click="deletePost(post.id)">
                                        <span class="text-red-600">Delete</span>
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </li>
                </ul>

                <div class="mt-4">
                    <nav class="flex items-center justify-between">
                        <div>
                            <span
                                v-for="link in postsState.meta.links"
                                :key="link.label"
                                :class="[
                                    'mr-2 cursor-pointer rounded-lg border border-gray-300 px-3 py-1',
                                    { 'mr-2 bg-gray-300': link.active },
                                ]"
                                @click="
                                    link.url &&
                                        loadMore(
                                            getPaginationPageNumber(link.url),
                                        )
                                "
                                v-html="link.label"></span>
                        </div>
                    </nav>
                </div>
            </div>
        </div>

        <!--Create Post Modal-->
        <DialogModal :show="openCreatePost" @close="closeCreatePostModal">
            <template #title> New Post </template>

            <template #content>
                <div class="mt-4">
                    <InputLabel for="title" value="Title" />
                    <TextInput
                        id="title"
                        ref="titleInput"
                        v-model="form.title"
                        class="mt-1 block w-3/4"
                        placeholder="Post Title"
                        type="text" />
                    <InputError :message="form.errors.title" class="mt-2" />
                </div>
                <div class="mt-4">
                    <InputLabel for="excerpt" value="Excerpt" />
                    <textarea
                        id="excerpt"
                        ref="excerptInput"
                        v-model="form.excerpt"
                        placeholder="Post Excerpt"
                        type="text"
                        rows="5"
                        class="mt-1 block w-3/4 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    <InputError :message="form.errors.excerpt" class="mt-2" />
                </div>
                <div class="mt-4">
                    <InputLabel for="title" value="Cover Image" />
                    <input
                        id="photo"
                        ref="photoInput"
                        type="file"
                        class="hidden"
                        @change="updatePhotoPreview" />
                    <div v-show="photoPreview" class="mt-2">
                        <span
                            class="block h-20 w-20 bg-cover bg-center bg-no-repeat"
                            :style="
                                'background-image: url(\'' +
                                photoPreview +
                                '\');'
                            " />
                    </div>
                    <SecondaryButton
                        class="me-2 mt-2"
                        type="button"
                        @click.prevent="selectNewPhoto">
                        Select Photo
                    </SecondaryButton>

                    <InputError :message="form.errors.photo" class="mt-2" />
                </div>
                <div class="mt-4">
                    <InputLabel for="body" value="Body" />
                    <textarea
                        id="body"
                        ref="bodyInput"
                        v-model="form.body"
                        class="mt-1 block w-3/4 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Post Body"
                        type="text"
                        rows="5" />
                    <InputError :message="form.errors.body" class="mt-2" />
                </div>

                <div class="mt-4">
                    <InputLabel for="status" value="Status" />
                    <select
                        id="status"
                        v-model="form.is_published"
                        class="mt-1 block w-3/4 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option :value="true" :selected="form.is_published">
                            Publish
                        </option>
                        <option :value="false" :selected="!form.is_published">
                            Draft
                        </option>
                    </select>
                    <InputError
                        :message="form.errors.is_published"
                        class="mt-2" />
                </div>

                <div class="mt-4">
                    <InputLabel for="featured" value="Featured Post" />
                    <select
                        id="featured"
                        v-model="form.is_featured"
                        class="mt-1 block w-3/4 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option :value="true" :selected="form.is_featured">
                            Yes
                        </option>
                        <option :value="false" :selected="!form.is_featured">
                            No
                        </option>
                    </select>
                    <InputError
                        :message="form.errors.is_featured"
                        class="mt-2" />
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="closeCreatePostModal">
                    Cancel
                </SecondaryButton>

                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    class="ms-3"
                    @click="createBlog">
                    Save
                </PrimaryButton>
            </template>
        </DialogModal>

        <!--Edit Post Modal-->
        <DialogModal :show="isEditModalOpen" @close="closeEditPostModal">
            <template #title> Edit Post </template>

            <template #content>
                <div class="mt-4">
                    <InputLabel for="title" value="Title" />
                    <TextInput
                        id="title"
                        ref="titleInput"
                        v-model="updatePostForm.title"
                        class="mt-1 block w-3/4"
                        placeholder="Post Title"
                        type="text" />
                    <InputError
                        :message="updatePostForm.errors.title"
                        class="mt-2" />
                </div>
                <div class="mt-4">
                    <InputLabel for="excerpt" value="Excerpt" />
                    <textarea
                        id="excerpt"
                        ref="excerptInput"
                        v-model="updatePostForm.excerpt"
                        placeholder="Post Excerpt"
                        type="text"
                        rows="5"
                        class="mt-1 block w-3/4 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    <InputError
                        :message="updatePostForm.errors.excerpt"
                        class="mt-2" />
                </div>
                <div class="mt-4">
                    <InputLabel for="title" value="Cover Image" />
                    <input
                        id="photo"
                        ref="photoInput"
                        type="file"
                        class="hidden"
                        @change="updatePhotoPreview" />
                    <div v-show="photoPreview || imagePreview" class="mt-2">
                        <span
                            v-if="photoPreview !== null"
                            class="block h-20 w-20 bg-cover bg-center bg-no-repeat"
                            :style="
                                'background-image: url(\'' +
                                photoPreview +
                                '\');'
                            " />
                        <span
                            v-else
                            class="block h-20 w-20 bg-cover bg-center bg-no-repeat"
                            :style="
                                'background-image: url(\'' +
                                imagePreview +
                                '\');'
                            " />
                    </div>
                    <SecondaryButton
                        class="me-2 mt-2"
                        type="button"
                        @click.prevent="selectNewPhoto">
                        Select New Photo
                    </SecondaryButton>

                    <InputError
                        :message="updatePostForm.errors.photo"
                        class="mt-2" />
                </div>
                <div class="mt-4">
                    <InputLabel for="body" value="Body" />
                    <textarea
                        id="body"
                        ref="bodyInput"
                        v-model="updatePostForm.body"
                        class="mt-1 block w-3/4 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Post Body"
                        type="text"
                        rows="5" />
                    <InputError
                        :message="updatePostForm.errors.body"
                        class="mt-2" />
                </div>

                <div class="mt-4">
                    <InputLabel for="status" value="Status" />
                    <select
                        id="status"
                        v-model="updatePostForm.is_published"
                        class="mt-1 block w-3/4 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option
                            :value="true"
                            :selected="updatePostForm.is_published">
                            Publish
                        </option>
                        <option
                            :value="false"
                            :selected="!updatePostForm.is_published">
                            Draft
                        </option>
                    </select>
                    <InputError
                        :message="updatePostForm.errors.is_published"
                        class="mt-2" />
                </div>

                <div class="mt-4">
                    <InputLabel for="featured" value="Featured Post" />
                    <select
                        id="featured"
                        v-model="updatePostForm.is_featured"
                        class="mt-1 block w-3/4 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option
                            :value="true"
                            :selected="updatePostForm.is_featured">
                            Yes
                        </option>
                        <option
                            :value="false"
                            :selected="!updatePostForm.is_featured">
                            No
                        </option>
                    </select>
                    <InputError
                        :message="updatePostForm.errors.is_featured"
                        class="mt-2" />
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="closeEditPostModal">
                    Cancel
                </SecondaryButton>

                <PrimaryButton
                    :class="{ 'opacity-25': updatePostForm.processing }"
                    :disabled="updatePostForm.processing"
                    class="ms-3"
                    @click="updateBlog">
                    Save
                </PrimaryButton>
            </template>
        </DialogModal>
    </BlogLayout>
</template>
