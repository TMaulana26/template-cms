<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, onMounted, watch, nextTick } from "vue";
import { router } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import { useToast } from "vue-toastification";
import "vue-toastification/dist/index.css";
import { VueGoodTable } from "vue-good-table-next";
import "vue-good-table-next/dist/vue-good-table-next.css";
import {
    PlusIcon,
    PencilSquareIcon,
    TrashIcon,
    ArrowPathIcon,
} from "@heroicons/vue/24/outline";

// Define props
const props = defineProps({
    menus: {
        type: Object,
        required: true,
    },
});

// Define modals state and methods
const modals = ref({
    add: false,
    edit: false,
    delete: false,
});

const toast = useToast();

const tableData = ref([]);
const loading = ref(false);
const currentMenu = ref(null);
const tableKey = ref(0);
const titleInputAdd = ref(null);
const titleInputEdit = ref(null);

const vFocus = {
    mounted: (el) => el.focus(),
};

// Initialize with default empty values
const defaultFormData = {
    title: "",
    route: "",
    url: "",
};

// Reusable form data with reset method
const formData = ref({ ...defaultFormData });
const resetForm = () => {
    formData.value = { ...defaultFormData };
};

// Table columns configuration
const columns = [
    {
        label: "Title",
        field: "title",
        sortable: true,
    },
    {
        label: "Route",
        field: "route",
        sortable: true,
    },
    {
        label: "Url",
        field: "url",
        sortable: true,
    },
    {
        label: "Created At",
        field: "created_at",
        sortable: true,
    },
    {
        label: "Updated At",
        field: "updated_at",
        sortable: true,
    },
    {
        label: "Actions",
        field: "actions",
        sortable: false,
        width: "150px",
    },
];

// Initialize table data
onMounted(() => {
    tableData.value = Array.isArray(props.menus)
        ? props.menus
        : Object.values(props.menus);
});

// Watch for modal state changes
watch(
    () => modals.value.add,
    (isOpen) => {
        if (isOpen) {
            nextTick(() => {
                titleInputAdd.value?.focus();
            });
        }
    }
);

watch(
    () => modals.value.edit,
    (isOpen) => {
        if (isOpen) {
            nextTick(() => {
                titleInputEdit.value?.focus();
            });
        }
    }
);

// Modal handlers - consolidated
const openModal = (modalType, menu = null) => {
    if (modalType === "edit" && menu) {
        currentMenu.value = menu;
        formData.value = { ...menu };
    } else if (modalType === "delete" && menu) {
        currentMenu.value = menu;
    } else if (modalType === "add") {
        resetForm();
    }

    modals.value[modalType] = true;
};

// Form submit handlers
const handleSubmit = (action) => {
    loading.value = true;

    if (action === "add") {
        router.post("/menu", formData.value, {
            onSuccess: () => {
                modals.value[action] = false;
                toast.success("Menu created successfully!");
                refreshTable();
            },
            onError: (errors) => {
                console.error(`Error during ${action} operation:`, errors);
                modals.value[action] = false;

                const errorDetails = formatErrorMessage(
                    errors,
                    "Menu creation failed!"
                );
                toast.error(errorDetails);

                loading.value = false;
            },
        });
    } else if (action === "edit") {
        router.put(`/menu/${currentMenu.value.id}`, formData.value, {
            onSuccess: () => {
                modals.value[action] = false;
                toast.success(
                    currentMenu.value.title + " updated successfully!"
                );
                refreshTable();
            },
            onError: (errors) => {
                console.error(`Error during ${action} operation:`, errors);
                modals.value[action] = false;

                const errorDetails = formatErrorMessage(
                    errors,
                    "Menu update failed!"
                );
                toast.error(errorDetails, { dangerouslyHTMLString: true });

                loading.value = false;
            },
        });
    } else if (action === "delete") {
        router.delete(`/menu/${currentMenu.value.id}`, {
            onSuccess: () => {
                modals.value[action] = false;
                toast.success(
                    currentMenu.value.title + " deleted successfully!"
                );
                refreshTable();
            },
            onError: (errors) => {
                console.error(`Error during ${action} operation:`, errors);

                const errorDetails = formatErrorMessage(
                    errors,
                    "Menu deletion failed!"
                );
                toast.error(errorDetails);

                loading.value = false;
            },
        });
    }
};

