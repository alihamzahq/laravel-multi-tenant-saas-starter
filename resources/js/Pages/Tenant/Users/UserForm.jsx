import { useEffect } from 'react';
import { useForm } from '@inertiajs/react';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import TextInput from '@/Components/TextInput';

export default function UserForm({ user = null, roles, onClose, editMode = false }) {
    const { data, setData, post, put, processing, errors, reset } = useForm({
        name: '',
        email: '',
        password: '',
        role: 'user',
    });

    useEffect(() => {
        if (user && editMode) {
            setData({
                name: user.name || '',
                email: user.email || '',
                password: '',
                role: user.role || 'user',
            });
        }
    }, [user, editMode]);

    const handleSubmit = (e) => {
        e.preventDefault();

        if (editMode) {
            put(route('tenant.users.update', user.id), {
                onSuccess: () => {
                    reset();
                    onClose();
                },
            });
        } else {
            post(route('tenant.users.store'), {
                onSuccess: () => {
                    reset();
                    onClose();
                },
            });
        }
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-4">
            <div>
                <InputLabel htmlFor="name" value="Name" />
                <TextInput
                    id="name"
                    type="text"
                    name="name"
                    value={data.name}
                    className="mt-1 block w-full"
                    autoComplete="name"
                    onChange={(e) => setData('name', e.target.value)}
                    required
                />
                <InputError message={errors.name} className="mt-2" />
            </div>

            <div>
                <InputLabel htmlFor="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    name="email"
                    value={data.email}
                    className="mt-1 block w-full"
                    autoComplete="email"
                    onChange={(e) => setData('email', e.target.value)}
                    required
                />
                <InputError message={errors.email} className="mt-2" />
            </div>

            <div>
                <InputLabel
                    htmlFor="password"
                    value={editMode ? 'Password (leave blank to keep current)' : 'Password'}
                />
                <TextInput
                    id="password"
                    type="password"
                    name="password"
                    value={data.password}
                    className="mt-1 block w-full"
                    autoComplete="new-password"
                    onChange={(e) => setData('password', e.target.value)}
                    required={!editMode}
                />
                <InputError message={errors.password} className="mt-2" />
            </div>

            <div>
                <InputLabel htmlFor="role" value="Role" />
                <select
                    id="role"
                    name="role"
                    value={data.role}
                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    onChange={(e) => setData('role', e.target.value)}
                    required
                >
                    {Object.entries(roles).map(([value, label]) => (
                        <option key={value} value={value}>
                            {label}
                        </option>
                    ))}
                </select>
                <InputError message={errors.role} className="mt-2" />
            </div>

            <div className="flex justify-end space-x-3 pt-4">
                <SecondaryButton type="button" onClick={onClose}>
                    Cancel
                </SecondaryButton>
                <PrimaryButton type="submit" disabled={processing}>
                    {processing ? 'Saving...' : editMode ? 'Update User' : 'Create User'}
                </PrimaryButton>
            </div>
        </form>
    );
}
