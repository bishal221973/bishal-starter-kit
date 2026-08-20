<script setup>
import { useForm } from "@inertiajs/vue3";
import Label from "@/Components/Label.vue";
import TextInput from "@/Components/Elements/TextInput.vue";
import Button from "../Elements/Button.vue";
import Modal from "../Modal.vue";
import { ref } from "vue";
import { RisingSelect } from "rising-select";
import { useTheme } from "@/composables/useTheme.js";
import { RisingPicker } from "rising-picker";

const props = defineProps({
  config: {
    type: Object,
    default: () => ({}),
  },
});

const { theme } = useTheme();

const showModal = ref(false);

const toggleModal = () => {
  showModal.value = !showModal.value;
};

const form = useForm({
  enable_screen_saver:
    props.config?.enable_screen_saver == 1,

  screen_saver_timeout:
    props.config?.screen_saver_timeout ?? 300,

  screen_saver_type:
    props.config?.screen_saver_type ?? "image",

  screen_saver_images:
    props.config?.screen_saver_images ?? null,

  screen_saver_video:
    props.config?.screen_saver_video ?? null,

  screen_saver_show_clock:
    props.config?.screen_saver_show_clock == 1,

  screen_saver_show_date:
    props.config?.screen_saver_show_date == 1,
});

const save = () => {
  form.post(route("configuration.setting.update"), {
    preserveScroll: true,

    onSuccess: () => {
      showModal.value = false;
    },
  });
};

const options = [
  {
    label: "Image",
    value: "image",
  },
  {
    label: "Image Slider",
    value: "slider",
  },
  {
    label: "Video",
    value: "video",
  },
];
</script>

