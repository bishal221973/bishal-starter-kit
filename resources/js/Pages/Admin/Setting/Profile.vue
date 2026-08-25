<script setup>
import { computed, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import PageHeader from "@/Components/PageHeader.vue";
import TextInput from "@/Components/Elements/TextInput.vue";
import Label from "@/Components/Label.vue";
import { RisingPicker } from "rising-picker";
import { RisingSelect } from "rising-select";
import ProfileMenu from "./Include/ProfileMenu.vue";
import PersonalInfo from "./Include/PersonalInfo.vue";
const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
  organization: {
    type: Object,
    default: null,
  },
  documents: {
    type: Array,
    default: () => [],
  },
});

const activeTab = ref("personal");
const photoPreview = ref(null);

const tabs = [
  {
    key: "personal",
    label: "Personal",
    icon: "fa-regular fa-user",
  },
  {
    key: "employment",
    label: "Employment",
    icon: "fa-solid fa-briefcase",
  },
  {
    key: "contact",
    label: "Contact",
    icon: "fa-solid fa-address-book",
  },
  {
    key: "emergency",
    label: "Emergency",
    icon: "fa-solid fa-phone",
  },
  {
    key: "identification",
    label: "Identification",
    icon: "fa-regular fa-id-card",
  },
];

const form = useForm({
  name: props.user?.name ?? "",
  email: props.user?.email ?? "",

  profile_photo: null,

  gender: props.employee?.gender ?? null,
  date_of_birth: props.employee?.date_of_birth ?? null,

  personal_email: props.employee?.personal_email ?? "",
  personal_phone: props.employee?.personal_phone ?? "",

  address: props.employee?.address ?? "",
  city: props.employee?.city ?? "",
  state: props.employee?.state ?? "",
  country: props.employee?.country ?? "",
  postal_code: props.employee?.postal_code ?? "",

  employee_code: props.employee?.employee_code ?? "",
  employee_type: props.employee?.employee_type ?? "full_time",
  department: props.employee?.department ?? "",
  designation: props.employee?.designation ?? "",

  joined_at: props.employee?.joined_at ?? null,
  probation_ends_at: props.employee?.probation_ends_at ?? null,
  employment_ends_at: props.employee?.employment_ends_at ?? null,

  salary: props.employee?.salary ?? "",
  salary_type: props.employee?.salary_type ?? "monthly",

  emergency_contact_name: props.employee?.emergency_contact_name ?? "",
  emergency_contact_phone: props.employee?.emergency_contact_phone ?? "",
  emergency_contact_relation: props.employee?.emergency_contact_relation ?? "",

  national_id: props.employee?.national_id ?? "",
  tax_number: props.employee?.tax_number ?? "",
});

const genderOptions = [
  {
    value: "male",
    label: "Male",
  },
  {
    value: "female",
    label: "Female",
  },
  {
    value: "other",
    label: "Other",
  },
];

const employeeTypeOptions = [
  {
    value: "full_time",
    label: "Full Time",
  },
  {
    value: "part_time",
    label: "Part Time",
  },
  {
    value: "contract",
    label: "Contract",
  },
  {
    value: "intern",
    label: "Intern",
  },
];

const salaryTypeOptions = [
  {
    value: "monthly",
    label: "Monthly",
  },
  {
    value: "yearly",
    label: "Yearly",
  },
  {
    value: "hourly",
    label: "Hourly",
  },
];

const initials = computed(() => {
  const name = props.user?.name || "User";

  return name
    .split(" ")
    .filter(Boolean)
    .slice(0, 2)
    .map((word) => word.charAt(0).toUpperCase())
    .join("");
});

const profilePhoto = computed(() => {
  if (photoPreview.value) {
    return photoPreview.value;
  }

  if (props.user?.profile_photo_url) {
    return props.user.profile_photo_url;
  }

  if (props.user?.profile_photo_path) {
    return `/storage/${props.user.profile_photo_path}`;
  }

  return null;
});

const handlePhotoChange = (event) => {
  const file = event.target.files?.[0];

  if (!file) {
    return;
  }

  form.profile_photo = file;

  photoPreview.value = URL.createObjectURL(file);
};

const submit = () => {
  form.post(route("profile.update"), {
    preserveScroll: true,
    forceFormData: true,
    onSuccess: () => {
      photoPreview.value = null;
    },
  });
};

const removePhoto = () => {
  form.profile_photo = null;
  photoPreview.value = null;
};

const goToTab = (tab) => {
  activeTab.value = tab;
};
</script>

