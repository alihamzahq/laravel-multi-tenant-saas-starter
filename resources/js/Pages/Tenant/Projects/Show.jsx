import TenantLayout from '@/Layouts/TenantLayout';
import DangerButton from '@/Components/DangerButton';
import SecondaryButton from '@/Components/SecondaryButton';
import { Head, Link, router } from '@inertiajs/react';

function StatusBadge({ status, label }) {
    const colors = {
        draft: 'bg-gray-100 text-gray-800',
        active: 'bg-green-100 text-green-800',
        completed: 'bg-blue-100 text-blue-800',
        archived: 'bg-yellow-100 text-yellow-800',
    };

    return (
        <span
            className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${colors[status] || 'bg-gray-100 text-gray-800'}`}
        >
            {label}
        </span>
    );
}

function DetailRow({ label, value, children }) {
    return (
        <div className="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5">
            <dt className="text-sm font-medium text-gray-500">{label}</dt>
            <dd className="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                {children || value || '-'}
            </dd>
        </div>
    );
}

export default function Show({ project }) {
    const handleDelete = () => {
        if (confirm(`Are you sure you want to delete "${project.name}"? This action cannot be undone.`)) {
            router.delete(route('tenant.projects.destroy', project.id));
        }
    };

    return (
        <TenantLayout
            header={
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <Link
                            href={route('tenant.projects.index')}
                            className="text-gray-400 hover:text-gray-600"
                        >
                            <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </Link>
                        <h2 className="text-xl font-semibold leading-tight text-gray-800">
                            {project.name}
                        </h2>
                        <StatusBadge status={project.status} label={project.status_label} />
                    </div>
                    {project.can_edit && (
                        <div className="flex items-center gap-2">
                            <Link href={route('tenant.projects.edit', project.id)}>
                                <SecondaryButton>Edit</SecondaryButton>
                            </Link>
                            <DangerButton onClick={handleDelete}>Delete</DangerButton>
                        </div>
                    )}
                </div>
            }
        >
            <Head title={project.name} />

            <div className="py-12">
                <div className="mx-auto max-w-3xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow sm:rounded-lg">
                        <div className="px-4 py-5 sm:px-6">
                            <h3 className="text-lg font-medium leading-6 text-gray-900">
                                Project Information
                            </h3>
                            <p className="mt-1 max-w-2xl text-sm text-gray-500">
                                Details and information about this project.
                            </p>
                        </div>
                        <div className="border-t border-gray-200">
                            <dl className="divide-y divide-gray-200 px-4 sm:px-6">
                                <DetailRow label="Project Name" value={project.name} />
                                <DetailRow label="Description">
                                    {project.description ? (
                                        <p className="whitespace-pre-wrap">{project.description}</p>
                                    ) : (
                                        <span className="text-gray-400 italic">No description provided</span>
                                    )}
                                </DetailRow>
                                <DetailRow label="Status">
                                    <StatusBadge status={project.status} label={project.status_label} />
                                </DetailRow>
                                <DetailRow label="Created By" value={project.creator} />
                                <DetailRow label="Created" value={project.created_at} />
                                <DetailRow label="Last Updated" value={project.updated_at} />
                            </dl>
                        </div>
                    </div>

                    <div className="mt-6 flex justify-between">
                        <Link href={route('tenant.projects.index')}>
                            <SecondaryButton>Back to Projects</SecondaryButton>
                        </Link>
                    </div>
                </div>
            </div>
        </TenantLayout>
    );
}
