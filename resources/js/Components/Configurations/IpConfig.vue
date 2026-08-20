<script setup>
import { useForm } from "@inertiajs/vue3";
import Label from "@/Components/Label.vue";
import TextInput from "@/Components/Elements/TextInput.vue";
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
const newIp = ref("");
const ipError = ref("");

const toggleModal = () => {
  showModal.value = !showModal.value;
};

/*
|--------------------------------------------------------------------------
| Parse Blacklisted IPs
|--------------------------------------------------------------------------
*/

const parseBlacklistedIps = () => {
  const value = props.config?.blacklisted_ips;

  if (!value) {
    return [];
  }

  // Already an array
  if (Array.isArray(value)) {
    return [...value];
  }

  // JSON string
  if (typeof value === "string") {
    try {
      const parsed = JSON.parse(value);

      if (Array.isArray(parsed)) {
        return parsed;
      }
    } catch (error) {
      // Ignore invalid JSON
    }

    // Support comma-separated values as fallback
    return value
      .split(",")
      .map((ip) => ip.trim())
      .filter(Boolean);
  }

  return [];
};

/*
|--------------------------------------------------------------------------
| Form
|--------------------------------------------------------------------------
*/

const form = useForm({
  enable_ip_blacklist:
    props.config?.enable_ip_blacklist == 1,

  blacklisted_ips: parseBlacklistedIps(),

  log_blocked_ip_attempts:
    props.config?.log_blocked_ip_attempts == 1,
});

/*
|--------------------------------------------------------------------------
| IP Validation
|--------------------------------------------------------------------------
*/

const isValidIPv4 = (ip) => {
  const parts = ip.trim().split(".");

  if (parts.length !== 4) {
    return false;
  }

  return parts.every((part) => {
    if (!/^\d+$/.test(part)) {
      return false;
    }

    const number = Number(part);

    return number >= 0 && number <= 255;
  });
};

const isValidIPv6 = (ip) => {
  return /^[0-9a-fA-F:]+$/.test(ip) && ip.includes(":");
};

const isValidIp = (ip) => {
  return isValidIPv4(ip) || isValidIPv6(ip);
};

/*
|--------------------------------------------------------------------------
| Add IP
|--------------------------------------------------------------------------
*/

const addIp = () => {
  const ip = newIp.value.trim();

  ipError.value = "";

  if (!ip) {
    ipError.value = "Please enter an IP address.";
    return;
  }

  if (!isValidIp(ip)) {
    ipError.value =
      "Please enter a valid IPv4 or IPv6 address.";
    return;
  }

  if (form.blacklisted_ips.includes(ip)) {
    ipError.value = "This IP address is already blacklisted.";
    return;
  }

  form.blacklisted_ips.push(ip);

  newIp.value = "";
};

/*
|--------------------------------------------------------------------------
| Remove IP
|--------------------------------------------------------------------------
*/

const removeIp = (index) => {
  form.blacklisted_ips.splice(index, 1);
};

/*
|--------------------------------------------------------------------------
| Clear All IPs
|--------------------------------------------------------------------------
*/

const clearIps = () => {
  form.blacklisted_ips = [];
};

/*
|--------------------------------------------------------------------------
| Save
|--------------------------------------------------------------------------
*/

const save = () => {
  form.post(route("configuration.setting.update"), {
    preserveScroll: true,

    onSuccess: () => {
      showModal.value = false;
    },
  });
};

/*
|--------------------------------------------------------------------------
| Computed
|--------------------------------------------------------------------------
*/

