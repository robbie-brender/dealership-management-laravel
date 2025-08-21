<script setup>
import { ref, onMounted, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    callLog: Object,
});

// Mock conversation data to match the screenshot exactly
const mockConversation = [
    { time: '0:00', speaker: 'Sarah', role: 'AI Assistant', text: 'Good morning, thank you for calling Premier Toyota Service. This is Sarah speaking, how can I help you today?' },
    { time: '0:10', speaker: 'Robert Lee', role: 'Customer', text: 'Hi Sarah, I\'d like to schedule an oil change for my car, please.' },
    { time: '0:00', speaker: 'Sarah', role: 'AI Assistant', text: 'Absolutely, I\'d be happy to help you with that. Can I get the year, make, and model of your vehicle?' },
    { time: '0:10', speaker: 'Robert Lee', role: 'Customer', text: 'It\'s a 2018 Toyota Camry. My name is Mike Johnson, and my number is 04123 890 098' },
    { time: '0:00', speaker: 'Sarah', role: 'AI Assistant', text: 'Thank you, Mr. Johnson. Now, what day were you looking to bring your Camry in for the oil change?' },
    { time: '0:10', speaker: 'Robert Lee', role: 'Customer', text: 'I was hoping for sometime next week if possible. What do you have available?' },
    { time: '0:00', speaker: 'Sarah', role: 'AI Assistant', text: 'Let me check our schedule for you!' },
    { time: '0:00', speaker: 'Sarah', role: 'AI Assistant', text: 'I have availability on Wednesday afternoon, 1:30PM. Will this work for you?' },
    { time: '0:10', speaker: 'Robert Lee', role: 'Customer', text: 'Customer: 1:30 PM would be perfect.' },
    { time: '0:00', speaker: 'Sarah', role: 'AI Assistant', text: 'Excellent! I have you scheduled for Friday, July 25th at 1:30 PM for an oil change on your 2018 Toyota Camry. Will you be waiting here at the dealership while we service your vehicle?' },
    { time: '0:10', speaker: 'Robert Lee', role: 'Customer', text: 'Yes, I\'ll be waiting there.' }
];

// Audio player state
const audioPlayer = ref(null);
const isPlaying = ref(false);
const currentTime = ref(0);
const duration = ref(0);

// Toggle audio play/pause
const toggleAudio = () => {
    if (!audioPlayer.value) return;
    
    if (isPlaying.value) {
        audioPlayer.value.pause();
    } else {
        audioPlayer.value.play();
    }
    isPlaying.value = !isPlaying.value;
};

// Update audio time
const updateTime = () => {
    if (!audioPlayer.value) return;
    currentTime.value = audioPlayer.value.currentTime;
};

// Set up audio event listeners when component is mounted
const setupAudioListeners = () => {
    if (!audioPlayer.value) return;
    
    audioPlayer.value.addEventListener('timeupdate', updateTime);
    audioPlayer.value.addEventListener('loadedmetadata', () => {
        duration.value = audioPlayer.value.duration;
    });
    audioPlayer.value.addEventListener('ended', () => {
        isPlaying.value = false;
        currentTime.value = 0;
    });
};

// Use real conversation data if available, otherwise use mock data
const conversation = ref([]);

// Check if recording URL exists and is valid
const hasRecording = computed(() => {
    return props.callLog && props.callLog.recording_url && props.callLog.recording_url.trim() !== '';
});

// Check if call is in progress
const isCallInProgress = computed(() => {
    return props.callLog && (props.callLog.status === 'initiated' || props.callLog.status === 'in-progress');
});

// Initialize audio player when component is mounted
onMounted(() => {
    setupAudioListeners();
    
    // Debug the call log data
    console.log('Call Log Data:', props.callLog);
    if (props.callLog.vapi_analysis) {
        console.log('VAPI Analysis Type:', typeof props.callLog.vapi_analysis);
        console.log('VAPI Analysis Content:', props.callLog.vapi_analysis);
    }
});

