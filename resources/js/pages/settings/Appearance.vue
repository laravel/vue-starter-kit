<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

import AppearanceTabs from '@/components/AppearanceTabs.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { type BreadcrumbItem } from '@/types';

import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';

const { t } = useI18n();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: t('settings.appearance.title'),
        href: '/settings/appearance',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="t('settings.appearance.title')" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall :title="t('settings.appearance.title')" :description="t('settings.appearance.description')" />
                <AppearanceTabs />

                <!-- Theme preview or additional appearance settings can go here -->
                <div class="text-sm text-muted-foreground">
                    {{ t('settings.appearance.description') }}
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
