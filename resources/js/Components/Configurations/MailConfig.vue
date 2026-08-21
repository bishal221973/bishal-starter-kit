<script setup>
import { useForm } from "@inertiajs/vue3";
import Label from "@/Components/Label.vue";
import Button from "../Elements/Button.vue";
import Modal from "../Modal.vue";
import { ref, computed } from "vue";

const props = defineProps({
  config: {
    type: Object,
    default: () => ({}),
  },
});

const showModal = ref(false);
const showPassword = ref(false);

const toggleModal = () => {
  showModal.value = !showModal.value;

  if (!showModal.value) {
    showPassword.value = false;
  }
};

const form = useForm({
  mailer: props.config?.mailer ?? "smtp",
  host: props.config?.host ?? "",
  port: props.config?.port ?? 587,
  username: props.config?.username ?? "",
  password: props.config?.password ?? "",
  encryption: props.config?.encryption ?? "tls",
  from_address: props.config?.from_address ?? "",
  from_name: props.config?.from_name ?? "",
});

const mailConfigured = computed(() => {
  return form.host && form.port && form.username && form.password && form.from_address;
});

const mailStatus = computed(() => {
  if (!mailConfigured.value) {
    return {
      label: "Not Configured",
      class: "bg-slate-100 text-slate-600",
      icon: "fa-circle-question",
    };
  }

  return {
    label: "Mail Configured",
    class: "bg-emerald-100 text-emerald-700",
    icon: "fa-circle-check",
  };
});

const clearConfiguration = () => {
  form.mailer = "smtp";
  form.host = "";
  form.port = 587;
  form.username = "";
  form.password = "";
  form.encryption = "tls";
  form.from_address = "";
  form.from_name = "";
};

