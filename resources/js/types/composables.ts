import type { InertiaLinkProps } from '@inertiajs/vue3';
import type { ComputedRef, DeepReadonly, Ref } from 'vue';

import type { Appearance, ResolvedAppearance } from './ui';

export type UseActiveUrlReturn = {
    currentUrl: DeepReadonly<ComputedRef<string>>;
    urlIsActive: (
        urlToCheck: NonNullable<InertiaLinkProps['href']>,
        currentUrl?: string,
    ) => boolean;
};

export type UseAppearanceReturn = {
    appearance: Ref<Appearance>;
    resolvedAppearance: ComputedRef<ResolvedAppearance>;
    updateAppearance: (value: Appearance) => void;
};

export type UseInitialsReturn = {
    getInitials: (fullName?: string) => string;
};

export type UseTwoFactorAuthReturn = {
    qrCodeSvg: Ref<string | null>;
    manualSetupKey: Ref<string | null>;
    recoveryCodesList: Ref<string[]>;
    errors: Ref<string[]>;
    hasSetupData: ComputedRef<boolean>;
    clearSetupData: () => void;
    clearErrors: () => void;
    clearTwoFactorAuthData: () => void;
    fetchQrCode: () => Promise<void>;
    fetchSetupKey: () => Promise<void>;
    fetchSetupData: () => Promise<void>;
    fetchRecoveryCodes: () => Promise<void>;
};