// Helper function to format error messages
const formatErrorMessage = (errors, title) => {
    let errorText = `${title}\n`;

    if (errors) {
        if (typeof errors === "object" && Object.keys(errors).length > 0) {
            for (const field in errors) {
                if (Array.isArray(errors[field])) {
                    errors[field].forEach((message) => {
                        errorText += `• ${message}\n`;
                    });
                } else {
                    errorText += `• ${errors[field]}\n`;
                }
            }
        } else if (typeof errors === "string") {
            errorText += errors;
        } else if (errors instanceof Error) {
            errorText += errors.message;
        }
    }

    return errorText;
};

// Improved table refresh with key update for guaranteed re-render
const refreshTable = () => {
    loading.value = true;
    router.reload({
        only: ["menus"],
        onSuccess: (page) => {
            tableData.value = Array.isArray(page.props.menus)
                ? page.props.menus
                : Object.values(page.props.menus);
            tableKey.value++; // Force table re-render
            loading.value = false;
        },
        onError: (errors) => {
            console.error("Error refreshing data:", errors);
            loading.value = false;
        },
    });
};
</script>

<template>
    <Head title="Menu" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Menu
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-center items-center mb-6">
                            <h3 class="text-xl font-semibold">Menu List</h3>
                        </div>

                        <vue-good-table
                            :key="tableKey"
                            :columns="columns"
                            :rows="tableData"
                            theme="polar-bear"
                            :search-options="{
                                enabled: true,
                                placeholder: 'Search menu items...',
                            }"
                            :pagination-options="{
                                enabled: true,
                                perPage: 10,
                                perPageDropdown: [5, 10, 20, 50],
                                dropdownAllowAll: true,
                                setCurrentPage: 1,
                                nextLabel: 'Next',
                                prevLabel: 'Prev',
                                rowsPerPageLabel: 'Rows per page',
                                ofLabel: 'of',
                                pageLabel: 'page',
                                allLabel: 'All',
                            }"
                            styleClass="vgt-table bordered striped"
                        >
                            <template #table-actions>
                                <div class="flex gap-2">
                                    <button
                                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded flex items-center gap-1"
                                        @click="openModal('add')"
                                    >
                                        <PlusIcon class="h-5 w-5" />
                                        <span>Add Menu</span>
                                    </button>
                                    <button
                                        @click="refreshTable"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded flex items-center gap-1"
                                        title="Refresh data"
                                    >
                                        <ArrowPathIcon class="h-5 w-5" />
                                        <span>Refresh</span>
                                    </button>
                                </div>
                            </template>
                            <template #table-row="props">
                                <div
                                    v-if="props.column.field === 'actions'"
                                    class="flex gap-2"
                                >
                                    <button
                                        @click="openModal('edit', props.row)"
                                        class="text-blue-500 hover:text-blue-700 bg-blue-100 hover:bg-blue-200 px-3 py-1 rounded flex items-center gap-1"
                                        title="Edit menu item"
                                    >
                                        <PencilSquareIcon class="h-4 w-4" />
                                        <span>Edit</span>
                                    </button>
                                    <button
                                        @click="openModal('delete', props.row)"
                                        class="text-red-500 hover:text-red-700 bg-red-100 hover:bg-red-200 px-3 py-1 rounded flex items-center gap-1"
                                        title="Delete menu item"
                                    >
                                        <TrashIcon class="h-4 w-4" />
                                        <span>Delete</span>
                                    </button>
                                </div>
                                <span v-else>
                                    {{ props.formattedRow[props.column.field] }}
                                </span>
                            </template>
                            <template #emptystate>
                                <div class="text-center p-4 text-gray-500">
                                    No menu items found. Click "Add Menu" to
                                    create one.
                                </div>
                            </template>
                        </vue-good-table>
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

        <!-- Add New Modal -->
        <Modal :show="modals.add" @close="modals.add = false" max-width="md">
            <div class="p-6">
                <div class="flex items-center gap-2">
                    <PlusIcon class="h-6 w-6 text-green-500" />
                    <h2 class="text-lg font-medium text-gray-900">
                        Add New Menu
                    </h2>
                </div>

                <div class="mt-6">
                    <div class="mb-4">
                        <label
                            for="title"
                            class="block text-sm font-medium text-gray-700"
                            >Title</label
                        >
                        <input
                            type="text"
                            id="title"
                            ref="titleInputAdd"
                            v-model="formData.title"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            v-focus
                        />
                    </div>
                    <div class="mb-4">
                        <label
                            for="route"
                            class="block text-sm font-medium text-gray-700"
                            >Route</label
                        >
                        <input
                            type="text"
                            id="route"
                            v-model="formData.route"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        />
                    </div>
                    <div class="mb-4">
                        <label
                            for="url"
                            class="block text-sm font-medium text-gray-700"
                            >URL</label
                        >
                        <input
                            type="text"
                            id="url"
                            v-model="formData.url"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        />
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button
                        type="button"
                        @click="modals.add = false"
                        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2 flex items-center gap-1"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="handleSubmit('add')"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 items-center gap-1"
                    >
                        <PlusIcon class="h-4 w-4" />
                        Save
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Edit Modal -->
        <Modal :show="modals.edit" @close="modals.edit = false" max-width="md">
            <div class="p-6">
                <div class="flex items-center gap-2">
                    <PencilSquareIcon class="h-6 w-6 text-blue-500" />
                    <h2 class="text-lg font-medium text-gray-900">Edit Menu</h2>
                </div>

                <div class="mt-6">
                    <div class="mb-4">
                        <label
                            for="edit-title"
                            class="block text-sm font-medium text-gray-700"
                            >Title</label
                        >
                        <input
                            type="text"
                            id="edit-title"
                            ref="titleInputEdit"
                            v-model="formData.title"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            v-focus
                        />
                    </div>
                    <div class="mb-4">
                        <label
                            for="edit-route"
                            class="block text-sm font-medium text-gray-700"
                            >Route</label
                        >
                        <input
                            type="text"
                            id="edit-route"
                            v-model="formData.route"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        />
                    </div>
                    <div class="mb-4">
                        <label
                            for="edit-url"
                            class="block text-sm font-medium text-gray-700"
                            >URL</label
                        >
                        <input
                            type="text"
                            id="edit-url"
                            v-model="formData.url"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        />
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button
                        type="button"
                        @click="modals.edit = false"
                        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="handleSubmit('edit')"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 items-center gap-1"
                    >
                        <PencilSquareIcon class="h-4 w-4" />
                        Update
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <Modal
            :show="modals.delete"
            @close="modals.delete = false"
            max-width="sm"
        >
            <div class="p-6">
                <div class="flex items-center gap-2">
                    <TrashIcon class="h-6 w-6 text-red-500" />
                    <h2 class="text-lg font-medium text-gray-900">
                        Delete Menu
                    </h2>
                </div>

                <div class="mt-4">
                    <p class="text-sm text-gray-600">
                        Are you sure you want to delete "{{
                            currentMenu?.title
                        }}"? This action cannot be undone.
                    </p>
                </div>

                <div class="mt-6 flex justify-end">
                    <button
                        type="button"
                        @click="modals.delete = false"
                        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="handleSubmit('delete')"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 items-center gap-1"
                    >
                        <TrashIcon class="h-4 w-4" />
                        Delete
                    </button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
