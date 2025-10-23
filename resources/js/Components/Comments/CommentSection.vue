<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Link } from '@inertiajs/vue3'
import CommentForm from './CommentForm.vue'
import CommentItem from './CommentItem.vue'
import axios from 'axios'

const props = defineProps({
    blogPostId: {
        type: String,
        required: true,
    },
    commentsCount: {
        type: Number,
        default: 0,
    }
})

// Reactive data
const comments = ref([])
const loading = ref(false)
const error = ref(null)
const currentCommentsCount = ref(props.commentsCount)

// Computed properties
const topLevelComments = computed(() => {
    return comments.value.filter(comment => !comment.parent_id)
})

// Methods
const fetchComments = async () => {
    loading.value = true
    error.value = null
    
    try {
        const response = await axios.get(`/blog/${props.blogPostId}/comments`)
        comments.value = response.data.data || []
    } catch (err) {
        error.value = 'Failed to load comments'
        console.error('Error fetching comments:', err)
    } finally {
        loading.value = false
    }
}

const handleCommentAdded = (newComment) => {
    comments.value.unshift(newComment)
    currentCommentsCount.value++
}

const handleCommentUpdated = (updatedComment) => {
    const index = comments.value.findIndex(c => c.id === updatedComment.id)
    if (index !== -1) {
        comments.value[index] = updatedComment
    }
}

const handleCommentDeleted = (deletedCommentId) => {
    comments.value = comments.value.filter(c => c.id !== deletedCommentId)
    currentCommentsCount.value--
}

const handleReplyAdded = (parentCommentId, newReply) => {
    const parentComment = comments.value.find(c => c.id === parentCommentId)
    if (parentComment) {
        if (!parentComment.replies) {
            parentComment.replies = []
        }
        parentComment.replies.push(newReply)
        currentCommentsCount.value++
    }
}

// Watch for prop changes
watch(() => props.commentsCount, (newCount) => {
    currentCommentsCount.value = newCount
})

// Lifecycle
onMounted(() => {
    fetchComments()
})
</script>

<template>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Comments Header -->
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Comments ({{ currentCommentsCount }})
                </h3>
            </div>

            <!-- Comment Form (for authenticated users) -->
            <div v-if="$page.props.auth.user" class="mb-8">
                <CommentForm 
                    :blog-post-id="blogPostId" 
                    @comment-added="handleCommentAdded"
                />
            </div>

            <!-- Sign in prompt for guests -->
            <div v-else class="mb-8 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <p class="text-gray-600 dark:text-gray-400">
                    <Link href="/login" class="text-blue-600 hover:text-blue-700 font-medium">
                        Sign in
                    </Link>
                    to join the discussion.
                </p>
            </div>

            <!-- Comments List -->
            <div v-if="comments.length > 0" class="space-y-6">
                <CommentItem 
                    v-for="comment in topLevelComments" 
                    :key="comment.id"
                    :comment="comment"
                    :blog-post-id="blogPostId"
                    @comment-updated="handleCommentUpdated"
                    @comment-deleted="handleCommentDeleted"
                    @reply-added="handleReplyAdded"
                />
            </div>

            <!-- No Comments Message -->
            <div v-else class="text-center py-8">
                <p class="text-gray-500 dark:text-gray-400">
                    Be the first to comment on this post!
                </p>
            </div>
        </div>
    </div>
</template>