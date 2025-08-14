<script setup lang="ts">
import { useLocale } from '@/composables/useLocale';
import { router, usePage } from '@inertiajs/vue3';
import { computed, defineAsyncComponent, watch } from 'vue';
import { useI18n } from 'vue-i18n';

interface Props {
    display?: 'dropdown' | 'select' | 'cards';
}

const { locale } = useI18n();
const page = usePage();
const { initializeLocale } = useLocale();

const availableLocales = (page.props.locale as any).available;

const capitalize = (s: string) => (s ? s.charAt(0).toUpperCase() + s.slice(1) : s);

const languages = availableLocales.map((code: string) => {
    const name = new Intl.DisplayNames([code], { type: 'language' }).of(code);
    return { code, name: capitalize(name || code) };
});

const currentLanguage = computed(() => {
    return languages.find((lang: any) => lang.code === locale.value) || languages[0];
});

watch(locale, async (newLocale, oldLocale) => {
    if (newLocale !== oldLocale && oldLocale) {
        await syncLocaleWithBackend(newLocale);
    }
});

const syncLocaleWithBackend = async (langCode: string) => {
    const isAuthenticated = (page.props as any)?.auth?.user;
    const routeName = isAuthenticated ? 'language.update' : 'language.update.guest';

    try {
        await router.patch(
            route(routeName),
            { locale: langCode },
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {},
                onError: () => {
                    initializeLocale();
                },
            },
        );
    } catch {
        initializeLocale();
    }
};

const switchLanguage = async (langCode: string) => {
    locale.value = langCode;
};

// Dynamic component mapping
const componentMap = {
    dropdown: defineAsyncComponent(() => import('./LanguageSwitcherDropdown.vue')),
    select: defineAsyncComponent(() => import('./LanguageSwitcherSelect.vue')),
    cards: defineAsyncComponent(() => import('./LanguageSwitcherCards.vue')),
};

const props = withDefaults(defineProps<Props>(), {
    display: 'dropdown',
});

const currentComponent = computed(() => {
    return componentMap[props.display as keyof typeof componentMap] || componentMap.dropdown;
});
</script>

<template>
    <component :is="currentComponent" :languages="languages" :current-language="currentLanguage" :locale="locale" @switch-language="switchLanguage" />
</template>
