<script setup>
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import PageHeader from "@/Components/PageHeader.vue";
import TextInput from "@/Components/Elements/TextInput.vue";
import Label from "@/Components/Label.vue";
import { RisingPicker } from "rising-picker";
import { RisingSelect } from "rising-select";
import { computed } from "vue";

const props = defineProps({
  parent: {
    type: Object,
    default: null,
  },
  organization: Object,
});

const form = useForm({
  parent_id: props?.parent?.id,
  parent_name: props?.parent?.name,
  name: props?.organization?.name ?? "",
  email: props?.organization?.email ?? "",
  phone: props?.organization?.phone ?? "",
  website: props?.organization?.website ?? "",

  logo: props?.organization?.logo ?? null,

  address: props?.organization?.address ?? "",
  city: props?.organization?.city ?? "",
  state: props?.organization?.state ?? "",
  country: props?.organization?.country ?? "Nepal",
  postal_code: props?.organization?.postal_code ?? "",

  is_active: props?.organization?.is_active ?? true,

  subscription_status: props?.organization?.subscription_status ?? "trial",
});

const submit = () => {
  if (props?.organization?.id) {
    form.put(route("organizations.update",props?.organization), {
      forceFormData: true,
    });
  } else {
    form.post(route("organizations.store"), {
      forceFormData: true,
    });
  }
};

const logoPreview = computed(() => {
  if (!form.logo) {
    return null;
  }

  if (form.logo instanceof File) {
    return URL.createObjectURL(form.logo);
  }

  return null;
});
</script>

