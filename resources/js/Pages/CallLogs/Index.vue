<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    callLogs: Object,
    filters: Object,
});

// Department filter state
const showDepartmentFilter = ref(false);
const selectedDepartment = ref(props.filters?.department || 'all');

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
    if (!phoneNumber) return 'Unknown';
    
    // Format phone number as (XXX) XXX-XXXX
    const cleaned = ('' + phoneNumber).replace(/\D/g, '');
    const match = cleaned.match(/^(\d{3})(\d{3})(\d{4})$/);
    if (match) {
        return '(' + match[1] + ') ' + match[2] + '-' + match[3];
    }
    return phoneNumber;
};

const formatDuration = (seconds) => {
    if (!seconds) return '0:00';
    
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    
    const date = new Date(dateString);
    return date.toLocaleString();
};

// Filter call logs by department - using server-side filtering
const filterByDepartment = (department) => {
    selectedDepartment.value = department;
    showDepartmentFilter.value = false;
    
    // Use Inertia router to navigate with the filter parameter
    router.get(
        route('call-logs.index'), 
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
</script>

<template>
    <Head title="Call Logs" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Call Logs</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="mb-6 flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900">AI Assistant Call Logs</h3>
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
                                    <tr v-for="callLog in callLogs.data" :key="callLog.id">
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
                                    <tr v-if="callLogs.data.length === 0">
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No call logs found.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            <Pagination :links="callLogs.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
