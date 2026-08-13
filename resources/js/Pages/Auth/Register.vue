<script setup>
import { ref } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
// import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/Elements/TextInput.vue";
import Button from "@/Components/Elements/Button.vue";
import HorizontalLogo from "@/Components/Logo/HorizontalLogo.vue";
import Label from "@/Components/Label.vue";
const step = ref(1);

const form = useForm({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
  terms: false,
});

const nextStep = () => {
  if (step.value < 3) {
    step.value++;
  }
};

const previousStep = () => {
  if (step.value > 1) {
    step.value--;
  }
};

const submit = () => {
  form.post(route("register"), {
    onFinish: () => form.reset("password", "password_confirmation"),
  });
};
</script>
<template>
  <Head title="Register" />

  <div class="min-h-screen flex bg-slate-50">
    <!-- Left Side -->
    <div
      class="hidden lg:flex lg:w-1/2 bg-primary text-white p-16 flex-col justify-between"
    >
      <div>
        <!-- <HorizontalLogo/> -->

        <h1 class="mt-12 text-5xl font-bold leading-tight">
          Build Faster With
          <br />
          Bishal Starter Kit
        </h1>

        <p class="mt-6 text-lg opacity-90">
          Modern Laravel + Vue starter kit with authentication, themes, reusable
          components, and best practices.
        </p>
      </div>

      <div class="space-y-5 text-lg">
        <div>✓ Laravel 12 + Vue 3 + Inertia</div>
        <div>✓ Authentication Ready</div>
        <div>✓ Theme Management</div>
        <div>✓ Modern UI Components</div>
      </div>
    </div>

    <!-- Right Side -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6">
      <div
        class="w-full max-w-xl bg-white rounded-3xl shadow-xl border border-slate-100 p-8"
      >
        <!-- Header -->
        <div class="text-center">
          <h2 class="text-3xl font-bold text-slate-800">Create Account</h2>

          <p class="mt-2 text-slate-500">Complete the steps below.</p>
        </div>

        <!-- Progress -->
        <div class="flex items-center mt-10 mb-8">
          <div class="step" :class="{ active: step >= 1 }">1</div>

          <div class="step-line" :class="{ active: step >= 2 }"></div>

          <div class="step" :class="{ active: step >= 2 }">2</div>

          <div class="step-line" :class="{ active: step >= 3 }"></div>

          <div class="step" :class="{ active: step >= 3 }">3</div>
        </div>

        <form @submit.prevent="submit">
          <!-- STEP 1 -->
          <div v-if="step === 1" class="space-y-5">
            <div>
              <!-- <InputLabel for="name" value="Full Name" /> -->

              <TextInput
                id="name"
                v-model="form.name"
                type="text"
                placeholder="Enter your full name"
              />

              <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
              <!-- <InputLabel for="email" value="Email Address" /> -->

              <TextInput
                id="email"
                v-model="form.email"
                type="email"
                placeholder="Enter your email"
              />

              <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <Button
              type="primary"
              class="w-full"
              @click.prevent="nextStep"
              text="Continue"
            >
            </Button>
          </div>

          <!-- STEP 2 -->
          <div v-if="step === 2" class="space-y-5">
            <div>
              <!-- <InputLabel for="password" value="Password" /> -->

              <TextInput
                id="password"
                v-model="form.password"
                type="password"
                placeholder="Enter password"
              />

              <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
              <!-- <InputLabel for="password_confirmation" value="Confirm Password" /> -->

              <TextInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                placeholder="Confirm password"
              />

              <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="flex gap-3">
              <Button type="secondary" @click.prevent="previousStep" text="Back">
              </Button>

              <Button
                type="primary"
                class="flex-1"
                @click.prevent="nextStep"
                text="Continue"
              >
              </Button>
            </div>
          </div>

          <!-- STEP 3 -->
          <div v-if="step === 3" class="space-y-6">
            <!-- <div
                            class="p-4 rounded-xl border border-slate-200 bg-slate-50"
                        >
                            <label class="flex items-start gap-3">
                                <Checkbox
                                    v-model:checked="form.terms"
                                    name="terms"
                                />

                                <span class="text-sm text-slate-600">
                                    I agree to the Terms &
                                    Privacy Policy
                                </span>
                            </label>
                        </div> -->
            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50">
              <label class="flex items-start gap-3">
                <Checkbox v-model:checked="form.terms" name="terms" />

                <span class="text-sm text-slate-600">
                  I agree to the
                  <Link
                    :href="route('terms.show')"
                    target="_blank"
                    class="font-medium text-[#348797] hover:underline"
                  >
                    Terms of Service
                  </Link>
                  and
                  <Link
                    :href="route('policy.show')"
                    target="_blank"
                    class="font-medium text-[#348797] hover:underline"
                  >
                    Privacy Policy
                  </Link>
                </span>
              </label>
            </div>

            <div class="flex gap-3">
              <Button type="secondary" @click.prevent="previousStep" text="Back">
              </Button>

              <Button
                submit
                type="primary"
                class="flex-1"
                :disabled="form.processing"
                :text="form.processing ? 'Creating...' : 'Create Account'"
              >
              </Button>
            </div>
          </div>
        </form>

        <div class="mt-8 text-center">
          <span class="text-slate-500"> Already registered? </span>

          <Link :href="route('login')" class="ml-1 text-primary font-semibold">
            Sign In
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>
<style scoped>
.step {
  width: 42px;
  height: 42px;
  border-radius: 9999px;
  background: #e2e8f0;
  color: #64748b;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  transition: all 0.3s ease;
}

.step.active {
  background: var(--primary);
  color: white;
}

.step-line {
  flex: 1;
  height: 4px;
  background: #e2e8f0;
  margin: 0 8px;
  border-radius: 999px;
  transition: all 0.3s ease;
}

.step-line.active {
  background: var(--primary);
}
</style>
