<script setup>
import { ref, reactive } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Textarea from '@/Components/Textarea.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: 'md',
    },
});

const emit = defineEmits(['close']);

const form = reactive({
    name: '',
    description: '',
    source_type: 'file', // Default to file upload
    source_url: '',
    content: '',
    files: [],
    processing: false,
    errors: {}
});

const fileInputRef = ref(null);
const selectedFiles = ref([]);
const uploadProgress = ref(0);
const showUploadProgress = ref(false);
const showSuccessNotification = ref(false);

const close = () => {
    resetForm();
    emit('close');
};

const resetForm = () => {
    form.name = '';
    form.description = '';
    form.source_type = 'file';
    form.source_url = '';
    form.content = '';
    form.files = [];
    form.errors = {};
    selectedFiles.value = [];
    uploadProgress.value = 0;
    showUploadProgress.value = false;
    showSuccessNotification.value = false;
};

const submitForm = () => {
    // Reset errors
    form.errors = {};
    
    // Create FormData for file upload
    const formData = new FormData();
    formData.append('name', form.name);
    formData.append('description', form.description);
    formData.append('source_type', form.source_type);
    
    if (form.source_type === 'link') {
        formData.append('source_url', form.source_url);
    } else if (form.source_type === 'manual') {
        formData.append('content', form.content);
    } else if (form.source_type === 'file' && form.files.length > 0) {
        for (let i = 0; i < form.files.length; i++) {
            formData.append(`files[${i}]`, form.files[i]);
        }
    }
    
    form.processing = true;
    
    // Use Axios for direct form submission with proper handling
    axios.post('/knowledge-bases', formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        onUploadProgress: (progressEvent) => {
            const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
            uploadProgress.value = percentCompleted;
            showUploadProgress.value = true;
        }
    })
    .then(response => {
        if (response.data.success) {
            // Reset form
            resetForm();
            showSuccessNotification.value = true;
            
            // Auto-hide success notification after 3 seconds
            setTimeout(() => {
                showSuccessNotification.value = false;
            }, 3000);
        }
    })
    .catch(error => {
        if (error.response && error.response.data && error.response.data.errors) {
            form.errors = error.response.data.errors;
        } else {
            form.errors = { general: ['An unexpected error occurred. Please try again.'] };
        }
    })
    .finally(() => {
        form.processing = false;
        showUploadProgress.value = false;
    });
};

const browseFiles = () => {
    if (fileInputRef.value) {
        fileInputRef.value.click();
    }
};

const handleFileSelection = (event) => {
    const files = event.target.files;
    if (!files.length) return;
    
    // Update the form files array
    form.files = Array.from(files);
    
    // Update the selected files display
    selectedFiles.value = Array.from(files).map(file => ({
        name: file.name,
        size: formatFileSize(file.size),
        type: file.type
    }));
    
    // Set source type to file
    form.source_type = 'file';
};

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const removeFile = (index) => {
    const newFiles = [...form.files];
    newFiles.splice(index, 1);
    form.files = newFiles;
    
    const newSelectedFiles = [...selectedFiles.value];
    newSelectedFiles.splice(index, 1);
    selectedFiles.value = newSelectedFiles;
};

