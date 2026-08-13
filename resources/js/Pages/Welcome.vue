<script setup>
import { Head, Link } from "@inertiajs/vue3";
import HorizontalLogo from "@/Components/Logo/HorizontalLogo.vue";
import ProfileDropdown from "@/Components/ProfileDropdown.vue";
import MainCard from "@/Components/Welcome/MainCard.vue";
import WelcomeCard from "@/Components/Welcome/WelcomeCard.vue";
import WelcomeFooter from "@/Components/Welcome/WelcomeFooter.vue";
defineProps({
  auth: Object,
});
</script>

<template>
  <Head title="Bishal Starter Kit" />

  <!-- <div class="min-h-screen bg-slate-50 text-slate-800 antialiased font-sans">
    <nav
      class="bg-white border-b border-slate-200 sticky top-0 z-50 px-4 sm:px-6 lg:px-8"
    >
      <div class="flex justify-between items-center h-16 max-w-7xl mx-auto">
        <div class="flex items-center space-x-8">
          <div class="shrink-0 flex items-center">
            <Link :href="route('dashboard')">
              <HorizontalLogo class="h-[100px]" />
            </Link>
          </div>
        </div>

        <ProfileDropdown />
      </div>
    </nav>

    <main class="max-w-4xl mx-auto px-4 py-10 sm:px-6">
     <div class="max-h-[50vh]">
       <MainCard />
     </div>

     

      <WelcomeFooter />
    </main>
  </div> -->
  <div
    class="min-h-screen flex flex-col bg-slate-50 text-slate-800 antialiased font-sans"
  >
    <nav
      class="bg-white border-b border-slate-200 sticky top-0 z-50 px-4 sm:px-6 lg:px-8"
    >
      <div class="flex justify-between items-center h-16 max-w-7xl mx-auto">
        <div class="flex items-center space-x-8">
          <div class="shrink-0 flex items-center">
            <Link :href="route('dashboard')">
              <HorizontalLogo class="h-[100px]" />
            </Link>
          </div>
        </div>

        <ProfileDropdown />
      </div>
    </nav>

    <main class="flex-1 max-w-4xl mx-auto w-full px-4 py-10 sm:px-6">
      <div class="max-h-[50vh]">
        <MainCard />
      </div>
    </main>

    <WelcomeFooter />
  </div>
</template>
