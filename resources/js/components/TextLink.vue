<script setup lang="ts">
import { InertiaLinkProps, Link } from '@inertiajs/vue3';

interface Props extends Pick<InertiaLinkProps, "href" | "method" | "as"> {
    tabindex?: number;
};

defineProps<Props>();
</script>

<template>
    <Link
        :href="href"
        :tabindex="tabindex"
        :method="method"
        :as="as"
        class="hover:!decoration-current text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out dark:decoration-neutral-500"
    >
        <slot />
    </Link>
</template>
