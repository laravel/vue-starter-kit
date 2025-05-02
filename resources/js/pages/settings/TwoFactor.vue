<script setup lang="ts">
import { ref, computed, watch, nextTick } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { PinInputInput, PinInputRoot } from 'reka-ui'

import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogDescription,
  DialogTitle,
  DialogTrigger
} from '@/components/ui/dialog';
import { Check, Copy, Eye, EyeOff, Loader2, ScanLine, LockKeyhole } from 'lucide-vue-next';
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';

const props = withDefaults(defineProps<{
  confirmed?: boolean;
  recoveryCodes?: string[];
}>(), {
  confirmed: false,
  recoveryCodes: () => []
});

const breadcrumbs = [
  {
    title: 'Two-Factor Authentication',
    href: '/settings/two-factor'
  }
];

const {
  confirmed,
  qrCodeSvg,
  secretKey,
  recoveryCodesList,
  copied,
  passcode,
  error,
  verifyStep,
  showingRecoveryCodes,
  showModal,
  confirm,
  regenerateRecoveryCodes,
  disable,
  copyToClipboard
} = useTwoFactorAuth(props.confirmed, props.recoveryCodes);

const pinValue = ref<string[]>([]);
const pinInputContainerRef = ref<HTMLElement | null>(null);

// Handle the passcode input change
const pinComplete = (value: string[]) => {
  passcode.value = value.join('');
  confirm();
};

// Watch passcode changes to clear pin input when needed
watch(() => passcode.value, (newValue: string) => {
  if (!newValue) {
    pinValue.value = [];
  }
});

