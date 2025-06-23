<script setup lang="ts">
import { store } from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import AuthBase from '@/layouts/AuthLayout.vue';
import { register } from '@/routes';
import { authentication_options, login } from '@/routes/passkeys';
import { request } from '@/routes/password';
import { LoginProps, LoginRequest } from '@/types/generated';
import { Head, useForm } from '@inertiajs/vue3';
import { startAuthentication, WebAuthnAbortService } from '@simplewebauthn/browser';
import { LoaderCircle } from 'lucide-vue-next';
import { onBeforeUnmount, onMounted } from 'vue';

defineProps<LoginProps>();

const passkeyForm = useForm('passkey', {});

const loginForm = useForm<LoginRequest>('login', {
	email: '',
	password: '',
	remember: false,
});

const submit = () => {
	loginForm.submit(store(), {
		onFinish: () => loginForm.reset('password'),
	});
};

const loginUsingPasskey = async () => {
	try {
		const options = await (await fetch(authentication_options().url)).json();
		const passkey = await startAuthentication({ optionsJSON: options });

		passkeyForm
			.transform(() => ({
				start_authentication_response: JSON.stringify(passkey),
			}))
			.submit(login());
	} catch (e) {}
};

onMounted(loginUsingPasskey);
onBeforeUnmount(() => {
	WebAuthnAbortService.cancelCeremony();
});
</script>

<template>
	<AuthBase title="Log in to your account">
		<Head title="Log in" />

		<div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
			{{ status }}
		</div>

		<Button type="submit" class="mt-4 w-full" :tabindex="4" :disabled="loginForm.processing" @click="loginUsingPasskey">
			<LoaderCircle v-if="loginForm.processing" class="h-4 w-4 animate-spin" />
			Sign in using passkey
		</Button>

		<InputError v-if="passkeyStatus" :message="passkeyStatus" class="self-center" />

		<div class="flex w-full items-center gap-x-2">
			<Separator class="shrink" />
			<span class="text-xs font-thin text-muted-foreground">OR</span>
			<Separator class="shrink" />
		</div>

		<form @submit.prevent="submit" class="flex flex-col gap-6">
			<div class="grid gap-6">
				<div class="grid gap-2">
					<Label for="email">Email address</Label>
					<Input
						id="email"
						type="email"
						required
						autofocus
						:tabindex="1"
						autocomplete="email webauthn"
						v-model="loginForm.email"
						placeholder="email@example.com"
					/>
					<InputError :message="loginForm.errors.email" />
				</div>

				<div class="grid gap-2">
					<div class="flex items-center justify-between">
						<Label for="password">Password</Label>
						<TextLink v-if="canResetPassword" :href="request()" class="text-sm" :tabindex="5"> Forgot password? </TextLink>
					</div>
					<Input
						id="password"
						type="password"
						required
						:tabindex="2"
						autocomplete="current-password"
						v-model="loginForm.password"
						placeholder="Password"
					/>
					<InputError :message="loginForm.errors.password" />
				</div>

				<div class="flex items-center justify-between">
					<Label for="remember" class="flex items-center space-x-3">
						<Checkbox id="remember" v-model="loginForm.remember" :tabindex="3" />
						<span>Remember me</span>
					</Label>
				</div>

				<Button type="submit" class="mt-4 w-full" :tabindex="4" :disabled="loginForm.processing">
					<LoaderCircle v-if="loginForm.processing" class="h-4 w-4 animate-spin" />
					Log in
				</Button>
			</div>

			<div class="text-center text-sm text-muted-foreground">
				Don't have an account?
				<TextLink :href="register()" :tabindex="5">Sign up</TextLink>
			</div>
		</form>
	</AuthBase>
</template>
