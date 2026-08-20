<template>
  <div class="w-full">
    <!-- Label -->
    <label
      v-if="label"
      class="mb-2 block text-sm font-medium text-slate-700"
    >
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>

    <!-- Main Card -->
    <div
      class="relative overflow-hidden rounded-xl border-2 border-dashed transition-all duration-200"
      :style="{
        '--primary-color-border': primaryColor,
      }"
      :class="[
        isDragging
          ? 'border-blue-500 bg-blue-50'
          : 'border-slate-300 hover:border-[var(--primary-color-border)]',
        disabled
          ? 'cursor-not-allowed opacity-50'
          : 'cursor-pointer',
      ]"
      @click="openFileBrowser"
      @dragover.prevent="handleDragOver"
      @dragleave.prevent="handleDragLeave"
      @drop.prevent="handleDrop"
    >
      <!-- Hidden Input -->
      <input
        ref="fileInput"
        type="file"
        class="hidden"
        :accept="accept"
        :multiple="multiple"
        :disabled="disabled"
        @change="handleFileChange"
      />

      <!-- ========================= -->
      <!-- Image Preview -->
      <!-- ========================= -->
      <div
        v-if="imageFiles.length"
        class="grid gap-3 p-3"
        :class="
          imageFiles.length === 1
            ? 'grid-cols-4'
            : 'grid-cols-2 sm:grid-cols-3 md:grid-cols-4'
        "
      >
        <div
          v-for="item in imageFiles"
          :key="item.key"
          class="group relative aspect-square overflow-hidden rounded-lg bg-slate-100"
          @click.stop
        >
          <!-- Image -->
          <img
            :src="item.preview"
            :alt="getFileName(item.file)"
            class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
            @error="handleImageError(item)"
          />

          <!-- Hover Overlay -->
          <div
            class="absolute inset-0 bg-black/0 transition group-hover:bg-black/30"
          />

          <!-- Remove -->
          <button
            type="button"
            class="absolute right-2 top-2 flex h-7 w-7 items-center justify-center rounded-full bg-white/90 text-slate-600 opacity-0 shadow transition group-hover:opacity-100 hover:bg-red-500 hover:text-white"
            @click.stop="removeFile(item.index)"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              stroke-width="2"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>

          <!-- File Name -->
          <div
            class="absolute bottom-0 left-0 right-0 truncate bg-black/50 px-2 py-1 text-xs text-white"
          >
            {{ getFileName(item.file) }}
          </div>
        </div>
      </div>

      <!-- ========================= -->
      <!-- Non Image Files -->
      <!-- ========================= -->

      <div
        v-if="nonImageFiles.length"
        class="space-y-2 p-3"
      >
        <div
          v-for="item in nonImageFiles"
          :key="item.key"
          class="flex items-center justify-between rounded-lg border border-slate-200 bg-white p-3"
          @click.stop
        >
          <div class="flex min-w-0 items-center gap-3">
            <!-- File Icon -->
            <div
              class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M7 3h7l4 4v14H7V3z"
                />
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M14 3v5h5"
                />
              </svg>
            </div>

            <!-- File Info -->
            <div class="min-w-0">
              <p
                class="truncate text-sm font-medium text-slate-700"
              >
                {{ getFileName(item.file) }}
              </p>

              <p class="text-xs text-slate-400">
                {{ getFileSize(item.file) }}
              </p>
            </div>
          </div>

          <!-- Remove -->
          <button
            type="button"
            class="ml-3 shrink-0 rounded-md p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-500"
            @click.stop="removeFile(item.index)"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              stroke-width="2"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>
      </div>

      <!-- ========================= -->
      <!-- Empty State -->
      <!-- ========================= -->

      <div
        v-if="!files.length"
        class="flex flex-col items-center justify-center px-6 py-10 text-center"
      >
        <!-- Upload Icon -->
        <div
          :style="{
            '--primary-color-bg': primaryColor,
          }"
          class="mb-4 flex h-14 w-14 items-center justify-center rounded-full text-[var(--primary-color-bg)]"
          style="
            background-color: color-mix(
              in srgb,
              var(--primary-color-bg) 15%,
              transparent
            );
          "
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-7 w-7"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M12 16V4m0 0L8 8m4-4l4 4M5 20h14"
            />
          </svg>
        </div>

        <p class="text-sm font-medium text-slate-700">
          {{ placeholder }}
        </p>

        <p class="mt-1 text-xs text-slate-500">
          {{
            multiple
              ? "You can select multiple files"
              : "Select a file"
          }}
        </p>

        <button
          type="button"
          :style="{
            '--primary-color-bg': primaryColor,
          }"
          :disabled="disabled"
          class="mt-4 inline-flex items-center rounded-lg bg-[var(--primary-color-bg)] px-4 py-2 text-sm font-medium text-white transition disabled:cursor-not-allowed"
          @click.stop="openFileBrowser"
        >
          Browse Files
        </button>

        <p
          v-if="accept || maxSize"
          class="mt-3 text-xs text-slate-400"
        >
          <span v-if="accept">
            Allowed: {{ accept }}
          </span>

          <span v-if="accept && maxSize">
            ·
          </span>

          <span v-if="maxSize">
            Max size: {{ formatSize(maxSize) }}
          </span>
        </p>
      </div>

      <!-- ========================= -->
      <!-- Add More -->
      <!-- ========================= -->

      <div
        v-if="files.length && multiple"
        class="border-t border-slate-200 bg-slate-50 px-4 py-3 text-center"
        @click.stop="openFileBrowser"
      >
        <span
          :style="{
            '--primary-color': primaryColor,
          }"
          class="text-sm font-medium text-[var(--primary-color)] transition-opacity hover:opacity-80"
        >
          + Add more files
        </span>
      </div>

      <!-- ========================= -->
      <!-- Single File Replace -->
      <!-- ========================= -->

      <div
        v-if="files.length && !multiple"
        class="border-t border-slate-200 bg-slate-50 px-4 py-3 text-center"
        @click.stop="openFileBrowser"
      >
        <span
          class="text-sm font-medium text-blue-600 hover:text-blue-700"
        >
          Change file
        </span>
      </div>
    </div>

    <!-- Error -->
    <p
      v-if="error"
      class="mt-2 text-sm text-red-500"
    >
      {{ error }}
    </p>
  </div>
