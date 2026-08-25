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
import Employeement from "./Include/Employeement.vue";
import Document from "./Include/Document.vue";
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
    key: "documents",
    label: "Documents",
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
        description="Manage your personal information, employment details, contact information and documents."
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
            <PersonalInfo :user="user" />

           
          </section>

          <!-- EMPLOYMENT -->
          <section v-if="activeTab === 'employment'" class="space-y-6">
          <Employeement :user="user"/>
            
          </section>

          <!-- documents -->
          <section v-if="activeTab === 'documents'" class="space-y-6">
          <Document :user="user"/>
            
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
