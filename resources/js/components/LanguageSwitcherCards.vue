<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
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
    <Card>
        <CardHeader>
            <CardTitle class="flex items-center gap-2">
                <Globe class="h-5 w-5" />
                {{ t('settings.language.title') }}
            </CardTitle>
            <CardDescription>
                {{ t('settings.language.description') }}
            </CardDescription>
        </CardHeader>
        <CardContent>
            <div class="grid gap-3">
                <Button
                    v-for="language in languages"
                    :key="language.code"
                    @click="handleLanguageSwitch(language.code)"
                    variant="outline"
                    :class="['justify-start', language.code === currentLanguage.code ? 'border-primary bg-primary/5' : 'hover:bg-muted']"
                >
                    <div class="flex w-full items-center justify-between">
                        <span class="font-medium">{{ language.name }}</span>
                        <span v-if="language.code === currentLanguage.code" class="text-xs text-muted-foreground">
                            {{ t('settings.language.currentLanguage') }}
                        </span>
                    </div>
                </Button>
            </div>
        </CardContent>
    </Card>
</template>
