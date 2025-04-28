<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import "vue-toastification/dist/index.css";
import { VueGoodTable } from "vue-good-table-next";
import "vue-good-table-next/dist/vue-good-table-next.css";
import {
    ArrowPathIcon,
    DocumentMagnifyingGlassIcon,
} from "@heroicons/vue/24/outline";
import Modal from "@/Components/Modal.vue";

// Define props
const props = defineProps({
    logs: {
        type: Object,
        required: true,
    },
});

const toast = useToast();
const tableData = ref([]);
const loading = ref(false);
const tableKey = ref(0);

// Modal state management
const showDetailsModal = ref(false);
const currentLog = ref(null);

// Table columns configuration
const columns = [
    {
        label: "Description",
        field: "description",
        sortable: true,
    },
    {
        label: "User",
        field: "causer_name",
        sortable: true,
    },
    {
        label: "Action Type",
        field: "event",
        sortable: true,
        formatFn: (value) => {
            return value.charAt(0).toUpperCase() + value.slice(1);
        },
    },
    {
        label: "Subject",
        field: "subject_type",
        sortable: true,
        formatFn: (value) => {
            // Extract model name from full namespace
            return value ? value.split("\\").pop() : "";
        },
    },
    {
        label: "Date & Time",
        field: "created_at",
        sortable: true,
        sortFn: (date) => new Date(date).getTime(),
        formatFn: (value) => {
            return new Date(value).toLocaleString();
        },
    },
];

// Format activity log data for the table
const formatLogData = (logs) => {
    if (!logs || !Array.isArray(logs)) return [];

    return logs.map((log) => {
        return {
            id: log.id,
            description: log.description,
            causer_name: log.causer ? log.causer.name : "System",
            causer_email: log.causer ? log.causer.email : "",
            event: log.event || "",
            log_name: log.log_name,
            subject_type: log.subject_type,
            subject_id: log.subject_id,
            properties: log.properties,
            created_at: log.created_at,
            full_properties: log.properties,
        };
    });
};

// Initialize table data
onMounted(() => {
    tableData.value = formatLogData(props.logs);
    toast.info("Activity Log loaded");
});

// Improved table refresh with key update for guaranteed re-render
const refreshTable = () => {
    loading.value = true;
    router.reload({
        only: ["logs"],
        onSuccess: (page) => {
            tableData.value = formatLogData(page.props.logs);
            tableKey.value++; // Force table re-render
            loading.value = false;
            toast.success("Activity log refreshed successfully!");
        },
        onError: (errors) => {
            console.error("Error refreshing data:", errors);
            toast.error("Failed to refresh activity log data.");
            loading.value = false;
        },
    });
};

// Function to display formatted properties
const formatProperties = (properties) => {
    if (!properties) return "No details available";

    try {
        const obj =
            typeof properties === "string"
                ? JSON.parse(properties)
                : properties;
        return JSON.stringify(obj, null, 2);
    } catch (e) {
        return String(properties);
    }
};

// Handle row click to show details via modal
const showLogDetails = (params) => {
    const row = params.row;
    currentLog.value = row;
    showDetailsModal.value = true;
};
</script>

<template>
    <Head title="Activity Log" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Activity Log
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-semibold">
                                System Activity Log
                            </h3>
                            <button
                                @click="refreshTable"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded flex items-center gap-1"
                                title="Refresh data"
                            >
                                <ArrowPathIcon class="h-5 w-5" />
                                <span>Refresh</span>
                            </button>
                        </div>

                        <vue-good-table
                            :key="tableKey"
                            :columns="columns"
                            :rows="tableData"
                            theme="polar-bear"
                            :search-options="{
                                enabled: true,
                                placeholder: 'Search activity logs...',
                            }"
                            :pagination-options="{
                                enabled: true,
                                perPage: 15,
                                perPageDropdown: [10, 15, 20, 50],
                                dropdownAllowAll: true,
                                setCurrentPage: 1,
                                nextLabel: 'Next',
                                prevLabel: 'Prev',
                                rowsPerPageLabel: 'Logs per page',
                                ofLabel: 'of',
                                pageLabel: 'page',
                                allLabel: 'All',
                            }"
                            styleClass="vgt-table bordered striped hover"
                            @row-click="showLogDetails"
                            row-style-class="cursor-pointer hover:bg-gray-50"
                        >
                            <template #emptystate>
                                <div class="text-center p-4 text-gray-500">
                                    No activity logs found.
                                </div>
                            </template>
                        </vue-good-table>

                        <div class="mt-4 text-sm text-gray-500 italic">
                            Click on any row to view detailed information.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading overlay -->
        <div
            v-if="loading"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        >
            <div class="bg-white p-4 rounded shadow flex items-center gap-2">
                <svg
                    class="animate-spin h-5 w-5 text-indigo-600"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    ></circle>
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                    ></path>
                </svg>
                Processing...
            </div>
        </div>

        <!-- Log Details Modal -->
        <Modal
            :show="showDetailsModal"
            @close="showDetailsModal = false"
            max-width="xl"
        >
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <DocumentMagnifyingGlassIcon
                            class="h-6 w-6 text-blue-500"
                        />
                        <h2 class="text-lg font-medium text-gray-900">
                            Log Details
                        </h2>
                    </div>
                    <button
                        @click="showDetailsModal = false"
                        class="text-gray-400 hover:text-gray-500"
                    >
                        <span class="sr-only">Close</span>
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>

                <div v-if="currentLog" class="mt-2 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">
                                Description
                            </h3>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ currentLog.description }}
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">
                                Event
                            </h3>
                            <p class="mt-1 text-sm text-gray-900">
                                {{
                                    currentLog.event
                                        ? currentLog.event
                                              .charAt(0)
                                              .toUpperCase() +
                                          currentLog.event.slice(1)
                                        : "n/a"
                                }}
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">
                                User
                            </h3>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ currentLog.causer_name || "System" }}
                                <span
                                    v-if="currentLog.causer_email"
                                    class="text-gray-500"
                                    >({{ currentLog.causer_email }})</span
                                >
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">
                                Date & Time
                            </h3>
                            <p class="mt-1 text-sm text-gray-900">
                                {{
                                    new Date(
                                        currentLog.created_at
                                    ).toLocaleString()
                                }}
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">
                                Subject Type
                            </h3>
                            <p class="mt-1 text-sm text-gray-900">
                                {{
                                    currentLog.subject_type
                                        ? currentLog.subject_type
                                              .split("\\")
                                              .pop()
                                        : "n/a"
                                }}
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">
                                Subject ID
                            </h3>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ currentLog.subject_id || "n/a" }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">
                            Properties
                        </h3>
                        <div
                            class="bg-gray-50 p-4 rounded-md overflow-auto max-h-80 text-sm font-mono"
                        >
                            <pre>{{
                                formatProperties(currentLog.full_properties)
                            }}</pre>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-6 text-gray-500">
                    No log details available
                </div>

                <div class="mt-6 flex justify-end">
                    <button
                        type="button"
                        @click="showDetailsModal = false"
                        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Close
                    </button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
