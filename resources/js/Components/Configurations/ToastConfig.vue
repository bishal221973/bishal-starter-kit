<script setup>
import { useForm } from "@inertiajs/vue3";
import Label from "@/Components/Label.vue";
import TextInput from "@/Components/Elements/TextInput.vue";
import Button from "../Elements/Button.vue";
import Modal from "../Modal.vue";
import { ref } from "vue";
import { RisingSelect } from "rising-select";
import { useTheme } from "@/composables/useTheme.js";

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
  position: props.config?.position ?? "top-right",

  timeout: props.config?.timeout ?? 10,

  closeOnClick: props.config?.closeOnClick == 1,

  draggablePercent: props.config?.draggablePercent ?? "0.7",

  draggable: props.config?.draggable == 1,

  pauseOnFocusLoss: props.config?.pauseOnFocusLoss == 1,

  pauseOnHover: props.config?.pauseOnHover == 1,

  closeButton: props.config?.closeButton == 1,

  hideProgressBar: props.config?.hideProgressBar == 1,

  rtl: props.config?.rtl == 1,

  icon: props.config?.icon ?? "true",
});

const save = () => {
  form.post(route("configuration.setting.update"), {
    preserveScroll: true,

    onSuccess: () => {
      showModal.value = false;
    },
  });
};

const positionOptions = [
  {
    label: "Top Right",
    value: "top-right",
  },
  {
    label: "Top Center",
    value: "top-center",
  },
  {
    label: "Top Left",
    value: "top-left",
  },
  {
    label: "Bottom Right",
    value: "bottom-right",
  },
  {
    label: "Bottom Center",
    value: "bottom-center",
  },
  {
    label: "Bottom Left",
    value: "bottom-left",
  },
];

const iconOptions = [
  {
    label: "Default",
    value: "true",
  },
  {
    label: "No Icon",
    value: "false",
  },
  {
    label: "Rocket",
    value: "fas fa-rocket",
  },
  {
    label: "My Icon Component",
    value: "MyIconComponent",
  },
  {
    label: "Material",
    value: "material",
  },
];

