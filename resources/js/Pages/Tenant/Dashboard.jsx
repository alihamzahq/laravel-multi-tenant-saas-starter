import TenantLayout from '@/Layouts/TenantLayout';
import StatCard from '@/Components/StatCard';
import { Head, Link, usePage } from '@inertiajs/react';

function StatusBadge({ status }) {
    const colors = {
        draft: 'bg-gray-100 text-gray-800',
        active: 'bg-green-100 text-green-800',
        completed: 'bg-blue-100 text-blue-800',
        archived: 'bg-yellow-100 text-yellow-800',
    };

    return (
        <span
            className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${colors[status] || colors.draft}`}
        >
            {status.charAt(0).toUpperCase() + status.slice(1)}
        </span>
    );
}

export default function Dashboard({ stats, recentProjects }) {
    const { auth, tenant } = usePage().props;

    return (
        <TenantLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Dashboard
                </h2>
            }
        >
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {/* Welcome Section */}
                    <div className="mb-8 overflow-hidden rounded-lg bg-white shadow">
                        <div className="p-6">
                            <h3 className="text-lg font-medium text-gray-900">
                                Welcome back, {auth.user?.name}!
                            </h3>
                            <p className="mt-1 text-sm text-gray-500">
                                {tenant?.name
                                    ? `You're working in ${tenant.name}`
                                    : "Here's what's happening with your projects"}
                            </p>
                        </div>
                    </div>

                    {/* Stats Grid */}
                    <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        <StatCard
                            title="Team Members"
                            value={stats?.totalUsers ?? 0}
                            description="Total users"
                        />
                        <StatCard
                            title="Total Projects"
                            value={stats?.totalProjects ?? 0}
                            description="All projects"
                        />
                        <StatCard
                            title="Active Projects"
                            value={stats?.activeProjects ?? 0}
                            description="Currently in progress"
                            colorClass="text-green-600"
                        />
                        <StatCard
                            title="Draft Projects"
                            value={stats?.draftProjects ?? 0}
                            description="Not yet started"
                            colorClass="text-gray-600"
                        />
                    </div>

                    {/* Recent Projects */}
                    <div className="mt-8">
                        <div className="overflow-hidden bg-white shadow sm:rounded-lg">
                            <div className="border-b border-gray-200 px-4 py-5 sm:px-6">
                                <div className="flex items-center justify-between">
                                    <div>
                                        <h3 className="text-lg font-medium leading-6 text-gray-900">
                                            Recent Projects
                                        </h3>
                                        <p className="mt-1 text-sm text-gray-500">
                                            Your latest projects
                                        </p>
                                    </div>
                                    <Link
                                        href={route('tenant.projects.index')}
                                        className="text-sm font-medium text-indigo-600 hover:text-indigo-500"
                                    >
                                        View all
                                    </Link>
                                </div>
                            </div>
                            <div className="overflow-x-auto">
                                {recentProjects && recentProjects.length > 0 ? (
                                    <table className="min-w-full divide-y divide-gray-200">
                                        <thead className="bg-gray-50">
                                            <tr>
                                                <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                    Name
                                                </th>
                                                <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                    Status
                                                </th>
                                                <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                    Created By
                                                </th>
                                                <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                    Created
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-gray-200 bg-white">
                                            {recentProjects.map((project) => (
                                                <tr
                                                    key={project.id}
                                                    className="hover:bg-gray-50"
                                                >
                                                    <td className="whitespace-nowrap px-6 py-4">
                                                        <span className="text-sm font-medium text-gray-900">
                                                            {project.name}
                                                        </span>
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4">
                                                        <StatusBadge
                                                            status={
                                                                project.status
                                                            }
                                                        />
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                                        {project.creator}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                                        {project.created_at}
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                ) : (
                                    <div className="px-6 py-12 text-center">
                                        <p className="text-sm text-gray-500">
                                            No projects yet.
                                        </p>
                                        <Link
                                            href={route('tenant.projects.index')}
                                            className="mt-2 inline-block text-sm font-medium text-indigo-600 hover:text-indigo-500"
                                        >
                                            Create your first project
                                        </Link>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </TenantLayout>
    );
}
