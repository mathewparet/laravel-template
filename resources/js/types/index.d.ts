import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Resource<T>
{
    data: Array<T,{can: ResourcePermission}>,
    links: ResourceLinks,
    can: ResourceCollectionPermission,
    meta: ResourceMetadata,
}

export interface ResourceCollectionPermission
{
    create: boolean,
    viewAny: boolean,
    [key: string]: boolean,
}

export interface ResourceMetadata
{
    current_page: number,
    from: number,
    last_page: number,
    path: string,
    per_page: number,
    to: number,
    total: number,
    links: Array<ResourceMetadataLink>
}

export interface ResourceMetadataLink
{
    url: string|null,
    label: string,
    active: boolean,
}

export interface ResourceLinks
{
    first: String,
    last: String,
    prev: String|null,
    next: String|null,
}

export interface ResourcePermissions
{
    update: boolean,
    view: boolean,
    delete: boolean,
    restore: boolean,
    forceDelete: boolean,
    [key: string]: boolean,
}

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
    name?: string,
    module?: string,
}

export interface GroupedNavItem extends NavItem {
    group: string
}

export interface SharedData extends PageProps {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
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

export type BreadcrumbItemType = BreadcrumbItem;
