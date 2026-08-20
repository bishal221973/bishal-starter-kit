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
  enable_auto_backup:
    props.config?.enable_auto_backup == 1,

  backup_frequency:
    props.config?.backup_frequency ?? "daily",

  backup_retention_days:
    props.config?.backup_retention_days ?? 30,
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
    label: "Daily",
    value: "daily",
  },
  {
    label: "Weekly",
    value: "weekly",
  },
  {
    label: "Monthly",
    value: "monthly",
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
      <i class="fa-solid fa-database"></i>
    </div>

    <!-- Content -->
    <div class="text-left">
      <Label class="block font-medium text-sm">
        Backup Config
      </Label>

      <Label class="block text-[12px] opacity-60 mt-0.5">
        Manage automatic backups and data retention
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
      <div
        class="px-6 py-4 border-b border-slate-100 bg-card_header_color"
      >
        <div class="flex items-center gap-3">
          <!-- Icon -->
          <div
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
          >
            <i class="fa-solid fa-database"></i>
          </div>

          <!-- Header Text -->
          <div>
            <Label class="font-bold text-lg text-slate-800">
              Backup Configuration
            </Label>

            <Label class="block text-xs opacity-70 mt-0.5">
              Configure automatic backups, scheduling, and data
              retention settings.
            </Label>
          </div>
        </div>
      </div>

      <!-- =====================================================
           BODY
      ====================================================== -->
      <div class="px-5 py-3">
        <form @submit.prevent="save">
          <!-- =================================================
               ENABLE AUTOMATIC BACKUP
          ================================================== -->
          <label
            for="enable_auto_backup"
            class="group flex items-center justify-between gap-4 rounded-xl border p-4 cursor-pointer transition-all duration-200"
            :class="
              form.enable_auto_backup
                ? 'border-primary/30 bg-primary/5'
                : 'border-slate-200 hover:bg-slate-50'
            "
          >
            <div class="flex items-start gap-3">
              <!-- Icon -->
              <div
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg transition-colors"
                :class="
                  form.enable_auto_backup
                    ? 'bg-primary text-white'
                    : 'bg-slate-100 text-slate-500'
                "
              >
                <i class="fa-solid fa-cloud-arrow-up"></i>
              </div>

              <!-- Text -->
              <div>
                <Label
                  for="enable_auto_backup"
                  class="font-semibold text-sm cursor-pointer"
                >
                  Enable Automatic Backup
                </Label>

                <Label class="block text-xs opacity-70 mt-1">
                  Automatically create scheduled backups to protect
                  your application data and simplify recovery.
                </Label>
              </div>
            </div>

            <!-- Toggle -->
            <div class="relative shrink-0">
              <input
                id="enable_auto_backup"
                v-model="form.enable_auto_backup"
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
               BACKUP SETTINGS
          ================================================== -->
          <div
            v-if="form.enable_auto_backup"
            class="mt-3 space-y-3"
          >
            <!-- ===============================================
                 SCHEDULE SETTINGS
            ================================================ -->
            <div>
              <div class="flex items-center gap-2 mb-3">
                <div
                  class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary"
                >
                  <i class="fa-solid fa-calendar-check text-sm"></i>
                </div>

                <div>
                  <Label
                    class="font-semibold text-base text-slate-800"
                  >
                    Backup Schedule
                  </Label>

                  <Label class="block text-xs opacity-70">
                    Choose how frequently automatic backups should
                    be created.
                  </Label>
                </div>
              </div>

              <div
                class="grid grid-cols-1 md:grid-cols-2 gap-4"
              >
                <!-- Frequency -->
                <div
                  class="rounded-xl border border-slate-200 p-4 bg-white"
                >
                  <Label
                    class="font-medium text-slate-700 mb-2 block"
                  >
                    Backup Frequency
                    <span class="text-red-500 ml-1">*</span>
                  </Label>

                  <RisingSelect
                    v-model="form.backup_frequency"
                    :options="options"
                    wrapperBg="bg-white"
                    wrapperRounded="rounded-xl"
                    wrapperClass="border border-slate-200 shadow-sm transition-all duration-200 ease-in-out outline-none hover:border-slate-300 focus:border-primary focus:ring-4 focus:ring-indigo-500/10 focus:shadow-md"
                    inputClass="text-sm focus:ring-0 py-[10px] focus:outline-0"
                    :primaryColor="theme?.primary"
                    placeholder="Select backup frequency"
                  />

                  <Label class="block text-xs opacity-70 mt-0.5">
                    Select how often the system should automatically
                    create a backup.
                  </Label>
                </div>

                <!-- Retention -->
                <div
                  class="rounded-xl border border-slate-200 p-4 bg-white"
                >
                  <TextInput
                    v-model="form.backup_retention_days"
                    type="number"
                    step="1"
                    min="1"
                    text="Backup Retention Days"
                    placeholder="Enter retention days"
                  />

                  <Label class="block text-xs opacity-70 mt-0.5">
                    Number of days backups should be kept before
                    older backups are removed.
                  </Label>
                </div>
              </div>
            </div>

            <!-- ===============================================
                 BACKUP INFORMATION
            ================================================ -->
            <div
                class="mt-4 flex items-start gap-2.5 rounded-lg bg-primary/5 border border-primary/10 px-3.5 py-3"
              >
                <i
                  class="fa-solid fa-lightbulb text-primary mt-0.5 text-sm"
                ></i>

                <Label class="text-xs leading-5 text-slate-600">
                  <Label class="font-semibold text-slate-700">
                    Recommended:
                  </Label>
                  <span class="opacity-60">Daily backups with at least 30 days of retention
                  provide a good balance between data protection
                  and storage usage for most applications.</span>
                </Label>
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
              <i class="fa-solid fa-database text-lg"></i>
            </div>

            <Label
              class="block mt-3 font-semibold text-slate-700"
            >
              Automatic Backup is Disabled
            </Label>

            <Label
              class="max-w-md mx-auto mt-1.5 text-xs leading-6 opacity-60"
            >
              Enable automatic backup above to configure the backup
              schedule <br> and retention period for your application.
            </Label>
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