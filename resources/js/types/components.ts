import type { User } from './auth';
import type { BreadcrumbItem } from './navigation';

export interface HeadingProps {
    title: string;
    description?: string;
    variant?: 'default' | 'small';
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
