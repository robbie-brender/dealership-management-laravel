<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AddNewLineModal from '@/Components/AddNewLineModal.vue';
import AddNewKnowledgeBaseModal from '@/Components/AddNewKnowledgeBaseModal.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import Pagination from '@/Components/Pagination.vue';

// Props for call logs data and stats
const props = defineProps({
    callLogs: Object,
    stats: Object,
    filters: Object,
});

// Modal and filter visibility states
const showNewLineModal = ref(false);
const showNewKnowledgeBaseModal = ref(false);
const showDepartmentFilter = ref(false);
const selectedDepartment = ref(props.filters?.department || 'all');

// Modal handlers
const openNewLineModal = () => {
    showNewLineModal.value = true;
};

const openNewKnowledgeBaseModal = () => {
    showNewKnowledgeBaseModal.value = true;
};

const closeNewLineModal = () => {
    showNewLineModal.value = false;
};

const closeNewKnowledgeBaseModal = () => {
    showNewKnowledgeBaseModal.value = false;
};

// Dashboard data
const dealershipName = ref('Chatswood Toyota');

// Stats from backend
const completedCalls = ref(props.stats?.completedCalls || 0);
const totalCalls = ref(props.stats?.totalCalls || 0);
const percentAnswered = ref(props.stats?.percentAnswered || 0);
const totalMinutes = ref(props.stats?.totalMinutes || 0);

// Department counts and minutes
const salesCalls = ref(props.stats?.salesCalls || 0);
const serviceCalls = ref(props.stats?.serviceCalls || 0);
const partsCalls = ref(props.stats?.partsCalls || 0);
const otherCalls = ref(props.stats?.otherCalls || 0);

// Department minutes
const salesMinutes = ref(props.stats?.salesMinutes || 0);
const serviceMinutes = ref(props.stats?.serviceMinutes || 0);
const partsMinutes = ref(props.stats?.partsMinutes || 0);

// Appointments (keeping for now, could be replaced with real data later)
const appointments = ref({
    booked: 28,
    rescheduled: 2,
    cancelled: 1
});

// Interactions
const totalInteractions = ref(totalCalls.value);

// Interaction types for chart - using department data for now
const interactionTypes = ref([
    { type: 'Sales', count: salesCalls.value, color: '#e8f5e9' },
    { type: 'Service', count: serviceCalls.value, color: '#ffebee' },
    { type: 'Parts', count: partsCalls.value, color: '#fff3e0' },
    { type: 'Other', count: otherCalls.value, color: '#e3f2fd' }
]);

// Helper function to format duration
const formatDuration = (value, isMinutes = true) => {
    // If value is in seconds, convert to minutes first
    let minutes = isMinutes ? value : value / 60;
    
    const mins = Math.floor(minutes);
    const secs = Math.round((minutes - mins) * 60);
    
    if (secs === 60) {
        // Handle edge case where seconds round up to 60
        return `${mins + 1} min`;
    } else if (secs === 0) {
        // Only show minutes if there are no seconds
        return `${mins} min`;
    } else {
        // Show minutes and seconds
        return `${mins} min ${secs} sec`;
    }
};

// Helper functions for call logs table
const getStatusClass = (status) => {
    switch (status) {
        case 'completed':
            return 'bg-green-50 text-green-700 border-green-100';
        case 'in-progress':
            return 'bg-blue-50 text-blue-700 border-blue-100';
        case 'failed':
            return 'bg-red-50 text-red-700 border-red-100';
        default:
            return 'bg-gray-50 text-gray-700 border-gray-100';
    }
};

const formatPhoneNumber = (phoneNumber) => {
    if (!phoneNumber) return '-';
    
    // Clean the phone number (remove non-numeric characters)
    const cleaned = phoneNumber.toString().replace(/\D/g, '');
    
    // Format for Australian numbers
    if (cleaned.startsWith('61') && cleaned.length >= 10) {
        return `+${cleaned.substring(0, 2)} ${cleaned.substring(2, 5)} ${cleaned.substring(5, 8)} ${cleaned.substring(8)}`;
    }
    
    // Default formatting if not recognized pattern
    return phoneNumber;
};

// Filter call logs by department - using server-side filtering
const filterByDepartment = (department) => {
    selectedDepartment.value = department;
    showDepartmentFilter.value = false;
    
    // Use Inertia router to navigate with the filter parameter
    router.get(
        route('dashboard'), 
        { department: department === 'all' ? null : department },
        { 
            preserveState: true,
            replace: true,
            preserveScroll: true,
            only: ['callLogs', 'filters']
        }
    );
};

