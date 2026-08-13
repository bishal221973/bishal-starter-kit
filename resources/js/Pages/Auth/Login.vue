<script setup>
import { ref } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/Elements/TextInput.vue";
import Button from "@/Components/Elements/Button.vue";
import Logo from "@/Components/Logo/Logo.vue";

defineProps({
  canResetPassword: Boolean,
  status: String,
});

const showPassword = ref(false);

const form = useForm({
  email: "",
  password: "",
  remember: true,
});

const submit = () => {
  form
    .transform((data) => ({
      ...data,
      remember: form.remember ? "on" : "",
    }))
    .post(route("login"), {
      onFinish: () => form.reset("password"),
    });
};
</script>

<template>
  <Head title="Log in" />

  <div class="login-page">
    <!-- Login Section -->
    <div class="login-section">
      <div class="login-box">
        <div class="logo-area">
          <div>
            <!-- <Logo /> -->
          </div>

          <h1>Welcome Back</h1>

          <p>Please enter your credentials to access your account.</p>
        </div>

        <div v-if="status" class="status-message">
          {{ status }}
        </div>

        <form class="login-form" @submit.prevent="submit">
          <!-- Email -->
          <div class="">
            <TextInput
              id="email"
              v-model="form.email"
              type="email"
              text="Email Address"
              placeholder="Enter your email"
              autocomplete="off"
              required
              autofocus
            />

            <InputError class="mt-2" :message="form.errors.email" />
          </div>

          <!-- Password -->
          <div>
            <TextInput
              id="password"
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              text="Password"
              placeholder="Enter your password"
              autocomplete="off"
              required
            />

            <InputError class="mt-2" :message="form.errors.password" />
          </div>

          <!-- Remember + Show Password -->
          <div class="options-wrapper">
            <label class="option-item">
              <Checkbox v-model:checked="showPassword" />

              <span> Show Password </span>
            </label>

            <div v-if="canResetPassword" class="forgot-wrapper">
              <Link :href="route('password.request')" class="forgot-link">
                Forgot your password?
              </Link>
            </div>
          </div>

          <!-- Forgot Password -->

          <!-- Submit -->
          <Button
            :submit="true"
            type="success"
            class="login-btn"
            :disabled="form.processing"
            :class="{
              'opacity-50 cursor-not-allowed': form.processing,
            }"
          >
            <span v-if="form.processing"> Signing In... </span>

            <span v-else> Sign In </span>
          </Button>
        </form>
      </div>
    </div>

    <!-- Right Side -->
    <div class="cover-section">
      <div class="cover-overlay"></div>

      <div class="cover-content">
        <Logo />
        <h2 class="mt-[15vh]">Bishal Starter Kit</h2>

        <p>
          Build scalable Laravel + Vue applications with beautiful UI, authentication,
          theme management, permissions, and reusable components.
        </p>

        <div class="copyright">© 2026 All Rights Reserved</div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  background: #348797;
}

.login-section {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 50px;
}

.login-box {
  width: 100%;
  max-width: 450px;
}

.logo-area h1 {
  margin-top: 20px;
  font-size: 3rem;
  font-weight: 800;
  color: #ffffff;
}

.logo-area p {
  margin-top: 10px;
  color: rgba(255, 255, 255, 0.85);
}

.status-message {
  margin-top: 20px;
  padding: 14px;
  border-radius: 12px;
  background: #dcfce7;
  color: #166534;
  font-size: 14px;
}

.login-form {
  margin-top: 30px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.options-wrapper {
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
}

.option-item {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #ffffff;
  font-size: 14px;
  cursor: pointer;
}

.forgot-wrapper {
  text-align: right;
}

.forgot-link {
  color: #ffffff;
  text-decoration: none;
  font-size: 14px;
}

.forgot-link:hover {
  text-decoration: underline;
}

/* .login-btn {
  width: 100%;
  margin-top: 10px;
} */

.cover-section {
  flex: 1;
  position: relative;
  overflow: hidden;
  background: linear-gradient(135deg, #0f172a, #1e293b);
  display: flex;
  align-items: center;
  justify-content: center;
}

.cover-overlay {
  position: absolute;
  inset: 0;
  background: radial-gradient(rgba(255, 255, 255, 0.12) 1px, transparent 1px);
  background-size: 24px 24px;
}

.cover-content {
  position: relative;
  z-index: 2;
  max-width: 500px;
  padding: 40px;
  color: white;
}

.cover-content h2 {
  font-size: 3rem;
  font-weight: 800;
  margin-bottom: 20px;
}

.cover-content p {
  font-size: 18px;
  line-height: 1.8;
  opacity: 0.9;
}

.copyright {
  margin-top: 40px;
  opacity: 0.6;
  font-size: 14px;
}

@media (max-width: 1024px) {
  .cover-section {
    display: none;
  }

  .login-section {
    padding: 30px;
  }

  .logo-area h1 {
    font-size: 2.25rem;
  }
}
</style>