</template>

<script setup>
import {
  computed,
  ref,
  watch,
  onBeforeUnmount,
} from "vue";

/*
|--------------------------------------------------------------------------
| Props
|--------------------------------------------------------------------------
*/

const props = defineProps({
  modelValue: {
    type: [File, String, Array],
    default: null,
  },

  label: {
    type: String,
    default: "Upload File",
  },

  placeholder: {
    type: String,
    default: "Drag & drop your file here or browse",
  },

  accept: {
    type: String,
    default: "",
  },

  multiple: {
    type: Boolean,
    default: false,
  },

  maxSize: {
    type: Number,
    default: 0,
  },

  required: {
    type: Boolean,
    default: false,
  },

  disabled: {
    type: Boolean,
    default: false,
  },

  primaryColor: {
    type: String,
    default: "#000",
  },

  /*
   * Laravel:
   *
   * /storage
   *
   * Or:
   *
   * https://example.com/storage
   */
  storageUrl: {
    type: String,
    default: "/storage",
  },
});

/*
|--------------------------------------------------------------------------
| Emits
|--------------------------------------------------------------------------
*/

const emit = defineEmits([
  "update:modelValue",
  "change",
  "error",
]);

/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const fileInput = ref(null);
const isDragging = ref(false);
const error = ref("");

const files = ref([]);

const previews = ref(new Map());

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

const getFileObjectKey = (file) => {
  return `${file.name}-${file.size}-${file.lastModified}`;
};

