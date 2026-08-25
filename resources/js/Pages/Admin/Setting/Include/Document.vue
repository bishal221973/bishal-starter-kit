<script setup>
import { ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import { RisingSelect } from "rising-select";

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});

/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const showModal = ref(false);
const editingDocument = ref(null);

/*
|--------------------------------------------------------------------------
| Documents
|--------------------------------------------------------------------------
*/

const documents = computed(() => props.user?.documents ?? []);

/*
|--------------------------------------------------------------------------
| Document Types
|--------------------------------------------------------------------------
*/

const documentTypeOptions = [
    {
        label: "Citizenship",
        value: "citizenship",
    },
    {
        label: "Passport",
        value: "passport",
    },
    {
        label: "Driving License",
        value: "driving_license",
    },
    {
        label: "Contract",
        value: "contract",
    },
    {
        label: "Qualification",
        value: "qualification",
    },
    {
        label: "Experience Letter",
        value: "experience_letter",
    },
    {
        label: "Certificate",
        value: "certificate",
    },
    {
        label: "Other",
        value: "other",
    },
];

/*
|--------------------------------------------------------------------------
| Form
|--------------------------------------------------------------------------
*/

const form = useForm({
    user_id: props.user?.id ?? null,
    document_type: "",
    title: "",
    file: null,
    notes: "",
});

/*
|--------------------------------------------------------------------------
| Form Helpers
|--------------------------------------------------------------------------
*/

const resetForm = () => {
    form.reset();

    form.user_id = props.user?.id ?? null;
    form.document_type = "";
    form.title = "";
    form.file = null;
    form.notes = "";

    editingDocument.value = null;
};

const openCreateModal = () => {
    resetForm();
    showModal.value = true;
};

const openEditModal = (document) => {
    editingDocument.value = document;

    form.user_id = props.user?.id ?? null;
    form.document_type = document.document_type ?? "";
    form.title = document.title ?? "";
    form.file = null;
    form.notes = document.notes ?? "";

    showModal.value = true;
};

const closeModal = () => {
    if (form.processing) {
        return;
    }

    showModal.value = false;
    resetForm();
};

const handleFileChange = (event) => {
    form.file = event.target.files?.[0] ?? null;
};

/*
|--------------------------------------------------------------------------
| Submit
|--------------------------------------------------------------------------
*/

const submit = () => {
    if (editingDocument.value) {
        form
            .transform((data) => ({
                ...data,
                _method: "PUT",
            }))
            .post(
                route(
                    "employee-documents.update",
                    editingDocument.value.id
                ),
                {
                    forceFormData: true,
                    preserveScroll: true,

                    onSuccess: () => {
                        showModal.value = false;
                        resetForm();
                    },
                }
            );

        return;
    }

    form.post(route("employee-documents.store"), {
        forceFormData: true,
        preserveScroll: true,

        onSuccess: () => {
            showModal.value = false;
            resetForm();
        },
    });
};

/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

const deleteDocument = (document) => {
    // Add Swal confirmation here if required.

    form.delete(
        route("employee-documents.destroy", document.id),
        {
            preserveScroll: true,
        }
    );
};

/*
|--------------------------------------------------------------------------
| Document Helpers
|--------------------------------------------------------------------------
*/

const getDocumentTypeLabel = (type) => {
    const item = documentTypeOptions.find(
        (option) => option.value === type
    );

    return item?.label ?? type ?? "Other";
};

const getDocumentIcon = (document) => {
    const type = document.file_type ?? "";

    if (type.includes("pdf")) {
        return "fa-regular fa-file-pdf";
    }

    if (type.includes("image")) {
        return "fa-regular fa-file-image";
    }

    if (
        type.includes("word") ||
        type.includes("document")
    ) {
        return "fa-regular fa-file-word";
    }

    if (
        type.includes("excel") ||
        type.includes("spreadsheet")
    ) {
        return "fa-regular fa-file-excel";
    }

    return "fa-regular fa-file-lines";
};

const formatFileSize = (size) => {
    if (!size) {
        return "";
    }

    if (size < 1024) {
        return `${size} B`;
    }

    if (size < 1024 * 1024) {
        return `${(size / 1024).toFixed(1)} KB`;
    }

    return `${(size / (1024 * 1024)).toFixed(1)} MB`;
};