// Parse transcript from database if available
if (props.callLog && props.callLog.transcript) {
    try {
        console.log('Parsing transcript:', props.callLog.transcript);
        
        // First check if the transcript is a JSON string that needs to be parsed
        let transcriptData = props.callLog.transcript;
        if (typeof transcriptData === 'string' && (transcriptData.startsWith('[') || transcriptData.startsWith('{'))) {
            try {
                transcriptData = JSON.parse(transcriptData);
                console.log('Parsed JSON transcript:', transcriptData);
            } catch (jsonError) {
                console.warn('Failed to parse transcript as JSON, treating as plain text:', jsonError);
            }
        }
        
        // Handle different transcript formats
        if (Array.isArray(transcriptData)) {
            // If it's already an array of conversation turns
            conversation.value = transcriptData.map(turn => ({
                time: turn.time || '0:00',
                speaker: turn.speaker || (turn.role === 'assistant' ? 'AI Assistant' : 'Customer'),
                role: turn.role === 'assistant' ? 'AI Assistant' : 'Customer',
                text: turn.text || turn.content || ''
            }));
        } else if (typeof transcriptData === 'string') {
            // Parse as plain text with line breaks
            const lines = transcriptData.split('\n');
            conversation.value = []; // Reset conversation array
            
            // Special handling for AI: and User: format (common in Vapi transcripts)
            lines.forEach(line => {
                const aiMatch = line.match(/^AI:\s*(.*)$/i);
                const userMatch = line.match(/^User:\s*(.*)$/i);
                
                if (aiMatch) {
                    conversation.value.push({
                        time: '0:00',
                        speaker: 'AI Assistant',
                        role: 'AI Assistant',
                        text: aiMatch[1].trim()
                    });
                } else if (userMatch) {
                    conversation.value.push({
                        time: '0:00',
                        speaker: 'Customer',
                        role: 'Customer',
                        text: userMatch[1].trim()
                    });
                } else {
                    // If line doesn't match AI: or User: pattern, try other patterns
                    const speakerMatch = 
                        line.match(/^(\d+:\d+)?\s*([^:]+):\s*(.*)$/) || // Time Speaker: Text
                        line.match(/^([^:]+):\s*(.*)$/);                  // Speaker: Text
                    
                    if (speakerMatch) {
                        if (speakerMatch.length === 4) {
                            // Format with time
                            const [, time, speaker, text] = speakerMatch;
                            conversation.value.push({
                                time: time || '0:00',
                                speaker: speaker.trim(),
                                role: speaker.toLowerCase().includes('ai') || 
                                     speaker.toLowerCase().includes('assistant') ? 'AI Assistant' : 'Customer',
                                text: text.trim()
                            });
                        } else {
                            // Format without time
                            const [, speaker, text] = speakerMatch;
                            conversation.value.push({
                                time: '0:00',
                                speaker: speaker.trim(),
                                role: speaker.toLowerCase().includes('ai') || 
                                     speaker.toLowerCase().includes('assistant') ? 'AI Assistant' : 'Customer',
                                text: text.trim()
                            });
                        }
                    } else if (conversation.value.length > 0) {
                        // If this line doesn't match any pattern, append to the last message
                        const lastMessage = conversation.value[conversation.value.length - 1];
                        lastMessage.text += ' ' + line.trim();
                    }
                }
            });
        }
        
        // If we couldn't parse any conversation turns, check if call is in progress
        if (conversation.value.length === 0) {
            if (isCallInProgress.value) {
                // For in-progress calls, show a message that the call is ongoing
                conversation.value = [{
                    time: '0:00',
                    speaker: 'System',
                    role: 'System',
                    text: 'Call is currently in progress. Transcript will be available when the call is completed.'
                }];
            } else {
                console.warn('No conversation turns parsed, using mock data');
                conversation.value = mockConversation;
            }
        }
    } catch (e) {
        console.error('Error parsing transcript:', e);
        // Check if call is in progress before using mock data
        if (isCallInProgress.value) {
            conversation.value = [{
                time: '0:00',
                speaker: 'System',
                role: 'System',
                text: 'Call is currently in progress. Transcript will be available when the call is completed.'
            }];
        } else {
            // Use mock data as fallback for completed calls
            conversation.value = mockConversation;
        }
    }
} else {
    // Check if call is in progress
    if (isCallInProgress.value) {
        conversation.value = [{
            time: '0:00',
            speaker: 'System',
            role: 'System',
            text: 'Call is currently in progress. Transcript will be available when the call is completed.'
        }];
    } else {
        console.log('No transcript available, using mock data');
        // Use mock data if no transcript is available and call is completed
        conversation.value = mockConversation;
    }
}

