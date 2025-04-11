<script setup lang="ts">
import { Breadcrumb, BreadcrumbItem, BreadcrumbLink, BreadcrumbList, BreadcrumbPage, BreadcrumbSeparator } from '@/components/ui/breadcrumb';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'

import { BreadcrumbItemType } from '@/types';
import { Link } from '@inertiajs/vue3';
import { ChevronDown } from 'lucide-vue-next'


interface BreadcrumbItem {
    title: string;
    href?: string;
    subItem?: BreadcrumbItem[]
}

defineProps<{
    breadcrumbs: BreadcrumbItem[];
}>();
</script>

<template>
    <Breadcrumb>
        <BreadcrumbList>
            <template v-for="(item, index) in breadcrumbs" :key="index">
                <BreadcrumbItem>
                    <template v-if="index === breadcrumbs.length - 1">
                        <BreadcrumbPage>{{ item.title }}</BreadcrumbPage>
                    </template>
                    <template v-else-if="item.subItems">
                        <DropdownMenu>
                            <DropdownMenuTrigger class="flex items-center gap-1">
                                {{ item.title }}
                                <ChevronDown class="h-4 w-4" />
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="start">
                                <template v-for="(subItem, subIndex) in item.subItems" :key="subIndex">
                                    <DropdownMenuItem>
                                        <Link :href="subItem.href ?? '#'">
                                        {{ subItem.title }}
                                        </Link>
                                    </DropdownMenuItem>
                                </template>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </template>
                    <template v-else>
                        <BreadcrumbLink as-child>
                            <Link :href="item.href ?? '#'">
                            {{ item.title }}
                            </Link>
                        </BreadcrumbLink>
                    </template>
                </BreadcrumbItem>
                <BreadcrumbSeparator v-if="index !== breadcrumbs.length - 1" />
            </template>
        </BreadcrumbList>
    </Breadcrumb>
</template>
