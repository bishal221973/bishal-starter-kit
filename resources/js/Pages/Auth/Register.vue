<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";

import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/Elements/TextInput.vue";
import Button from "@/Components/Elements/Button.vue";

const form = useForm({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
  terms: false,
});

const submit = () => {
  form.post(route("register"), {
    onFinish: () => {
      form.reset("password", "password_confirmation");
    },
  });
};
</script>

<template>
  <Head title="Register" />

  <div class="min-h-screen bg-slate-50 flex">

    <!-- =====================================================
         LEFT SIDE
    ====================================================== -->

    <div
      class="hidden lg:flex lg:w-1/2 relative overflow-hidden
             bg-primary text-white p-12 xl:p-16
             flex-col justify-between"
    >
      <!-- Decorative background -->
      <div
        class="absolute -top-32 -right-32 w-96 h-96
               rounded-full bg-white/10 blur-3xl"
      ></div>

      <div
        class="absolute -bottom-40 -left-40 w-[28rem] h-[28rem]
               rounded-full bg-black/10 blur-3xl"
      ></div>

      <!-- Content -->
      <div class="relative z-10">

        <!-- Logo -->
        <div class="flex items-center gap-3">
          <div
            class="w-11 h-11 rounded-xl bg-white/15
                   flex items-center justify-center
                   backdrop-blur-sm"
          >
            <i class="fas fa-layer-group text-lg"></i>
          </div>

          <span class="text-xl font-bold">
            Bishal Starter Kit
          </span>
        </div>

        <!-- Heading -->
        <div class="mt-20 max-w-xl">

          <div
            class="inline-flex items-center gap-2
                   px-3 py-1.5 rounded-full
                   bg-white/10 border border-white/10
                   text-sm font-medium"
          >
            <span
              class="w-2 h-2 rounded-full bg-white"
            ></span>

            Get started today
          </div>

          <h1
            class="mt-6 text-5xl xl:text-6xl
                   font-bold leading-[1.08]"
          >
            Build something
            <br />
            amazing.
          </h1>

          <p
            class="mt-6 max-w-lg
                   text-lg leading-relaxed
                   text-white/80"
          >
            Create your account and start building with
            a modern Laravel, Vue 3 and Inertia starter
            kit designed for developers.
          </p>
        </div>

        <!-- Features -->
        <div class="mt-12 space-y-4">

          <div class="flex items-center gap-3">
            <div
              class="w-8 h-8 rounded-lg
                     bg-white/10
                     flex items-center justify-center"
            >
              <i class="fas fa-check text-sm"></i>
            </div>

            <span class="text-white/90">
              Laravel + Vue 3 + Inertia
            </span>
          </div>

          <div class="flex items-center gap-3">
            <div
              class="w-8 h-8 rounded-lg
                     bg-white/10
                     flex items-center justify-center"
            >
              <i class="fas fa-check text-sm"></i>
            </div>

            <span class="text-white/90">
              Authentication ready
            </span>
          </div>

          <div class="flex items-center gap-3">
            <div
              class="w-8 h-8 rounded-lg
                     bg-white/10
                     flex items-center justify-center"
            >
              <i class="fas fa-check text-sm"></i>
            </div>

            <span class="text-white/90">
              Theme & reusable components
            </span>
          </div>

        </div>
      </div>

      <!-- Footer -->
      <div
        class="relative z-10
               text-sm text-white/60"
      >
        © {{ new Date().getFullYear() }} Bishal Starter Kit
      </div>
    </div>

    <!-- =====================================================
         RIGHT SIDE
    ====================================================== -->

    <div
      class="w-full lg:w-1/2
             min-h-screen
             flex items-center justify-center
             px-5 py-10 sm:px-8"
    >

      <div class="w-full max-w-md">

        <!-- Mobile Logo -->
        <div class="lg:hidden flex justify-center mb-8">

          <div class="flex items-center gap-3">

            <div
              class="w-10 h-10 rounded-xl
                     bg-primary text-white
                     flex items-center justify-center"
            >
              <i class="fas fa-layer-group"></i>
            </div>

            <span class="font-bold text-xl text-slate-800">
              Bishal Starter Kit
            </span>

          </div>

        </div>

        <!-- Card -->
        

          <!-- Header -->
          <div class="mb-8">

            <h2
              class="text-2xl sm:text-3xl
                     font-bold text-slate-800"
            >
              Create your account
            </h2>

            <p class="mt-2 text-sm text-slate-500">
              Fill in your details to get started.
            </p>

          </div>

          <!-- Form -->
          <form
            @submit.prevent="submit"
            class="space-y-5"
          >

            <!-- Name -->
            <div>

              <label
                for="name"
                class="block mb-2
                       text-sm font-medium
                       text-slate-700"
              >
                Full Name
              </label>

              <TextInput
                id="name"
                v-model="form.name"
                type="text"
                autocomplete="name"
                placeholder="Enter your full name"
                class="w-full"
                :disabled="form.processing"
              />

              <InputError
                class="mt-2"
                :message="form.errors.name"
              />

            </div>

            <!-- Email -->
            <div>

              <label
                for="email"
                class="block mb-2
                       text-sm font-medium
                       text-slate-700"
              >
                Email Address
              </label>

              <TextInput
                id="email"
                v-model="form.email"
                type="email"
                autocomplete="email"
                placeholder="you@example.com"
                class="w-full"
                :disabled="form.processing"
              />

              <InputError
                class="mt-2"
                :message="form.errors.email"
              />

            </div>

            <!-- Password -->
            <div>

              <label
                for="password"
                class="block mb-2
                       text-sm font-medium
                       text-slate-700"
              >
                Password
              </label>

              <TextInput
                id="password"
                v-model="form.password"
                type="password"
                autocomplete="new-password"
                placeholder="Create a strong password"
                class="w-full"
                :disabled="form.processing"
              />

              <InputError
                class="mt-2"
                :message="form.errors.password"
              />

            </div>

            <!-- Confirm Password -->
            <div>

              <label
                for="password_confirmation"
                class="block mb-2
                       text-sm font-medium
                       text-slate-700"
              >
                Confirm Password
              </label>

              <TextInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                autocomplete="new-password"
                placeholder="Confirm your password"
                class="w-full"
                :disabled="form.processing"
              />

              <InputError
                class="mt-2"
                :message="form.errors.password_confirmation"
              />

            </div>

            <!-- Terms -->
            <div
              class="p-4
                     rounded-xl
                     border border-slate-200
                     bg-slate-50"
            >

              <label
                class="flex items-start gap-3
                       cursor-pointer"
              >

                <Checkbox
                  v-model:checked="form.terms"
                  name="terms"
                  :disabled="form.processing"
                  class="mt-0.5"
                />

                <span
                  class="text-sm
                         leading-5
                         text-slate-600"
                >
                  I agree to the

                  <Link
                    :href="route('terms.show')"
                    target="_blank"
                    class="font-semibold
                           text-primary
                           hover:underline"
                  >
                    Terms of Service
                  </Link>

                  and

                  <Link
                    :href="route('policy.show')"
                    target="_blank"
                    class="font-semibold
                           text-primary
                           hover:underline"
                  >
                    Privacy Policy
                  </Link>
                </span>

              </label>

              <InputError
                class="mt-2 ml-7"
                :message="form.errors.terms"
              />

            </div>

            <!-- Register -->
            <Button
              submit
              type="primary"
              class="w-full !py-3"
              :disabled="form.processing"
              :text="
                form.processing
                  ? 'Creating Account...'
                  : 'Create Account'
              "
            />

          </form>

          <!-- Divider -->
          <div class="relative my-7">

            <div class="absolute inset-0 flex items-center">
              <div
                class="w-full border-t
                       border-slate-200"
              ></div>
            </div>

            <div class="relative flex justify-center">

              <span
                class="px-4
                       bg-white
                       text-xs
                       text-slate-400
                       uppercase
                       tracking-wider"
              >
                Already have an account?
              </span>

            </div>

          </div>

          <!-- Login -->
          <div class="text-center">

            <Link
              :href="route('login')"
              class="inline-flex items-center
                     justify-center
                     w-full
                     px-4 py-3
                     rounded-xl
                     border border-slate-200
                     text-sm font-semibold
                     text-slate-700
                     hover:bg-slate-50
                     hover:border-slate-300
                     transition"
            >
              Sign in to your account

              <i
                class="fas fa-arrow-right
                       ml-2 text-xs"
              ></i>
            </Link>

          </div>


        <!-- Bottom -->
        <p
          class="mt-6
                 text-center
                 text-xs
                 text-slate-400"
        >
          By creating an account, you agree to our
          Terms of Service and Privacy Policy.
        </p>

      </div>
    </div>

  </div>
</template>