const getStatusClass = (status) => {
    switch (status) {
        case 'completed':
            return 'bg-green-50 text-green-700';
        case 'in-progress':
            return 'bg-blue-50 text-blue-700';
        case 'failed':
            return 'bg-red-50 text-red-700';
        default:
            return 'bg-gray-50 text-gray-700';
    }
};

const formatPhoneNumber = (phoneNumber) => {
    if (!phoneNumber) return 'Unknown';
    
    // Format phone number as (XXX) XXX-XXXX
    const cleaned = ('' + phoneNumber).replace(/\D/g, '');
    const match = cleaned.match(/^(\d{3})(\d{3})(\d{4})$/);
    if (match) {
        return '(' + match[1] + ') ' + match[2] + '-' + match[3];
    }
    return phoneNumber;
};

// Format duration for display
const formatDuration = (seconds) => {
    if (!seconds) return '0:00';
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins}:${secs.toString().padStart(2, '0')}`;
};

// Format time for audio player display
const formatTime = (seconds) => {
    if (!seconds || isNaN(seconds)) return '0:00';
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins}:${secs.toString().padStart(2, '0')}`;
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: '2-digit', month: '2-digit', day: '2-digit' });
};

const formatTimeString = (dateString) => {
    if (!dateString) return '';
    
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
};

// Get customer name from metadata or use a default
const getCustomerName = () => {
    if (props.callLog.metadata && props.callLog.metadata.customer && props.callLog.metadata.customer.name) {
        return props.callLog.metadata.customer.name;
    }
    return 'Customer';
};

// Get call summary from vapi_summary field or fallback to metadata or default
const getCallSummary = () => {
    // For initiated or in-progress calls, show a different message
    if (props.callLog.status === 'initiated' || props.callLog.status === 'in-progress') {
        return 'Call is currently in progress. Summary will be available when the call is completed.';
    }
    
    // Check for vapi_summary first (preferred source)
    if (props.callLog.vapi_summary) {
        // If it's a string, return it directly
        if (typeof props.callLog.vapi_summary === 'string') {
            return props.callLog.vapi_summary;
        }
        // If it's an array or object, try to extract meaningful content
        if (typeof props.callLog.vapi_summary === 'object') {
            // If it has a summary property, use that
            if (props.callLog.vapi_summary.summary) {
                return props.callLog.vapi_summary.summary;
            }
            // Otherwise, convert the object to a string
            return JSON.stringify(props.callLog.vapi_summary);
        }
    }
    
    // Fall back to metadata if vapi_summary is not available
    if (props.callLog.metadata && props.callLog.metadata.summary) {
        return props.callLog.metadata.summary;
    }
    
    // Default fallback
    return 'No summary available for this call.';
};


// Get frustration level from metadata or use a default
const getFrustrationLevel = () => {
    // For initiated or in-progress calls, show a dash
    if (props.callLog.status === 'initiated' || props.callLog.status === 'in-progress') {
        return '-';
    }
    
    if (props.callLog.metadata && props.callLog.metadata.frustration) {
        return props.callLog.metadata.frustration;
    }
    return 1;
};

// Format the call status for display
const getDisplayStatus = (status) => {
    if (status === 'completed') {
        return 'Resolved';
    }
    return status.charAt(0).toUpperCase() + status.slice(1);
};

// Get VAPI analysis data for display
const getVapiAnalysis = () => {
    // For initiated or in-progress calls, return empty
    if (props.callLog.status === 'initiated' || props.callLog.status === 'in-progress') {
        return null;
    }
    
    console.log('VAPI Analysis raw:', props.callLog.vapi_analysis);
    
    // Check if vapi_analysis exists and is not empty
    if (props.callLog.vapi_analysis) {
        // Laravel's array casting should have already converted this to an object
        // But sometimes it might still be a string if the data was manually inserted
        if (typeof props.callLog.vapi_analysis === 'string') {
            try {
                // Try to parse it if it's a string
                const parsed = JSON.parse(props.callLog.vapi_analysis);
                console.log('Parsed VAPI analysis:', parsed);
                return parsed;
            } catch (e) {
                console.error('Error parsing VAPI analysis:', e);
                // If it's not valid JSON but still a string, return it as is
                return { summary: props.callLog.vapi_analysis };
            }
        }
        
        // If it's already an object (which is the expected case with Laravel's array casting)
        console.log('VAPI analysis is already an object');
        return props.callLog.vapi_analysis;
    }
    
    console.log('No VAPI analysis found');
    return null;
};

