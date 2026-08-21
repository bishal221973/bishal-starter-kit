<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import TextInput from "@/Components/Elements/TextInput.vue";
import Button from "@/Components/Elements/Button.vue";
import InputError from "@/Components/InputError.vue";

const form = useForm({
  current_password: "",
  password: "",
  password_confirmation: "",
});

const submit = () => {
  form.post(route("password.change.store"), {
    onFinish: () => {
      form.reset("current_password", "password", "password_confirmation");
    },
  });
};
</script>

<template>
  <Head title="Change Password" />

  <div class="password-page">
    <!-- Background decorations -->
    <div class="bg-decoration decoration-one"></div>
    <div class="bg-decoration decoration-two"></div>
    <div class="bg-decoration decoration-three"></div>

    <div class="password-container">
      <!-- Brand -->
      <div class="brand animate-fade-down">
        <div class="brand-icon">
          <i class="fas fa-lock"></i>
        </div>

        <span> Bishal Starter Kit </span>
      </div>

      <!-- Card -->
      <div class="password-card animate-card">
        <!-- Icon -->
        <div class="icon-wrapper">
          <div class="icon-pulse"></div>

          <div class="security-icon">
            <i class="fas fa-shield-halved"></i>
          </div>
        </div>

        <!-- Heading -->
        <div class="heading animate-fade-up delay-100">
          <h1>Your password has expired</h1>

          <p>
            For your security, you need to create a new password before continuing to your
            account.
          </p>
        </div>

        <!-- Divider -->
        <div class="divider animate-fade-up delay-200"></div>

        <!-- Form -->
        <form @submit.prevent="submit" class="form animate-fade-up delay-300">
          <!-- Current Password -->
          <div class="form-group">
            <label for="current_password"> Current Password </label>

            <div class="input-wrapper">
              <!-- <i class="fas fa-lock input-icon"></i> -->

              <TextInput
                id="current_password"
                v-model="form.current_password"
                type="password"
                autocomplete="current-password"
                placeholder="Enter your current password"
                class="w-full"
                :disabled="form.processing"
              />
            </div>

            <InputError class="mt-2" :message="form.errors.current_password" />
          </div>

          <!-- New Password -->
          <div class="form-group">
            <label for="password"> New Password </label>

            <div class="input-wrapper">
              <!-- <i class="fas fa-key input-icon"></i> -->

              <TextInput
                id="password"
                v-model="form.password"
                type="password"
                autocomplete="new-password"
                placeholder="Create a new password"
                class="w-full"
                :disabled="form.processing"
              />
            </div>

            <InputError class="mt-2" :message="form.errors.password" />
          </div>

          <!-- Confirm -->
          <div class="form-group">
            <label for="password_confirmation"> Confirm New Password </label>

            <div class="input-wrapper">
              <!-- <i class="fas fa-check-double input-icon"></i> -->

              <TextInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                autocomplete="new-password"
                placeholder="Confirm your new password"
                class="w-full"
                :disabled="form.processing"
              />
            </div>

            <InputError class="mt-2" :message="form.errors.password_confirmation" />
          </div>

          <!-- Info -->
          <div class="info-box">
            <div class="info-icon">
              <i class="fas fa-circle-info"></i>
            </div>

            <p>
              Use a strong password containing uppercase, lowercase, numbers and special
              characters.
            </p>
          </div>

          <!-- Button -->
          <Button
            submit
            type="primary"
            class="w-full !py-3 submit-button"
            :disabled="form.processing"
            :text="form.processing ? 'Updating Password...' : 'Create New Password'"
          />
        </form>
      </div>

      <!-- Footer -->
      <div class="footer animate-fade-up delay-500">
        <i class="fas fa-shield-halved"></i>

        <span> Your account security is important to us. </span>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* ============================================================
   PAGE
============================================================ */

.password-page {
  min-height: 100vh;

  display: flex;
  align-items: center;
  justify-content: center;

  padding: 40px 20px;

  position: relative;
  overflow: hidden;

  background: radial-gradient(
      circle at 10% 20%,
      rgba(52, 135, 151, 0.08),
      transparent 30%
    ),
    radial-gradient(circle at 90% 80%, rgba(52, 135, 151, 0.08), transparent 30%), #f8fafc;
}

