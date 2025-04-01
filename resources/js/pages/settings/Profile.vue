<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import Avatar from '@/components/ui/avatar/Avatar.vue';
import AvatarFallback from '@/components/ui/avatar/AvatarFallback.vue';
import AvatarImage from '@/components/ui/avatar/AvatarImage.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useInitials } from '@/composables/useInitials';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';
import { Trash2 } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: '/settings/profile',
    },
];

const page = usePage<SharedData>();
const user = page.props.auth.user as User;

const { getInitials } = useInitials();

const form = useForm({
    name: user.name,
    email: user.email,
    profile_photo: null as File | null,
    remove_avatar: false as boolean,
    _method: 'PATCH',
});

const handleAvatarChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        if (file.size > 2 * 1024 * 1024) {
            form.errors.profile_photo = 'File size exceeds 2MB.';
            return;
        }
        form.profile_photo = file;
    }
};

const removeAvatar = () => {
    form.profile_photo = null;
    user.avatar = undefined;
    form.remove_avatar = true;
    form.errors.profile_photo = '';
};

const submit = () => {
    form.post(route('profile.update'), {
        preserveScroll: true,
        forceFormData: true,
    });
};

const avatarSrc = computed(() => {
    if (form.profile_photo instanceof File) {
        return URL.createObjectURL(form.profile_photo);
    }
    return form.profile_photo === null ? null : user.avatar;
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Profile settings" />
        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Profile information" description="Update your name, email and avatar" />
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="group relative">
                            <label for="avatar-upload" class="cursor-pointer" aria-label="Upload avatar">
                                <Avatar size="lg">
                                    <AvatarImage :src="avatarSrc || user.avatar || ''" alt="User avatar" />
                                    <AvatarFallback>{{ getInitials(user.name) }}</AvatarFallback>
                                </Avatar>
                            </label>
                            <input id="avatar-upload" type="file" accept="image/*" class="hidden" @change="handleAvatarChange" />
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Click the avatar to upload a new photo.</p>
                            <p class="text-sm text-muted-foreground">Max size: 2MB</p>
                            <Dialog>
                                <DialogTrigger as-child>
                                    <Button
                                        v-if="user.avatar || avatarSrc"
                                        type="button"
                                        variant="destructive"
                                        size="sm"
                                        class="mt-2"
                                        aria-label="Open remove avatar dialog"
                                    >
                                        <Trash2 /> Remove avatar
                                    </Button>
                                </DialogTrigger>

                                <DialogContent>
                                    <DialogHeader>
                                        <DialogTitle>Remove profile photo?</DialogTitle>
                                        <DialogDescription>
                                            <p class="mt-4">
                                                Don’t worry — this just removes the photo from preview. Nothing is permanent until you hit “Save”.
                                            </p>
                                            <p class="mt-4">You can reload the page to bring it back.</p>
                                        </DialogDescription>
                                    </DialogHeader>

                                    <DialogFooter class="gap-2">
                                        <DialogClose as-child>
                                            <Button variant="secondary">Cancel</Button>
                                        </DialogClose>

                                        <DialogClose as-child>
                                            <Button variant="destructive" @click="removeAvatar"> Yes, remove it </Button>
                                        </DialogClose>
                                    </DialogFooter>
                                </DialogContent>
                            </Dialog>
                        </div>
                    </div>
                    <InputError :message="form.errors.profile_photo" class="mt-1" />

                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input id="name" class="mt-1 block w-full" v-model="form.name" required autocomplete="name" placeholder="Full name" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input
                            id="email"
                            class="mt-1 block w-full"
                            type="email"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="Email address"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="-mt-4 text-sm text-muted-foreground">
                            Your email address is unverified.
                            <Link
                                :href="route('verification.send')"
                                method="post"
                                as="button"
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:!decoration-current dark:decoration-neutral-500"
                            >
                                Click here to resend the verification email.
                            </Link>
                        </p>

                        <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                            A new verification link has been sent to your email address.
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">Save</Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
                        </Transition>
                    </div>
                </form>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
