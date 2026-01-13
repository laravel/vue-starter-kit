<script setup lang="ts">
import { AlertCircle } from 'lucide-vue-next';
import { computed } from 'vue';

import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';

interface Props {
    errors: string[];
    title?: string;
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Something went wrong.',
});

const uniqueErrors = computed(() => Array.from(new Set(props.errors)));
</script>

<template>
    <Alert variant="destructive">
        <AlertCircle class="size-4" />
        <AlertTitle>{{ title }}</AlertTitle>
        <AlertDescription>
            <ul class="list-inside list-disc text-sm">
                <li v-for="(error, index) in uniqueErrors" :key="index">
                    {{ error }}
                </li>
            </ul>
        </AlertDescription>
    </Alert>
</template>
