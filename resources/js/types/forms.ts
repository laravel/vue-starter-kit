export interface FormErrors {
    [key: string]: string | string[];
}

export interface PasswordFormData {
    current_password: string;
    password: string;
    password_confirmation: string;
}

export interface ProfileFormData {
    name: string;
    email: string;
}

export interface DeleteAccountFormData {
    password: string;
}