// Handle click outside to close department filter dropdown
const handleClickOutside = (event) => {
    const dropdown = document.querySelector('.department-filter-dropdown');
    const button = document.querySelector('.department-filter-button');
    
    if (dropdown && button && !dropdown.contains(event.target) && !button.contains(event.target)) {
        showDepartmentFilter.value = false;
    }
};

// Add and remove event listeners for click outside
onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    
    const date = new Date(dateString);
    return date.toLocaleString();
};
</script>

<template>
    <Head :title="dealershipName" />

    <AuthenticatedLayout
        @show-new-line-modal="openNewLineModal"
        @show-new-knowledge-base-modal="openNewKnowledgeBaseModal"
    >

        <div class="p-6">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">{{ dealershipName }}</h1>
                    <div class="flex items-center space-x-3">
                        <span class="px-2 py-1 bg-green-50 text-green-700 text-sm font-medium rounded-md border border-green-100">{{ appointments.booked }} Booked</span>
                        <span class="px-2 py-1 bg-orange-50 text-orange-700 text-sm font-medium rounded-md border border-orange-100">{{ appointments.rescheduled }} Rescheduled</span>
                        <span class="px-2 py-1 bg-red-50 text-red-700 text-sm font-medium rounded-md border border-red-100">{{ appointments.cancelled }} Cancelled</span>
                        <button class="px-3 py-1.5 text-sm bg-white border border-gray-200 rounded-md flex items-center">
                            <span class="mr-1">Filter by line</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <button class="px-3 py-1.5 text-sm bg-white border border-gray-200 rounded-md flex items-center">
                            <span class="mr-1">Today</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-5 gap-4 mb-6">
                    <div class="p-4 bg-white rounded-lg border border-gray-100">
                        <h3 class="text-xs uppercase font-medium text-gray-500">CALLS ANSWERED / TOTAL</h3>
                        <p class="text-2xl font-bold text-gray-800 mt-2">{{ completedCalls }} <span class="text-sm font-normal text-gray-500">/ {{ totalCalls }}</span></p>
                    </div>
                    <div class="p-4 bg-white rounded-lg border border-gray-100">
                        <h3 class="text-xs uppercase font-medium text-gray-500">% OF CALLS ANSWERED</h3>
                        <p class="text-2xl font-bold text-gray-800 mt-2">{{ percentAnswered }}%</p>
                    </div>
                    <div class="p-4 bg-white rounded-lg border border-gray-100">
                        <h3 class="text-xs uppercase font-medium text-gray-500">TOTAL SALES CALL TIME</h3>
                        <p class="text-2xl font-bold text-gray-800 mt-2">{{ formatDuration(salesMinutes) }}</p>
                    </div>
                    <div class="p-4 bg-white rounded-lg border border-gray-100">
                        <h3 class="text-xs uppercase font-medium text-gray-500">TOTAL SERVICE CALL TIME</h3>
                        <p class="text-2xl font-bold text-gray-800 mt-2">{{ formatDuration(serviceMinutes) }}</p>
                    </div>
                    <div class="p-4 bg-white rounded-lg border border-gray-100">
                        <h3 class="text-xs uppercase font-medium text-gray-500">TOTAL PARTS CALL TIME</h3>
                        <p class="text-2xl font-bold text-gray-800 mt-2">{{ formatDuration(partsMinutes) }}</p>
                    </div>
                </div>
                


                <!-- Charts -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <!-- Interactions Over Time Chart -->
                    <div class="bg-white rounded-lg border border-gray-100 p-4">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-xs uppercase font-medium text-gray-500">INTERACTIONS OVER TIME</h3>
                            <span class="text-xs text-gray-500">{{ totalInteractions }} TOTAL</span>
                        </div>
                        <div class="h-40 rounded">
                            <!-- Chart with time labels -->
                            <svg class="w-full h-full" viewBox="0 0 400 150" xmlns="http://www.w3.org/2000/svg">
                                <!-- Time labels -->
                                <text x="20" y="140" class="text-xs text-gray-500">6 am</text>
                                <text x="80" y="140" class="text-xs text-gray-500">8 am</text>
                                <text x="140" y="140" class="text-xs text-gray-500">10 am</text>
                                <text x="200" y="140" class="text-xs text-gray-500">12 pm</text>
                                <text x="260" y="140" class="text-xs text-gray-500">2 pm</text>
                                <text x="320" y="140" class="text-xs text-gray-500">4 pm</text>
                                <text x="380" y="140" class="text-xs text-gray-500">6 pm</text>
                                
                                <!-- Chart line -->
                                <path d="M20,100 L40,90 L60,80 L80,70 L100,60 L120,40 L140,30 L160,50 L180,60 L200,70 L220,60 L240,50 L260,40 L280,50 L300,40 L320,50 L340,60 L360,70 L380,80" stroke="#93c5fd" stroke-width="2" fill="none" />
                                <path d="M20,100 L40,90 L60,80 L80,70 L100,60 L120,40 L140,30 L160,50 L180,60 L200,70 L220,60 L240,50 L260,40 L280,50 L300,40 L320,50 L340,60 L360,70 L380,80 L380,120 L20,120 Z" fill="#dbeafe" fill-opacity="0.5" />
                            </svg>
                        </div>
                    </div>

                    <!-- Interactions by Type Chart (Department Distribution) -->
                    <div class="bg-white rounded-lg border border-gray-100 p-4">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-sm font-medium">Calls by Department</h3>
                        </div>
                        <div class="flex flex-col space-y-2">
                            <div v-for="(item, index) in interactionTypes" :key="index" class="flex items-center">
                                <div class="w-full">
                                    <div class="flex justify-between mb-1">
                                        <span class="text-xs font-medium">{{ item.type }}</span>
                                        <span class="text-xs text-gray-500">{{ item.count }}</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2">
                                        <div class="h-2 rounded-full" :style="{ width: totalInteractions > 0 ? `${(item.count / totalInteractions) * 100}%` : '0%', backgroundColor: item.color }"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Call Logs Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="mb-6 flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900">Recent Call Logs</h3>
                            <div class="flex space-x-2">
                                <div class="relative inline-block text-left">
                                    <button @click="showDepartmentFilter = !showDepartmentFilter" class="department-filter-button px-3 py-1.5 text-sm bg-white border border-gray-200 rounded-md flex items-center" :class="{'border-orange-400': selectedDepartment !== 'all'}">
                                        <span class="mr-1">Filter by department{{ selectedDepartment !== 'all' ? ': ' + selectedDepartment.charAt(0).toUpperCase() + selectedDepartment.slice(1) : '' }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div v-if="showDepartmentFilter" class="department-filter-dropdown origin-top-right absolute right-0 mt-2 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
                                        <div class="py-1">
                                            <button @click="filterByDepartment('all')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">All departments</button>
                                            <button @click="filterByDepartment('sales')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sales</button>
                                            <button @click="filterByDepartment('service')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Service</button>
                                            <button @click="filterByDepartment('parts')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Parts</button>
                                            <button @click="filterByDepartment('other')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Other</button>
                                        </div>
                                    </div>
                                </div>
                                <button class="px-3 py-1.5 text-sm bg-white border border-gray-200 rounded-md flex items-center">
                                    <span class="mr-1">Date range</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Duration
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Direction
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Phone Number
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Department
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Started At
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="callLog in props.callLogs.data" :key="callLog.id">
                                        <td class="py-2 px-4 border-b">{{ callLog.duration ? formatDuration(callLog.duration, false) : 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-medium rounded-md border" :class="getStatusClass(callLog.status)">
                                                {{ callLog.status.charAt(0).toUpperCase() + callLog.status.slice(1) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ callLog.direction.charAt(0).toUpperCase() + callLog.direction.slice(1) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatPhoneNumber(callLog.caller_number) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span v-if="callLog.department" class="px-2 py-1 text-xs font-medium rounded-md border bg-purple-50 text-purple-700 border-purple-100">
                                                {{ callLog.department.charAt(0).toUpperCase() + callLog.department.slice(1) }}
                                            </span>
                                            <span v-else>-</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(callLog.call_started_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <Link :href="route('call-logs.show', callLog.id)" class="text-orange-600 hover:text-orange-900">
                                                View Details
                                            </Link>
                                        </td>
                                    </tr>
                                    <tr v-if="!props.callLogs.data || props.callLogs.data.length === 0">
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No call logs found.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6" v-if="props.callLogs && props.callLogs.links">
                            <Pagination :links="props.callLogs.links" />
                        </div>
                    </div>
                </div>
        </div>
    </AuthenticatedLayout>
    
    <!-- Modals -->
    <AddNewLineModal 
        :show="showNewLineModal" 
        @close="closeNewLineModal" 
    />
    
    <AddNewKnowledgeBaseModal 
        :show="showNewKnowledgeBaseModal" 
        @close="closeNewKnowledgeBaseModal" 
    />
</template>

<style scoped>
/* Additional styles for perfect matching */
.last\:border-0:last-child {
    border-width: 0;
}
</style>
