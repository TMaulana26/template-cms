<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, onMounted, onBeforeUnmount, computed } from "vue";
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
import InfoButton from "@/Components/InfoButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import { trans } from "laravel-vue-i18n";

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

// Dark mode detection
const isDarkMode = ref(false);

// Update the isDarkMode value and re-render the table when dark mode changes
const updateDarkModeState = () => {
    if (typeof document !== "undefined") {
        isDarkMode.value = document.documentElement.classList.contains("dark");
        // Force table to re-render when theme changes
        tableKey.value++;
    }
};

// Compute table theme based on dark mode
const tableTheme = computed(() => {
    return isDarkMode.value ? "nocturnal" : "polar-bear";
});

// Table columns configuration
const columns = computed(() => [
    {
        label: trans("pages.activity_log.Description"),
        field: "description",
        sortable: true,
    },
    {
        label: trans("pages.activity_log.User"),
        field: "causer_name",
        sortable: true,
    },
    {
        label: trans("pages.activity_log.Action_Type"),
        field: "event",
        sortable: true,
        formatFn: (value) => {
            return value.charAt(0).toUpperCase() + value.slice(1);
        },
    },
    {
        label: trans("pages.activity_log.Subject"),
        field: "subject_type",
        sortable: true,
        formatFn: (value) => {
            // Extract model name from full namespace
            return value ? value.split("\\").pop() : "";
        },
    },
    {
        label: trans("pages.activity_log.Date_Time"),
        field: "created_at",
        sortable: true,
        sortFn: (date) => new Date(date).getTime(),
        formatFn: (value) => {
            return new Date(value).toLocaleString();
        },
    },
]);

// Format activity log data for the table
const formatLogData = (logs) => {
    if (!logs || !Array.isArray(logs)) return [];

    return logs.map((log) => {
        return {
            id: log.id,
            description: log.description,
            causer_name: log.causer
                ? log.causer.name
                : trans("pages.activity_log.System"),
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

// Set up MutationObserver to detect dark mode changes
let observer;
onMounted(() => {
    // Set initial dark mode state
    updateDarkModeState();

    // Set up observer to watch for dark mode class changes
    if (typeof document !== "undefined") {
        observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === "class") {
                    updateDarkModeState();
                }
            });
        });

        // Start observing the document root for class changes
        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ["class"],
        });
    }

    // Initialize table data
    tableData.value = formatLogData(props.logs);
});

// Clean up observer when component is destroyed
onBeforeUnmount(() => {
    if (observer) {
        observer.disconnect();
    }
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
            toast.info(trans("pages.activity_log.Activity_log_refreshed"));
        },
        onError: (errors) => {
            console.error("Error refreshing data:", errors);
            toast.error(trans("pages.activity_log.Failed_to_refresh"));
            loading.value = false;
        },
    });
};

