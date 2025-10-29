<script setup>
import { ref, watch } from 'vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => []
    },
    label: {
        type: String,
        default: 'Tags'
    },
    placeholder: {
        type: String,
        default: 'Enter tags separated by commas or press Enter'
    },
    errorMessage: {
        type: String,
        default: ''
    }
})

const emit = defineEmits(['update:modelValue'])

const inputValue = ref('')
const tags = ref([...props.modelValue])

// Watch for external changes to modelValue
watch(() => props.modelValue, (newValue) => {
    tags.value = [...newValue]
}, { deep: true })

// Emit changes to parent
watch(tags, (newTags) => {
    emit('update:modelValue', newTags)
}, { deep: true })

const addTag = (tagText) => {
    const trimmedTag = tagText.trim()
    if (trimmedTag && !tags.value.includes(trimmedTag)) {
        tags.value.push(trimmedTag)
    }
}

const removeTag = (index) => {
    tags.value.splice(index, 1)
}

const handleKeydown = (event) => {
    if (event.key === 'Enter' || event.key === ',') {
        event.preventDefault()
        addTag(inputValue.value)
        inputValue.value = ''
    } else if (event.key === 'Backspace' && inputValue.value === '' && tags.value.length > 0) {
        // Remove last tag if input is empty and backspace is pressed
        removeTag(tags.value.length - 1)
    }
}

const handleBlur = () => {
    if (inputValue.value.trim()) {
        addTag(inputValue.value)
        inputValue.value = ''
    }
}

const handlePaste = (event) => {
    event.preventDefault()
    const pastedText = (event.clipboardData || window.clipboardData).getData('text')
    const pastedTags = pastedText.split(/[,\n\r]+/).map(tag => tag.trim()).filter(tag => tag)
    pastedTags.forEach(tag => addTag(tag))
}
</script>

<template>
    <div>
        <InputLabel :for="`tag-input-${Math.random()}`" :value="label" />
        
        <div class="mt-1 min-h-[42px] w-3/4 rounded-md border border-gray-300 bg-white p-2 shadow-sm focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500">
            <div class="flex flex-wrap gap-1">
                <!-- Display tags -->
                <span
                    v-for="(tag, index) in tags"
                    :key="index"
                    class="inline-flex items-center gap-1 rounded-full bg-indigo-100 px-3 py-1 text-sm text-indigo-800">
                    {{ tag }}
                    <button
                        type="button"
                        @click="removeTag(index)"
                        class="hover:text-indigo-600 focus:outline-none">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </span>
                
                <!-- Input field -->
                <input
                    v-model="inputValue"
                    type="text"
                    :placeholder="tags.length === 0 ? placeholder : ''"
                    class="flex-1 min-w-[120px] border-0 bg-transparent p-0 text-sm outline-none focus:ring-0"
                    @keydown="handleKeydown"
                    @blur="handleBlur"
                    @paste="handlePaste"
                />
            </div>
        </div>
        
        <div v-if="tags.length > 0" class="mt-1 text-xs text-gray-500">
            {{ tags.length }} tag{{ tags.length === 1 ? '' : 's' }}
        </div>
        
        <InputError :message="errorMessage" class="mt-2" />
    </div>
</template>