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

  <div class="min-h-screen bg-slate-50 text-slate-800 antialiased font-sans">
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
      <MainCard />

      <div class="mb-10">
        <h2 class="text-xl font-bold text-slate-900 mb-5 flex items-center">
          <span class="w-1.5 h-5 bg-indigo-600 rounded-sm mr-3"></span>
          Your First Steps
        </h2>

        <div class="space-y-4">
          <WelcomeCard
            icon="🚀"
            title="Initialize Your Jetstream Project"
            :description="`
                Finalize setup by verifying stack features or execution configurations.
                Run <code class='bg-slate-100 px-1.5 py-0.5 rounded text-rose-600 font-mono text-xs'>
                php artisan jetstream:install
                </code>
                if missing.
            `"
          />

          <WelcomeCard
            icon="⚙️"
            title="Configure Authentication & Teams"
            :description="`
                Review options inside the configuration file
                <code class='bg-slate-100 px-1.5 py-0.5 rounded text-indigo-600 font-mono text-xs'>config/jetstream.php</code>
                to enable or disable API tokens, Teams, profile photos, and other Jetstream features.
            `"
          />
          <WelcomeCard
            icon="💡"
            title="Explore Your Starter Resources"
            :description="`
                Read the official Laravel Jetstream reference manual to harness absolute
                  control over security scaffolding layers, Livewire/Inertia options, and
                  team management tools.
            `"
            type="success"
            :outline="true"
          />
          
        </div>
      </div>

      <WelcomeFooter />
    </main>
  </div>
</template>
