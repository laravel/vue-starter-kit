import type { User } from './auth';
import type { BreadcrumbItem } from './navigation';

export interface HeadingProps {
    title: string;
    description?: string;
}

export interface UserInfoProps {
    user: User;
    showEmail?: boolean;
}

export interface BreadcrumbsProps {
    breadcrumbs: BreadcrumbItem[];
}

export interface AlertErrorProps {
    errors: string[];
    title?: string;
}

export interface IconProps {
    name: string;
    class?: string;
    size?: number | string;
    color?: string;
    strokeWidth?: number | string;
}
