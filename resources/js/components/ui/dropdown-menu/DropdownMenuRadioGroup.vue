<script setup lang="ts">
import { DropdownMenuRadioGroup, useForwardPropsEmits, type DropdownMenuRadioGroupEmits, type DropdownMenuRadioGroupProps } from 'reka-ui';

const props = defineProps<DropdownMenuRadioGroupProps>();
const emits = defineEmits<DropdownMenuRadioGroupEmits>();

const forwarded = useForwardPropsEmits(props, emits);
</script>

<template>
    <DropdownMenuRadioGroup v-bind="forwarded">
        <slot />
    </DropdownMenuRadioGroup>
</template>
