<script setup lang="ts">
import { ref, computed, watch, nextTick } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { PinInputRoot, PinInputInput } from 'reka-ui';

const recovery = ref(false);
const pinValue = ref<string[]>([]);
const pinInputContainerRef = ref<HTMLElement | null>(null);
const recoveryInputRef = ref<HTMLInputElement | null>(null);

const form = useForm({
  code: '',
  recovery_code: '',
  recovery: false
});

// Handle completion of pin input
const pinComplete = (value: string[]) => {
  form.code = value.join('');
  if (form.code.length === 6) {
    submitForm();
  }
};

const submitForm = () => {
  form.recovery = recovery.value;
  form.post(route('two-factor.challenge'));
};

// handle focus states for the pin input and recovery input
watch(recovery, (value) => {
  if (value) {
    nextTick(() => {
      if (recoveryInputRef.value) {
          recoveryInputRef.value.$el.focus();
      }
    });
  } else {
    nextTick(() => {
      if (pinInputContainerRef.value) {
        const firstInput = pinInputContainerRef.value.querySelector('input');
        if (firstInput) firstInput.focus();
      }
    });
  }
})


const toggleRecovery = () => {
  recovery.value = !recovery.value;
  form.code = '';
  form.recovery_code = '';
  form.clearErrors();
  pinValue.value = [];
};

const isDisabled = computed(() => {
  return form.processing || 
    (recovery.value ? !form.recovery_code : !form.code || form.code.length !== 6);
});
</script>

<template>
  <AuthLayout
    :title="recovery ? 'Recovery Code' : 'Authentication Code'"
    :description="recovery 
      ? 'Please confirm access to your account by entering one of your emergency recovery codes.'
      : 'Enter the authentication code provided by your authenticator application.'"
  >
    <Head title="Two Factor Authentication" />

    <div class="relative space-y-2">
      <template v-if="!recovery">
        <form @submit.prevent="submitForm" class="space-y-4">
          <div class="flex flex-col items-center justify-center space-y-3 text-center">
            <div ref="pinInputContainerRef" class="w-full flex items-center justify-center">
              <PinInputRoot
                id="otp"
                v-model="pinValue"
                placeholder="○"
                class="flex gap-2 items-center mt-1"
                @complete="pinComplete"
              >
                <PinInputInput
                  v-for="(id, index) in 6"
                  :key="id"
                  :index="index"
                  :autofocus="index === 0"
                  class="w-10 h-10 bg-white rounded-lg text-center shadow-sm border text-green10 placeholder:text-mauve8 focus:shadow-[0_0_0_2px] focus:shadow-stone-800 outline-none"
                />

              </PinInputRoot>
            </div>
            <InputError :message="form.errors.code" />
          </div>
          <Button type="submit" class="w-full" :disabled="isDisabled">
            Continue
          </Button>
        </form>
      </template>
      
      <template v-else>
        <form @submit.prevent="submitForm" class="space-y-4">
          <Input
            ref="recoveryInputRef"
            v-model="form.recovery_code"
            type="text"
            class="block w-full"
            autocomplete="one-time-code"
            placeholder="Enter recovery code"
            autofocus
            required
          />
          <InputError :message="form.errors.recovery_code" />
          <Button type="submit" class="w-full" :disabled="isDisabled">
            Continue
          </Button>
        </form>
      </template>

      <div class="space-x-0.5 text-center text-sm leading-5 text-muted-foreground mt-4">
        <span class="opacity-80">or you can </span>
        <button
          type="button"
          class="font-medium underline opacity-80 cursor-pointer bg-transparent border-0 p-0"
          @click="toggleRecovery"
        >
          {{ recovery
            ? 'login using an authentication code'
            : 'login using a recovery code' }}
        </button>
      </div>
    </div>
  </AuthLayout>
</template>