const blockedIpCount = computed(() => {
  return form.blacklisted_ips.length;
});
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
      <i class="fa-solid fa-network-wired"></i>
    </div>

    <!-- Content -->

    <div class="text-left">

      <span class="block font-medium text-slate-800">
        IP Security
      </span>

      <span class="block text-xs text-slate-500 mt-0.5">
        Manage IP blacklist and blocked access attempts
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
            <i class="fa-solid fa-shield-halved"></i>
          </div>

          <!-- Title -->

          <div>

            <Label
              class="font-bold text-xl text-slate-800"
            >
              IP Security
            </Label>

            <p class="text-sm text-slate-500 mt-0.5">
              Control access using IP address blacklisting and
              monitor blocked connection attempts.
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
               ENABLE IP BLACKLIST
          ================================================== -->

          <label
            for="enable_ip_blacklist"
            class="group flex items-center justify-between gap-4 rounded-xl border p-4 cursor-pointer transition-all duration-200"
            :class="
              form.enable_ip_blacklist
                ? 'border-primary/30 bg-primary/5'
                : 'border-slate-200 hover:bg-slate-50'
            "
          >

            <div class="flex items-start gap-3">

              <div
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg"
                :class="
                  form.enable_ip_blacklist
                    ? 'bg-primary text-white'
                    : 'bg-slate-100 text-slate-500'
                "
              >
                <i class="fa-solid fa-ban"></i>
              </div>

              <div>

                <Label
                  for="enable_ip_blacklist"
                  class="font-semibold text-slate-800 cursor-pointer"
                >
                  Enable IP Blacklist
                </Label>

                <p class="text-sm text-slate-500 mt-1">
                  Prevent users or devices from accessing the
                  application from specified IP addresses.
                </p>

              </div>

            </div>


            <!-- Toggle -->

            <div class="relative shrink-0">

              <input
                id="enable_ip_blacklist"
                v-model="form.enable_ip_blacklist"
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
               BLACKLIST SETTINGS
          ================================================== -->

          <div
            v-if="form.enable_ip_blacklist"
            class="mt-5"
          >

            <!-- Section Header -->

            <div class="flex items-center justify-between mb-3">

              <div class="flex items-center gap-2">

                <div
                  class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary"
                >
                  <i class="fa-solid fa-list-check text-sm"></i>
                </div>

                <div>

                  <Label
                    class="font-semibold text-base text-slate-800"
                  >
                    Blacklisted IP Addresses
                  </Label>

                  <p class="text-xs text-slate-500">
                    Add IP addresses that should be denied access.
                  </p>

                </div>

              </div>


              <!-- Count -->

              <span
                class="inline-flex items-center rounded-full bg-primary/10 px-2.5 py-1 text-xs font-semibold text-primary"
              >
                {{ blockedIpCount }}
                {{ blockedIpCount === 1 ? "IP" : "IPs" }}
              </span>

            </div>


            <!-- Add IP -->

            <div
              class="rounded-xl border border-slate-200 p-4"
            >

              <div class="flex flex-col sm:flex-row gap-3">

                <div class="flex-1">

                  <TextInput
                    v-model="newIp"
                    type="text"
                    text="IP Address"
                    placeholder="Example: 192.168.1.100"
                    @keyup.enter="addIp"
                  />

                </div>

                <div
                  class="flex items-end"
                >

                  <button
                    type="button"
                    @click="addIp"
                    class="h-[42px] px-5 rounded-xl bg-primary text-white text-sm font-medium hover:opacity-90 transition-all flex items-center justify-center gap-2 w-full sm:w-auto"
                  >

                    <i class="fa-solid fa-plus"></i>

                    Add IP

                  </button>

                </div>

              </div>


              <!-- Validation Error -->

              <p
                v-if="ipError"
                class="mt-2 text-xs text-red-500 flex items-center gap-1"
              >

                <i class="fa-solid fa-circle-exclamation"></i>

                {{ ipError }}

              </p>


              <!-- Help -->

              <div
                class="mt-3 flex items-start gap-2 text-xs text-slate-500"
              >

                <i
                  class="fa-solid fa-circle-info mt-0.5 text-primary"
                ></i>

                <span>
                  You can add both IPv4 and IPv6 addresses.
                  Each address will be blocked when the blacklist
                  is enabled.
                </span>

              </div>

            </div>


            <!-- =================================================
                 IP LIST
            ================================================== -->

            <div
              v-if="form.blacklisted_ips.length"
              class="mt-3 rounded-xl border border-slate-200 overflow-hidden"
            >

              <!-- List Header -->

              <div
                class="flex items-center justify-between px-4 py-3 bg-slate-50 border-b border-slate-200"
              >

                <div class="flex items-center gap-2">

                  <i
                    class="fa-solid fa-server text-slate-400"
                  ></i>

                  <span
                    class="text-sm font-medium text-slate-700"
                  >
                    Blocked Addresses
                  </span>

                </div>

                <button
                  type="button"
                  @click="clearIps"
                  class="text-xs font-medium text-red-500 hover:text-red-600 transition"
                >
                  Clear All
                </button>

              </div>


              <!-- IP Items -->

              <div class="divide-y divide-slate-100">

                <div
                  v-for="(ip, index) in form.blacklisted_ips"
                  :key="`${ip}-${index}`"
                  class="flex items-center justify-between gap-3 px-4 py-3 hover:bg-slate-50 transition"
                >

                  <div class="flex items-center gap-3">

                    <div
                      class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-50 text-red-500"
                    >
                      <i class="fa-solid fa-ban text-sm"></i>
                    </div>

                    <div>

                      <code
                        class="text-sm font-medium text-slate-700"
                      >
                        {{ ip }}
                      </code>

                      <p class="text-[11px] text-slate-400">
                        Blocked IP address
                      </p>

                    </div>

                  </div>


                  <!-- Remove -->

                  <button
                    type="button"
                    @click="removeIp(index)"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-500 transition"
                    title="Remove IP"
                  >

                    <i class="fa-solid fa-trash text-sm"></i>

                  </button>

                </div>

              </div>

            </div>


            <!-- Empty State -->

            <div
              v-else
              class="mt-3 rounded-xl border border-dashed border-slate-300 p-6 text-center"
            >

              <div
                class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-400"
              >

                <i class="fa-solid fa-network-wired"></i>

              </div>

              <Label
                class="block mt-3 font-medium text-slate-700"
              >
                No IP addresses blacklisted
              </Label>

              <p class="text-xs text-slate-500 mt-1">
                Add an IP address above to prevent it from
                accessing the application.
              </p>

            </div>

          </div>


          <!-- =================================================
               LOG BLOCKED ATTEMPTS
          ================================================== -->

          <div class="mt-5">

            <label
              for="log_blocked_ip_attempts"
              class="group flex items-center justify-between gap-4 rounded-xl border p-4 cursor-pointer transition-all duration-200"
              :class="
                form.log_blocked_ip_attempts
                  ? 'border-primary/30 bg-primary/5'
                  : 'border-slate-200 hover:bg-slate-50'
              "
            >

              <div class="flex items-start gap-3">

                <div
                  class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg"
                  :class="
                    form.log_blocked_ip_attempts
                      ? 'bg-primary text-white'
                      : 'bg-slate-100 text-slate-500'
                  "
                >
                  <i class="fa-solid fa-file-shield"></i>
                </div>

                <div>

                  <Label
                    for="log_blocked_ip_attempts"
                    class="font-semibold text-slate-800 cursor-pointer"
                  >
                    Log Blocked IP Attempts
                  </Label>

                  <p class="text-sm text-slate-500 mt-1">
                    Record access attempts made by blacklisted
                    IP addresses for security auditing.
                  </p>

                </div>

              </div>


              <!-- Toggle -->

              <div class="relative shrink-0">

                <input
                  id="log_blocked_ip_attempts"
                  v-model="form.log_blocked_ip_attempts"
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

          </div>


          <!-- =================================================
               SECURITY INFORMATION
          ================================================== -->

          <div
            class="mt-5 rounded-xl border border-primary/10 bg-primary/5 p-4"
          >

            <div class="flex items-start gap-3">

              <div
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
              >
                <i class="fa-solid fa-lightbulb"></i>
              </div>

              <div>

                <Label
                  class="font-semibold text-slate-800"
                >
                  IP Security Information
                </Label>

                <p
                  class="text-sm leading-6 text-slate-600 mt-1"
                >
                  IP blacklisting can be useful for blocking known
                  malicious addresses, unauthorized systems, or
                  unwanted network traffic. Enable blocked-attempt
                  logging if you need an audit trail for security
                  monitoring and investigation.
                </p>

              </div>

            </div>

          </div>


          <!-- =================================================
               SECURITY SUMMARY
          ================================================== -->

          <div
            class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3"
          >

            <!-- Blacklist -->

            <div
              class="rounded-xl border border-slate-200 bg-white p-3"
            >

              <div class="flex items-center gap-2">

                <i
                  class="fa-solid fa-ban text-sm"
                  :class="
                    form.enable_ip_blacklist
                      ? 'text-red-500'
                      : 'text-slate-400'
                  "
                ></i>

                <span class="text-xs text-slate-500">
                  IP Blacklist
                </span>

              </div>

              <div
                class="mt-1 text-sm font-bold"
                :class="
                  form.enable_ip_blacklist
                    ? 'text-emerald-600'
                    : 'text-slate-500'
                "
              >
                {{
                  form.enable_ip_blacklist
                    ? "Enabled"
                    : "Disabled"
                }}
              </div>

            </div>


            <!-- IP Count -->

            <div
              class="rounded-xl border border-slate-200 bg-white p-3"
            >

              <div class="flex items-center gap-2">

                <i
                  class="fa-solid fa-list text-sm text-slate-400"
                ></i>

                <span class="text-xs text-slate-500">
                  Blocked IPs
                </span>

              </div>

              <div class="mt-1 text-sm font-bold text-slate-700">
                {{ blockedIpCount }}
              </div>

            </div>


            <!-- Logging -->

            <div
              class="rounded-xl border border-slate-200 bg-white p-3"
            >

              <div class="flex items-center gap-2">

                <i
                  class="fa-solid fa-file-lines text-sm"
                  :class="
                    form.log_blocked_ip_attempts
                      ? 'text-emerald-500'
                      : 'text-slate-400'
                  "
                ></i>

                <span class="text-xs text-slate-500">
                  Attempt Logging
                </span>

              </div>

              <div
                class="mt-1 text-sm font-bold"
                :class="
                  form.log_blocked_ip_attempts
                    ? 'text-emerald-600'
                    : 'text-slate-500'
                "
              >
                {{
                  form.log_blocked_ip_attempts
                    ? "Enabled"
                    : "Disabled"
                }}
              </div>

            </div>

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