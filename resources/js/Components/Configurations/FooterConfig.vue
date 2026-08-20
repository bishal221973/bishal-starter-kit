<script setup>
import { useForm } from "@inertiajs/vue3";
import Label from "@/Components/Label.vue";
import Button from "../Elements/Button.vue";
import Modal from "../Modal.vue";
import { ref, computed } from "vue";

const props = defineProps({
  config: {
    type: Object,
    default: () => ({}),
  },
});

const showModal = ref(false);

const toggleModal = () => {
  showModal.value = !showModal.value;
};

const form = useForm({
  footer_text: props.config?.footer_text ?? "",
});

const maxLength = 500;

const characterCount = computed(() => {
  return form.footer_text?.length ?? 0;
});

const save = () => {
  form.post(route("configuration.setting.update"), {
    preserveScroll: true,

    onSuccess: () => {
      showModal.value = false;
    },
  });
};
</script>

<template>
  <!-- =========================================================
       SETTINGS BUTTON
  ========================================================== -->

  <button
    type="button"
    @click="toggleModal"
    class="group flex items-center gap-3 w-full p-4 rounded-xl border border-slate-200 bg-white hover:border-primary hover:shadow-sm transition-all duration-200"
  >
    <div
      class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary transition-all duration-200 group-hover:bg-primary group-hover:text-white"
    >
      <i class="fa-solid fa-window-maximize"></i>
    </div>

    <div class="text-left">
      <span class="block font-medium text-slate-800"> Footer Configuration </span>

      <span class="block text-xs text-slate-500 mt-0.5">
        Configure the text displayed in the application footer
      </span>
    </div>

    <i
      class="fa-solid fa-chevron-right ml-auto text-sm text-slate-400 group-hover:text-primary transition-colors"
    ></i>
  </button>

  <!-- =========================================================
       MODAL
  ========================================================== -->

  <Modal :show="showModal">
    <div class="overflow-hidden rounded-xl bg-white">
      <!-- =====================================================
           HEADER
      ====================================================== -->

      <div class="px-6 py-4 border-b border-slate-100 bg-card_header_color">
        <div class="flex items-center gap-3">
          <div
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
          >
            <i class="fa-solid fa-window-maximize"></i>
          </div>

          <div>
            <Label class="font-bold text-xl text-slate-800"> Footer Configuration </Label>

            <p class="text-sm text-slate-500 mt-0.5">
              Customize the text displayed at the bottom of your application pages.
            </p>
          </div>
        </div>
      </div>

      <!-- =====================================================
           BODY
      ====================================================== -->

      <div class="p-5 sm:p-6">
        <form @submit.prevent="save">
          <!-- =================================================
               FOOTER TEXT
          ================================================== -->

          <div>
            <div class="flex items-center justify-between mb-2">
              <div>
                <Label class="font-semibold text-slate-700"> Footer Text </Label>

                <p class="text-xs text-slate-500 mt-1">
                  Enter the copyright, company information, or other text you want to
                  display in the footer.
                </p>
              </div>

              <span
                class="text-xs"
                :class="characterCount > maxLength ? 'text-red-500' : 'text-slate-400'"
              >
                {{ characterCount }}/{{ maxLength }}
              </span>
            </div>

            <!-- Textarea -->

            <div class="relative">
              <textarea
                v-model="form.footer_text"
                :maxlength="maxLength"
                rows="5"
                placeholder="Example: © 2026 Rising Tech Nepal. All rights reserved."
                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 placeholder-slate-400 outline-none transition-all duration-200 resize-none hover:border-slate-300 focus:border-primary focus:ring-4 focus:ring-primary/10"
              ></textarea>
            </div>

            <!-- Helper -->

            <div class="mt-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
              <div class="flex items-start gap-3">
                <div
                  class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
                >
                  <i class="fa-solid fa-circle-info"></i>
                </div>

                <div>
                  <Label class="font-medium text-slate-700"> Footer Information </Label>

                  <p class="text-sm text-slate-500 leading-6 mt-1">
                    This text will be displayed at the bottom of application pages. You
                    can use it for copyright notices, company information, contact
                    details, or a short application message.
                  </p>
                </div>
              </div>
            </div>
          </div>

          

         
          <!-- =================================================
               FOOTER
          ================================================== -->

          <div
            class="mt-6 flex flex-col-reverse sm:flex-row gap-3 justify-end border-t border-slate-100 pt-4"
          >
            <Button @click="toggleModal" type="danger" text="Close" />

            <Button
              :submit="true"
              :text="form.processing ? 'Saving...' : 'Save Settings'"
              :processing="form.processing"
              :disabled="form.processing"
            />
          </div>
        </form>
      </div>
    </div>
  </Modal>
</template>
