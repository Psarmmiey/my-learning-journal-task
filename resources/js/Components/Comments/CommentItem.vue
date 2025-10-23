<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { formatDistanceToNow, parseISO } from 'date-fns'
import CommentForm from './CommentForm.vue'
import axios from 'axios'

const page = usePage()

const props = defineProps({
    comment: {
        type: Object,
        required: true,
    },
    blogPostId: {
        type: String,
        required: true,
    }
})

const emit = defineEmits(['comment-updated', 'comment-deleted', 'reply-added'])

// Reactive data
const isEditing = ref(false)
const showReplyForm = ref(false)
const editLoading = ref(false)
const editErrors = ref({})
const timeLeft = ref(0)

const editForm = reactive({
    content: props.comment.content
})

// Computed properties
const canEdit = computed(() => {
    return page.props.auth.user && 
           page.props.auth.user.id === props.comment.user?.id && 
           timeLeft.value > 0
})

const canDelete = computed(() => {
    return page.props.auth.user && 
           page.props.auth.user.id === props.comment.user?.id
})

// Methods
const formatDate = (dateString) => {
    try {
        return formatDistanceToNow(parseISO(dateString), { addSuffix: true })
    } catch {
        return dateString
    }
}

const calculateTimeLeft = () => {
    const createdAt = parseISO(props.comment.created_at)
    const now = new Date()
    const diffInMinutes = (now - createdAt) / (1000 * 60)
    const remaining = Math.max(0, 15 - Math.floor(diffInMinutes))
    timeLeft.value = remaining
}

const startEdit = () => {
    editForm.content = props.comment.content
    isEditing.value = true
    editErrors.value = {}
}

const cancelEdit = () => {
    isEditing.value = false
    editForm.content = props.comment.content
    editErrors.value = {}
}

const saveEdit = async () => {
    editLoading.value = true
    editErrors.value = {}

    try {
        const response = await axios.patch(`/comments/${props.comment.id}`, {
            content: editForm.content
        })
        
        emit('comment-updated', response.data.data)
        isEditing.value = false
    } catch (error) {
        if (error.response?.status === 422) {
            editErrors.value = error.response.data.errors || {}
        } else if (error.response?.status === 403) {
            editErrors.value = { content: 'You can only edit comments within 15 minutes of posting.' }
        } else {
            editErrors.value = { content: 'Failed to update comment. Please try again.' }
        }
    } finally {
        editLoading.value = false
    }
}

const deleteComment = async () => {
    if (!confirm('Are you sure you want to delete this comment?')) {
        return
    }

    try {
        await axios.delete(`/comments/${props.comment.id}`)
        emit('comment-deleted', props.comment.id)
    } catch (error) {
        alert('Failed to delete comment. Please try again.')
        console.error('Error deleting comment:', error)
    }
}

const toggleReply = () => {
    showReplyForm.value = !showReplyForm.value
}

const handleReplyAdded = (parentCommentId, newReply) => {
    showReplyForm.value = false
    emit('reply-added', parentCommentId, newReply)
}

// Lifecycle
onMounted(() => {
    calculateTimeLeft()
    // Update time left every minute
    setInterval(calculateTimeLeft, 60000)
})
</script>

<template>
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
        <!-- Comment Header -->
        <div class="flex items-start space-x-4">
            <!-- User Avatar -->
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                    <span class="text-white font-medium text-sm">
                        {{ comment.user?.name?.charAt(0).toUpperCase() }}
                    </span>
                </div>
            </div>

            <!-- Comment Content -->
            <div class="flex-1 min-w-0">
                <!-- User Info and Timestamp -->
                <div class="flex items-center space-x-2 mb-2">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ comment.user?.name }}
                    </h4>
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        {{ formatDate(comment.created_at) }}
                    </span>
                    <span v-if="comment.is_reply" class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded">
                        Reply
                    </span>
                </div>

                <!-- Comment Text (Edit Mode) -->
                <div v-if="isEditing" class="mb-4">
                    <textarea
                        v-model="editForm.content"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                    ></textarea>
                    <div v-if="editErrors.content" class="mt-1 text-sm text-red-600">
                        {{ editErrors.content }}
                    </div>
                    <div class="mt-2 flex space-x-2">
                        <button
                            @click="saveEdit"
                            :disabled="editLoading"
                            class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
                        >
                            {{ editLoading ? 'Saving...' : 'Save' }}
                        </button>
                        <button
                            @click="cancelEdit"
                            class="px-3 py-1 text-sm bg-gray-300 text-gray-700 rounded hover:bg-gray-400"
                        >
                            Cancel
                        </button>
                    </div>
                </div>

                <!-- Comment Text (View Mode) -->
                <div v-else class="mb-4">
                    <p class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ comment.content }}</p>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center space-x-4">
                    <!-- Reply Button -->
                    <button
                        v-if="$page.props.auth.user && !isEditing"
                        @click="toggleReply"
                        class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                    >
                        {{ showReplyForm ? 'Cancel' : 'Reply' }}
                    </button>

                    <!-- Edit Button (only for comment owner) -->
                    <button
                        v-if="canEdit && !isEditing"
                        @click="startEdit"
                        class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                    >
                        Edit
                    </button>

                    <!-- Delete Button (only for comment owner) -->
                    <button
                        v-if="canDelete && !isEditing"
                        @click="deleteComment"
                        class="text-sm text-red-500 hover:text-red-700"
                    >
                        Delete
                    </button>

                    <!-- Time left for editing -->
                    <span v-if="canEdit && timeLeft > 0" class="text-xs text-gray-400">
                        Edit available for {{ timeLeft }}m
                    </span>
                </div>

                <!-- Reply Form -->
                <div v-if="showReplyForm" class="mt-4">
                    <CommentForm
                        :blog-post-id="blogPostId"
                        :parent-comment-id="comment.id"
                        :parent-comment="comment"
                        :is-reply="true"
                        @reply-added="handleReplyAdded"
                        @cancel-reply="toggleReply"
                    />
                </div>

                <!-- Replies -->
                <div v-if="comment.replies && comment.replies.length > 0" class="mt-6 space-y-4">
                    <div class="border-l-2 border-gray-200 dark:border-gray-600 pl-4">
                        <CommentItem
                            v-for="reply in comment.replies"
                            :key="reply.id"
                            :comment="reply"
                            :blog-post-id="blogPostId"
                            @comment-updated="$emit('comment-updated', $event)"
                            @comment-deleted="$emit('comment-deleted', $event)"
                            @reply-added="$emit('reply-added', $event)"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