const fileKey = (file, index) => {
  if (typeof file === "string") {
    return `${file}-${index}`;
  }

  if (file instanceof File) {
    return `${getFileObjectKey(file)}-${index}`;
  }

  return `file-${index}`;
};

const getFileName = (file) => {
  if (typeof file === "string") {
    return file.split("/").pop() || file;
  }

  if (file instanceof File) {
    return file.name;
  }

  return "";
};

const getFileSize = (file) => {
  if (!(file instanceof File)) {
    return "";
  }

  return formatSize(file.size);
};

/*
|--------------------------------------------------------------------------
| Image Detection
|--------------------------------------------------------------------------
*/

const isImage = (file) => {
  if (!file) {
    return false;
  }

  /*
   * Existing backend path
   */
  if (typeof file === "string") {
    return /\.(jpg|jpeg|png|gif|webp|svg|bmp|avif)(\?.*)?$/i.test(
      file
    );
  }

  /*
   * New uploaded File
   */
  if (file instanceof File) {
    return file.type?.startsWith("image/");
  }

  return false;
};

/*
|--------------------------------------------------------------------------
| Build Storage URL
|--------------------------------------------------------------------------
*/

const buildStorageUrl = (path) => {
  if (!path) {
    return null;
  }

  /*
   * Full URL
   */
  if (
    path.startsWith("http://") ||
    path.startsWith("https://") ||
    path.startsWith("blob:")
  ) {
    return path;
  }

  /*
   * Remove leading slash
   */
  const cleanPath = path.replace(/^\/+/, "");

  /*
   * Remove trailing slash from storage URL
   */
  const baseUrl = props.storageUrl.replace(
    /\/+$/,
    ""
  );

  return `${baseUrl}/${cleanPath}`;
};

/*
|--------------------------------------------------------------------------
| Create Object URL Previews
|--------------------------------------------------------------------------
*/

const createPreviews = () => {
  const currentKeys = new Set();

  for (const file of files.value) {
    /*
     * Existing backend images don't need
     * createObjectURL().
     */
    if (typeof file === "string") {
      continue;
    }

    /*
     * Only process File objects
     */
    if (!(file instanceof File)) {
      continue;
    }

    /*
     * Only images
     */
    if (!file.type?.startsWith("image/")) {
      continue;
    }

    const key = getFileObjectKey(file);

    currentKeys.add(key);

    if (!previews.value.has(key)) {
      const url = URL.createObjectURL(file);

      previews.value.set(key, url);
    }
  }

  /*
   * Cleanup old object URLs
   */
  for (const [key, url] of previews.value) {
    if (!currentKeys.has(key)) {
      URL.revokeObjectURL(url);

      previews.value.delete(key);
    }
  }
};

/*
|--------------------------------------------------------------------------
| Get Preview
|--------------------------------------------------------------------------
*/

const getPreview = (file) => {
  if (!file) {
    return null;
  }

  /*
   * Existing backend image
   */
  if (typeof file === "string") {
    return buildStorageUrl(file);
  }

  /*
   * New File image
   */
  if (file instanceof File) {
    if (!file.type?.startsWith("image/")) {
      return null;
    }

    const key = getFileObjectKey(file);

    return previews.value.get(key) || null;
  }

  return null;
};