const setSourceType = (type) => {
    form.source_type = type;
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto h-full w-full z-50" @click.self="close">

        
        <div class="bg-white rounded-lg shadow-xl max-w-lg mx-auto mt-20 overflow-hidden">

            
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Add New Knowledge Base</h2>
                    <button @click="close" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <!-- Success Message -->
                <div v-if="showSuccessNotification" class="mb-6 bg-orange-100 border-l-4 border-orange-500 text-orange-700 p-4 rounded-md" role="alert">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium">Success!</span>
                        <span class="ml-1">Knowledge base created successfully.</span>
                    </div>
                </div>

                <div class="space-y-6">
                    <p class="text-gray-700">
                        Knowledge bases provide additional intelligence to your assistants, allowing them to answer questions based on your team's information.
                    </p>

                    <!-- Knowledge Base Name -->
                    <div>
                        <InputLabel value="Name your knowledge base" />
                        <TextInput
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm bg-gray-50"
                            :disabled="form.processing"
                        />
                        <InputError v-if="form.errors.name" :message="form.errors.name" class="mt-2" />
                    </div>

                    <!-- Description -->
                    <div>
                        <InputLabel value="Description (optional)" />
                        <Textarea
                            v-model="form.description"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm bg-gray-50"
                            :disabled="form.processing"
                        />
                        <InputError v-if="form.errors.description" :message="form.errors.description" class="mt-2" />
                    </div>

                    <!-- Source Selection -->
                    <div>
                        <h3 class="text-lg font-medium text-orange-500 mb-3">Choose source</h3>
                        
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <button 
                                @click="setSourceType('file')"
                                :class="[
                                    'flex flex-col items-center justify-center p-4 border rounded-md',
                                    form.source_type === 'file' 
                                        ? 'border-orange-500 bg-orange-50' 
                                        : 'border-gray-300 bg-white hover:bg-gray-50'
                                ]"
                            >
                                <svg class="h-8 w-8 mb-2 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>Upload Files</span>
                            </button>
                            
                            <button 
                                @click="setSourceType('link')"
                                :class="[
                                    'flex flex-col items-center justify-center p-4 border rounded-md',
                                    form.source_type === 'link' 
                                        ? 'border-orange-500 bg-orange-50' 
                                        : 'border-gray-300 bg-white hover:bg-gray-50'
                                ]"
                            >
                                <svg class="h-8 w-8 mb-2 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                                <span>External Link</span>
                            </button>
                            
                            <button 
                                @click="setSourceType('manual')"
                                :class="[
                                    'flex flex-col items-center justify-center p-4 border rounded-md',
                                    form.source_type === 'manual' 
                                        ? 'border-orange-500 bg-orange-50' 
                                        : 'border-gray-300 bg-white hover:bg-gray-50'
                                ]"
                            >
                                <svg class="h-8 w-8 mb-2 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>Manual Entry</span>
                            </button>
                        </div>
                        
                        <!-- File Upload Section -->
                        <div v-if="form.source_type === 'file'" class="mt-4">
                            <input 
                                type="file" 
                                ref="fileInputRef" 
                                @change="handleFileSelection" 
                                class="hidden" 
                                multiple 
                                accept=".pdf,.docx,.txt,.csv,.xls,.xlsx,.doc,.json"
                            />
                            
                            <button 
                                @click="browseFiles"
                                class="w-full flex items-center justify-center px-4 py-3 bg-white border-2 border-dashed border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                                :disabled="form.processing"
                            >
                                <svg class="h-6 w-6 mr-2 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <span>Click to browse or drag files here</span>
                            </button>
                            
                            <p class="mt-2 text-sm text-gray-500">
                                Supported formats: PDF, DOCX, TXT, CSV, XLS, XLSX, DOC, JSON (Max 50MB)
                            </p>
                            
                            <!-- Selected Files List -->
                            <div v-if="selectedFiles.length > 0" class="mt-4 space-y-2">
                                <h4 class="font-medium">Selected Files:</h4>
                                <div v-for="(file, index) in selectedFiles" :key="index" class="flex items-center justify-between p-2 bg-gray-50 rounded-md">
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 mr-2 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium">{{ file.name }}</p>
                                            <p class="text-xs text-gray-500">{{ file.size }}</p>
                                        </div>
                                    </div>
                                </div>
                                <button 
                                    @click="removeFile(index)" 
                                    class="text-gray-400 hover:text-red-500"
                                    :disabled="form.processing"
                                >
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <InputError v-if="form.errors.files" :message="form.errors.files" class="mt-2" />
                    </div>
                    
                    <!-- Link Source Section -->
                    <div v-if="form.source_type === 'link'" class="mt-4">
                        <InputLabel value="External URL" />
                        <TextInput
                            v-model="form.source_url"
                            type="url"
                            placeholder="https://example.com/resource"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm bg-gray-50"
                            :disabled="form.processing"
                        />
                        <InputError v-if="form.errors.source_url" :message="form.errors.source_url" class="mt-2" />
                    </div>
                    
                    <!-- Manual Entry Section -->
                    <div v-if="form.source_type === 'manual'" class="mt-4">
                        <InputLabel value="Content" />
                        <Textarea
                            v-model="form.content"
                            placeholder="Enter your knowledge base content here..."
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm bg-gray-50"
                            rows="6"
                            :disabled="form.processing"
                        />
                        <InputError v-if="form.errors.content" :message="form.errors.content" class="mt-2" />
                    </div>
                </div>

                <!-- Upload Progress -->
                <div v-if="showUploadProgress" class="my-4">
                    <p class="text-sm font-medium mb-1">Uploading files... {{ uploadProgress }}%</p>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-orange-500 h-2.5 rounded-full" :style="{ width: uploadProgress + '%' }"></div>
                    </div>
                </div>
                
                <!-- General Error Message -->
                <div v-if="form.errors.general" class="mt-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                    <div class="flex">
                        <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <p class="font-medium">Error</p>
                            <div v-for="(error, index) in form.errors.general" :key="index" class="text-sm">{{ error }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-start space-x-3">
                    <button
                        type="button"
                        class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        @click="submitForm"
                        :disabled="form.processing || (form.source_type === 'file' && form.files.length === 0) || (form.source_type === 'link' && !form.source_url) || (form.source_type === 'manual' && !form.content) || !form.name"
                    >
                        <span v-if="!form.processing">Save</span>
                        <span v-else>Saving...</span>
                    </button>
                    <button
                        type="button"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        @click="close"
                        :disabled="form.processing"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
