<script setup>
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Dropdown from '@/Components/Dropdown.vue';
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

const phoneNumbers = ref([
    { id: 1, name: 'Showroom (02) 9201 8888' },
    { id: 2, name: 'Service (02) 9200 9966' },
]);

const agents = ref([
    { id: 1, name: 'Steve (upbeat young professional with Aussie accent)' },
    { id: 2, name: 'Sarah (friendly professional with British accent)' },
]);

const knowledgeBases = ref([
    { id: 1, name: 'Inventory List Summer 2025' },
    { id: 2, name: 'Service Department FAQ' },
]);

const selectedPhoneNumber = ref(phoneNumbers.value[0]);
const selectedAgent = ref(agents.value[0]);
const selectedKnowledgeBase = ref(knowledgeBases.value[0]);
const additionalInstructions = ref('');
const showSuccessNotification = ref(false);
const errors = ref({});

const close = () => {
    showSuccessNotification.value = false;
    errors.value = {};
    emit('close');
};

const save = () => {
    // Reset errors
    errors.value = {};
    
    // Validate form (example validation)
    if (!selectedPhoneNumber.value) {
        errors.value.phoneNumber = 'Please select a phone number';
        return;
    }
    
    // Handle save logic here
    // For demo purposes, show success notification
    showSuccessNotification.value = true;
    
    // Auto-hide success notification after 3 seconds
    setTimeout(() => {
        showSuccessNotification.value = false;
    }, 3000);
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto h-full w-full z-50" @click.self="close">
        <div class="bg-white rounded-lg shadow-xl max-w-lg mx-auto mt-20 overflow-hidden">
            <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Add New Line</h2>
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
                    <span class="ml-1">New line created successfully.</span>
                </div>
            </div>
            
            <!-- General Error Message -->
            <div v-if="Object.keys(errors).length > 0" class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                <div class="flex">
                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        <p class="font-medium">Please fix the following errors:</p>
                        <ul class="mt-1 list-disc list-inside text-sm">
                            <li v-for="(error, field) in errors" :key="field">{{ error }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Phone Number Selection -->
                <div>
                    <InputLabel value="Choose phone number" />
                    <div class="relative mt-1">
                        <div class="border border-gray-300 rounded-md p-3 bg-gray-50 flex justify-between items-center cursor-pointer">
                            <span>{{ selectedPhoneNumber.name }}</span>
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Agent Selection -->
                <div>
                    <InputLabel value="Choose agent" />
                    <div class="relative mt-1">
                        <div class="border border-gray-300 rounded-md p-3 bg-gray-50 flex justify-between items-center cursor-pointer">
                            <span>{{ selectedAgent.name }}</span>
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Knowledge Base Selection -->
                <div>
                    <InputLabel value="Choose Knowledge Base" />
                    <div class="relative mt-1">
                        <div class="border border-gray-300 rounded-md p-3 bg-gray-50 flex justify-between items-center cursor-pointer">
                            <span>{{ selectedKnowledgeBase.name }}</span>
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex justify-end mt-1">
                        <button class="flex items-center text-orange-500 hover:text-orange-600">
                            <svg class="h-5 w-5 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                            </svg>
                            <span>Preview</span>
                        </button>
                    </div>
                </div>

                <!-- Additional Instructions -->
                <div>
                    <InputLabel value="Additional instructions" />
                    <Textarea
                        v-model="additionalInstructions"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                        placeholder="Optional"
                    />
                    <p class="mt-2 text-sm text-gray-600">
                        You can provide additional instructions for agent to follow, eg. don't book appointments after 4pm, avoid certain words and more.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-start space-x-3">
                    <button
                        type="button"
                        class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                        @click="save"
                    >
                        Save
                    </button>
                    <button
                        type="button"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                        @click="close"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
        </div>
    </div>
</template>
