<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { Check, ChevronsUpDown, Plus, Users } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import CreateTeamModal from '@/components/CreateTeamModal.vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { switchMethod } from '@/routes/teams';
import type { Team } from '@/types';

const props = withDefaults(
    defineProps<{
        inHeader?: boolean;
    }>(),
    {
        inHeader: false,
    },
);

const page = usePage();
const isMobile = ref(false);
let mediaQuery: MediaQueryList | null = null;
const updateIsMobile = () => {
    if (mediaQuery) {
        isMobile.value = mediaQuery.matches;
    }
};

const currentTeam = computed(() => page.props.currentTeam);
const teams = computed(() => page.props.teams ?? []);
const menuContentClass = computed(() =>
    props.inHeader
        ? 'w-56'
        : 'w-[--reka-dropdown-menu-trigger-width] min-w-56 rounded-lg',
);
const teamItemClass = computed(() =>
    props.inHeader ? 'cursor-pointer gap-2' : 'cursor-pointer gap-2 p-2',
);
const checkIconClass = computed(() =>
    props.inHeader ? 'ml-auto size-4' : 'ml-auto h-4 w-4',
);
const plusIconClass = computed(() => (props.inHeader ? 'size-4' : 'h-4 w-4'));

const switchTeam = (team: Team) => {
    const previousTeamSlug = currentTeam.value?.slug;

    router.visit(switchMethod(team.slug), {
        onFinish: () => {
            if (!previousTeamSlug || typeof window === 'undefined') {
                router.reload();

                return;
            }

            const currentUrl = `${window.location.pathname}${window.location.search}${window.location.hash}`;
            const segment = `/${previousTeamSlug}`;

            if (currentUrl.includes(segment)) {
                router.visit(currentUrl.replace(segment, `/${team.slug}`), {
                    replace: true,
                });

                return;
            }

            router.reload();
        },
    });
};

onMounted(() => {
    mediaQuery = window.matchMedia('(max-width: 767px)');
    updateIsMobile();
    mediaQuery.addEventListener('change', updateIsMobile);
});

onUnmounted(() => {
    mediaQuery?.removeEventListener('change', updateIsMobile);
});
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button
                data-test="team-switcher-trigger"
                variant="ghost"
                :class="
                    props.inHeader
                        ? 'h-8 gap-1 px-2'
                        : 'w-full justify-start px-2 has-[>svg]:px-2 data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground'
                "
            >
                <Users
                    :class="
                        props.inHeader
                            ? 'hidden'
                            : 'hidden size-4 shrink-0 group-data-[collapsible=icon]:block'
                    "
                />
                <div
                    :class="
                        props.inHeader
                            ? 'grid flex-1 text-left text-sm leading-tight'
                            : 'grid flex-1 text-left text-sm leading-tight group-data-[collapsible=icon]:hidden'
                    "
                >
                    <span
                        :class="
                            props.inHeader
                                ? 'max-w-[120px] truncate font-medium'
                                : 'truncate font-semibold'
                        "
                    >
                        {{ currentTeam?.name ?? 'Select team' }}
                    </span>
                </div>
                <ChevronsUpDown
                    :class="
                        props.inHeader
                            ? 'size-4 opacity-50'
                            : 'ml-auto group-data-[collapsible=icon]:hidden'
                    "
                />
            </Button>
        </DropdownMenuTrigger>

        <DropdownMenuContent
            :class="menuContentClass"
            :side="props.inHeader ? undefined : isMobile ? 'bottom' : 'right'"
            :align="props.inHeader ? 'end' : 'start'"
            :side-offset="props.inHeader ? undefined : 4"
        >
            <DropdownMenuLabel class="text-xs text-muted-foreground">
                Teams
            </DropdownMenuLabel>
            <DropdownMenuItem
                v-for="team in teams"
                :key="team.id"
                data-test="team-switcher-item"
                :class="teamItemClass"
                @click="switchTeam(team)"
            >
                {{ team.name }}
                <Check
                    v-if="currentTeam?.id === team.id"
                    :class="checkIconClass"
                />
            </DropdownMenuItem>
            <DropdownMenuSeparator />
            <CreateTeamModal>
                <DropdownMenuItem
                    data-test="team-switcher-new-team"
                    :class="teamItemClass"
                    @select.prevent
                >
                    <Plus :class="plusIconClass" />
                    <span class="text-muted-foreground">New team</span>
                </DropdownMenuItem>
            </CreateTeamModal>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