// Toggle recovery codes visibility
const toggleRecoveryCodes = () => {
  showingRecoveryCodes.value = !showingRecoveryCodes.value;
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Two-Factor Authentication" />
    <SettingsLayout>
      <div class="space-y-6">
        <HeadingSmall title="Two-Factor Authentication" description="Manage your two-factor authentication settings" />
        
        <div v-if="!confirmed" class="flex flex-col items-start justify-start space-y-5">
          <Badge
            variant="outline"
            class="bg-orange-50 text-orange-700 border-orange-200 hover:bg-orange-50"
          >
            Disabled
          </Badge>
          
          <p class="-translate-y-1 text-stone-500 dark:text-stone-400">
            When you enable 2FA, you'll be prompted for a secure code during login, which can be
            retrieved from your phone's Google Authenticator app.
          </p>

          <Dialog :open="showModal" @update:open="showModal = $event">
            <DialogTrigger as-child>
              <Button @click="showModal = true">Enable</Button>
            </DialogTrigger>
            <DialogContent class="sm:max-w-md">
              <DialogHeader class="flex items-center justify-center">
                <div class="p-0.5 w-auto rounded-full border mb-3 border-stone-100 dark:border-stone-600 bg-white dark:bg-stone-800 shadow-sm">
                  <div class="p-2.5 rounded-full border border-stone-200 dark:border-stone-600 overflow-hidden bg-stone-100 dark:bg-stone-200 relative">
                    <div class="flex items-stretch absolute inset-0 w-full h-full divide-x [&>div]:flex-1 divide-stone-200 dark:divide-stone-300 justify-around opacity-50">
                      <div v-for="i in 5" :key="i"></div>
                    </div>
                    <div class="flex flex-col items-stretch absolute w-full h-full divide-y [&>div]:flex-1 inset-0 divide-stone-200 dark:divide-stone-300 justify-around opacity-50">
                      <div v-for="i in 5" :key="i"></div>
                    </div>
                    <ScanLine class="size-6 relative z-20 dark:text-black" />
                  </div>
                </div>
                <DialogTitle>
                  {{ !verifyStep ? 'Turn on 2-step Verification' : 'Verify Authentication Code' }}
                </DialogTitle>
                <DialogDescription class="text-center">
                  {{ !verifyStep
                    ? 'Open your authenticator app and choose Scan QR code'
                    : 'Enter the 6-digit code from your authenticator app' }}
                </DialogDescription>
              </DialogHeader>

              <div class="relative w-auto items-center justify-center flex flex-col space-y-5">
                <template v-if="!verifyStep">
                  <div class="relative max-w-md mx-auto overflow-hidden flex items-center p-8 pt-0">
                    <div class="border border-stone-200 dark:border-stone-700 rounded-lg relative overflow-hidden w-64 aspect-square mx-auto">
                      <div v-if="!qrCodeSvg" class="bg-white dark:bg-stone-700 animate-pulse flex items-center justify-center absolute inset-0 w-full h-auto aspect-square z-10">
                        <Loader2 class="size-6 animate-spin" />
                      </div>
                      <div v-else class="relative z-10">
                        <img :src="'data:image/svg+xml;base64,' + qrCodeSvg" alt="QR Code" class="w-full h-full aspect-square" />
                      </div>
                    </div>
                    <div v-if="qrCodeSvg" class="h-1/2 z-20 w-full border-t border-blue-500 absolute bottom-0 left-0 -translate-y-4">
                      <div class="absolute inset-0 w-full h-full bg-gradient-to-b from-blue-500 to-transparent opacity-20"></div>
                    </div>
                  </div>

                  <div class="flex items-center space-x-5 w-full">
                    <Button class="w-full" @click="() => {
                      verifyStep = true;
                      // Focus the pin input after the DOM updates
                      nextTick(() => {
                        if (pinInputContainerRef) {
                          const firstInput = pinInputContainerRef.querySelector('input');
                          if (firstInput) firstInput.focus();
                        }
                      });
                    }">
                      Continue
                    </Button>
                  </div>

                  <div class="flex items-center relative w-full justify-center">
                    <div class="w-full absolute inset-0 top-1/2 bg-stone-200 dark:bg-stone-600 h-px"></div>
                    <span class="px-2 py-1 bg-white dark:bg-stone-800 relative">
                      or, enter the code manually
                    </span>
                  </div>

                  <div class="flex items-center justify-center w-full space-x-2">
                    <div class="w-full rounded-xl flex items-stretch border dark:border-stone-700 overflow-hidden">
                      <div v-if="!secretKey" class="w-full h-full flex items-center justify-center bg-stone-100 dark:bg-stone-700 p-3">
                        <Loader2 class="size-4 animate-spin" />
                      </div>
                      <template v-else>
                        <input
                          type="text"
                          readonly
                          :value="secretKey"
                          class="w-full h-full p-3 text-black dark:text-stone-100 bg-white dark:bg-stone-800"
                        />
                        <button
                          @click="copyToClipboard(secretKey)"
                          class="block relative border-l border-stone-200 dark:border-stone-600 px-3 hover:bg-stone-100 dark:hover:bg-stone-600 h-auto"
                        >
                          <Check v-if="copied" class="w-4 text-green-500" />
                          <Copy v-else class="w-4" />
                        </button>
                      </template>
                    </div>
                  </div>
                </template>
                
                <template v-else>
                  <div ref="pinInputContainerRef" class="relative w-full space-y-3">

                    <div class="w-full py-2 flex flex-col items-center justify-center space-y-3">
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
                          class="w-10 h-10 bg-white rounded-lg text-center shadow-sm border text-green10 placeholder:text-mauve8 focus:shadow-[0_0_0_2px] focus:shadow-stone-800 outline-none"
                        />
                      </PinInputRoot>
                      <div v-if="error" class="mt-2 text-sm text-red-600">{{ error }}</div>
                    </div>
                    
                    <div class="flex items-center space-x-5 w-full">
                      <Button
                        variant="outline"
                        class="w-auto flex-1"
                        @click="verifyStep = false"
                      >
                        Back
                      </Button>
                      <Button
                        class="w-auto flex-1"
                        @click="confirm"
                        :disabled="!pinValue || pinValue.length < 6"
                      >
                        Confirm
                      </Button>
                    </div>
                  </div>
                </template>
              </div>
            </DialogContent>
          </Dialog>
        </div>

        <div v-if="confirmed" class="flex flex-col space-y-5">
          <Badge
            variant="outline"
            class="bg-green-50 text-green-700 border-green-200 hover:bg-green-50"
          >
            Enabled
          </Badge>
          <p class="text-stone-500 dark:text-stone-400">
            With two factor authentication enabled, you'll be prompted for a secure, random
            token during login, which you can retrieve from your Google Authenticator app.
          </p>

          <div>
            <div class="flex items-start p-4 bg-stone-50 dark:bg-stone-800 border border-stone-200 dark:border-stone-700 rounded-t-xl">
              <LockKeyhole class="size-5 mr-2 text-stone-500" />
              <div class="space-y-1">
                <h3 class="font-medium">2FA Recovery Codes</h3>
                <p class="text-sm text-stone-500 dark:text-stone-400">
                  Recovery codes let you regain access if you lose your 2FA device. Store them
                  in a secure password manager.
                </p>
              </div>
            </div>

            <div class="bg-stone-100 dark:bg-stone-800 rounded-b-xl border-t-0 border border-stone-200 dark:border-stone-700 text-sm">
              <div
                @click="toggleRecoveryCodes"
                class="h-10 group cursor-pointer flex items-center select-none justify-between px-5 text-xs"
              >
                <div
                  :class="`relative ${!showingRecoveryCodes ? 'opacity-40 hover:opacity-60' : 'opacity-60'}`"
                >
                  <span v-if="!showingRecoveryCodes" class="flex items-center space-x-1">
                    <Eye class="size-4" /> <span>View My Recovery Codes</span>
                  </span>
                  <span v-else class="flex items-center space-x-1">
                    <EyeOff class="size-4" /> <span>Hide Recovery Codes</span>
                  </span>
                </div>

                <Button
                  v-if="showingRecoveryCodes"
                  size="sm"
                  variant="outline"
                  class="text-stone-600"
                  @click.stop="regenerateRecoveryCodes"
                >
                  Regenerate Codes
                </Button>
              </div>

              <div
                class="relative overflow-hidden transition-all duration-300"
                :style="{
                  height: showingRecoveryCodes ? 'auto' : '0',
                  opacity: showingRecoveryCodes ? 1 : 0
                }"
              >
                <div class="grid max-w-xl gap-1 px-4 py-4 font-mono text-sm bg-stone-200 dark:bg-stone-900 dark:text-stone-100">
                  <div v-for="(code, index) in recoveryCodesList" :key="index">{{ code }}</div>
                </div>
                <p class="px-4 py-3 text-xs select-none text-stone-500 dark:text-stone-400">
                  You have {{ recoveryCodesList.length }} recovery codes left. Each can be used
                  once to access your account and will be removed after use. If you need more,
                  click <span class="font-bold">Regenerate Codes</span> above.
                </p>
              </div>
            </div>
          </div>

          <div class="inline relative">
            <Button variant="destructive" @click="disable">
              Disable 2FA
            </Button>
          </div>
        </div>
      </div>
    </SettingsLayout>
  </AppLayout>
</template>