<template>
  <!-- =========================================================
       SETTINGS BUTTON
  ========================================================== -->
  <button
    type="button"
    @click="toggleModal"
    class="group flex items-center gap-3 w-full p-4 rounded-xl border border-slate-200 bg-white hover:border-primary hover:shadow-sm transition-all duration-200"
  >
    <!-- Icon -->
    <div
      class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary transition-all duration-200 group-hover:bg-primary group-hover:text-white"
    >
      <i class="fa-solid fa-display"></i>
    </div>

    <!-- Content -->
    <div class="text-left">
      <span class="block font-medium text-slate-800">
        Screen Saver Config
      </span>

      <span class="block text-xs text-slate-500 mt-0.5">
        Manage screen saver timing, media and display options
      </span>
    </div>

    <!-- Arrow -->
    <i
      class="fa-solid fa-chevron-right ml-auto text-sm text-slate-400 group-hover:text-primary transition-colors"
    ></i>
  </button>

  <!-- =========================================================
       MODAL
  ========================================================== -->
  <Modal :show="showModal">
    <div class="overflow-hidden rounded-xl bg-white">
      <!-- =====================================================
           HEADER
      ====================================================== -->
      <div
        class="px-6 py-4 border-b border-slate-100 bg-card_header_color"
      >
        <div class="flex items-center gap-3">
          <!-- Icon -->
          <div
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
          >
            <i class="fa-solid fa-display"></i>
          </div>

          <!-- Header Text -->
          <div>
            <Label class="font-bold text-xl text-slate-800">
              Screen Saver Configuration
            </Label>

            <p class="text-sm text-slate-500 mt-0.5">
              Configure inactivity timing, screen saver media and
              display information.
            </p>
          </div>
        </div>
      </div>

      <!-- =====================================================
           BODY
      ====================================================== -->
      <div class="p-5 sm:p-6">
        <form @submit.prevent="save">
          <!-- =================================================
               ENABLE SCREEN SAVER
          ================================================== -->
          <label
            for="enable_screen_saver"
            class="group flex items-center justify-between gap-4 rounded-xl border p-4 cursor-pointer transition-all duration-200"
            :class="
              form.enable_screen_saver
                ? 'border-primary/30 bg-primary/5'
                : 'border-slate-200 hover:bg-slate-50'
            "
          >
            <div class="flex items-start gap-3">
              <!-- Icon -->
              <div
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg transition-colors"
                :class="
                  form.enable_screen_saver
                    ? 'bg-primary text-white'
                    : 'bg-slate-100 text-slate-500'
                "
              >
                <i class="fa-solid fa-moon"></i>
              </div>

              <!-- Text -->
              <div>
                <Label
                  for="enable_screen_saver"
                  class="font-semibold text-slate-800 cursor-pointer"
                >
                  Enable Screen Saver
                </Label>

                <p class="text-sm text-slate-500 mt-1">
                  Automatically activate the screen saver after a
                  period of inactivity to protect your screen and
                  reduce unnecessary display usage.
                </p>
              </div>
            </div>

            <!-- Toggle -->
            <div class="relative shrink-0">
              <input
                id="enable_screen_saver"
                v-model="form.enable_screen_saver"
                type="checkbox"
                class="peer sr-only"
              />

              <div
                class="w-11 h-6 rounded-full bg-slate-300 peer-checked:bg-primary transition-colors duration-200"
              ></div>

              <div
                class="absolute left-1 top-1 w-4 h-4 rounded-full bg-white shadow-sm transition-transform duration-200 peer-checked:translate-x-5"
              ></div>
            </div>
          </label>

          <!-- =================================================
               SCREEN SAVER SETTINGS
          ================================================== -->
          <div
            v-if="form.enable_screen_saver"
            class="mt-5 space-y-6"
          >
            <!-- ===============================================
                 BASIC SETTINGS
            ================================================ -->
            <div>
              <div class="flex items-center gap-2 mb-3">
                <div
                  class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary"
                >
                  <i class="fa-solid fa-sliders text-sm"></i>
                </div>

                <div>
                  <Label
                    class="font-semibold text-base text-slate-800"
                  >
                    Screen Saver Settings
                  </Label>

                  <p class="text-xs text-slate-500">
                    Configure when and how the screen saver should
                    appear.
                  </p>
                </div>
              </div>

              <div
                class="grid grid-cols-1 md:grid-cols-2 gap-4"
              >
                <!-- Timeout -->
                <div
                  class="rounded-xl border border-slate-200 p-4 bg-white"
                >
                  <TextInput
                    v-model="form.screen_saver_timeout"
                    type="number"
                    step="1"
                    min="1"
                    text="Screen Saver Timeout (Minutes)"
                    placeholder="Enter timeout"
                    required
                  />

                  <p class="mt-1.5 text-xs text-slate-500">
                    The screen saver will start after this many
                    minutes of inactivity.
                  </p>
                </div>

                <!-- Type -->
                <div
                  class="rounded-xl border border-slate-200 p-4 bg-white"
                >
                  <Label
                    class="font-medium text-slate-700 mb-2 block"
                  >
                    Screen Saver Type
                    <span class="text-red-500 ml-1">*</span>
                  </Label>

                  <RisingSelect
                    v-model="form.screen_saver_type"
                    :options="options"
                    wrapperBg="bg-white"
                    wrapperRounded="rounded-xl"
                    wrapperClass="border border-slate-200 shadow-sm transition-all duration-200 ease-in-out outline-none hover:border-slate-300 focus:border-primary focus:ring-4 focus:ring-indigo-500/10 focus:shadow-md"
                    inputClass="text-sm focus:ring-0 py-[10px] focus:outline-0"
                    :primaryColor="theme?.primary"
                    placeholder="Select screen saver type"
                  />

                  <p class="mt-1.5 text-xs text-slate-500">
                    Choose an image, image slider, or video based
                    screen saver.
                  </p>
                </div>
              </div>
            </div>

            <!-- ===============================================
                 MEDIA
            ================================================ -->
            <div>
              <div class="flex items-center gap-2 mb-3">
                <div
                  class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary"
                >
                  <i class="fa-solid fa-photo-film text-sm"></i>
                </div>

                <div>
                  <Label
                    class="font-semibold text-base text-slate-800"
                  >
                    Screen Saver Media
                  </Label>

                  <p class="text-xs text-slate-500">
                    Select the media that should be displayed when
                    the screen saver is active.
                  </p>
                </div>
              </div>

              <!-- IMAGE -->
              <div
                v-if="
                  form.screen_saver_type === 'image' ||
                  form.screen_saver_type === 'slider'
                "
                class="rounded-xl border border-slate-200 bg-white p-4"
              >
                <RisingPicker
                  v-model="form.screen_saver_images"
                  :label="
                    form.screen_saver_type === 'slider'
                      ? 'Screen Saver Images'
                      : 'Screen Saver Image'
                  "
                  accept="image/*"
                  :max-size="2 * 1024 * 1024"
                  :multiple="
                    form.screen_saver_type === 'slider'
                  "
                  :primaryColor="theme?.primary"
                />

                <p class="mt-2 text-xs text-slate-500">
                  {{
                    form.screen_saver_type === "slider"
                      ? "Upload multiple images to create a rotating image slider."
                      : "Upload the image that should be displayed as the screen saver."
                  }}
                </p>

                <div
                  v-if="form.screen_saver_type === 'slider'"
                  class="mt-3 flex items-start gap-2 rounded-lg bg-primary/5 border border-primary/10 px-3 py-2.5"
                >
                  <i
                    class="fa-solid fa-circle-info text-primary mt-0.5 text-xs"
                  ></i>

                  <p class="text-xs text-slate-500">
                    Multiple images will be displayed sequentially
                    while the screen saver is active.
                  </p>
                </div>
              </div>

              <!-- VIDEO -->
              <div
                v-if="form.screen_saver_type === 'video'"
                class="rounded-xl border border-slate-200 bg-white p-4"
              >
                <Label
                  class="font-medium text-slate-700 mb-2 block"
                >
                  Screen Saver Video
                  <span class="text-red-500 ml-1">*</span>
                </Label>

                <!--
                  Replace this input with your preferred file
                  browser/uploader component if needed.
                -->
                <input
                  v-model="form.screen_saver_video"
                  type="text"
                  placeholder="Enter video path or URL"
                  class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none transition focus:border-primary focus:ring-4 focus:ring-indigo-500/10"
                />

                <p class="mt-2 text-xs text-slate-500">
                  Provide the path or URL of the video that should
                  play when the screen saver is active.
                </p>

                <div
                  class="mt-3 flex items-start gap-2 rounded-lg bg-amber-50 border border-amber-100 px-3 py-2.5"
                >
                  <i
                    class="fa-solid fa-triangle-exclamation text-amber-500 mt-0.5 text-xs"
                  ></i>

                  <p class="text-xs text-slate-600">
                    Use a compressed video suitable for your display
                    resolution to reduce bandwidth and storage
                    usage.
                  </p>
                </div>
              </div>
            </div>

            <!-- ===============================================
                 DISPLAY OPTIONS
            ================================================ -->
            <div>
              <div class="flex items-center gap-2 mb-3">
                <div
                  class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary"
                >
                  <i class="fa-solid fa-eye text-sm"></i>
                </div>

                <div>
                  <Label
                    class="font-semibold text-base text-slate-800"
                  >
                    Display Options
                  </Label>

                  <p class="text-xs text-slate-500">
                    Choose additional information to show over the
                    screen saver.
                  </p>
                </div>
              </div>

              <div
                class="grid grid-cols-1 md:grid-cols-2 gap-3"
              >
                <!-- Clock -->
                <label
                  class="group flex items-start gap-3 rounded-xl border p-4 cursor-pointer transition-all duration-200"
                  :class="
                    form.screen_saver_show_clock
                      ? 'border-primary/30 bg-primary/5'
                      : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50'
                  "
                >
                  <input
                    v-model="form.screen_saver_show_clock"
                    type="checkbox"
                    class="mt-0.5 w-5 h-5 rounded cursor-pointer accent-primary"
                  />

                  <div>
                    <Label
                      class="font-medium text-slate-800 cursor-pointer"
                    >
                      Show Clock
                    </Label>

                    <p class="text-xs text-slate-500 mt-1">
                      Display the current time while the screen
                      saver is active.
                    </p>
                  </div>
                </label>

                <!-- Date -->
                <label
                  class="group flex items-start gap-3 rounded-xl border p-4 cursor-pointer transition-all duration-200"
                  :class="
                    form.screen_saver_show_date
                      ? 'border-primary/30 bg-primary/5'
                      : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50'
                  "
                >
                  <input
                    v-model="form.screen_saver_show_date"
                    type="checkbox"
                    class="mt-0.5 w-5 h-5 rounded cursor-pointer accent-primary"
                  />

                  <div>
                    <Label
                      class="font-medium text-slate-800 cursor-pointer"
                    >
                      Show Date
                    </Label>

                    <p class="text-xs text-slate-500 mt-1">
                      Display the current date while the screen
                      saver is active.
                    </p>
                  </div>
                </label>
              </div>
            </div>

            <!-- ===============================================
                 SCREEN SAVER SUMMARY
            ================================================ -->
            <div
              class="rounded-xl border border-slate-200 bg-slate-50/80 p-4"
            >
              <div class="flex items-start gap-3">
                <div
                  class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
                >
                  <i class="fa-solid fa-desktop"></i>
                </div>

                <div>
                  <Label class="font-semibold text-slate-800">
                    Screen Saver Overview
                  </Label>

                  <p
                    class="text-sm leading-6 text-slate-500 mt-1"
                  >
                    The screen saver will automatically activate
                    after the configured inactivity period and
                    display the selected media.
                  </p>
                </div>
              </div>

              <!-- Summary Cards -->
              <div
                class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3"
              >
                <!-- Timeout -->
                <div
                  class="rounded-lg bg-white border border-slate-200 p-3"
                >
                  <div
                    class="text-xs text-slate-500 mb-1"
                  >
                    Timeout
                  </div>

                  <div
                    class="text-lg font-bold text-slate-800"
                  >
                    {{ form.screen_saver_timeout }}
                  </div>

                  <div class="text-[11px] text-slate-400">
                    minutes
                  </div>
                </div>

                <!-- Type -->
                <div
                  class="rounded-lg bg-white border border-slate-200 p-3"
                >
                  <div
                    class="text-xs text-slate-500 mb-1"
                  >
                    Type
                  </div>

                  <div
                    class="text-sm font-bold text-slate-800 capitalize"
                  >
                    {{
                      form.screen_saver_type === "slider"
                        ? "Image Slider"
                        : form.screen_saver_type
                    }}
                  </div>

                  <div class="text-[11px] text-slate-400">
                    media mode
                  </div>
                </div>

                <!-- Clock -->
                <div
                  class="rounded-lg bg-white border border-slate-200 p-3"
                >
                  <div
                    class="text-xs text-slate-500 mb-1"
                  >
                    Clock
                  </div>

                  <div
                    class="text-sm font-bold"
                    :class="
                      form.screen_saver_show_clock
                        ? 'text-emerald-600'
                        : 'text-slate-500'
                    "
                  >
                    {{
                      form.screen_saver_show_clock
                        ? "Visible"
                        : "Hidden"
                    }}
                  </div>

                  <div class="text-[11px] text-slate-400">
                    display option
                  </div>
                </div>

                <!-- Date -->
                <div
                  class="rounded-lg bg-white border border-slate-200 p-3"
                >
                  <div
                    class="text-xs text-slate-500 mb-1"
                  >
                    Date
                  </div>

                  <div
                    class="text-sm font-bold"
                    :class="
                      form.screen_saver_show_date
                        ? 'text-emerald-600'
                        : 'text-slate-500'
                    "
                  >
                    {{
                      form.screen_saver_show_date
                        ? "Visible"
                        : "Hidden"
                    }}
                  </div>

                  <div class="text-[11px] text-slate-400">
                    display option
                  </div>
                </div>
              </div>

              <!-- Tip -->
              <div
                class="mt-4 flex items-start gap-2.5 rounded-lg bg-primary/5 border border-primary/10 px-3.5 py-3"
              >
                <i
                  class="fa-solid fa-lightbulb text-primary mt-0.5 text-sm"
                ></i>

                <p class="text-xs leading-5 text-slate-600">
                  <span class="font-semibold text-slate-700">
                    Tip:
                  </span>
                  Use a shorter timeout for public displays and
                  longer intervals for personal workstations.
                  Images and videos should be optimized for the
                  screen's resolution.
                </p>
              </div>
            </div>
          </div>

          <!-- =================================================
               DISABLED STATE
          ================================================== -->
          <div
            v-else
            class="mt-5 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center"
          >
            <div
              class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400"
            >
              <i class="fa-solid fa-moon text-lg"></i>
            </div>

            <Label
              class="block mt-3 font-semibold text-slate-700"
            >
              Screen Saver is Disabled
            </Label>

            <p
              class="max-w-md mx-auto mt-1.5 text-sm leading-6 text-slate-500"
            >
              Enable the screen saver above to configure the
              inactivity timeout, media type, and display options.
            </p>
          </div>

          <!-- =================================================
               FOOTER
          ================================================== -->
          <div
            class="mt-6 flex flex-col-reverse sm:flex-row gap-3 justify-end border-t border-slate-100 pt-4"
          >
            <Button
              @click="toggleModal"
              type="danger"
              text="Close"
            />

            <Button
              :submit="true"
              :text="
                form.processing
                  ? 'Saving...'
                  : 'Save Settings'
              "
              :processing="form.processing"
              :disabled="form.processing"
            />
          </div>
        </form>
      </div>
    </div>
  </Modal>
</template>