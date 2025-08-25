<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import TwoFactorRecoveryCodes from '@/components/TwoFactorRecoveryCodes.vue';
import TwoFactorSetupModal from '@/components/TwoFactorSetupModal.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { disable, enable, show } from '@/routes/two-factor';
import { BreadcrumbItem } from '@/types';
import { Form, Head } from '@inertiajs/vue3';
import { ShieldBan, ShieldCheck } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    requiresConfirmation?: boolean;
    twoFactorEnabled?: boolean;
}

withDefaults(defineProps<Props>(), {
    requiresConfirmation: false,
    twoFactorEnabled: false,
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Two-Factor Authentication',
        href: show.url(),
    },
];

const { qrCodeSvg, manualSetupKey } = useTwoFactorAuth();
const showSetupModal = ref<boolean>(false);
const setupModalRef = ref<InstanceType<typeof TwoFactorSetupModal>>();
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Two-Factor Authentication" />
        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall title="Two-Factor Authentication" description="Manage your two-factor authentication settings" />

                <div v-if="!twoFactorEnabled" class="flex flex-col items-start justify-start space-y-4">
                    <Badge variant="destructive">Disabled</Badge>
                    <p class="text-muted-foreground">
                        When you enable 2FA, you'll be prompted for a secure code during login, which can be retrieved from your phone's TOTP
                        supported app.
                    </p>

                    <div>
                        <Button v-if="qrCodeSvg && manualSetupKey" @click="showSetupModal = true"> <ShieldCheck />Enable </Button>
                        <Form v-else v-bind="enable.form()" @success="showSetupModal = true" #default="{ processing }">
                            <Button type="submit" :disabled="processing"> <ShieldCheck />{{ processing ? 'Enabling...' : 'Enable' }} </Button>
                        </Form>
                    </div>
                </div>

                <div v-else class="flex flex-col items-start justify-start space-y-4">
                    <Badge variant="default">Enabled</Badge>
                    <p class="text-muted-foreground">
                        With two factor authentication enabled, you'll be prompted for a secure, random token during login, which you can retrieve
                        from your TOTP Authenticator app.
                    </p>

                    <TwoFactorRecoveryCodes />

                    <div class="relative inline">
                        <Form v-bind="disable.form()" method="delete" #default="{ processing }">
                            <Button variant="destructive" type="submit" :disabled="processing">
                                <ShieldBan />
                                {{ processing ? 'Disabling...' : 'Disable 2FA' }}
                            </Button>
                        </Form>
                    </div>
                </div>

                <TwoFactorSetupModal
                    ref="setupModalRef"
                    v-model:isOpen="showSetupModal"
                    :requiresConfirmation="requiresConfirmation"
                    :twoFactorEnabled="twoFactorEnabled"
                />
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
