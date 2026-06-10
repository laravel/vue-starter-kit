<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import TeamInvitationController from '@/actions/App/Http/Controllers/Teams/TeamInvitationController';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import type { DashboardInvitation } from '@/types';

type Props = {
    invitations: DashboardInvitation[];
};

const props = defineProps<Props>();

const open = ref(true);
const processingCode = ref<string | null>(null);

const acceptInvitation = (invitation: DashboardInvitation) => {
    router.visit(TeamInvitationController.accept(invitation), {
        onStart: () => (processingCode.value = invitation.code),
        onFinish: () => (processingCode.value = null),
    });
};

const declineInvitation = (invitation: DashboardInvitation) => {
    router.visit(TeamInvitationController.decline(invitation), {
        onStart: () => (processingCode.value = invitation.code),
        onFinish: () => (processingCode.value = null),
        onSuccess: () => {
            if (props.invitations.length === 1) {
                open.value = false;
            }
        },
    });
};
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent data-test="pending-invitations-modal">
            <DialogHeader>
                <DialogTitle>Pending team invitations</DialogTitle>
                <DialogDescription>
                    Accept or decline the teams you have been invited to join.
                </DialogDescription>
            </DialogHeader>

            <div class="grid gap-4">
                <div
                    v-for="invitation in props.invitations"
                    :key="invitation.code"
                    data-test="pending-invitation-row"
                    class="rounded-lg border p-4"
                >
                    <div class="space-y-1">
                        <p class="font-medium">{{ invitation.team.name }}</p>
                        <p class="text-sm text-muted-foreground">
                            {{ invitation.inviterName }} invited you to join
                            this team.
                        </p>
                    </div>

                    <div class="mt-4 flex justify-end gap-2">
                        <Button
                            variant="secondary"
                            data-test="pending-invitation-decline"
                            :disabled="processingCode === invitation.code"
                            @click="declineInvitation(invitation)"
                        >
                            Decline
                        </Button>

                        <Button
                            data-test="pending-invitation-accept"
                            :disabled="processingCode === invitation.code"
                            @click="acceptInvitation(invitation)"
                        >
                            Accept
                        </Button>
                    </div>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