const save = () => {
  form.post(route("configuration.mail.setting.update"), {
    preserveScroll: true,

    onSuccess: () => {
      showModal.value = false;
      showPassword.value = false;
    },
  });
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
    <div
      class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary transition-all duration-200 group-hover:bg-primary group-hover:text-white"
    >
      <i class="fa-solid fa-envelope"></i>
    </div>

    <div class="text-left">
      <Label class="block font-medium text-sm"> Mail Configuration </Label>

      <Label class="block text-[12px] opacity-60 mt-0.5">
        Configure SMTP and application email settings
      </Label>
    </div>

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
          <div
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
          >
            <i class="fa-solid fa-envelope"></i>
          </div>

          <div>
            <Label class="font-bold text-lg"> Mail Configuration </Label>

            <Label class="block text-xs opacity-70">
              Configure SMTP settings used to send application emails.
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
               MAILER
          ================================================== -->

          <div class="mb-3">
            <Label class="font-semibold text-sm"> Mail Driver </Label>

            <Label class="block text-xs opacity-70 mb-2">
              Select the mail transport used by your application.
            </Label>

            <select
              v-model="form.mailer"
              class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition-all duration-200 hover:border-slate-300 focus:border-primary focus:ring-4 focus:ring-primary/10"
            >
              <option value="smtp">SMTP</option>

              <option value="log">Log</option>
            </select>
          </div>

          <!-- =================================================
               SMTP SERVER
          ================================================== -->

          <div class="mb-3">
            <div class="mb-2">
              <Label class="font-semibold text-sm"> SMTP Server </Label>

              <Label class="block text-xs opacity-70">
                Configure your SMTP server connection.
              </Label>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
              <!-- Host -->

              <div class="sm:col-span-2">
                <Label class="block text-xs font-medium mb-1.5"> SMTP Host </Label>

                <input
                  v-model="form.host"
                  type="text"
                  autocomplete="off"
                  placeholder="smtp.gmail.com"
                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 placeholder-slate-400 outline-none transition-all duration-200 hover:border-slate-300 focus:border-primary focus:ring-4 focus:ring-primary/10"
                />

                <p v-if="form.errors.host" class="mt-1 text-xs text-red-500">
                  {{ form.errors.host }}
                </p>
              </div>

              <!-- Port -->

              <div>
                <Label class="block text-xs font-medium mb-1.5"> Port </Label>

                <input
                  v-model="form.port"
                  type="number"
                  min="1"
                  max="65535"
                  placeholder="587"
                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 placeholder-slate-400 outline-none transition-all duration-200 hover:border-slate-300 focus:border-primary focus:ring-4 focus:ring-primary/10"
                />

                <p v-if="form.errors.port" class="mt-1 text-xs text-red-500">
                  {{ form.errors.port }}
                </p>
              </div>
            </div>
          </div>

          <!-- =================================================
               ENCRYPTION
          ================================================== -->

          <div class="mb-3">
            <Label class="block text-xs font-medium mb-1.5"> Encryption </Label>

            <select
              v-model="form.encryption"
              class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition-all duration-200 hover:border-slate-300 focus:border-primary focus:ring-4 focus:ring-primary/10"
            >
              <option value="tls">TLS</option>

              <option value="ssl">SSL</option>

              <option value="">None</option>
            </select>

            <Label class="block text-[11px] opacity-60 mt-1.5">
              Usually use TLS with port 587 or SSL with port 465.
            </Label>
          </div>

          <!-- =================================================
               SMTP AUTHENTICATION
          ================================================== -->

          <div class="mb-3">
            <div class="mb-2">
              <Label class="font-semibold text-sm"> SMTP Authentication </Label>

              <Label class="block text-xs opacity-70">
                Enter the credentials provided by your mail provider.
              </Label>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <!-- Username -->

              <div>
                <Label class="block text-xs font-medium mb-1.5"> Username / Email </Label>

                <input
                  v-model="form.username"
                  type="text"
                  autocomplete="off"
                  placeholder="your@email.com"
                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 placeholder-slate-400 outline-none transition-all duration-200 hover:border-slate-300 focus:border-primary focus:ring-4 focus:ring-primary/10"
                />

                <p v-if="form.errors.username" class="mt-1 text-xs text-red-500">
                  {{ form.errors.username }}
                </p>
              </div>

              <!-- Password -->

              <div>
                <Label class="block text-xs font-medium mb-1.5"> Password </Label>

                <div class="relative">
                  <input
                    v-model="form.password"
                    :type="showPassword ? 'text' : 'password'"
                    autocomplete="new-password"
                    placeholder="SMTP password"
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 pr-12 text-sm text-slate-700 placeholder-slate-400 outline-none transition-all duration-200 hover:border-slate-300 focus:border-primary focus:ring-4 focus:ring-primary/10"
                  />

                  <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute right-2 top-1/2 -translate-y-1/2 flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition"
                    :title="showPassword ? 'Hide password' : 'Show password'"
                  >
                    <i
                      class="fa-solid"
                      :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"
                    ></i>
                  </button>
                </div>

                <p v-if="form.errors.password" class="mt-1 text-xs text-red-500">
                  {{ form.errors.password }}
                </p>
              </div>
            </div>
          </div>

          <!-- =================================================
               FROM ADDRESS
          ================================================== -->

          <div class="mb-5">
            <div class="mb-2">
              <Label class="font-semibold text-sm"> Sender Information </Label>

              <Label class="block text-xs opacity-70">
                Configure the name and email address shown to recipients.
              </Label>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <!-- From Address -->

              <div>
                <Label class="block text-xs font-medium mb-1.5"> From Email </Label>

                <input
                  v-model="form.from_address"
                  type="email"
                  autocomplete="off"
                  placeholder="noreply@example.com"
                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 placeholder-slate-400 outline-none transition-all duration-200 hover:border-slate-300 focus:border-primary focus:ring-4 focus:ring-primary/10"
                />

                <p v-if="form.errors.from_address" class="mt-1 text-xs text-red-500">
                  {{ form.errors.from_address }}
                </p>
              </div>

              <!-- From Name -->

              <div>
                <Label class="block text-xs font-medium mb-1.5"> From Name </Label>

                <input
                  v-model="form.from_name"
                  type="text"
                  autocomplete="off"
                  placeholder="My Application"
                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 placeholder-slate-400 outline-none transition-all duration-200 hover:border-slate-300 focus:border-primary focus:ring-4 focus:ring-primary/10"
                />

                <p v-if="form.errors.from_name" class="mt-1 text-xs text-red-500">
                  {{ form.errors.from_name }}
                </p>
              </div>
            </div>
          </div>

          

          <!-- =================================================
               FOOTER
          ================================================== -->

          <div
            class="flex flex-col-reverse sm:flex-row gap-3 justify-between border-t border-slate-100 pt-4"
          >
            <div></div>

            <div class="flex gap-3 justify-end">
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