<template>
  <AppLayout>
    <!-- HEADER -->
    <div class="sticky top-0 z-20 border-b border-slate-200 bg-white/90 backdrop-blur">
      <PageHeader
        :description="`Set up your ${
          parent?.id ? 'branch' : 'organization'
        } profile and workspace preferences.`"
        :breadcrumbs="[
          {
            label: parent?.id ? 'Branch' : 'Organizations',
            href: parent?.id ? route('branches.index') : route('organizations.index'),
          },
          {
            label: 'Create',
          },
        ]"
      />
    </div>

    <div class="mx-auto">
      <form @submit.prevent="submit">
        <div class="grid gap-6 lg:grid-cols-[350px_minmax(0,1fr)]">
          <!-- ================================================
               SIDEBAR
          ================================================= -->
          <aside class="sticky top-20 h-fit space-y-4">
            <!-- Preview -->
            <div
              class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >
              <div
                class="h-24 bg-gradient-to-br from-primary/20 via-primary/5 to-white"
              />

              <div class="-mt-10 px-5 pb-5">
                <div
                  class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-2xl border-4 border-white bg-slate-100 shadow-sm"
                >
                  <img
                    v-if="form.logo"
                    :src="logoPreview"
                    :alt="parent?.id ? 'Branch Profile' : 'Organization Profile'"
                    class="h-full w-full object-cover"
                  />

                  <svg
                    v-else
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-8 w-8 text-slate-400"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.5"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"
                    />
                  </svg>
                </div>

                <Label class="mt-4 text-lg font-bold text-slate-800">
                  {{ form.name || parent?.id ? "Your Branch" : "Your Organization" }}
                </Label>

                <Label class="block opacity-60 mt-1 text-xs leading-5 text-slate-400">
                  Your {{ parent?.id ? "branch" : "organization" }} profile will appear
                  throughout the workspace.
                </Label>
              </div>
            </div>

            <!-- Status -->
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
              <div class="flex items-center justify-between gap-4">
                <div>
                  <Label class="block text-xs font-semibold text-slate-700"
                    >{{ parent?.id ? "Branch" : "Organization" }} status</Label
                  >

                  <Label class="opacity-60 mt-1 text-[11px] text-slate-400">
                    Control whether this organization is active.
                  </Label>
                </div>

                <button
                  type="button"
                  @click="form.is_active = !form.is_active"
                  class="relative h-6 w-11 shrink-0 rounded-full transition"
                  :class="form.is_active ? 'bg-primary' : 'bg-slate-200'"
                >
                  <span
                    class="absolute top-1 h-4 w-4 rounded-full bg-white shadow-sm transition"
                    :class="form.is_active ? 'left-6' : 'left-1'"
                  />
                </button>
              </div>

              <div class="mt-4 flex items-center gap-2">
                <span
                  class="h-2 w-2 rounded-full"
                  :class="form.is_active ? 'bg-emerald-500' : 'bg-slate-300'"
                />

                <Label class="text-[11px] font-medium text-slate-500">
                  {{ form.is_active ? "Active" : "Inactive" }}
                </Label>
              </div>
            </div>
          </aside>

          <!-- ================================================
               MAIN
          ================================================= -->
          <main class="space-y-6">
            <!-- ==============================================
                 ORGANIZATION PROFILE
            =============================================== -->
            <section
              class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >
              <div class="border-b border-slate-100 px-6 py-5">
                <Label class="text-sm font-bold text-slate-800"
                  >{{ parent?.id ? "Branch" : "Organization" }} Profile</Label
                >

                <Label class="block opacity-60 mt-1 text-[11px] text-slate-400">
                  Basic information about your
                  {{ parent?.id ? "lranch" : "organization" }}.
                </Label>
              </div>

              <div class="p-6">
                <div class="grid gap-4 sm:grid-cols-2">
                  <!-- {{ parent }} -->
                  <div class="col-span-2" v-if="parent?.id">
                    <TextInput
                      v-model="form.parent_name"
                      type="text"
                      text="Organization Name"
                      placeholder="e.g. Rising Technology"
                      required
                      :disable="true"
                    />
                  </div>
                  <div>
                    <TextInput
                      v-model="form.name"
                      type="text"
                      :text="parent?.id ? 'Branch Name' : 'Organization Name'"
                      placeholder="e.g. Rising Technology"
                      required
                    />

                    <p v-if="form.errors.name" class="mt-1 text-[11px] text-red-500">
                      {{ form.errors.name }}
                    </p>
                  </div>

                  <div>
                    <TextInput
                      v-model="form.email"
                      type="email"
                      text="Email"
                      placeholder="hello@example.com"
                    />
                  </div>

                  <div>
                    <TextInput
                      v-model="form.phone"
                      type="text"
                      text="Phone"
                      placeholder="+977 98XXXXXXXX"
                    />
                  </div>

                  <div>
                    <TextInput
                      v-model="form.website"
                      type="url"
                      text="Website"
                      placeholder="https://example.com"
                    />
                  </div>
                </div>

                <!-- ==========================================
                     BRAND ASSETS
                =========================================== -->
                <div class="mt-6 border-t border-slate-100 pt-6">
                  <div class="mb-4">
                    <Label class="text-xs font-bold text-slate-700">Brand Assets</Label>

                    <Label class="block opacity-60 mt-1 text-[11px] text-slate-400">
                      Upload your organization's logo and favicon.
                    </Label>
                  </div>

                  <div class="grid gap-4 sm:grid-cols-1">
                    <!-- LOGO -->
                    <div>
                      <RisingPicker
                        v-model="form.logo"
                        label="Organization Logo"
                        accept="image/*"
                        primaryColor="var(--primary)"
                      />

                      <p v-if="form.errors.logo" class="mt-1 text-[11px] text-red-500">
                        {{ form.errors.logo }}
                      </p>
                    </div>

                    <!-- FAVICON -->
                  </div>

                  <!-- ==============================================
                 LOCATION
            =============================================== -->

                  <div class="border-b border-slate-100 py-5">
                    <Label class="text-sm font-bold text-slate-800">Location</Label>

                    <Label class="block opacity-60 mt-1 text-[11px] text-slate-400">
                      Where your organization is located.
                    </Label>
                  </div>

                  <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                      <Label class="mb-1 block text-sm font-semibold text-slate-600">
                        Address
                      </Label>

                      <textarea
                        v-model="form.address"
                        rows="2"
                        placeholder="Street address"
                        class="w-full resize-none rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-700 outline-none transition placeholder:text-slate-400 hover:border-slate-300 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10"
                      />
                    </div>

                    <TextInput
                      v-model="form.city"
                      type="text"
                      text="City"
                      placeholder="Nepalgunj"
                    />

                    <TextInput
                      v-model="form.state"
                      type="text"
                      text="State / Province"
                      placeholder="Lumbini"
                    />

                    <!-- RISING SELECT -->
                    <div>
                      <TextInput
                        v-model="form.country"
                        type="text"
                        text="Country"
                        placeholder="Country"
                      />

                      <p v-if="form.errors.country" class="mt-1 text-[11px] text-red-500">
                        {{ form.errors.country }}
                      </p>
                    </div>

                    <TextInput
                      v-model="form.postal_code"
                      type="text"
                      text="Postal Code"
                      placeholder="21900"
                    />
                  </div>
                </div>
              </div>
            </section>

            <!-- ==============================================
                 ACTIONS
            =============================================== -->
            <div
              class="sticky bottom-4 z-10 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white/95 p-4 shadow-lg shadow-slate-200/50 backdrop-blur sm:flex-row sm:items-center sm:justify-between"
            >
              <div class="flex items-center gap-2">
                <div
                  class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-50 text-emerald-500"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-3.5 w-3.5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                </div>

                <span class="text-[10px] text-slate-400">
                  You can update these settings later.
                </span>
              </div>

              <div class="flex items-center gap-2">
                <button
                  type="button"
                  @click="window.history.back()"
                  class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-50"
                >
                  Cancel
                </button>

                <button
                  type="submit"
                  :disabled="form.processing || !form.name?.trim()"
                  class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-60"
                >
                  <svg
                    v-if="form.processing"
                    class="h-3.5 w-3.5 animate-spin"
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
                    />

                    <path
                      class="opacity-75"
                      fill="currentColor"
                      d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                    />
                  </svg>

                  <span v-if="organization?.id">
                    {{
                      form.processing
                        ? "Updating..."
                        : parent?.id
                        ? "Update Branch"
                        : "Update Organization"
                    }}
                  </span>
                  <span v-else>
                    {{
                      form.processing
                        ? "Creating..."
                        : parent?.id
                        ? "Create Branch"
                        : "Create Organization"
                    }}
                  </span>
                </button>
              </div>
            </div>
          </main>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
