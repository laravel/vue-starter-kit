import { qrCode, recoveryCodes, secretKey } from '@/routes/two-factor';
import { ref } from 'vue';

const fetchJson = async <T>(url: string): Promise<T> => {
    const response = await fetch(url, {
        headers: { Accept: 'application/json' },
    });

    if (!response.ok) {
        throw new Error(`Failed to fetch: ${response.status}`);
    }

    return response.json();
};

export const useTwoFactorAuth = () => {
    const qrCodeSvg = ref<string | null>(null);
    const manualSetupKey = ref<string | null>(null);
    const recoveryCodesList = ref<string[]>([]);

    const fetchQrCode = async (): Promise<void> => {
        const { svg } = await fetchJson<{ svg: string; url: string }>(qrCode.url());
        qrCodeSvg.value = svg;
    };

    const fetchSetupKey = async (): Promise<void> => {
        const { secretKey: key } = await fetchJson<{ secretKey: string }>(secretKey.url());
        manualSetupKey.value = key;
    };

    const fetchRecoveryCodes = async (): Promise<void> => {
        try {
            recoveryCodesList.value = await fetchJson<string[]>(recoveryCodes.url());
        } catch (error) {
            console.error('Failed to fetch recovery codes:', error);
            recoveryCodesList.value = [];
        }
    };

    const fetchSetupData = async (): Promise<void> => {
        try {
            await Promise.all([fetchQrCode(), fetchSetupKey()]);
        } catch (error) {
            console.error('Failed to fetch setup data:', error);
            qrCodeSvg.value = null;
            manualSetupKey.value = null;
        }
    };

    return {
        qrCodeSvg,
        manualSetupKey,
        recoveryCodesList,
        fetchQrCode,
        fetchSetupKey,
        fetchSetupData,
        fetchRecoveryCodes,
    };
};
