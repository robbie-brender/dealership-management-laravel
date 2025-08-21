<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

// Define emits for modal events
const emit = defineEmits(['showNewLineModal', 'showNewKnowledgeBaseModal']);

const showingNavigationDropdown = ref(false);

// Modal handlers
const openNewLineModal = () => {
    emit('showNewLineModal');
};

const openNewKnowledgeBaseModal = () => {
    emit('showNewKnowledgeBaseModal');
};
</script>

<template>
    <div class="flex h-screen bg-gray-50">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-50 border-r border-gray-100 flex flex-col py-6 px-4">
            <!-- Logo -->
            <div class="mb-8 px-2">
                <Link :href="route('dashboard')" class="flex items-center">
                    <svg width="24" height="24" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg" class="mr-2">
                        <path d="M13.8662 0V2.88867H17.9111L18.0596 2.89258C19.5859 2.96984 20.7996 4.23184 20.7998 5.77734V15.0225L20.7959 15.1709C20.721 16.6481 19.5368 17.8324 18.0596 17.9072L17.9111 17.9111H9.24414V20.5107H6.93359V17.9111H2.88867L2.74023 17.9072C1.26308 17.8323 0.0787947 16.6481 0.00390625 15.1709L0 15.0225V5.77734C0.000226771 4.23191 1.21397 2.96994 2.74023 2.89258L2.88867 2.88867H11.5557V0H13.8662ZM2.88867 5.2002C2.56982 5.20031 2.31176 5.45852 2.31152 5.77734V15.0225C2.31165 15.3414 2.56975 15.5995 2.88867 15.5996H6.93359V9.24414H11.5557V5.2002H2.88867ZM13.8662 11.5557H9.24414V15.5996H17.9111C18.2301 15.5996 18.4891 15.3414 18.4893 15.0225V5.77734C18.489 5.45845 18.2301 5.20021 17.9111 5.2002H13.8662V11.5557Z" fill="#FF8858"/>
                    </svg>
                    <span class="text-lg font-medium text-gray-800">Projuno.ai</span>
                </Link>
            </div>
            
            <!-- Navigation Items -->
            <div class="flex flex-col space-y-1">
                <Link :href="route('dashboard')" class="flex items-center px-2 py-2 text-sm font-medium rounded-md" :class="route().current('dashboard') ? 'text-gray-900 bg-gray-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-5 w-5" :class="route().current('dashboard') ? 'text-gray-500' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Dashboard
                </Link>
                
                <Link :href="route('call-logs.index')" class="flex items-center px-2 py-2 text-sm font-medium rounded-md" :class="route().current('call-logs.*') ? 'text-gray-900 bg-gray-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-5 w-5" :class="route().current('call-logs.*') ? 'text-gray-500' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    Call Logs
                </Link>
                
                <button @click="openNewKnowledgeBaseModal" class="flex items-center px-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-100 hover:text-gray-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    New Knowledge Base
                </button>
                
                <button @click="openNewLineModal" class="flex items-center px-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-100 hover:text-gray-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Line
                </button>
            </div>
            
            <!-- User Profile at Bottom -->
            <div class="mt-auto px-2">
                <div class="flex items-center group">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center">
                            <span class="text-sm font-medium">{{ $page.props.auth.user.name.charAt(0) }}</span>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-700 group-hover:text-gray-900">{{ $page.props.auth.user.name }}</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-auto h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-4 sm:p-6 lg:p-8">
                <div class="bg-white rounded-lg border border-gray-200 min-h-full">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