const formatDate = (date) => {
    if (!date) {
        return "—";
    }

    const parsed = new Date(date);

    if (isNaN(parsed.getTime())) {
        return date;
    }

    return parsed.toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};

const isExpired = (date) => {
    if (!date) {
        return false;
    }

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const expiry = new Date(date);
    expiry.setHours(0, 0, 0, 0);

    return expiry < today;
};

const isExpiringSoon = (date) => {
    if (!date || isExpired(date)) {
        return false;
    }

    const today = new Date();
    const expiry = new Date(date);

    const difference =
        (expiry.getTime() - today.getTime()) /
        (1000 * 60 * 60 * 24);

    return difference <= 30;
};
</script>

<template>
    <div
        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
    >
        <!-- =========================================================
             HEADER
        ========================================================== -->

        <div
            class="flex flex-col gap-4 border-b border-slate-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h2 class="font-semibold text-slate-900">
                    Documents
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Manage your uploaded employee documents.
                </p>
            </div>

            <button
                type="button"
                @click="openCreateModal"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
            >
                <i class="fa-solid fa-plus text-xs"></i>

                Add Document
            </button>
        </div>

        <!-- =========================================================
             DOCUMENT CONTENT
        ========================================================== -->

        <div class="p-6">

            <!-- Empty State -->
            <div
                v-if="!documents.length"
                class="rounded-xl border border-dashed border-slate-300 py-12 text-center"
            >
                <div
                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-slate-100 text-slate-400"
                >
                    <i
                        class="fa-regular fa-folder-open text-xl"
                    ></i>
                </div>

                <h3
                    class="mt-4 text-sm font-semibold text-slate-800"
                >
                    No documents yet
                </h3>

                <p
                    class="mx-auto mt-1 max-w-md text-sm text-slate-500"
                >
                    Upload your citizenship, passport,
                    qualification, contract or other employee
                    documents.
                </p>

                <button
                    type="button"
                    @click="openCreateModal"
                    class="mt-5 inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                >
                    <i class="fa-solid fa-plus text-xs"></i>

                    Upload Document
                </button>
            </div>

            <!-- Document List -->
            <div
                v-else
                class="divide-y divide-slate-100"
            >
                <div
                    v-for="document in documents"
                    :key="document.id"
                    class="flex flex-col gap-4 py-5 first:pt-0 last:pb-0 md:flex-row md:items-center"
                >
                    <!-- Icon -->
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500"
                    >
                        <i
                            :class="getDocumentIcon(document)"
                            class="text-lg"
                        ></i>
                    </div>

                    <!-- Information -->
                    <div class="min-w-0 flex-1">

                        <div
                            class="flex flex-wrap items-center gap-2"
                        >
                            <h3
                                class="truncate text-sm font-semibold text-slate-900"
                            >
                                {{ document.title }}
                            </h3>

                            <span
                                class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600"
                            >
                                {{
                                    getDocumentTypeLabel(
                                        document.document_type
                                    )
                                }}
                            </span>

                            
                        </div>

                        <!-- File Information -->
                        <div
                            class="mt-1.5 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500"
                        >
                            
                            <span
                                v-if="document.file_name"
                                class="truncate"
                            >
                                <i
                                    class="fa-regular fa-file mr-1"
                                ></i>

                                {{ document.file_name }}
                            </span>

                            <span
                                v-if="document.file_size"
                            >
                                {{ formatFileSize(document.file_size) }}
                            </span>
                        </div>

                        <!-- Dates -->
                        
                    </div>

                    <!-- Actions -->
                    <div
                        class="flex shrink-0 items-center gap-1"
                    >
                        <!-- View -->
                        <a
                            :href="`/storage/${document.file_path}`"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-900"
                            title="View"
                        >
                            <i
                                class="fa-solid fa-eye text-xs"
                            ></i>
                        </a>

                        <!-- Download -->
                        <a
                            :href="`/storage/${document.file_path}`"
                            download
                            class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-900"
                            title="Download"
                        >
                            <i
                                class="fa-solid fa-download text-xs"
                            ></i>
                        </a>

                        <!-- Edit -->
                        <button
                            type="button"
                            @click="openEditModal(document)"
                            class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-blue-50 hover:text-blue-600"
                            title="Edit"
                        >
                            <i
                                class="fa-solid fa-pen text-xs"
                            ></i>
                        </button>

                        <!-- Delete -->
                        <button
                            type="button"
                            @click="deleteDocument(document)"
                            class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-red-50 hover:text-red-600"
                            title="Delete"
                        >
                            <i
                                class="fa-solid fa-trash text-xs"
                            ></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- =============================================================
         MODAL
    ============================================================= -->

    <Teleport to="body">
        <div
            v-if="showModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm"
            @click.self="closeModal"
        >
            <div
                class="w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl"
            >
                <!-- Modal Header -->
                <div
                    class="flex items-center justify-between border-b border-slate-100 px-6 py-5"
                >
                    <div>
                        <h2
                            class="text-lg font-semibold text-slate-900"
                        >
                            {{
                                editingDocument
                                    ? "Edit Document"
                                    : "Add Document"
                            }}
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            {{
                                editingDocument
                                    ? "Update employee document information."
                                    : "Upload a new employee document."
                            }}
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="closeModal"
                        class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                    >
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Form -->
                <form
                    @submit.prevent="submit"
                    class="max-h-[75vh] overflow-y-auto"
                >
                    <div
                        class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2"
                    >
                        <!-- Document Type -->
                        <div>
                            <label
                                class="text-sm font-medium text-slate-700"
                            >
                                Document Type
                                <span class="text-red-500">*</span>
                            </label>

                            <RisingSelect
                                v-model="form.document_type"
                                :options="documentTypeOptions"
                                placeholder="Select document type"
                                class="mt-1.5 w-full"
                            />

                            <p
                                v-if="form.errors.document_type"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{
                                    form.errors.document_type
                                }}
                            </p>
                        </div>

                        <!-- Title -->
                        <div>
                            <label
                                class="text-sm font-medium text-slate-700"
                            >
                                Document Title
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                v-model="form.title"
                                type="text"
                                placeholder="e.g. Citizenship Certificate"
                                class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-slate-500 focus:ring-1 focus:ring-slate-500"
                            />

                            <p
                                v-if="form.errors.title"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.title }}
                            </p>
                        </div>

                        <!-- Document Number -->
                        

                        <!-- File -->
                        <div class="md:col-span-2">
                            <label
                                class="text-sm font-medium text-slate-700"
                            >
                                Document File

                                <span
                                    v-if="!editingDocument"
                                    class="text-red-500"
                                >
                                    *
                                </span>
                            </label>

                            <div
                                class="mt-1.5 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-5"
                            >
                                <input
                                    type="file"
                                    @change="handleFileChange"
                                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                    class="block w-full text-sm text-slate-500 file:mr-4 file:rounded-lg file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-slate-800"
                                />

                                <p
                                    class="mt-2 text-xs text-slate-500"
                                >
                                    PDF, JPG, PNG, DOC or DOCX.
                                    Maximum file size 5MB.
                                </p>

                                <p
                                    v-if="
                                        editingDocument &&
                                        editingDocument.file_name
                                    "
                                    class="mt-2 text-xs text-slate-600"
                                >
                                    Current file:

                                    <strong>
                                        {{
                                            editingDocument.file_name
                                        }}
                                    </strong>
                                </p>
                            </div>

                            <p
                                v-if="form.errors.file"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.file }}
                            </p>
                        </div>

                        <!-- Issued Date -->
                        

                        <!-- Notes -->
                        <div class="md:col-span-2">
                            <label
                                class="text-sm font-medium text-slate-700"
                            >
                                Notes
                            </label>

                            <textarea
                                v-model="form.notes"
                                rows="3"
                                placeholder="Additional information..."
                                class="mt-1.5 w-full resize-none rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-slate-500 focus:ring-1 focus:ring-slate-500"
                            ></textarea>

                            <p
                                v-if="form.errors.notes"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.notes }}
                            </p>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div
                        class="flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4"
                    >
                        <button
                            type="button"
                            @click="closeModal"
                            class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <i
                                v-if="form.processing"
                                class="fa-solid fa-spinner fa-spin text-xs"
                            ></i>

                            <i
                                v-else
                                class="fa-solid fa-check text-xs"
                            ></i>

                            {{
                                form.processing
                                    ? "Saving..."
                                    : editingDocument
                                    ? "Update Document"
                                    : "Save Document"
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>