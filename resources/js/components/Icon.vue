<script setup lang="ts">
import * as icons from 'lucide-vue-next';
import { computed } from 'vue';

import { cn } from '@/lib/utils';
import type { IconProps } from '@/types';

const props = withDefaults(defineProps<IconProps>(), {
    class: '',
    size: 16,
    strokeWidth: 2,
});

const className = computed(() => cn('h-4 w-4', props.class));

const icon = computed(() => {
    const iconName = props.name.charAt(0).toUpperCase() + props.name.slice(1);
    return (icons as Record<string, any>)[iconName];
});
</script>

<template>
    <component
        :is="icon"
        :class="className"
        :size="size"
        :stroke-width="strokeWidth"
        :color="color"
    />
</template>
