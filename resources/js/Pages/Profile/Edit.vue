<script setup>
import SiteLayout from '@/Layouts/SiteLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});
</script>

<template>
    <SiteLayout>
        <Head title="Profile" />

        <section class="mx-auto max-w-7xl px-4 py-10 pt-[100px]">
            <h1 class="text-2xl font-semibold">My Profile</h1>
            <p class="mt-1 text-sm text-gray-500">
                Kelola informasi akun Anda.
            </p>

            <div class="mt-8 space-y-6">
                <!-- Update Profile -->
                <div class="rounded-lg border bg-white p-6 shadow-sm">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <!-- Update Password -->
                <div class="rounded-lg border bg-white p-6 shadow-sm">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <!-- Delete User -->
                <div class="rounded-lg border bg-white p-6 shadow-sm">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </section>
    </SiteLayout>
</template>
