<script setup>
import { ref, reactive, watch } from 'vue'
import axios from 'axios'

const props = defineProps({
    blogPostId: {
        type: String,
        required: true,
    },
    parentCommentId: {
        type: String,
        default: null,
    },
    parentComment: {
        type: Object,
        default: null,
    },
    isReply: {
        type: Boolean,
        default: false,
    }
})

const emit = defineEmits(['comment-added', 'reply-added', 'cancel-reply'])

// Reactive data
const loading = ref(false)
const errors = ref({})
const form = reactive({
    content: ''
})

// Methods
const submitComment = async () => {
    if (!form.content.trim()) return
    
    loading.value = true
    errors.value = {}

    try {
        let response
        
        if (props.isReply && props.parentCommentId) {
            // Post reply
            response = await axios.post(`/comments/${props.parentCommentId}/reply`, {
                content: form.content
            })
            emit('reply-added', props.parentCommentId, response.data.data)
        } else {
            // Post new comment
            response = await axios.post(`/blog/${props.blogPostId}/comments`, {
                content: form.content
            })
            emit('comment-added', response.data.data)
        }

        // Reset form
        form.content = ''
        
        // If it was a reply, emit cancel to close the form
        if (props.isReply) {
            emit('cancel-reply')
        }

    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors || {}
        } else {
            errors.value = { content: 'Failed to post comment. Please try again.' }
        }
        console.error('Error posting comment:', error)
    } finally {
        loading.value = false
    }
}

const cancelReply = () => {
    form.content = ''
    emit('cancel-reply')
}

// Watch for character limit
watch(() => form.content, (newContent) => {
    if (newContent.length > 2000) {
        form.content = newContent.substring(0, 2000)
    }
})
</script>

<template>
    <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <form @submit.prevent="submitComment">
            <!-- Header -->
            <div class="mb-4">
                <h4 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ isReply ? 'Reply to comment' : 'Leave a comment' }}
                </h4>
                <div v-if="isReply" class="mt-2 p-3 bg-gray-50 dark:bg-gray-800 rounded border-l-4 border-blue-500">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Replying to <span class="font-medium">{{ parentComment?.user?.name }}</span>
                    </p>
                </div>
            </div>

            <!-- Comment Textarea -->
            <div class="mb-4">
                <label for="comment" class="sr-only">Comment</label>
                <textarea
                    id="comment"
                    v-model="form.content"
                    rows="4"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                    :placeholder="isReply ? 'Write your reply...' : 'Share your thoughts...'"
                    required
                ></textarea>
                <div v-if="errors.content" class="mt-1 text-sm text-red-600">
                    {{ errors.content }}
                </div>
                
                <!-- Character counter -->
                <div class="mt-1 text-xs text-gray-500 text-right">
                    {{ form.content.length }}/2000
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    <span v-if="$page.props.auth.user" class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $page.props.auth.user.name }}
                    </span>
                </div>

                <div class="flex space-x-3">
                    <button
                        v-if="isReply"
                        type="button"
                        @click="cancelReply"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Cancel
                    </button>
                    
                    <button
                        type="submit"
                        :disabled="loading || !form.content.trim()"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="loading" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Posting...
                        </span>
                        <span v-else>
                            {{ isReply ? 'Post Reply' : 'Post Comment' }}
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>