/* ============================================================
   CONTAINER
============================================================ */

.password-container {
  width: 100%;
  max-width: 450px;

  position: relative;
  z-index: 2;
}

/* ============================================================
   BRAND
============================================================ */

.brand {
  display: flex;
  align-items: center;
  justify-content: center;

  gap: 10px;

  margin-bottom: 28px;

  font-size: 18px;
  font-weight: 700;

  color: #1e293b;
}

.brand-icon {
  width: 42px;
  height: 42px;

  border-radius: 12px;

  display: flex;
  align-items: center;
  justify-content: center;

  background: var(--primary);
  color: white;

  box-shadow: 0 8px 20px rgba(52, 135, 151, 0.25);

  animation: brandFloat 3s ease-in-out infinite;
}

/* ============================================================
   CARD
============================================================ */

.password-card {
  position: relative;

  background: white;

  border: 1px solid #e2e8f0;

  border-radius: 28px;

  padding: 34px;

  box-shadow: 0 25px 70px rgba(15, 23, 42, 0.08), 0 8px 20px rgba(15, 23, 42, 0.04);

  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.password-card:hover {
  transform: translateY(-3px);

  box-shadow: 0 30px 80px rgba(15, 23, 42, 0.11), 0 10px 25px rgba(15, 23, 42, 0.05);
}

/* ============================================================
   SECURITY ICON
============================================================ */

.icon-wrapper {
  width: 78px;
  height: 78px;

  margin: 0 auto 20px;

  position: relative;

  display: flex;
  align-items: center;
  justify-content: center;
}

.security-icon {
  width: 68px;
  height: 68px;

  border-radius: 20px;

  display: flex;
  align-items: center;
  justify-content: center;

  background: linear-gradient(135deg, rgba(52, 135, 151, 0.12), rgba(52, 135, 151, 0.05));

  color: var(--primary);

  font-size: 27px;

  position: relative;
  z-index: 2;

  animation: iconFloat 3s ease-in-out infinite;
}

.icon-pulse {
  position: absolute;

  width: 68px;
  height: 68px;

  border-radius: 20px;

  border: 1px solid rgba(52, 135, 151, 0.25);

  animation: pulse 2.5s ease-out infinite;
}

/* ============================================================
   HEADING
============================================================ */

.heading {
  text-align: center;
}

.heading h1 {
  margin: 0;

  font-size: 25px;
  line-height: 1.3;

  font-weight: 800;

  color: #1e293b;
}

.heading p {
  margin-top: 10px;

  font-size: 14px;
  line-height: 1.7;

  color: #64748b;
}

/* ============================================================
   DIVIDER
============================================================ */

.divider {
  height: 1px;

  background: #f1f5f9;

  margin: 26px 0;
}

/* ============================================================
   FORM
============================================================ */

.form {
  display: flex;
  flex-direction: column;

  gap: 20px;
}

.form-group {
  animation: inputReveal 0.5s ease both;
}

.form-group:nth-child(1) {
  animation-delay: 0.35s;
}

.form-group:nth-child(2) {
  animation-delay: 0.45s;
}

.form-group:nth-child(3) {
  animation-delay: 0.55s;
}

.form-group label {
  display: block;

  margin-bottom: 8px;

  font-size: 13px;
  font-weight: 600;

  color: #334155;
}

/* ============================================================
   INPUT
============================================================ */

.input-wrapper {
  position: relative;
}

.input-icon {
  position: absolute;

  left: 13px;
  top: 50%;

  transform: translateY(-50%);

  z-index: 5;

  font-size: 13px;

  color: #94a3b8;

  transition: color 0.2s ease, transform 0.2s ease;
}

.input-wrapper:focus-within .input-icon {
  color: var(--primary);

  transform: translateY(-50%) scale(1.1);
}

/* ============================================================
   INFO BOX
============================================================ */

.info-box {
  display: flex;

  align-items: flex-start;

  gap: 11px;

  padding: 13px 14px;

  border-radius: 13px;

  background: #f0f9ff;

  border: 1px solid #dbeafe;

  animation: infoGlow 3s ease-in-out infinite;
}

.info-icon {
  flex-shrink: 0;

  color: var(--primary);

  margin-top: 2px;
}

.info-box p {
  margin: 0;

  font-size: 11px;

  line-height: 1.6;

  color: #475569;
}

/* ============================================================
   BUTTON
============================================================ */

.submit-button {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.submit-button:hover {
  transform: translateY(-2px);

  box-shadow: 0 10px 25px rgba(52, 135, 151, 0.22);
}

.submit-button:active {
  transform: translateY(0) scale(0.98);
}

/* ============================================================
   FOOTER
============================================================ */

.footer {
  display: flex;

  align-items: center;
  justify-content: center;

  gap: 7px;

  margin-top: 22px;

  color: #94a3b8;

  font-size: 11px;
}

.footer i {
  color: var(--primary);
}

/* ============================================================
   BACKGROUND DECORATIONS
============================================================ */

.bg-decoration {
  position: absolute;

  border-radius: 50%;

  filter: blur(2px);

  opacity: 0.5;

  pointer-events: none;
}

.decoration-one {
  width: 220px;
  height: 220px;

  top: -80px;
  left: -80px;

  background: rgba(52, 135, 151, 0.08);

  animation: decorationFloat 8s ease-in-out infinite;
}

.decoration-two {
  width: 280px;
  height: 280px;

  right: -100px;
  bottom: -100px;

  background: rgba(52, 135, 151, 0.06);

  animation: decorationFloat 10s ease-in-out infinite reverse;
}

.decoration-three {
  width: 80px;
  height: 80px;

  top: 20%;
  right: 15%;

  background: rgba(52, 135, 151, 0.05);

  animation: decorationFloat 6s ease-in-out infinite;
}

/* ============================================================
   ANIMATIONS
============================================================ */

.animate-card {
  animation: cardEnter 0.7s cubic-bezier(0.16, 1, 0.3, 1);
}

.animate-fade-down {
  animation: fadeDown 0.6s ease both;
}

.animate-fade-up {
  animation: fadeUp 0.6s ease both;
}

.delay-100 {
  animation-delay: 0.1s;
}

.delay-200 {
  animation-delay: 0.2s;
}

.delay-300 {
  animation-delay: 0.3s;
}

.delay-500 {
  animation-delay: 0.5s;
}

@keyframes cardEnter {
  from {
    opacity: 0;
    transform: translateY(30px) scale(0.97);
  }

  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes fadeDown {
  from {
    opacity: 0;
    transform: translateY(-15px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeUp {
  from {
    opacity: 0;
    transform: translateY(15px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes inputReveal {
  from {
    opacity: 0;
    transform: translateX(-10px);
  }

  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes pulse {
  0% {
    transform: scale(1);
    opacity: 0.7;
  }

  70% {
    transform: scale(1.3);
    opacity: 0;
  }

  100% {
    transform: scale(1.3);
    opacity: 0;
  }
}

@keyframes iconFloat {
  0%,
  100% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(-5px);
  }
}

@keyframes brandFloat {
  0%,
  100% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(-3px);
  }
}

@keyframes decorationFloat {
  0%,
  100% {
    transform: translate(0, 0);
  }

  50% {
    transform: translate(20px, -15px);
  }
}

@keyframes infoGlow {
  0%,
  100% {
    box-shadow: 0 0 0 rgba(52, 135, 151, 0);
  }

  50% {
    box-shadow: 0 0 20px rgba(52, 135, 151, 0.06);
  }
}

/* ============================================================
   REDUCED MOTION
============================================================ */

@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}

/* ============================================================
   MOBILE
============================================================ */

@media (max-width: 480px) {
  .password-page {
    padding: 20px 14px;
  }

  .password-card {
    padding: 25px 20px;
    border-radius: 22px;
  }

  .heading h1 {
    font-size: 22px;
  }

  .brand {
    margin-bottom: 20px;
  }
}
</style>
