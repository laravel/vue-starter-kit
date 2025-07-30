<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Globe } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';

interface Props {
    languages: Array<{ code: string; name: string }>;
    currentLanguage: { code: string; name: string };
    locale: string;
}

defineProps<Props>();

const emit = defineEmits<{
    switchLanguage: [langCode: string];
}>();

const { t } = useI18n();

const handleLanguageSwitch = (langCode: string) => {
    emit('switchLanguage', langCode);
};
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" size="icon" class="h-8 w-8 p-0">
                <Globe class="h-4 w-4 text-foreground" />
                <span class="sr-only">{{ t('common.switchLanguage') }}</span>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
            <DropdownMenuItem
                v-for="language in languages"
                :key="language.code"
                @click="handleLanguageSwitch(language.code)"
                :class="{ 'bg-accent': language.code === currentLanguage.code }"
            >
                {{ language.name }}
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