// Function to display formatted properties
const formatProperties = (properties) => {
    if (!properties) return trans("pages.activity_log.No_details");

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

// Pagination options with translations
const paginationOptions = computed(() => {
    return {
        enabled: true,
        perPage: 15,
        perPageDropdown: [10, 15, 20, 50],
        dropdownAllowAll: true,
        setCurrentPage: 1,
        nextLabel: trans("pages.activity_log.Next"),
        prevLabel: trans("pages.activity_log.Prev"),
        rowsPerPageLabel: trans("pages.activity_log.Logs_per_page"),
        ofLabel: trans("pages.activity_log.of"),
        pageLabel: trans("pages.activity_log.page"),
        allLabel: trans("pages.activity_log.All"),
    };
});
</script>

<template>
    <Head :title="$t('pages.activity_log.Activity_Log')" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                {{ $t("pages.activity_log.Activity_Log") }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg"
                >
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3
                                class="text-xl font-semibold dark:text-gray-200"
                            >
                                {{
                                    $t("pages.activity_log.System_Activity_Log")
                                }}
                            </h3>
                            <InfoButton @click="refreshTable">
                                <ArrowPathIcon class="h-5 w-5" />
                                <span>{{
                                    $t("pages.activity_log.Refresh")
                                }}</span>
                            </InfoButton>
                        </div>

                        <vue-good-table
                            :key="tableKey"
                            :columns="columns"
                            :rows="tableData"
                            :theme="tableTheme"
                            :search-options="{
                                enabled: true,
                                placeholder: $t(
                                    'pages.activity_log.Search_logs'
                                ),
                            }"
                            :pagination-options="paginationOptions"
                            styleClass="vgt-table bordered striped hover"
                            @row-click="showLogDetails"
                            :row-style-class="
                                isDarkMode
                                    ? 'cursor-pointer hover:bg-gray-700'
                                    : 'cursor-pointer hover:bg-gray-50'
                            "
                        >
                            <template #emptystate>
                                <div
                                    class="text-center p-4 text-gray-500 dark:text-gray-400"
                                >
                                    {{ $t("pages.activity_log.No_logs_found") }}
                                </div>
                            </template>
                        </vue-good-table>

                        <div
                            class="mt-4 text-sm text-indigo-500 dark:text-amber-400 italic"
                        >
                            {{ $t("pages.activity_log.Click_row_details") }}
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
            <div
                class="bg-white dark:bg-gray-800 p-4 rounded shadow flex items-center gap-2 dark:text-gray-200"
            >
                <ArrowPathIcon
                    class="h-5 w-5 text-indigo-600 dark:text-amber-500 animate-spin"
                />
                {{ $t("pages.activity_log.Processing") }}
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
                            class="h-6 w-6 text-blue-500 dark:text-blue-400"
                        />
                        <h2
                            class="text-lg font-medium text-gray-900 dark:text-gray-200"
                        >
                            {{ $t("pages.activity_log.Log_Details") }}
                        </h2>
                    </div>
                    <button
                        @click="showDetailsModal = false"
                        class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400"
                    >
                        <span class="sr-only">{{
                            $t("pages.activity_log.Close")
                        }}</span>
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
                            <h3
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                {{ $t("pages.activity_log.Description") }}
                            </h3>
                            <p
                                class="mt-1 text-sm text-gray-900 dark:text-gray-200"
                            >
                                {{ currentLog.description }}
                            </p>
                        </div>
                        <div>
                            <h3
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                {{ $t("pages.activity_log.Event") }}
                            </h3>
                            <p
                                class="mt-1 text-sm text-gray-900 dark:text-gray-200"
                            >
                                {{
                                    currentLog.event
                                        ? currentLog.event
                                              .charAt(0)
                                              .toUpperCase() +
                                          currentLog.event.slice(1)
                                        : $t("pages.activity_log.NA")
                                }}
                            </p>
                        </div>
                        <div>
                            <h3
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                {{ $t("pages.activity_log.User") }}
                            </h3>
                            <p
                                class="mt-1 text-sm text-gray-900 dark:text-gray-200"
                            >
                                {{
                                    currentLog.causer_name ||
                                    $t("pages.activity_log.System")
                                }}
                                <span
                                    v-if="currentLog.causer_email"
                                    class="text-gray-500 dark:text-gray-400"
                                    >({{ currentLog.causer_email }})</span
                                >
                            </p>
                        </div>
                        <div>
                            <h3
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                {{ $t("pages.activity_log.Date_Time") }}
                            </h3>
                            <p
                                class="mt-1 text-sm text-gray-900 dark:text-gray-200"
                            >
                                {{
                                    new Date(
                                        currentLog.created_at
                                    ).toLocaleString()
                                }}
                            </p>
                        </div>
                        <div>
                            <h3
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                {{ $t("pages.activity_log.Subject_Type") }}
                            </h3>
                            <p
                                class="mt-1 text-sm text-gray-900 dark:text-gray-200"
                            >
                                {{
                                    currentLog.subject_type
                                        ? currentLog.subject_type
                                              .split("\\")
                                              .pop()
                                        : $t("pages.activity_log.NA")
                                }}
                            </p>
                        </div>
                        <div>
                            <h3
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                {{ $t("pages.activity_log.Subject_ID") }}
                            </h3>
                            <p
                                class="mt-1 text-sm text-gray-900 dark:text-gray-200"
                            >
                                {{
                                    currentLog.subject_id ||
                                    $t("pages.activity_log.NA")
                                }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <h3
                            class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2"
                        >
                            {{ $t("pages.activity_log.Properties") }}
                        </h3>
                        <div
                            class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md overflow-auto max-h-80 text-sm font-mono dark:text-gray-200"
                        >
                            <pre>{{
                                formatProperties(currentLog.full_properties)
                            }}</pre>
                        </div>
                    </div>
                </div>

                <div
                    v-else
                    class="text-center py-6 text-gray-500 dark:text-gray-400"
                >
                    {{ $t("pages.activity_log.No_log_details") }}
                </div>

                <div class="mt-6 flex justify-end">
                    <DangerButton
                        class="mr-3"
                        type="button"
                        @click="showDetailsModal = false"
                    >
                        {{ $t("pages.activity_log.Close") }}
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
