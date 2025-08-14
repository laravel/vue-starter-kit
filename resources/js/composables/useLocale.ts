import { usePage } from '@inertiajs/vue3';
import { watch } from 'vue';
import { useI18n } from 'vue-i18n';

export function useLocale() {
    const { locale } = useI18n();
    const page = usePage();

    // Helper function to extract current locale from the new structure
    const getCurrentLocale = (localeData: any): string => {
        if (typeof localeData === 'object' && localeData?.current) {
            return localeData.current;
        }
        if (typeof localeData === 'string') {
            return localeData;
        }
        return 'en';
    };

    // Initialize locale from page props
    const initializeLocale = () => {
        const localeData = (page.props as any)?.locale;
        const userLocale = getCurrentLocale(localeData);
        if (locale.value !== userLocale) {
            locale.value = userLocale;
        }
    };

    // Watch for page changes and update locale accordingly
    watch(
        () => (page.props as any)?.locale,
        (newLocaleData) => {
            const newLocale = getCurrentLocale(newLocaleData);
            if (newLocale && newLocale !== locale.value) {
                locale.value = newLocale;
            }
        },
        { immediate: true },
    );

    return {
        locale,
        initializeLocale,
    };
}
