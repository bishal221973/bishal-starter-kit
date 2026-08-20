<script setup>
import { Link } from "@inertiajs/vue3";
import Label from "@/Components/Label.vue";

defineProps({
  title: {
    type: String,
    required: true,
  },

  description: {
    type: String,
    default: "",
  },

  breadcrumbs: {
    type: Array,
    default: () => [],
  },

  icon: {
    type: String,
    default: "",
  },

  homeHref: {
    type: String,
    default: "/",
  },
});
</script>

<template>
  <div class="mb-6">

    <!-- =====================================================
         BREADCRUMB
    ====================================================== -->

    <div
      class="flex items-center gap-2 text-sm mb-1"
    >

      <!-- Home -->

      <Link
        :href="homeHref"
        class="flex items-center gap-1.5 text-slate-400 hover:text-primary transition-colors"
      >
        <i class="fas fa-home text-[11px]"></i>
        <span>Home</span>
      </Link>


      <!-- Breadcrumbs -->

      <template
        v-for="(breadcrumb, index) in breadcrumbs"
        :key="index"
      >

        <!-- Separator -->

        <span class="text-slate-300">
          <i class="fas fa-chevron-right text-[9px]"></i>
        </span>


        <!-- Clickable breadcrumb -->

        <Link
          v-if="
            breadcrumb.href &&
            index !== breadcrumbs.length - 1
          "
          :href="breadcrumb.href"
          class="text-slate-400 hover:text-primary transition-colors"
        >
          {{ breadcrumb.label }}
        </Link>


        <!-- Current breadcrumb -->

        <span
          v-else
          class="font-medium text-slate-600"
        >
          {{ breadcrumb.label }}
        </span>

      </template>

    </div>


    <!-- =====================================================
         TITLE
    ====================================================== -->

    <div class="flex items-center gap-3">


      <!-- Title + Description -->

      <div>

        <Label
          v-if="description"
          class="block text-[13px] text-slate-500 mt-1"
          opacity="80"
        >
          {{ description }}
        </Label>

      </div>

    </div>

  </div>
</template>