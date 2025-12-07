import { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import TenantLayout from '@/Layouts/TenantLayout';
import Modal from '@/Components/Modal';
import PrimaryButton from '@/Components/PrimaryButton';
import UserForm from './UserForm';

function RoleBadge({ role, roles }) {
    const colors = {
        admin: 'bg-purple-100 text-purple-800',
        user: 'bg-gray-100 text-gray-800',
    };

    return (
        <span
            className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${colors[role] || colors.user}`}
        >
            {roles[role] || role}
        </span>
    );
}

export default function Index({ users, roles, canManageUsers }) {
    const [showModal, setShowModal] = useState(false);
    const [editingUser, setEditingUser] = useState(null);

    const openCreateModal = () => {
        setEditingUser(null);
        setShowModal(true);
    };

    const openEditModal = (user) => {
        setEditingUser(user);
        setShowModal(true);
    };

    const closeModal = () => {
        setShowModal(false);
        setEditingUser(null);
    };

    const handleDelete = (user) => {
        if (confirm(`Are you sure you want to delete "${user.name}"?`)) {
            router.delete(route('tenant.users.destroy', user.id));
        }
    };

    const formatDate = (dateString) => {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    };

    return (
        <TenantLayout
            header={
                <div className="flex items-center justify-between">
                    <h2 className="text-xl font-semibold leading-tight text-gray-800">
                        Users
                    </h2>
                    {canManageUsers && (
                        <PrimaryButton onClick={openCreateModal}>
                            Add User
                        </PrimaryButton>
                    )}
                </div>
            }
        >
            <Head title="Users" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="overflow-x-auto">
                            {users.length > 0 ? (
                                <table className="min-w-full divide-y divide-gray-200">
                                    <thead className="bg-gray-50">
                                        <tr>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Name
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Email
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Role
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Created
                                            </th>
                                            {canManageUsers && (
                                                <th className="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                                    Actions
                                                </th>
                                            )}
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-200 bg-white">
                                        {users.map((user) => (
                                            <tr
                                                key={user.id}
                                                className="hover:bg-gray-50"
                                            >
                                                <td className="whitespace-nowrap px-6 py-4">
                                                    <span className="text-sm font-medium text-gray-900">
                                                        {user.name}
                                                    </span>
                                                </td>
                                                <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                                    {user.email}
                                                </td>
                                                <td className="whitespace-nowrap px-6 py-4">
                                                    <RoleBadge
                                                        role={user.role}
                                                        roles={roles}
                                                    />
                                                </td>
                                                <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                                    {formatDate(user.created_at)}
                                                </td>
                                                {canManageUsers && (
                                                    <td className="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                                        <button
                                                            onClick={() =>
                                                                openEditModal(user)
                                                            }
                                                            className="text-indigo-600 hover:text-indigo-900"
                                                        >
                                                            Edit
                                                        </button>
                                                        <button
                                                            onClick={() =>
                                                                handleDelete(user)
                                                            }
                                                            className="ml-4 text-red-600 hover:text-red-900"
                                                        >
                                                            Delete
                                                        </button>
                                                    </td>
                                                )}
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            ) : (
                                <div className="px-6 py-12 text-center">
                                    <p className="text-sm text-gray-500">
                                        No users found.
                                    </p>
                                    {canManageUsers && (
                                        <button
                                            onClick={openCreateModal}
                                            className="mt-2 text-sm font-medium text-indigo-600 hover:text-indigo-500"
                                        >
                                            Add your first user
                                        </button>
                                    )}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            <Modal
                show={showModal}
                onClose={closeModal}
                title={editingUser ? 'Edit User' : 'Add User'}
                maxWidth="md"
            >
                <UserForm
                    user={editingUser}
                    roles={roles}
                    onClose={closeModal}
                    editMode={!!editingUser}
                />
            </Modal>
        </TenantLayout>
    );
}