/*
|--------------------------------------------------------------------------
| Sync modelValue
|--------------------------------------------------------------------------
*/
const syncModelValue = (value) => {
  /*
   * Empty
   */
  if (
    value === null ||
    value === undefined ||
    value === ""
  ) {
    files.value = [];
    createPreviews();
    return;
  }

  /*
   * Laravel/database may return JSON string:
   *
   * '["screen_saver/image.jpg"]'
   *
   * Convert it back to an array.
   */
  if (typeof value === "string") {
    try {
      const parsed = JSON.parse(value);

      if (Array.isArray(parsed)) {
        value = parsed;
      }
    } catch {
      /*
       * Normal string path:
       *
       * "screen_saver/image.jpg"
       *
       * Keep it as a single file.
       */
    }
  }

  /*
   * Multiple
   */
  if (props.multiple) {
    files.value = Array.isArray(value)
      ? [...value]
      : [value];

    createPreviews();
    return;
  }

  /*
   * Single
   */
  files.value = [
    Array.isArray(value)
      ? value[0]
      : value,
  ].filter(Boolean);

  createPreviews();
};
const syncModelValue1 = (value) => {
  /*
   * Empty value
   */
  if (
    value === null ||
    value === undefined ||
    value === ""
  ) {
    files.value = [];
    createPreviews();
    return;
  }

  /*
   * Multiple mode
   *
   * Example edit value:
   *
   * [
   *   "screen/123.jpg",
   *   "screen/456.jpg"
   * ]
   *
   * New files:
   *
   * [
   *   "screen/123.jpg",
   *   File,
   *   File
   * ]
   */
  if (props.multiple) {
    files.value = Array.isArray(value)
      ? [...value]
      : [value];

    createPreviews();

    return;
  }

  /*
   * Single mode
   *
   * Supports:
   *
   * "screen/123.jpg"
   *
   * or:
   *
   * ["screen/123.jpg"]
   */
  files.value = [
    Array.isArray(value)
      ? value[0]
      : value,
  ].filter(Boolean);

  createPreviews();
};

/*
|--------------------------------------------------------------------------
| IMPORTANT:
| Watch modelValue directly
|--------------------------------------------------------------------------
*/

watch(
  () => props.modelValue,
  (value) => {
    syncModelValue(value);
  },
  {
    immediate: true,
    deep: true,
  }
);

/*
|--------------------------------------------------------------------------
| Image Files
|--------------------------------------------------------------------------
*/

const imageFiles = computed(() => {
  return files.value
    .map((file, index) => {
      return {
        file,
        index,
        preview: getPreview(file),
        key: fileKey(file, index),
      };
    })
    .filter((item) => {
      return !!item.preview;
    });
});

/*
|--------------------------------------------------------------------------
| Non Image Files
|--------------------------------------------------------------------------
*/

const nonImageFiles = computed(() => {
  return files.value
    .map((file, index) => {
      return {
        file,
        index,
        key: fileKey(file, index),
      };
    })
    .filter((item) => {
      return !isImage(item.file);
    });
});

/*
|--------------------------------------------------------------------------
| Open File Browser
|--------------------------------------------------------------------------
*/

const openFileBrowser = () => {
  if (props.disabled) {
    return;
  }

  fileInput.value?.click();
};

/*
|--------------------------------------------------------------------------
| File Change
|--------------------------------------------------------------------------
*/

const handleFileChange = (event) => {
  const selectedFiles = Array.from(
    event.target.files || []
  );

  processFiles(selectedFiles);

  /*
   * Allow selecting the same file again
   */
  event.target.value = "";
};

/*
|--------------------------------------------------------------------------
| Drag Over
|--------------------------------------------------------------------------
*/

const handleDragOver = () => {
  if (props.disabled) {
    return;
  }

  isDragging.value = true;
};

/*
|--------------------------------------------------------------------------
| Drag Leave
|--------------------------------------------------------------------------
*/

const handleDragLeave = () => {
  isDragging.value = false;
};

/*
|--------------------------------------------------------------------------
| Drop
|--------------------------------------------------------------------------
*/

const handleDrop = (event) => {
  if (props.disabled) {
    return;
  }

  isDragging.value = false;

  const droppedFiles = Array.from(
    event.dataTransfer.files || []
  );

  processFiles(droppedFiles);
};

/*
|--------------------------------------------------------------------------
| Process Files
|--------------------------------------------------------------------------
*/

const processFiles = (selectedFiles) => {
  error.value = "";

  if (!selectedFiles.length) {
    return;
  }

  const validFiles = [];

  for (const file of selectedFiles) {
    if (!validateFile(file)) {
      return;
    }

    validFiles.push(file);
  }

  /*
   * Multiple
   */
  if (props.multiple) {
    files.value = removeDuplicates([
      ...files.value,
      ...validFiles,
    ]);

    createPreviews();

    emitValue(files.value);

    emit("change", files.value);

    return;
  }

  /*
   * Single
   */
  files.value = [validFiles[0]];

  createPreviews();

  emitValue(validFiles[0]);

  emit("change", validFiles[0]);
};

