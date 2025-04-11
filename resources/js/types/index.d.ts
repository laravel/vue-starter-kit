import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface SharedData extends PageProps {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface PaginatedResult<T> {
  data: T[];
  links: {
    first: string;
    last: string;
    next?: string;
    prev?: string;
  };
  meta: {
    current_page: number;
    from?: number;
    last_page: number;
    links: {
      active: boolean;
      label: string;
      url?: string;
    }[];
    path: string;
    per_page: number;
    to?: number;
    total: number;
  };
};

export type BreadcrumbItemType = BreadcrumbItem;
