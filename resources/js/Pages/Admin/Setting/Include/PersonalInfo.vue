<script setup>
import { useForm } from "@inertiajs/vue3";
import Label from "@/Components/Label.vue";
import TextInput from "@/Components/Elements/TextInput.vue";
import { RisingPicker } from "rising-picker";
import { RisingSelect } from "rising-select";
import Button from "@/Components/Elements/Button.vue";
const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
  employee: {
    type: Object,
    default: null,
  },
});

const genderOptions = [
  {
    label: "Male",
    value: "male",
  },
  {
    label: "Female",
    value: "female",
  },
  {
    label: "Other",
    value: "other",
  },
];

const form = useForm({
  name: props.user?.name ?? "",
  email: props.user?.email ?? "",

  profile_photo: null,

  gender: props.user?.info?.gender ?? null,
  date_of_birth: props.user?.info?.date_of_birth ?? null,

  personal_phone: props.user?.info?.personal_phone ?? "",

  address: props.user?.info?.address ?? "",
  city: props.user?.info?.city ?? "",
  state: props.user?.info?.state ?? "",
  country: props.user?.info?.country ?? "",
  postal_code: props.user?.info?.postal_code ?? "",

  tax_number: props.user?.info?.tax_number ?? "",
});

const submit = () => {
  form.put(route("user.personal.info"), {
    preserveScroll: true,
  });
};
</script>

<template>
  <form @submit.prevent="submit" class="space-y-6">
    <!-- ========================================= -->
    <!-- Personal Information -->
    <!-- ========================================= -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
      <!-- Header -->
      <div class="border-b border-slate-100 px-6 py-5">
        <div class="flex items-start gap-3">
          <div
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              stroke-width="1.8"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M4.5 20.25a7.5 7.5 0 0115 0"
              />
            </svg>
          </div>

          <div>
            <Label class="font-semibold text-lg">Personal Information</Label>

            <Label class="mt-1 text-sm block opacity-60">
              Basic information associated with your account.
            </Label>
          </div>
        </div>
      </div>

      <!-- Fields -->
      <div class="grid grid-cols-1 gap-5 p-3 md:grid-cols-3">
        <!-- Full Name -->
        <div>
          <TextInput
            v-model="form.name"
            class="mt-1.5 w-full"
            placeholder="Enter your full name"
            text="Full Name"
            required
          />

          <p v-if="form.errors.name" class="mt-1.5 text-xs text-red-600">
            {{ form.errors.name }}
          </p>
        </div>

        <!-- Email -->
        <div>
          <TextInput
            v-model="form.email"
            type="email"
            class="mt-1.5 w-full"
            placeholder="name@example.com"
            text="Email"
            required
            disabled
          />

          <p v-if="form.errors.email" class="mt-1.5 text-xs text-red-600">
            {{ form.errors.email }}
          </p>
        </div>

        <!-- Gender -->
        <div>
          <Label class="font-medium text-sm mb-[17px] block">Gender</Label>

          <RisingSelect
            v-model="form.gender"
            :options="genderOptions"
            placeholder="Select gender"
            class="mt-1.5 w-full"
          />

          <p v-if="form.errors.gender" class="mt-1.5 text-xs text-red-600">
            {{ form.errors.gender }}
          </p>
        </div>

        <!-- Date of Birth -->
        <div>
          <TextInput
            v-model="form.date_of_birth"
            type="date"
            class="mt-1.5 w-full"
            placeholder="name@example.com"
            text="Date of Birth"
          />
          <p v-if="form.errors.date_of_birth" class="mt-1.5 text-xs text-red-600">
            {{ form.errors.date_of_birth }}
          </p>
        </div>

        <!-- Personal Phone -->
        <div>
          <Label value="Personal Phone" />

          <TextInput
            v-model="form.personal_phone"
            type="tel"
            class="mt-1.5 w-full"
            placeholder="Enter your phone number"
            text="Phone"
            required
          />

          <p v-if="form.errors.personal_phone" class="mt-1.5 text-xs text-red-600">
            {{ form.errors.personal_phone }}
          </p>
        </div>

        <!-- Tax Number -->
        <div>
          <Label value="Tax Number" />

          <TextInput
            v-model="form.tax_number"
            class="mt-1.5 w-full"
            placeholder="Enter your tax number"
            text="Tax Number"
          />

          <p v-if="form.errors.tax_number" class="mt-1.5 text-xs text-red-600">
            {{ form.errors.tax_number }}
          </p>
        </div>
      </div>

      <hr />
      <!-- <Label class="px-5 font-bold">Address Information</Label> -->
      <div class="grid grid-cols-1 gap-5 px-6 md:grid-cols-12 pb-6">
        <!-- Address -->
        <div class="col-span-6">
          <TextInput
            v-model="form.address"
            class="mt-1.5 w-full"
            placeholder="Enter your full address"
            text="Address"
          />

          <p v-if="form.errors.address" class="mt-1.5 text-xs text-red-600">
            {{ form.errors.address }}
          </p>
        </div>

        <!-- City -->
        <div class="col-span-6">
          <TextInput
            v-model="form.city"
            class="mt-1.5 w-full"
            placeholder="Enter your city"
            text="City"
          />

          <p v-if="form.errors.city" class="mt-1.5 text-xs text-red-600">
            {{ form.errors.city }}
          </p>
        </div>

        <!-- State -->
        <div class="col-span-4">
          <TextInput
            v-model="form.state"
            class="mt-1.5 w-full"
            placeholder="Enter your state or province"
            text="State / Province"
          />

          <p v-if="form.errors.state" class="mt-1.5 text-xs text-red-600">
            {{ form.errors.state }}
          </p>
        </div>

        <!-- Country -->
        <div class="col-span-4">
          <Label value="" />

          <TextInput
            v-model="form.country"
            class="mt-1.5 w-full"
            placeholder="Enter your country"
            text="Country"
          />

          <p v-if="form.errors.country" class="mt-1.5 text-xs text-red-600">
            {{ form.errors.country }}
          </p>
        </div>

        <!-- Postal Code -->
        <div class="col-span-4">
          <TextInput
            v-model="form.postal_code"
            class="mt-1.5 w-full"
            placeholder="Enter postal code"
            text="Postal Code"
          />

          <p v-if="form.errors.postal_code" class="mt-1.5 text-xs text-red-600">
            {{ form.errors.postal_code }}
          </p>
        </div>
      </div>
    </div>

    <!-- ========================================= -->
    <!-- Actions -->
    <!-- ========================================= -->
    <div
      class="flex flex-col-reverse gap-3 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between"
    >
      <Label class="text-sm">Keep your personal information up to date.</Label>

      <Button
        :submit="true"
        :text="form.processing ? 'Updating...' : 'Update Profile'"
        :processing="form.processing"
        :disabled="form.processing"
      />
    </div>
  </form>
</template>
