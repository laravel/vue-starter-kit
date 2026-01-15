import type { InertiaLinkProps } from '@inertiajs/vue3';
import type { ComputedRef, DeepReadonly, Ref } from 'vue';

import type { Appearance } from './ui';

// useActiveUrl return type
export interface UseActiveUrlReturn {
    currentUrl: DeepReadonly<ComputedRef<string>>;
    urlIsActive: (
        urlToCheck: NonNullable<InertiaLinkProps['href']>,
        currentUrl?: string,
    ) => boolean;
}

// useAppearance return type
export interface UseAppearanceReturn {
    appearance: Ref<Appearance>;
    updateAppearance: (value: Appearance) => void;
}

// useInitials return type
export interface UseInitialsReturn {
    getInitials: (fullName?: string) => string;
}

// useTwoFactorAuth return type
export interface UseTwoFactorAuthReturn {
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
}
