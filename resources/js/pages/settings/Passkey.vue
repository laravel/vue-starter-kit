<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
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
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { destroy, registerOptions, store } from '@/routes/passkey';
import type { BreadcrumbItem } from '@/types';
import type { PasskeyProp, PasskeyProps, PasskeyRegistrationOptionsRequest, StorePasskeyRequest } from '@/types/generated';
import { Head, useForm } from '@inertiajs/vue3';
import { startRegistration } from '@simplewebauthn/browser';
import { LoaderCircle, Trash2 } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

defineProps<PasskeyProps>();

const breadcrumbItems: BreadcrumbItem[] = [
	{
		title: 'Passkey settings',
		href: '/settings/passkey',
	},
];

const form = useForm<StorePasskeyRequest>('create_passkey', {
	name: '',
	passkey: '',
});

const storePasskey = async () => {
	const response = await fetch(registerOptions({ query: { name: form.name } as PasskeyRegistrationOptionsRequest }).url, {
		headers: {
			Accept: 'application/json',
		},
	});
	const options = await response.json();

	if (response.status === 422) {
		form.setError('name', options.errors.name[0]);
		return;
	}

	try {
		const passkey = await startRegistration(options);

		form.transform((data) => ({
			...data,
			passkey: JSON.stringify(passkey),
		})).submit(store(), {
			preserveScroll: true,
			onSuccess: () => form.reset(),
		});
	} catch (e) {
		form.setError('name', 'Unable to create passkey. Please try again.');
	}
};

const deleteForm = useForm('delete_passkey', {});

const deletePasskey = (passkey: PasskeyProp) => {
	deleteForm.submit(destroy(passkey.id as unknown as number), {
		onSuccess: () => {
			toast(`Passkey "${passkey.name}" deleted`);
		},
	});
};
</script>

<template>
	<AppLayout :breadcrumbs="breadcrumbItems">
		<Head title="Passkey settings" />

		<SettingsLayout>
			<div class="space-y-6">
				<HeadingSmall title="Manage passkeys" description="Add and manage your passkeys" />

				<form @submit.prevent="storePasskey" class="space-y-6">
					<div class="grid gap-2">
						<Label for="name">Passkey name</Label>
						<Input id="name" v-model="form.name" type="text" class="mt-1 block w-full" placeholder="Passkey name" required />
						<InputError :message="form.errors.name" />
					</div>

					<div class="flex items-center gap-4">
						<Button :disabled="form.processing">Create passkey</Button>

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
			<div v-if="passkeys.length" class="space-y-6">
				<HeadingSmall title="Your passkeys" />

				<div v-for="passkey in passkeys" :key="passkey.id" class="flex items-center justify-between">
					<div class="flex flex-col">
						<span class="font-semibold">{{ passkey.name }}</span>
						<span class="text-sm font-thin text-muted-foreground">Last used: {{ passkey.lastUsedAtForHumans || 'Never' }}</span>
					</div>
					<Dialog>
						<DialogTrigger as-child>
							<Button variant="destructive" :disabled="deleteForm.processing">
								<LoaderCircle v-if="deleteForm.processing" class="mr-2 h-4 w-4 animate-spin" />
								<Trash2 v-else class="mr-2 h-4 w-4" />
								Delete
							</Button>
						</DialogTrigger>
						<DialogContent>
							<form class="space-y-6" @submit.prevent="deletePasskey(passkey)">
								<DialogHeader class="space-y-3">
									<DialogTitle>Are you sure you want to delete your passkey?</DialogTitle>
									<DialogDescription>
										Once deleted your passkey cannot be restored and you will not be able to log in using this passkey.
									</DialogDescription>
								</DialogHeader>

								<DialogFooter class="gap-2">
									<DialogClose as-child>
										<Button variant="secondary"> Cancel </Button>
									</DialogClose>

									<Button type="submit" variant="destructive" :disabled="deleteForm.processing">
										<LoaderCircle v-if="deleteForm.processing" class="mr-2 h-4 w-4 animate-spin" />
										<Trash2 v-else class="mr-2 h-4 w-4" />
										Delete passkey
									</Button>
								</DialogFooter>
							</form>
						</DialogContent>
					</Dialog>
				</div>
			</div>
		</SettingsLayout>
	</AppLayout>
</template>