/*
|--------------------------------------------------------------------------
| Validation
|--------------------------------------------------------------------------
*/

const validateFile = (file) => {
  /*
   * Size
   */
  if (
    props.maxSize &&
    file.size > props.maxSize
  ) {
    error.value =
      `${file.name} exceeds the maximum size of ` +
      `${formatSize(props.maxSize)}.`;

    emit("error", error.value);

    return false;
  }

  /*
   * Type
   */
  if (props.accept) {
    const acceptedTypes = props.accept
      .split(",")
      .map((type) =>
        type.trim().toLowerCase()
      )
      .filter(Boolean);

    const fileName =
      file.name.toLowerCase();

    const fileType =
      file.type.toLowerCase();

    const isValid = acceptedTypes.some(
      (type) => {
        /*
         * .jpg
         */
        if (type.startsWith(".")) {
          return fileName.endsWith(type);
        }

        /*
         * image/*
         */
        if (type.endsWith("/*")) {
          const baseType =
            type.replace("/*", "");

          return fileType.startsWith(
            `${baseType}/`
          );
        }

        /*
         * image/jpeg
         */
        return fileType === type;
      }
    );

    if (!isValid) {
      error.value =
        `${file.name} is not an allowed file type.`;

      emit("error", error.value);

      return false;
    }
  }

  return true;
};

/*
|--------------------------------------------------------------------------
| Remove File
|--------------------------------------------------------------------------
*/

const removeFile = (index) => {
  if (props.disabled) {
    return;
  }

  files.value.splice(index, 1);

  createPreviews();

  if (props.multiple) {
    emitValue(files.value);

    emit("change", files.value);
  } else {
    emitValue(null);

    emit("change", null);
  }

  error.value = "";
};

/*
|--------------------------------------------------------------------------
| Remove Duplicates
|--------------------------------------------------------------------------
*/

const removeDuplicates = (fileList) => {
  const unique = [];

  for (const file of fileList) {
    /*
     * Existing backend path
     */
    if (typeof file === "string") {
      if (!unique.includes(file)) {
        unique.push(file);
      }

      continue;
    }

    /*
     * New File
     */
    if (file instanceof File) {
      const exists = unique.some(
        (item) =>
          item instanceof File &&
          item.name === file.name &&
          item.size === file.size &&
          item.lastModified ===
            file.lastModified
      );

      if (!exists) {
        unique.push(file);
      }
    }
  }

  return unique;
};

/*
|--------------------------------------------------------------------------
| Emit
|--------------------------------------------------------------------------
*/

const emitValue = (value) => {
  emit(
    "update:modelValue",
    value
  );
};

/*
|--------------------------------------------------------------------------
| Image Error
|--------------------------------------------------------------------------
*/

const handleImageError = (item) => {
  console.error(
    "Failed to load image:",
    item.preview,
    item.file
  );
};

/*
|--------------------------------------------------------------------------
| Format Size
|--------------------------------------------------------------------------
*/

const formatSize = (bytes) => {
  if (!bytes) {
    return "0 Bytes";
  }

  const units = [
    "Bytes",
    "KB",
    "MB",
    "GB",
  ];

  const index = Math.floor(
    Math.log(bytes) / Math.log(1024)
  );

  return `${parseFloat(
    (
      bytes /
      Math.pow(1024, index)
    ).toFixed(2)
  )} ${units[index]}`;
};

/*
|--------------------------------------------------------------------------
| Cleanup
|--------------------------------------------------------------------------
*/

onBeforeUnmount(() => {
  for (const url of previews.value.values()) {
    URL.revokeObjectURL(url);
  }

  previews.value.clear();
});
</script>
