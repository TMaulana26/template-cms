<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import {
    ref,
    onMounted,
    watch,
    nextTick,
    computed,
    onBeforeUnmount,
} from "vue";
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
import InfoButton from "@/Components/InfoButton.vue";
import SuccessButton from "@/Components/SuccessButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import { trans } from "laravel-vue-i18n";

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
    tableData.value = Array.isArray(props.menus)
        ? props.menus
        : Object.values(props.menus);
});

// Clean up observer when component is destroyed
onBeforeUnmount(() => {
    if (observer) {
        observer.disconnect();
    }
});

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
const columns = computed(() => [
    {
        label: trans("pages.menu.Title"),
        field: "title",
        sortable: true,
    },
    {
        label: trans("pages.menu.Route"),
        field: "route",
        sortable: true,
    },
    {
        label: trans("pages.menu.Url"),
        field: "url",
        sortable: true,
    },
    {
        label: trans("pages.menu.Created_At"),
        field: "created_at",
        sortable: true,
    },
    {
        label: trans("pages.menu.Updated_At"),
        field: "updated_at",
        sortable: true,
    },
    {
        label: trans("pages.menu.Actions"),
        field: "actions",
        sortable: false,
        width: "150px",
    },
]);

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
                toast.success(trans("pages.menu.Menu_created"));
                refreshTable();
            },
            onError: (errors) => {
                console.error(`Error during ${action} operation:`, errors);
                modals.value[action] = false;

                const errorDetails = formatErrorMessage(
                    errors,
                    trans("pages.menu.Menu_creation_failed")
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
                    currentMenu.value.title +
                        " " +
                        trans("pages.menu.updated_successfully")
                );
                refreshTable();
            },
            onError: (errors) => {
                console.error(`Error during ${action} operation:`, errors);
                modals.value[action] = false;

                const errorDetails = formatErrorMessage(
                    errors,
                    trans("pages.menu.Menu_update_failed")
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
                    currentMenu.value.title +
                        " " +
                        trans("pages.menu.deleted_successfully")
                );
                refreshTable();
            },
            onError: (errors) => {
                console.error(`Error during ${action} operation:`, errors);

                const errorDetails = formatErrorMessage(
                    errors,
                    trans("pages.menu.Menu_deletion_failed")
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
            tableKey.value++;
            loading.value = false;
            toast.info(trans("pages.menu.Menu_refreshed"));
        },
        onError: (errors) => {
            console.error("Error refreshing data:", errors);
            loading.value = false;
        },
    });
};

// Pagination options with translations
const paginationOptions = computed(() => {
    return {
        enabled: true,
        perPage: 10,
        perPageDropdown: [5, 10, 20, 50],
        dropdownAllowAll: true,
        setCurrentPage: 1,
        nextLabel: trans("pages.menu.Next"),
        prevLabel: trans("pages.menu.Prev"),
        rowsPerPageLabel: trans("pages.menu.Rows_per_page"),
        ofLabel: trans("pages.menu.of"),
        pageLabel: trans("pages.menu.page"),
        allLabel: trans("pages.menu.All"),
    };
});
</script>