// Format the VAPI analysis for display
const formattedVapiAnalysis = computed(() => {
    // Get the analysis data
    const analysis = getVapiAnalysis();
    console.log('Analysis for formatting:', analysis);
    
    // If no analysis data is available, return null
    if (!analysis) return null;
    
    // Extract relevant information from the analysis
    let result = '';
    
    // Handle string data (in case it wasn't parsed properly)
    if (typeof analysis === 'string') {
        return analysis;
    }
    
    // Check for summary field (from the example JSON structure)
    if (analysis.summary) {
        result += `${analysis.summary}`;
    }
    
    // Check for successEvaluation field
    if (analysis.successEvaluation !== undefined) {
        const successValue = analysis.successEvaluation;
        // Handle both string and boolean values
        const isSuccess = successValue === true || successValue === 'true';
        result += `\nCall Success: ${isSuccess ? 'Yes' : 'No'}`;
    }
    
    // Also check for the original structure fields we were looking for
    
    // Add vehicle information if available
    if (analysis.vehicle) {
        const vehicle = analysis.vehicle;
        result += `\nMake: ${vehicle.make || 'Unknown'}, Year: ${vehicle.year || 'Unknown'}, Model: ${vehicle.model || 'Unknown'}`;
    }
    
    // Add customer type if available
    if (analysis.customer_type) {
        result += `\nCustomer: ${analysis.customer_type}`;
    }
    
    // Add work type if available
    if (analysis.work_type) {
        result += `\nWork type: ${analysis.work_type}`;
    }
    
    // Add preferred time if available
    if (analysis.preferred_time) {
        result += `\nStated preferred time: ${analysis.preferred_time}`;
    }
    
    // Add reasoning if available
    if (analysis.reasoning) {
        result += `\n${analysis.reasoning}`;
    }
    
    // Add additional instructions if available
    if (analysis.additional_instructions) {
        result += `\n${analysis.additional_instructions}`;
    }
    
    return result || 'No detailed analysis available for this call.';
});

// Format analysis paragraph with bold text for labels
const formatAnalysisParagraph = (paragraph) => {
    if (!paragraph) return '';
    
    // Replace patterns like "Make: Toyota" with "<strong>Make:</strong> Toyota"
    return paragraph.replace(/([A-Za-z\s]+):\s/g, '<strong>$1:</strong> ');
};

// Extract customer name from analysis or metadata
const customerName = computed(() => {
    const analysis = getVapiAnalysis();
    
    // Try to get name from analysis
    if (analysis && analysis.customer_name) {
        return analysis.customer_name;
    }
    
    // Try to extract name from transcript or summary
    if (props.callLog.vapi_summary && typeof props.callLog.vapi_summary === 'string') {
        // Look for name patterns like "caller: John" or "customer: John"
        const nameMatch = props.callLog.vapi_summary.match(/(?:caller|customer):\s*([A-Za-z\s]+)/i);
        if (nameMatch && nameMatch[1]) {
            return nameMatch[1].trim();
        }
    }
    
    // Try to get from metadata
    if (props.callLog.metadata && props.callLog.metadata.customer_name) {
        return props.callLog.metadata.customer_name;
    }
    
    // Extract from summary if it contains a name
    const summary = getCallSummary();
    if (summary) {
        const nameMatch = summary.match(/([A-Za-z]+)\s+called/i);
        if (nameMatch && nameMatch[1]) {
            return nameMatch[1];
        }
    }
    
    return null;
});

// Extract vehicle information
const callVehicleInfo = computed(() => {
    const analysis = getVapiAnalysis();
    
    if (analysis) {
        // Check for vehicle info in the structured format
        if (analysis.vehicle) {
            const vehicle = analysis.vehicle;
            return `${vehicle.year || ''} ${vehicle.make || ''} ${vehicle.model || ''}`.trim() || null;
        }
        
        // Check for vehicle info in the summary
        if (analysis.summary) {
            const carMatch = analysis.summary.match(/(?:Toyota|Honda|Ford|BMW|Mercedes|Audi|Lexus|Nissan)\s+([A-Za-z0-9]+)/i);
            if (carMatch) {
                return carMatch[0];
            }
        }
    }
    
    return null;
});

