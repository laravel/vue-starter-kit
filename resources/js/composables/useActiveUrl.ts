import type { InertiaLinkProps } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { computed, readonly } from 'vue';

import { toUrl } from '@/lib/utils';

const page = usePage();
const currentUrlReactive = computed(
    () => new URL(page.url, window?.location.origin).pathname,
);

export function useActiveUrl() {
    function urlIsActive(
        urlToCheck: NonNullable<InertiaLinkProps['href']>,
        currentUrl?: string,
    ) {
        const urlToCompare = currentUrl ?? currentUrlReactive.value;
        return toUrl(urlToCheck) === urlToCompare;
    }

    return {
        currentUrl: readonly(currentUrlReactive),
        urlIsActive,
    };
}
