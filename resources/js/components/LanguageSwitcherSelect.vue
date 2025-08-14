<script setup lang="ts">
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

interface Props {
    languages: Array<{ code: string; name: string }>;
    currentLanguage: { code: string; name: string };
    locale: string;
}

defineProps<Props>();

const emit = defineEmits<{
    'update:locale': [value: string];
    switchLanguage: [langCode: string];
}>();

const handleLanguageChange = (langCode: string) => {
    emit('update:locale', langCode);
    emit('switchLanguage', langCode);
};
</script>

<template>
    <Select :model-value="locale" @update:model-value="handleLanguageChange">
        <SelectTrigger class="w-[180px]">
            <SelectValue class="text-foreground">
                <span class="flex items-center gap-2">
                    <span class="text-foreground">{{ currentLanguage.name }}</span>
                </span>
            </SelectValue>
        </SelectTrigger>
        <SelectContent>
            <SelectItem v-for="language in languages" :key="language.code" :value="language.code">
                <span class="flex items-center gap-2">
                    <span>{{ language.name }}</span>
                </span>
            </SelectItem>
        </SelectContent>
    </Select>
</template>