// Extract work type
const callWorkType = computed(() => {
    const analysis = getVapiAnalysis();
    
    if (analysis) {
        // Check for work_type in structured format
        if (analysis.work_type) {
            return analysis.work_type;
        }
        
        // Check for common service types in summary
        if (analysis.summary) {
            const serviceTypes = ['oil change', 'service', 'repair', 'maintenance', 'inspection', 'tire', 'battery', 'brake'];
            for (const type of serviceTypes) {
                if (analysis.summary.toLowerCase().includes(type)) {
                    return type.charAt(0).toUpperCase() + type.slice(1);
                }
            }
        }
    }
    
    return null;
});

// Extract preferred time
const callPreferredTime = computed(() => {
    const analysis = getVapiAnalysis();
    
    if (analysis) {
        // Check for preferred_time in structured format
        if (analysis.preferred_time) {
            return analysis.preferred_time;
        }
    }
    
    return null;
});

// Format department name with first letter capitalized
const formatDepartment = (department) => {
    if (!department) return 'General Inquiry';
    
    // Capitalize first letter
    return department.charAt(0).toUpperCase() + department.slice(1);
};

</script>

<template>
    <Head title="Call Log Details" />

    <AuthenticatedLayout>
        <div class="p-6 bg-white rounded-lg shadow">
            <!-- Header -->
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-xl font-medium text-gray-800">Incoming call with {{ getCustomerName() }}</h2>
                    <div class="flex items-center text-sm text-gray-500 mt-1">
                        <span>{{ formatDate(callLog.call_started_at) }}</span>
                        <span class="mx-2">•</span>
                        <span>{{ formatTimeString(callLog.call_started_at) }}</span>
                        <span class="mx-2">•</span>
                        <a href="#" class="text-blue-500 hover:underline">{{ getCustomerName() }}'s profile → CRM</a>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <!-- Status badge -->
                    <div class="flex items-center px-3 py-1 rounded-md bg-green-50 text-green-700 text-sm">
                        <svg class="w-4 h-4 mr-1.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        {{ getDisplayStatus(callLog.status) }}
                    </div>
                    
                    <!-- Frustration badge -->
                    <div class="flex items-center px-3 py-1 text-green-600 text-sm">
                        <svg class="w-5 h-5 mr-1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                            <path d="M8 14C8 14 9.5 16 12 16C14.5 16 16 14 16 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="9" cy="10" r="1.5" fill="currentColor"/>
                            <circle cx="15" cy="10" r="1.5" fill="currentColor"/>
                        </svg>
                        Frustration: {{ getFrustrationLevel() }}
                    </div>
                    
                    
                    <!-- Department badge -->
                    <div class="flex items-center px-3 py-1 text-purple-700 text-sm" v-if="callLog.department">
                        <svg class="w-4 h-4 mr-1.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9 10C10.1046 10 11 9.10457 11 8C11 6.89543 10.1046 6 9 6C7.89543 6 7 6.89543 7 8C7 9.10457 7.89543 10 9 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M15 8H15.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M17 14H17.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M13 12H13.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Department: {{ callLog.department ? callLog.department.charAt(0).toUpperCase() + callLog.department.slice(1) : '-' }}
                    </div>
                    
                    <!-- Manually transfer dropdown -->
                    <div class="relative flex items-center px-3 py-1 rounded-md bg-gray-50 text-gray-700 text-sm cursor-pointer">
                        Manually transfer to
                        <svg class="w-4 h-4 ml-1.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    
                    <!-- Flag button -->
                    <div class="flex items-center px-3 py-1 rounded-md bg-gray-50 text-gray-700 text-sm cursor-pointer">
                        <svg class="w-4 h-4 mr-1.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 15S4 9 12 9 20 4 20 4V18S12 14 4 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M4 22V4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        Flag
                    </div>
                </div>
            </div>

            <!-- Call summary and Recording side by side -->
            <div class="flex flex-col md:flex-row gap-6 mb-6">
                <!-- Call summary - Left side -->
                <div class="flex-1 w-full md:w-1/2">
                    <h3 class="text-sm font-medium text-orange-400 mb-2">Call summary:</h3>
                    <p class="text-sm text-gray-700">{{ getCallSummary() }}</p>
                </div>

                <!-- Recording - Right side -->
                <div class="flex-1 w-full md:w-1/2">
                    <h3 class="text-sm font-medium text-orange-400 mb-2">Recording:</h3>
                    <div class="p-3 flex items-center">
                        <!-- Add audio element with controls hidden -->
                        <audio v-if="hasRecording" ref="audioPlayer" :src="callLog.recording_url" preload="metadata" class="hidden"></audio>
                        
                        <button @click="toggleAudio" :disabled="!hasRecording" :class="['h-8 w-8 rounded-full flex items-center justify-center', hasRecording ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-400 cursor-not-allowed']">
                            <svg v-if="!isPlaying" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="5 3 19 12 5 21 5 3"></polygon>
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="6" y="4" width="4" height="16"></rect>
                                <rect x="14" y="4" width="4" height="16"></rect>
                            </svg>
                        </button>
                        <div class="flex-1 mx-4">
                            <!-- Audio waveform visualization -->
                            <div class="h-10 flex items-center">
                                <div class="w-full h-10 relative">
                                    <!-- Progress overlay -->
                                    <div v-if="hasRecording" class="absolute top-0 left-0 h-full bg-orange-100 opacity-30 pointer-events-none" 
                                         :style="{ width: `${(currentTime / (duration || 1)) * 100}%` }"></div>
                                    <!-- SVG waveform -->
                                    <svg width="100%" height="41" viewBox="0 0 628 41" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet">
                                        <rect x="0.244141" y="17.6218" width="3" height="6" fill="#FF8858"/>
                                        <rect x="7.24414" y="17.6218" width="3" height="6" fill="#FF8858"/>
                                        <rect x="14.2441" y="14.6218" width="3" height="12" fill="#FF8858"/>
                                        <rect x="21.2441" y="10.6218" width="3" height="20" fill="#FF8858"/>
                                        <rect x="28.2441" y="8.62183" width="3" height="24" fill="#FF8858"/>
                                        <rect x="35.2441" y="13.6218" width="3" height="14" fill="#FF8858"/>
                                        <rect x="42" y="15.6218" width="3" height="9" fill="#FF8858"/>
                                        <rect x="49.2441" y="8.62183" width="3" height="24" fill="#FF8858"/>
                                        <rect x="56.2441" y="14.6218" width="3" height="12" fill="#FF8858"/>
                                        <rect x="63.2441" y="17.6218" width="3" height="6" fill="#FF8858"/>
                                        <rect x="70.2441" y="14.6218" width="3" height="12" fill="#FF8858"/>
                                        <rect x="77.2441" y="10.6218" width="3" height="20" fill="#FF8858"/>
                                        <rect x="84" y="0.621826" width="3" height="40" fill="#FF8858"/>
                                        <rect x="91.2441" y="2.62183" width="3" height="35" fill="#FF8858"/>
                                        <rect x="98.2441" y="10.6218" width="3" height="20" fill="#FF8858"/>
                                        <rect x="105.244" y="4.62183" width="3" height="32" fill="#FF8858"/>
                                        <rect x="112.244" y="10.6218" width="3" height="20" fill="#FF8858"/>
                                        <rect x="119.244" y="13.6218" width="3" height="14" fill="#FF8858"/>
                                        <rect x="126.244" y="14.6218" width="3" height="12" fill="#FF8858"/>
                                        <rect x="133.244" y="8.62183" width="3" height="24" fill="#FF8858"/>
                                        <rect x="140.244" y="11.6218" width="3" height="17" fill="#FF8858"/>
                                        <rect x="147.244" y="14.6218" width="3" height="12" fill="#FF8858"/>
                                        <rect x="154.244" y="17.6218" width="3" height="6" fill="#FF8858"/>
                                        <rect x="161.244" y="17.6218" width="3" height="6" fill="#FF8858"/>
                                        <rect x="287.244" y="17.6218" width="3" height="6" fill="#FF8858"/>
                                        <rect x="413.244" y="17.6218" width="3" height="6" fill="#FF8858"/>
                                        <rect x="168.244" y="17.6218" width="3" height="6" fill="#FF8858"/>
                                        <rect x="294.244" y="17.6218" width="3" height="6" fill="#FF8858"/>
                                        <rect x="420.244" y="17.6218" width="3" height="6" fill="#FF8858"/>
                                        <rect x="175.244" y="14.6218" width="3" height="12" fill="#FF8858"/>
                                        <rect x="301.244" y="14.6218" width="3" height="12" fill="#FF8858"/>
                                        <rect x="427.244" y="14.6218" width="3" height="12" fill="#FF8858"/>
                                        <rect x="182.244" y="10.6218" width="3" height="20" fill="#FF8858"/>
                                        <rect x="308.244" y="10.6218" width="3" height="20" fill="#FF8858"/>
                                        <rect x="434.244" y="10.6218" width="3" height="20" fill="#FF8858"/>
                                        <rect x="189.244" y="6.62183" width="3" height="27" fill="#FF8858"/>
                                        <rect x="315.244" y="6.62183" width="3" height="27" fill="#FF8858"/>
                                        <rect x="441.244" y="6.62183" width="3" height="27" fill="#FF8858"/>
                                        <rect x="196.244" y="1.62183" width="3" height="38" fill="#FF8858"/>
                                        <rect x="322.244" y="1.62183" width="3" height="38" fill="#FF8858"/>
                                        <rect x="448" y="13" width="3" height="15" fill="#FF8858"/>
                                        <rect x="203.244" y="6.62183" width="3" height="27" fill="#FF8858"/>
                                        <rect x="329.244" y="6.62183" width="3" height="27" fill="#FF8858"/>
                                        <rect x="455.244" y="6.62183" width="3" height="27" fill="#FF8858"/>
                                        <rect x="210.244" y="9.62183" width="3" height="22" fill="#FF8858"/>
                                        <rect x="336.244" y="9.62183" width="3" height="22" fill="#FF8858"/>
                                        <rect x="462.244" y="9.62183" width="3" height="22" fill="#FF8858"/>
                                        <rect x="217.244" y="4.62183" width="3" height="32" fill="#FF8858"/>
                                        <rect x="343.244" y="4.62183" width="3" height="32" fill="#FF8858"/>
                                        <rect x="469.244" y="4.62183" width="3" height="32" fill="#FF8858"/>
                                        <rect x="224.244" y="9.62183" width="3" height="22" fill="#FF8858"/>
                                        <rect x="350.244" y="9.62183" width="3" height="22" fill="#FF8858"/>
                                        <rect x="476.244" y="9.62183" width="3" height="22" fill="#FF8858"/>
                                        <rect x="231.244" y="13.6218" width="3" height="14" fill="#FF8858"/>
                                        <rect x="357.244" y="13.6218" width="3" height="14" fill="#FF8858"/>
                                        <rect x="483.244" y="13.6218" width="3" height="14" fill="#FF8858"/>
                                        <rect x="540.244" y="13.6218" width="3" height="14" fill="#FF8858"/>
                                        <rect x="238.244" y="17.6218" width="3" height="6" fill="#FF8858"/>
                                        <rect x="364.244" y="17.6218" width="3" height="6" fill="#FF8858"/>
                                        <rect x="490.244" y="17.6218" width="3" height="6" fill="#FF8858"/>
                                        <rect x="547.244" y="17.6218" width="3" height="6" fill="#FF8858"/>
                                        <rect x="245.244" y="13.6218" width="3" height="14" fill="#FF8858"/>
                                        <rect x="371.244" y="13.6218" width="3" height="14" fill="#FF8858"/>
                                        <rect x="497.244" y="13.6218" width="3" height="14" fill="#FF8858"/>
                                        <rect x="554.244" y="13.6218" width="3" height="14" fill="#FF8858"/>
                                        <rect x="252.244" y="9.62183" width="3" height="22" fill="#FF8858"/>
                                        <rect x="378.244" y="9.62183" width="3" height="22" fill="#FF8858"/>
                                        <rect x="504.244" y="9.62183" width="3" height="22" fill="#FF8858"/>
                                        <rect x="561.244" y="9.62183" width="3" height="22" fill="#FF8858"/>
                                        <rect x="596.244" y="9.62183" width="3" height="22" fill="#FF8858"/>
                                        <rect x="259.244" y="13.6218" width="3" height="14" fill="#FF8858"/>
                                        <rect x="385.244" y="13.6218" width="3" height="14" fill="#FF8858"/>
                                        <rect x="511.244" y="13.6218" width="3" height="14" fill="#FF8858"/>
                                        <rect x="568.244" y="13.6218" width="3" height="14" fill="#FF8858"/>
                                        <rect x="603.244" y="13.6218" width="3" height="14" fill="#FF8858"/>
                                        <rect x="266.244" y="17.6218" width="3" height="6" fill="#FF8858"/>
                                        <rect x="392.244" y="17.6218" width="3" height="6" fill="#FF8858"/>
                                        <rect x="518.244" y="17.6218" width="3" height="6" fill="#FF8858"/>
                                        <rect x="575.244" y="17.6218" width="3" height="6" fill="#FF8858"/>
                                        <rect x="610.244" y="17.6218" width="3" height="6" fill="#FF8858"/>
                                        <rect x="273.244" y="17.6218" width="3" height="6" fill="#FF8858"/>
                                        <rect x="399.244" y="17.6218" width="3" height="6" fill="#FF8858"/>
                                        <rect x="525.244" y="17.6218" width="3" height="6" fill="#FF8858"/>
                                        <rect x="582.244" y="17.6218" width="3" height="6" fill="#FF8858"/>
                                        <rect x="617.244" y="17.6218" width="3" height="6" fill="#FF8858"/>
                                        <rect x="280.244" y="17.6218" width="3" height="6" fill="#FF8858"/>
                                        <rect x="406.244" y="17.6218" width="3" height="6" fill="#FF8858"/>
                                        <rect x="532.244" y="17.6218" width="3" height="6" fill="#FF8858"/>
                                        <rect x="589.244" y="17.6218" width="3" height="6" fill="#FF8858"/>
                                        <rect x="624.244" y="17.6218" width="3" height="6" fill="#FF8858"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <span class="text-xs text-gray-500">{{ hasRecording ? `${formatTime(currentTime)} / ${formatTime(duration)}` : 'No recording available' }}</span>
                    </div>
                </div>
            </div>

            <!-- Conversation -->
            <div class="space-y-6">
                <div v-for="(message, index) in conversation" :key="index" class="flex">
                    <div class="w-12 text-xs text-gray-500 pt-1 text-right pr-2">{{ message.time }}</div>
                    <div class="flex-1">
                        <div class="flex items-center mb-1">
                            <span class="font-medium text-gray-800 mr-2">{{ message.speaker }}</span>
                            <span class="text-xs text-gray-500">{{ message.role }}</span>
                        </div>
                        <p class="text-sm text-gray-700">{{ message.text }}</p>
                    </div>
                    <div class="flex items-center ml-2" v-if="index === 5 || index === 7">
                        <button class="text-gray-400 hover:text-gray-500">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 15S4 9 12 9 20 4 20 4V18S12 14 4 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M4 22V4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>
                        <span class="text-xs text-gray-400 ml-1">Flag</span>
                    </div>
                    <div class="flex items-center ml-2" v-if="index === 7">
                        <button class="text-gray-400 hover:text-gray-500 ml-2">
                            <span class="text-xs">Hide reasoning</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Customer Details -->
            <div class="mt-6 pt-4 border-t border-gray-200" v-if="!isCallInProgress">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Customer Details</h3>
                <div class="bg-gray-50 p-4 rounded-md text-sm text-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="mb-2"><strong>Customer Name:</strong> {{ customerName || 'Unknown' }}</p>
                            <p class="mb-2"><strong>Phone Number:</strong> {{ props.callLog.caller_number || 'Unknown' }}</p>
                            <p class="mb-2"><strong>Purpose:</strong> {{ formatDepartment(props.callLog.department) || 'General Inquiry' }}</p>
                        </div>
                        
                    </div>
                </div>
                <div class="flex justify-end mt-4">
                    <button class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 15S4 9 12 9 20 4 20 4V18S12 14 4 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M4 22V4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        Flag
                    </button>
                    <button class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-500 ml-2">
                        Hide reasoning
                    </button>
                </div>
            </div>

            <!-- Back button -->
            <div class="mt-8">
                <Link :href="route('call-logs.index')" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Back to Call Logs
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