const testNotification = () => {
  const options = {
    position: form.position,
    autoClose: Number(form.timeout) * 1000,
    closeOnClick: form.closeOnClick,
    draggable: form.draggable,
    draggablePercent: Number(form.draggablePercent),
    pauseOnFocusLoss: form.pauseOnFocusLoss,
    pauseOnHover: form.pauseOnHover,
    closeButton: form.closeButton,
    hideProgressBar: form.hideProgressBar,
    rtl: form.rtl,
  };

  toast.success("This is a test notification.", options);
};
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
      <i class="fa-solid fa-bell"></i>
    </div>

    <!-- Content -->
    <div class="text-left">
      <Label class="block font-medium text-sm"> Toast Notification Config </Label>

      <Label class="block text-[12px] opacity-60 mt-0.5">
        Manage notification position, timing, appearance and behavior
      </Label>
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
      <div class="px-6 py-4 border-b border-slate-100 bg-card_header_color">
        <div class="flex items-center gap-3">
          <!-- Icon -->
          <div
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
          >
            <i class="fa-solid fa-bell"></i>
          </div>

          <!-- Header Text -->
          <div>
            <Label class="font-bold text-lg text-slate-800">
              Toast Notification Configuration
            </Label>

            <Label class="block text-xs opacity-70 mt-0.5">
              Configure notification position, duration, interaction and appearance.
            </Label>
          </div>
        </div>
      </div>

      <!-- =====================================================
           BODY
      ====================================================== -->
      <div class="p-5 sm:p-6">
        <form @submit.prevent="save">
          <div class="space-y-6">
            <!-- =================================================
                 POSITION & TIMING
            ================================================== -->
            <div>
              <div class="flex items-center gap-2 mb-3">
                <div
                  class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary"
                >
                  <i class="fa-solid fa-sliders text-sm"></i>
                </div>

                <div>
                  <Label class="font-semibold text-base text-sm">
                    Position & Timing
                  </Label>

                  <Label class="block text-xs opacity-70">
                    Configure where notifications appear and how long they remain visible.
                  </Label>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Position -->
                <div class="rounded-xl border border-slate-200 p-4 bg-white">
                  <Label class="font-medium text-slate-700 mb-2 block">
                    Notification Position
                    <span class="text-red-500 ml-1">*</span>
                  </Label>

                  <RisingSelect
                    v-model="form.position"
                    :options="positionOptions"
                    wrapperBg="bg-white"
                    wrapperRounded="rounded-xl"
                    wrapperClass="border border-slate-200 shadow-sm transition-all duration-200 ease-in-out outline-none hover:border-slate-300 focus:border-primary focus:ring-4 focus:ring-indigo-500/10 focus:shadow-md"
                    inputClass="text-sm focus:ring-0 py-[10px] focus:outline-0"
                    :primaryColor="theme?.primary"
                    placeholder="Select notification position"
                  />

                  <Label class="block text-xs opacity-70 mt-1">
                    Choose where toast notifications should appear on the screen.
                  </Label>
                </div>

                <!-- Timeout -->
                <div class="rounded-xl border border-slate-200 p-4 bg-white">
                  <TextInput
                    v-model="form.timeout"
                    type="number"
                    step="1"
                    min="1"
                    text="Notification Timeout (Seconds)"
                    placeholder="Enter timeout"
                    required
                  />

                  <Label class="block text-xs opacity-70 mt-1">
                    How long the notification should remain visible.
                  </Label>
                </div>
              </div>
            </div>

            <!-- =================================================
                 DRAGGING
            ================================================== -->
            <div>
              <div class="flex items-center gap-2 mb-3">
                <div
                  class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary"
                >
                  <i class="fa-solid fa-hand-pointer text-sm"></i>
                </div>

                <div>
                  <Label class="font-semibold text-base text-sm">
                    Drag & Interaction
                  </Label>

                  <Label class="block text-xs opacity-70">
                    Configure how users can interact with notifications.
                  </Label>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <!-- Draggable -->
                <label
                  class="group flex items-start gap-3 rounded-xl border p-4 cursor-pointer transition-all duration-200"
                  :class="
                    form.draggable
                      ? 'border-primary/30 bg-primary/5'
                      : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50'
                  "
                >
                  <input
                    v-model="form.draggable"
                    type="checkbox"
                    class="mt-0.5 w-5 h-5 rounded cursor-pointer accent-primary"
                  />

                  <div>
                    <Label class="font-medium text-sm cursor-pointer"> Draggable </Label>

                    <Label class="block text-xs opacity-70">
                      Allow users to drag notifications.
                    </Label>
                  </div>
                </label>

                <!-- Close On Click -->
                <label
                  class="group flex items-start gap-3 rounded-xl border p-4 cursor-pointer transition-all duration-200"
                  :class="
                    form.closeOnClick
                      ? 'border-primary/30 bg-primary/5'
                      : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50'
                  "
                >
                  <input
                    v-model="form.closeOnClick"
                    type="checkbox"
                    class="mt-0.5 w-5 h-5 rounded cursor-pointer accent-primary"
                  />

                  <div>
                    <Label class="font-medium text-sm cursor-pointer">
                      Close On Click
                    </Label>

                    <Label class="block text-xs opacity-70">
                      Close the notification when the user clicks it.
                    </Label>
                  </div>
                </label>

                <!-- Pause On Hover -->
                <label
                  class="group flex items-start gap-3 rounded-xl border p-4 cursor-pointer transition-all duration-200"
                  :class="
                    form.pauseOnHover
                      ? 'border-primary/30 bg-primary/5'
                      : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50'
                  "
                >
                  <input
                    v-model="form.pauseOnHover"
                    type="checkbox"
                    class="mt-0.5 w-5 h-5 rounded cursor-pointer accent-primary"
                  />

                  <div>
                    <Label class="font-medium text-sm cursor-pointer">
                      Pause On Hover
                    </Label>

                    <Label class="block text-xs opacity-70">
                      Pause the notification timer while hovering over it.
                    </Label>
                  </div>
                </label>

                <!-- Pause On Focus Loss -->
                <label
                  class="group flex items-start gap-3 rounded-xl border p-4 cursor-pointer transition-all duration-200"
                  :class="
                    form.pauseOnFocusLoss
                      ? 'border-primary/30 bg-primary/5'
                      : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50'
                  "
                >
                  <input
                    v-model="form.pauseOnFocusLoss"
                    type="checkbox"
                    class="mt-0.5 w-5 h-5 rounded cursor-pointer accent-primary"
                  />

                  <div>
                    <Label class="font-medium text-sm cursor-pointer">
                      Pause On Focus Loss
                    </Label>

                    <Label class="block text-xs opacity-70">
                      Pause notifications when the browser loses focus.
                    </Label>
                  </div>
                </label>
              </div>

              <!-- Draggable Percent -->
              <div
                v-if="form.draggable"
                class="mt-3 rounded-xl border border-slate-200 p-4 bg-white"
              >
                <TextInput
                  v-model="form.draggablePercent"
                  type="number"
                  step="0.1"
                  min="0"
                  max="1"
                  text="Draggable Percent"
                  placeholder="0.7"
                  required
                />

                <Label class="block text-xs opacity-70 mt-1">
                  Percentage of the notification width required to trigger a dismiss
                  gesture. Example: 0.7 means 70%.
                </Label>
              </div>
            </div>

            <!-- =================================================
                 APPEARANCE
            ================================================== -->
            <div>
              <div class="flex items-center gap-2 mb-3">
                <div
                  class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary"
                >
                  <i class="fa-solid fa-palette text-sm"></i>
                </div>

                <div>
                  <Label class="font-semibold text-base text-sm"> Appearance </Label>

                  <Label class="block text-xs opacity-70">
                    Customize the visual behavior of toast notifications.
                  </Label>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <!-- Close Button -->
                <label
                  class="group flex items-start gap-3 rounded-xl border p-4 cursor-pointer transition-all duration-200"
                  :class="
                    form.closeButton
                      ? 'border-primary/30 bg-primary/5'
                      : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50'
                  "
                >
                  <input
                    v-model="form.closeButton"
                    type="checkbox"
                    class="mt-0.5 w-5 h-5 rounded cursor-pointer accent-primary"
                  />

                  <div>
                    <Label class="font-medium text-sm cursor-pointer">
                      Close Button
                    </Label>

                    <Label class="block text-xs opacity-70">
                      Display a close button on the notification.
                    </Label>
                  </div>
                </label>

                <!-- Progress Bar -->
                <label
                  class="group flex items-start gap-3 rounded-xl border p-4 cursor-pointer transition-all duration-200"
                  :class="
                    form.hideProgressBar
                      ? 'border-primary/30 bg-primary/5'
                      : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50'
                  "
                >
                  <input
                    v-model="form.hideProgressBar"
                    type="checkbox"
                    class="mt-0.5 w-5 h-5 rounded cursor-pointer accent-primary"
                  />

                  <div>
                    <Label class="font-medium text-sm cursor-pointer">
                      Hide Progress Bar
                    </Label>

                    <Label class="block text-xs opacity-70">
                      Hide the countdown progress bar from notifications.
                    </Label>
                  </div>
                </label>

                <!-- RTL -->
                <label
                  class="group flex items-start gap-3 rounded-xl border p-4 cursor-pointer transition-all duration-200"
                  :class="
                    form.rtl
                      ? 'border-primary/30 bg-primary/5'
                      : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50'
                  "
                >
                  <input
                    v-model="form.rtl"
                    type="checkbox"
                    class="mt-0.5 w-5 h-5 rounded cursor-pointer accent-primary"
                  />

                  <div>
                    <Label class="font-medium text-sm cursor-pointer"> RTL </Label>

                    <Label class="block text-xs opacity-70">
                      Display notifications using right-to-left direction.
                    </Label>
                  </div>
                </label>

                <!-- Icon -->
                <div class="rounded-xl border border-slate-200 p-4 bg-white">
                  <Label class="font-medium text-slate-700 mb-2 block">
                    Notification Icon
                  </Label>

                  <RisingSelect
                    v-model="form.icon"
                    :options="iconOptions"
                    wrapperBg="bg-white"
                    wrapperRounded="rounded-xl"
                    wrapperClass="border border-slate-200 shadow-sm transition-all duration-200 ease-in-out outline-none hover:border-slate-300 focus:border-primary focus:ring-4 focus:ring-indigo-500/10 focus:shadow-md"
                    inputClass="text-sm focus:ring-0 py-[10px] focus:outline-0"
                    :primaryColor="theme?.primary"
                    placeholder="Select notification icon"
                  />

                  <Label class="block text-xs opacity-70 mt-1">
                    Select the icon style used by toast notifications.
                  </Label>
                </div>
              </div>
            </div>

            <!-- =================================================
                 SUMMARY
            ================================================== -->
            <div
              class="mt-4 flex items-start gap-2.5 rounded-lg bg-primary/5 border border-primary/10 px-3.5 py-3"
            >
              <i class="fa-solid fa-lightbulb text-primary mt-0.5 text-sm"></i>

              <Label class="text-xs leading-5 text-slate-600">
                <Label class="font-semibold text-slate-700"> Tip: </Label>

                <span class="opacity-60">
                  Keep the notification timeout short for success and informational
                  messages. Enable pause-on-hover if users need more time to read
                  notifications.
                </span>
              </Label>
            </div>
          </div>

          <!-- =================================================
               FOOTER
          ================================================== -->
          <div
            class="flex flex-col-reverse sm:flex-row gap-3 justify-between border-t border-slate-100 pt-4 mt-6"
          >
            <Button type="secondary" text="Test Notification" @click="testNotification" />

            <div class="flex flex-col-reverse sm:flex-row gap-3">
              <Button @click="toggleModal" type="danger" text="Close" />

              <Button
                :submit="true"
                :text="form.processing ? 'Saving...' : 'Save Settings'"
                :processing="form.processing"
                :disabled="form.processing"
              />
            </div>
          </div>
        </form>
      </div>
    </div>
  </Modal>
</template>
