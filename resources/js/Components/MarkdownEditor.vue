<script setup>
import { ref, computed, onMounted } from 'vue'
import MarkdownIt from 'markdown-it'
import DOMPurify from 'dompurify'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'

const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    label: {
        type: String,
        default: 'Content'
    },
    placeholder: {
        type: String,
        default: 'Write your content using Markdown...'
    },
    errorMessage: {
        type: String,
        default: ''
    },
    rows: {
        type: Number,
        default: 10
    },
    showPreview: {
        type: Boolean,
        default: true
    }
})

const emit = defineEmits(['update:modelValue'])

const markdown = ref('')
const showPreviewTab = ref(false)
const md = new MarkdownIt({
    html: false, // Disable HTML tags in source for security
    xhtmlOut: true,
    breaks: true,
    linkify: true,
    typographer: true
})

// Computed property for rendered HTML
const renderedHtml = computed(() => {
    if (!markdown.value) return ''
    const rawHtml = md.render(markdown.value)
    return DOMPurify.sanitize(rawHtml)
})

// Watch for external changes to modelValue
const updateValue = (value) => {
    markdown.value = value
    emit('update:modelValue', value)
}

// Initialize with prop value
onMounted(() => {
    markdown.value = props.modelValue
})

// Handle textarea input
const handleInput = (event) => {
    const value = event.target.value
    updateValue(value)
}

// Handle tab insertion in textarea
const handleKeydown = (event) => {
    if (event.key === 'Tab') {
        event.preventDefault()
        const textarea = event.target
        const start = textarea.selectionStart
        const end = textarea.selectionEnd
        
        // Insert tab character
        const newValue = markdown.value.substring(0, start) + '\t' + markdown.value.substring(end)
        updateValue(newValue)
        
        // Move cursor
        requestAnimationFrame(() => {
            textarea.selectionStart = textarea.selectionEnd = start + 1
        })
    }
}


</script>

<template>
    <div class="markdown-editor">
        <InputLabel :value="label" class="mb-2" />
        
        <!-- Tab buttons when preview is enabled -->
        <div v-if="showPreview" class="flex border-b border-gray-200 mb-2">
            <button
                type="button"
                @click="showPreviewTab = false"
                :class="[
                    'px-4 py-2 text-sm font-medium border-b-2 transition-colors',
                    !showPreviewTab
                        ? 'border-indigo-500 text-indigo-600'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
            >
                Edit
            </button>
            <button
                type="button"
                @click="showPreviewTab = true"
                :class="[
                    'px-4 py-2 text-sm font-medium border-b-2 transition-colors',
                    showPreviewTab
                        ? 'border-indigo-500 text-indigo-600'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
            >
                Preview
            </button>
        </div>
        
        <!-- Editor content -->
        <div class="relative">
            <!-- Textarea for editing -->
            <textarea
                v-show="!showPreview || !showPreviewTab"
                :value="markdown"
                @input="handleInput"
                @keydown="handleKeydown"
                :rows="rows"
                :placeholder="placeholder"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono text-sm resize-vertical"
            ></textarea>
            
            <!-- Preview pane -->
            <div
                v-if="showPreview && showPreviewTab"
                :class="[
                    'w-full px-3 py-2 border border-gray-300 rounded-md bg-white prose prose-sm max-w-none',
                    `min-h-[${rows * 1.5}rem]`
                ]"
                v-html="renderedHtml"
            ></div>
        </div>
        
        <!-- Markdown help -->
        <div class="mt-2 text-xs text-gray-500">
            <details class="cursor-pointer">
                <summary class="hover:text-gray-700">Markdown formatting help</summary>
                <div class="mt-2 space-y-1 bg-gray-50 p-3 rounded">
                    <div><code>**bold**</code> → <strong>bold</strong></div>
                    <div><code>*italic*</code> → <em>italic</em></div>
                    <div><code># Heading 1</code></div>
                    <div><code>## Heading 2</code></div>
                    <div><code>[link text](url)</code></div>
                    <div><code>![alt text](image-url)</code></div>
                    <div><code>`code`</code> → <code>code</code></div>
                    <div><code>```</code> for code blocks</div>
                    <div><code>- List item</code></div>
                    <div><code>1. Numbered item</code></div>
                    <div><code>&gt; Quote</code></div>
                </div>
            </details>
        </div>
        
        <InputError :message="errorMessage" class="mt-2" />
    </div>
</template>

<style scoped>
/* Ensure proper syntax highlighting in code blocks */
.prose pre {
    @apply bg-gray-100 p-4 rounded overflow-x-auto;
}

.prose code {
    @apply bg-gray-100 px-1 rounded text-sm;
}

.prose pre code {
    @apply bg-transparent px-0;
}

/* Style for blockquotes */
.prose blockquote {
    @apply border-l-4 border-gray-300 pl-4 italic text-gray-600;
}

/* Style for tables */
.prose table {
    @apply w-full border-collapse;
}

.prose th,
.prose td {
    @apply border border-gray-300 px-3 py-2;
}

.prose th {
    @apply bg-gray-100 font-medium;
}
</style>