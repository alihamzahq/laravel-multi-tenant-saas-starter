import CentralLayout from '@/Layouts/CentralLayout';
import DangerButton from '@/Components/DangerButton';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import { Head, Link, router } from '@inertiajs/react';

function StatusBadge({ isActive }) {
    return (
        <span
            className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${
                isActive
                    ? 'bg-green-100 text-green-800'
                    : 'bg-red-100 text-red-800'
            }`}
        >
            {isActive ? 'Active' : 'Inactive'}
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

export default function Show({ tenant }) {

    const handleToggleStatus = () => {
        router.post(route('admin.tenants.toggle-status', tenant.id), {}, {
            preserveScroll: true,
        });
    };

    const handleDelete = () => {
        if (confirm(`Are you sure you want to delete "${tenant.name}"? This action cannot be undone and will delete all tenant data.`)) {
            router.delete(route('admin.tenants.destroy', tenant.id));
        }
    };

    return (
        <CentralLayout
            header={
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <Link
                            href={route('admin.tenants.index')}
                            className="text-gray-400 hover:text-gray-600"
                        >
                            <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </Link>
                        <h2 className="text-xl font-semibold leading-tight text-gray-800">
                            {tenant.name}
                        </h2>
                        <StatusBadge isActive={tenant.is_active} />
                    </div>
                    <div className="flex items-center gap-2">
                        {tenant.is_active && (
                            <a
                                href={route('admin.tenants.login-as', tenant.id)}
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                <PrimaryButton>Login as Tenant</PrimaryButton>
                            </a>
                        )}
                        <Link href={route('admin.tenants.edit', tenant.id)}>
                            <SecondaryButton>Edit</SecondaryButton>
                        </Link>
                        <button onClick={handleToggleStatus}>
                            {tenant.is_active ? (
                                <SecondaryButton className="!text-yellow-700 !border-yellow-300 hover:!bg-yellow-50">
                                    Deactivate
                                </SecondaryButton>
                            ) : (
                                <SecondaryButton className="!text-green-700 !border-green-300 hover:!bg-green-50">
                                    Activate
                                </SecondaryButton>
                            )}
                        </button>
                        <DangerButton onClick={handleDelete}>Delete</DangerButton>
                    </div>
                </div>
            }
        >
            <Head title={tenant.name} />

            <div className="py-12">
                <div className="mx-auto max-w-3xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow sm:rounded-lg">
                        <div className="px-4 py-5 sm:px-6">
                            <h3 className="text-lg font-medium leading-6 text-gray-900">
                                Tenant Information
                            </h3>
                            <p className="mt-1 max-w-2xl text-sm text-gray-500">
                                Details and configuration for this tenant.
                            </p>
                        </div>
                        <div className="border-t border-gray-200">
                            <dl className="divide-y divide-gray-200 px-4 sm:px-6">
                                <DetailRow label="Tenant ID" value={tenant.id} />
                                <DetailRow label="Name" value={tenant.name} />
                                <DetailRow label="Domain">
                                    <a
                                        href={`http://${tenant.domain}`}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="text-indigo-600 hover:text-indigo-500"
                                    >
                                        {tenant.domain}
                                        <svg className="ml-1 inline-block h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                </DetailRow>
                                <DetailRow label="Admin Email" value={tenant.admin_email} />
                                <DetailRow label="Status">
                                    <StatusBadge isActive={tenant.is_active} />
                                </DetailRow>
                                <DetailRow label="Created" value={tenant.created_at} />
                                <DetailRow label="Last Updated" value={tenant.updated_at} />
                            </dl>
                        </div>
                    </div>

                    <div className="mt-6 flex justify-between">
                        <Link href={route('admin.tenants.index')}>
                            <SecondaryButton>Back to Tenants</SecondaryButton>
                        </Link>
                    </div>
                </div>
            </div>
        </CentralLayout>
    );
}
