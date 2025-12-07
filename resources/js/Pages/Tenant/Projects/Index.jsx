import TenantLayout from '@/Layouts/TenantLayout';
import { Head } from '@inertiajs/react';

export default function Index() {
    return (
        <TenantLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Projects
                </h2>
            }
        >
            <Head title="Projects" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            Projects will be listed here.
                        </div>
                    </div>
                </div>
            </div>
        </TenantLayout>
    );
}
