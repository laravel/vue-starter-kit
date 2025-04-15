import { LinkableItemType } from '@/types';
import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function getNavItemLink(item:LinkableItemType,defaultValue:string  =  '#') : string{
    if(!item.href ) return defaultValue;
    return item.type === 'route' ? route(item.href):item.href;
}