<template>
  <AppLayout>
    <!-- HEADER -->
    <div class="sticky top-0 z-20 border-b border-slate-200 bg-white/90 backdrop-blur">
      <PageHeader
        description="Manage your personal information, employment details, contact information and identification."
        :breadcrumbs="[
          {
            label: 'User',
            href: '#',
          },
          {
            label: 'Profile',
          },
        ]"
      />
    </div>

    <div class="mx-auto">
      <!-- PROFILE HERO -->

      <!-- CONTENT -->
      <div class="mt-6 flex">
        <!-- SIDEBAR -->
        <ProfileMenu
          :user="user"
          :employee="employee"
          :tabs="tabs"
          :active-tab="activeTab"
          :profile-photo="profilePhoto"
          @update:active-tab="activeTab = $event"
          @photo-change="handlePhotoChange"
        />

        <!-- MAIN -->
        <main class="w-full">
          <!-- PERSONAL -->
          <section v-if="activeTab === 'personal'" class="space-y-6">
            <PersonalInfo :user="user" :employee="employee" />

            <!-- PASSWORD INFO -->
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
              <div class="flex gap-4">
                <div
                  class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700"
                >
                  <i class="fa-solid fa-shield-halved"></i>
                </div>

                <div>
                  <h3 class="text-sm font-semibold text-amber-900">
                    Keep your account secure
                  </h3>

                  <p class="mt-1 text-sm leading-6 text-amber-800">
                    Your password and login security settings are managed separately from
                    your profile information.
                  </p>

                  <button
                    type="button"
                    class="mt-3 text-sm font-semibold text-amber-900 hover:underline"
                  >
                    Change password
                    <i class="fa-solid fa-arrow-right ml-1 text-xs"></i>
                  </button>
                </div>
              </div>
            </div>
          </section>

          <!-- EMPLOYMENT -->
          <section v-if="activeTab === 'employment'" class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
              <div class="border-b border-slate-100 px-6 py-5">
                <h2 class="font-semibold text-slate-900">Employment Information</h2>

                <p class="mt-1 text-sm text-slate-500">
                  Information about your position and employment.
                </p>
              </div>

              <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2">
                <div>
                  <Label value="Employee Code" />

                  <TextInput
                    v-model="form.employee_code"
                    class="mt-1.5 w-full bg-slate-50"
                    disabled
                  />
                </div>

                <div>
                  <Label value="Employee Type" />

                  <RisingSelect
                    v-model="form.employee_type"
                    :options="employeeTypeOptions"
                    placeholder="Select employee type"
                    class="mt-1.5 w-full"
                  />
                </div>

                <div>
                  <Label value="Department" />

                  <TextInput
                    v-model="form.department"
                    class="mt-1.5 w-full"
                    placeholder="e.g. Engineering"
                  />
                </div>

                <div>
                  <Label value="Designation" />

                  <TextInput
                    v-model="form.designation"
                    class="mt-1.5 w-full"
                    placeholder="e.g. Senior Developer"
                  />
                </div>

                <div>
                  <Label value="Joined Date" />

                  <RisingPicker
                    v-model="form.joined_at"
                    type="date"
                    class="mt-1.5 w-full"
                  />
                </div>

                <div>
                  <Label value="Probation Ends" />

                  <RisingPicker
                    v-model="form.probation_ends_at"
                    type="date"
                    class="mt-1.5 w-full"
                  />
                </div>

                <div>
                  <Label value="Employment Ends" />

                  <RisingPicker
                    v-model="form.employment_ends_at"
                    type="date"
                    class="mt-1.5 w-full"
                  />
                </div>

                <div>
                  <Label value="Salary Type" />

                  <RisingSelect
                    v-model="form.salary_type"
                    :options="salaryTypeOptions"
                    class="mt-1.5 w-full"
                  />
                </div>

                <div>
                  <Label value="Salary" />

                  <TextInput
                    v-model="form.salary"
                    type="number"
                    class="mt-1.5 w-full"
                    placeholder="0.00"
                  />
                </div>
              </div>
            </div>
          </section>

          <!-- CONTACT -->
          <section v-if="activeTab === 'contact'" class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
              <div class="border-b border-slate-100 px-6 py-5">
                <h2 class="font-semibold text-slate-900">Contact Information</h2>

                <p class="mt-1 text-sm text-slate-500">
                  Your personal contact details and address.
                </p>
              </div>

              <div class="space-y-6 p-6">
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                  <div>
                    <Label value="Personal Email" />

                    <TextInput
                      v-model="form.personal_email"
                      type="email"
                      class="mt-1.5 w-full"
                      placeholder="personal@example.com"
                    />
                  </div>

                  <div>
                    <Label value="Personal Phone" />

                    <TextInput
                      v-model="form.personal_phone"
                      class="mt-1.5 w-full"
                      placeholder="+977 98XXXXXXXX"
                    />
                  </div>
                </div>

                <div>
                  <Label value="Address" />

                  <textarea
                    v-model="form.address"
                    rows="3"
                    class="mt-1.5 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-slate-500 focus:ring-slate-500"
                    placeholder="Enter your address"
                  ></textarea>
                </div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                  <div>
                    <Label value="City" />

                    <TextInput
                      v-model="form.city"
                      class="mt-1.5 w-full"
                      placeholder="City"
                    />
                  </div>

                  <div>
                    <Label value="State" />

                    <TextInput
                      v-model="form.state"
                      class="mt-1.5 w-full"
                      placeholder="State"
                    />
                  </div>

                  <div>
                    <Label value="Country" />

                    <TextInput
                      v-model="form.country"
                      class="mt-1.5 w-full"
                      placeholder="Country"
                    />
                  </div>

                  <div>
                    <Label value="Postal Code" />

                    <TextInput
                      v-model="form.postal_code"
                      class="mt-1.5 w-full"
                      placeholder="Postal code"
                    />
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- EMERGENCY -->
          <section v-if="activeTab === 'emergency'" class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
              <div class="border-b border-slate-100 px-6 py-5">
                <h2 class="font-semibold text-slate-900">Emergency Contact</h2>

                <p class="mt-1 text-sm text-slate-500">
                  Someone your organization can contact in case of an emergency.
                </p>
              </div>

              <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2">
                <div>
                  <Label value="Contact Name" />

                  <TextInput
                    v-model="form.emergency_contact_name"
                    class="mt-1.5 w-full"
                    placeholder="Full name"
                  />
                </div>

                <div>
                  <Label value="Phone Number" />

                  <TextInput
                    v-model="form.emergency_contact_phone"
                    class="mt-1.5 w-full"
                    placeholder="+977 98XXXXXXXX"
                  />
                </div>

                <div>
                  <Label value="Relationship" />

                  <TextInput
                    v-model="form.emergency_contact_relation"
                    class="mt-1.5 w-full"
                    placeholder="e.g. Father, Mother, Spouse"
                  />
                </div>
              </div>
            </div>
          </section>

          <!-- IDENTIFICATION -->
          <section v-if="activeTab === 'identification'" class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
              <div class="border-b border-slate-100 px-6 py-5">
                <h2 class="font-semibold text-slate-900">Identification</h2>

                <p class="mt-1 text-sm text-slate-500">
                  Government and tax identification details.
                </p>
              </div>

              <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2">
                <div>
                  <Label value="National ID" />

                  <TextInput
                    v-model="form.national_id"
                    class="mt-1.5 w-full"
                    placeholder="National ID number"
                  />
                </div>

                <div>
                  <Label value="Tax Number" />

                  <TextInput
                    v-model="form.tax_number"
                    class="mt-1.5 w-full"
                    placeholder="PAN / Tax number"
                  />
                </div>
              </div>
            </div>

            <!-- DOCUMENTS -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
              <div
                class="flex items-center justify-between border-b border-slate-100 px-6 py-5"
              >
                <div>
                  <h2 class="font-semibold text-slate-900">Documents</h2>

                  <p class="mt-1 text-sm text-slate-500">
                    Your uploaded employee documents.
                  </p>
                </div>

                <button
                  type="button"
                  class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-3.5 py-2 text-sm font-semibold text-white hover:bg-slate-800"
                >
                  <i class="fa-solid fa-plus text-xs"></i>
                  Add Document
                </button>
              </div>

              <div class="p-6">
                <div v-if="documents.length" class="divide-y divide-slate-100">
                  <div
                    v-for="document in documents"
                    :key="document.id"
                    class="flex items-center gap-4 py-4 first:pt-0 last:pb-0"
                  >
                    <div
                      class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500"
                    >
                      <i class="fa-regular fa-file-lines"></i>
                    </div>

                    <div class="min-w-0 flex-1">
                      <p class="truncate text-sm font-semibold text-slate-800">
                        {{ document.title }}
                      </p>

                      <p class="mt-0.5 text-xs text-slate-500">
                        {{ document.document_type }}

                        <span v-if="document.document_number">
                          ·
                          {{ document.document_number }}
                        </span>
                      </p>
                    </div>

                    <a
                      :href="`/storage/${document.file_path}`"
                      target="_blank"
                      class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900"
                    >
                      <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                    </a>
                  </div>
                </div>

                <div
                  v-else
                  class="rounded-xl border border-dashed border-slate-300 py-10 text-center"
                >
                  <div
                    class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400"
                  >
                    <i class="fa-regular fa-folder-open text-lg"></i>
                  </div>

                  <h3 class="mt-3 text-sm font-semibold text-slate-800">
                    No documents yet
                  </h3>

                  <p class="mt-1 text-sm text-slate-500">
                    Upload your citizenship, passport, qualification or other documents.
                  </p>
                </div>
              </div>
            </div>
          </section>
        </main>
      </div>

      <!-- MOBILE SAVE -->
      <div class="mt-6 flex justify-end lg:hidden">
        <button
          type="button"
          class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-sm disabled:opacity-50"
          :disabled="form.processing"
          @click="submit"
        >
          <i v-if="form.processing" class="fa-solid fa-spinner fa-spin text-xs"></i>

          <i v-else class="fa-solid fa-check text-xs"></i>

          Save Changes
        </button>
      </div>
    </div>
  </AppLayout>
</template>
