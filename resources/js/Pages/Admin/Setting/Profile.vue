<script setup>
import { computed, ref } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
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
import PasswordChange from "./Include/PasswordChange.vue";
import TwoFactor from "./Include/TwoFactor.vue";
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

const page=usePage()
const activeTab = ref("personal");
const twoFactorEnabled = computed(() => {
    return page.props.registration?.two_factor ?? false;
});

const tabs = computed(() => {
    const items = [
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
        {
            key: "passwordChange",
            label: "Change Password",
            icon: "fa-solid fa-lock",
        },
    ];

    // Only show when registration.two_factor === true
    if (twoFactorEnabled.value) {
        items.push({
            key: "twoFactor",
            label: "Two-Factor Auth",
            icon: "fa-solid fa-shield-halved",
        });
    }

    return items;
});


</script>

<template>
  <AppLayout>
  {{ twoFactorEnabled ? 'adas' : 'oo8' }}
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
          @update:active-tab="activeTab = $event"
        />

        <!-- MAIN -->
        <main class="w-full">
          <!-- PERSONAL -->
          <section v-if="activeTab === 'personal'" class="space-y-6">
            <PersonalInfo :user="user" />
          </section>

          <!-- EMPLOYMENT -->
          <section v-if="activeTab === 'employment'" class="space-y-6">
            <Employeement :user="user" />
          </section>

          <!-- documents -->
          <section v-if="activeTab === 'documents'" class="space-y-6">
            <Document :user="user" />
          </section>
          <section v-if="activeTab === 'passwordChange'" class="space-y-6">
            <PasswordChange :user="user" />
          </section>
          <section v-if="activeTab === 'twoFactor'" class="space-y-6">
            <TwoFactor :user="user" />
          </section>
        </main>
      </div>
    </div>
  </AppLayout>
</template>
