import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';

interface CategoryProps {
    title: string;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin',
        href: dashboard().url,
    },
    {
        title: 'Categories',
        href: '#',
    },
];

export default function Categories({ title }: CategoryProps) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={title} />
            <div className="p-4">
                <h1 className="text-3xl font-bold mb-4">{title}</h1>
                <p>This is a static categories page.</p>
            </div>
        </AppLayout>
    );
}
