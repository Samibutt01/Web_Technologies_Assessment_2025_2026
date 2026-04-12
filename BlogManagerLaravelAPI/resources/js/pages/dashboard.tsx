import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';
import { usePage } from '@inertiajs/react';
import { FileText, FolderOpen, MessageSquare } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

interface DashboardProps {
    stats: {
        total_posts: number;
        total_categories: number;
        total_comments: number;
    };
}

export default function Dashboard() {
    const { stats } = usePage<{ stats: DashboardProps['stats'] }>().props;

    const cards = [
        {
            label: 'Total Posts',
            value: stats?.total_posts ?? 0,
            icon: FileText,
            color: 'bg-blue-50 text-blue-600',
        },
        {
            label: 'Total Categories',
            value: stats?.total_categories ?? 0,
            icon: FolderOpen,
            color: 'bg-green-50 text-green-600',
        },
        {
            label: 'Total Comments',
            value: stats?.total_comments ?? 0,
            icon: MessageSquare,
            color: 'bg-purple-50 text-purple-600',
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <h1 className="text-2xl font-bold">Dashboard</h1>

                <div className="grid gap-4 md:grid-cols-3">
                    {cards.map((card) => (
                        <div
                            key={card.label}
                            className="rounded-xl border border-sidebar-border/70 bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-gray-900"
                        >
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">
                                        {card.label}
                                    </p>
                                    <p className="mt-1 text-3xl font-bold">
                                        {card.value}
                                    </p>
                                </div>
                                <div className={`rounded-full p-3 ${card.color}`}>
                                    <card.icon className="h-6 w-6" />
                                </div>
                            </div>
                        </div>
                    ))}
                </div>

                <div className="rounded-xl border border-sidebar-border/70 bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-gray-900">
                    <h2 className="mb-2 text-lg font-semibold">Welcome back!</h2>
                    <p className="text-gray-500 dark:text-gray-400">
                        Use the sidebar to explore menu.
                    </p>
                </div>
            </div>
        </AppLayout>
    );
}
