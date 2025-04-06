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
    id: string;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    roles?: Role[],
}
export interface Role {
    id: string,
    name: string,
    created_at: string;
    updated_at: string;
}

export interface Transaction {
    id: string
    type: 'deposit' | 'withdrawal' | 'transfer_in' | 'transfer_out'
    amount: number
    created_at: string
    description?: string
  }
  
export interface PaginationInfo {
    current_page: number
    total_pages: number
    per_page: number
    next_page_url: string | null
    previous_page_url: string | null
    total: number
  }

  
export type BreadcrumbItemType = BreadcrumbItem;
