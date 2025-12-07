import TenantLayout from '@/Layouts/TenantLayout';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Head, useForm } from '@inertiajs/react';
import { useRef } from 'react';

function UpdateProfileInformation({ user }) {
    const { data, setData, patch, errors, processing } = useForm({
        name: user.name,
        email: user.email,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        patch(route('tenant.profile.update'));
    };

    return (
        <section>
            <header>
                <h2 className="text-lg font-medium text-gray-900">
                    Profile Information
                </h2>
                <p className="mt-1 text-sm text-gray-600">
                    Update your account's profile information and email address.
                </p>
            </header>

            <form onSubmit={handleSubmit} className="mt-6 space-y-6">
                <div>
                    <InputLabel htmlFor="name" value="Name" />
                    <TextInput
                        id="name"
                        className="mt-1 block w-full"
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        required
                        isFocused
                        autoComplete="name"
                    />
                    <InputError className="mt-2" message={errors.name} />
                </div>

                <div>
                    <InputLabel htmlFor="email" value="Email" />
                    <TextInput
                        id="email"
                        type="email"
                        className="mt-1 block w-full"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        required
                        autoComplete="username"
                    />
                    <InputError className="mt-2" message={errors.email} />
                </div>

                <div className="flex items-center gap-4">
                    <PrimaryButton disabled={processing}>
                        {processing ? 'Saving...' : 'Save'}
                    </PrimaryButton>
                </div>
            </form>
        </section>
    );
}

function UpdatePassword() {
    const passwordInput = useRef();
    const currentPasswordInput = useRef();

    const { data, setData, errors, put, reset, processing } = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();

        put(route('tenant.profile.password'), {
            preserveScroll: true,
            onSuccess: () => reset(),
            onError: (errors) => {
                if (errors.password) {
                    reset('password', 'password_confirmation');
                    passwordInput.current.focus();
                }

                if (errors.current_password) {
                    reset('current_password');
                    currentPasswordInput.current.focus();
                }
            },
        });
    };

    return (
        <section>
            <header>
                <h2 className="text-lg font-medium text-gray-900">
                    Update Password
                </h2>
                <p className="mt-1 text-sm text-gray-600">
                    Ensure your account is using a long, random password to stay secure.
                </p>
            </header>

            <form onSubmit={handleSubmit} className="mt-6 space-y-6">
                <div>
                    <InputLabel htmlFor="current_password" value="Current Password" />
                    <TextInput
                        id="current_password"
                        ref={currentPasswordInput}
                        value={data.current_password}
                        onChange={(e) => setData('current_password', e.target.value)}
                        type="password"
                        className="mt-1 block w-full"
                        autoComplete="current-password"
                    />
                    <InputError message={errors.current_password} className="mt-2" />
                </div>

                <div>
                    <InputLabel htmlFor="password" value="New Password" />
                    <TextInput
                        id="password"
                        ref={passwordInput}
                        value={data.password}
                        onChange={(e) => setData('password', e.target.value)}
                        type="password"
                        className="mt-1 block w-full"
                        autoComplete="new-password"
                    />
                    <InputError message={errors.password} className="mt-2" />
                </div>

                <div>
                    <InputLabel htmlFor="password_confirmation" value="Confirm Password" />
                    <TextInput
                        id="password_confirmation"
                        value={data.password_confirmation}
                        onChange={(e) => setData('password_confirmation', e.target.value)}
                        type="password"
                        className="mt-1 block w-full"
                        autoComplete="new-password"
                    />
                    <InputError message={errors.password_confirmation} className="mt-2" />
                </div>

                <div className="flex items-center gap-4">
                    <PrimaryButton disabled={processing}>
                        {processing ? 'Saving...' : 'Save'}
                    </PrimaryButton>
                </div>
            </form>
        </section>
    );
}

export default function Edit({ user }) {
    return (
        <TenantLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Profile
                </h2>
            }
        >
            <Head title="Profile" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                    <div className="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                        <UpdateProfileInformation user={user} />
                    </div>

                    <div className="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                        <UpdatePassword />
                    </div>
                </div>
            </div>
        </TenantLayout>
    );
}