<template>
    <Head :title="$t('pages.menu.Menu')" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                {{ $t("pages.menu.Menu") }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg"
                >
                    <div class="p-6">
                        <div class="flex justify-center items-center mb-6">
                            <h3
                                class="text-xl font-semibold dark:text-gray-200"
                            >
                                {{ $t("pages.menu.Menu_List") }}
                            </h3>
                        </div>

                        <vue-good-table
                            :key="tableKey"
                            :columns="columns"
                            :rows="tableData"
                            :theme="tableTheme"
                            :search-options="{
                                enabled: true,
                                placeholder: $t('pages.menu.Search_menu_items'),
                            }"
                            :pagination-options="paginationOptions"
                            styleClass="vgt-table bordered striped"
                        >
                            <template #table-actions>
                                <div class="flex gap-2 mr-2 items-center">
                                    <SuccessButton @click="openModal('add')">
                                        <PlusIcon class="h-5 w-5" />
                                        <span>{{
                                            $t("pages.menu.Add_Menu")
                                        }}</span>
                                    </SuccessButton>
                                    <InfoButton @click="refreshTable">
                                        <ArrowPathIcon class="h-5 w-5" />
                                        <span>{{
                                            $t("pages.menu.Refresh")
                                        }}</span>
                                    </InfoButton>
                                </div>
                            </template>
                            <template #table-row="props">
                                <div
                                    v-if="props.column.field === 'actions'"
                                    class="flex gap-2"
                                >
                                    <button
                                        @click="openModal('edit', props.row)"
                                        class="text-blue-500 hover:text-blue-700 bg-blue-100 hover:bg-blue-200 dark:text-blue-400 dark:hover:text-blue-300 dark:bg-gray-700 dark:hover:bg-gray-600 px-3 py-1 rounded flex items-center gap-1"
                                        :title="$t('pages.menu.Edit_menu_item')"
                                    >
                                        <PencilSquareIcon class="h-4 w-4" />
                                        <span>{{ $t("pages.menu.Edit") }}</span>
                                    </button>
                                    <button
                                        @click="openModal('delete', props.row)"
                                        class="text-red-500 hover:text-red-700 bg-red-100 hover:bg-red-200 dark:text-red-400 dark:hover:text-red-300 dark:bg-gray-700 dark:hover:bg-gray-600 px-3 py-1 rounded flex items-center gap-1"
                                        :title="
                                            $t('pages.menu.Delete_menu_item')
                                        "
                                    >
                                        <TrashIcon class="h-4 w-4" />
                                        <span>{{
                                            $t("pages.menu.Delete")
                                        }}</span>
                                    </button>
                                </div>
                                <span v-else>
                                    {{ props.formattedRow[props.column.field] }}
                                </span>
                            </template>
                            <template #emptystate>
                                <div
                                    class="text-center p-4 text-gray-500 dark:text-gray-400"
                                >
                                    {{ $t("pages.menu.No_menu_items") }}
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
            <div
                class="bg-white dark:bg-gray-800 p-4 rounded shadow flex items-center gap-2 dark:text-gray-200"
            >
                <ArrowPathIcon
                    class="h-5 w-5 text-indigo-600 dark:text-amber-500 animate-spin"
                />
                {{ $t("pages.menu.Processing") }}
            </div>
        </div>

        <!-- Add New Modal -->
        <Modal :show="modals.add" @close="modals.add = false" max-width="md">
            <div class="p-6">
                <div class="flex items-center gap-2">
                    <PlusIcon
                        class="h-6 w-6 text-green-500 dark:text-green-400"
                    />
                    <h2
                        class="text-lg font-medium text-gray-900 dark:text-gray-200"
                    >
                        {{ $t("pages.menu.Add_New_Menu") }}
                    </h2>
                </div>

                <div class="mt-6">
                    <div class="mb-4">
                        <InputLabel
                            for="title"
                            :value="$t('pages.menu.Title')"
                        />
                        <TextInput
                            id="title"
                            type="text"
                            ref="titleInputAdd"
                            class="mt-1 block w-full"
                            v-model="formData.title"
                            required
                            v-focus
                        />
                    </div>
                    <div class="mb-4">
                        <InputLabel
                            for="route"
                            :value="$t('pages.menu.Route')"
                        />
                        <TextInput
                            id="route"
                            type="text"
                            v-model="formData.route"
                            class="mt-1 block w-full"
                            required
                        />
                    </div>
                    <div class="mb-4">
                        <InputLabel for="url" :value="$t('pages.menu.URL')" />
                        <TextInput
                            id="url"
                            type="text"
                            v-model="formData.url"
                            class="mt-1 block w-full"
                            required
                        />
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <DangerButton
                        class="mr-3"
                        type="button"
                        @click="modals.add = false"
                    >
                        {{ $t("pages.menu.Cancel") }}
                    </DangerButton>
                    <SuccessButton
                        class="mr-3"
                        type="button"
                        @click="handleSubmit('add')"
                    >
                        <PlusIcon class="h-4 w-4" />
                        {{ $t("pages.menu.Save") }}
                    </SuccessButton>
                </div>
            </div>
        </Modal>

        <!-- Edit Modal -->
        <Modal :show="modals.edit" @close="modals.edit = false" max-width="md">
            <div class="p-6">
                <div class="flex items-center gap-2">
                    <PencilSquareIcon
                        class="h-6 w-6 text-blue-500 dark:text-blue-400"
                    />
                    <h2
                        class="text-lg font-medium text-gray-900 dark:text-gray-200"
                    >
                        {{ $t("pages.menu.Edit_Menu") }}
                    </h2>
                </div>

                <div class="mt-6">
                    <div class="mb-4">
                        <InputLabel
                            for="edit-title"
                            :value="$t('pages.menu.Title')"
                        />
                        <TextInput
                            id="edit-title"
                            type="text"
                            ref="titleInputEdit"
                            v-model="formData.title"
                            class="mt-1 block w-full"
                            v-focus
                        />
                    </div>
                    <div class="mb-4">
                        <InputLabel
                            for="edit-route"
                            :value="$t('pages.menu.Route')"
                        />
                        <TextInput
                            id="edit-route"
                            type="text"
                            v-model="formData.route"
                            class="mt-1 block w-full"
                        />
                    </div>
                    <div class="mb-4">
                        <InputLabel
                            for="edit-url"
                            :value="$t('pages.menu.URL')"
                        />
                        <TextInput
                            id="edit-url"
                            type="text"
                            v-model="formData.url"
                            class="mt-1 block w-full"
                        />
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <DangerButton
                        class="mr-3"
                        type="button"
                        @click="modals.edit = false"
                    >
                        {{ $t("pages.menu.Cancel") }}
                    </DangerButton>

                    <SuccessButton
                        class="mr-3"
                        type="button"
                        @click="handleSubmit('edit')"
                    >
                        <PencilSquareIcon class="h-4 w-4" />
                        {{ $t("pages.menu.Update") }}
                    </SuccessButton>
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
                    <TrashIcon class="h-6 w-6 text-red-500 dark:text-red-400" />
                    <h2
                        class="text-lg font-medium text-gray-900 dark:text-gray-200"
                    >
                        {{ $t("pages.menu.Delete_Menu") }}
                    </h2>
                </div>

                <div class="mt-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{
                            $t("pages.menu.Delete_confirmation", {
                                name: currentMenu?.title,
                            })
                        }}
                    </p>
                </div>

                <div class="mt-6 flex justify-end">
                    <DangerButton
                        class="mr-3"
                        type="button"
                        @click="modals.delete = false"
                    >
                        {{ $t("pages.menu.Cancel") }}
                    </DangerButton>

                    <SuccessButton
                        class="mr-3"
                        type="button"
                        @click="handleSubmit('delete')"
                    >
                        <TrashIcon class="h-4 w-4" />
                        {{ $t("pages.menu.Delete") }}
                    </SuccessButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
