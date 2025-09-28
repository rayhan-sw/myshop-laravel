<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    address: '',
    password: '',
    password_confirmation: '',
});

const normalizePhone = () => {
    // Ketik 08xxxx → otomatis jadi +628xxxx
    if (/^0[0-9]+$/.test(form.phone)) {
        form.phone = form.phone.replace(/^0/, '+62');
    }
};

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit" class="space-y-4">
            <!-- Name -->
            <div>
                <InputLabel for="name" value="Name" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model.trim="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <!-- Email -->
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model.trim="form.email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- Phone (+62...) -->
            <div>
                <InputLabel for="phone" value="Phone (+62…)" />
                <TextInput
                    id="phone"
                    type="tel"
                    class="mt-1 block w-full"
                    v-model.trim="form.phone"
                    inputmode="tel"
                    placeholder="+6281234567890"
                    pattern="^\+62[0-9]{9,13}$"
                    title="Nomor harus diawali +62 dan diikuti 9–13 digit angka"
                    maxlength="16"
                    autocomplete="tel"
                    @blur="normalizePhone"
                />
                <p class="mt-1 text-xs text-gray-500">
                    Format: <code>+62</code> diikuti 9–13 digit angka (contoh
                    <code>+6281234567890</code>).
                </p>
                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

            <!-- Address -->
            <div>
                <InputLabel for="address" value="Address" />
                <textarea
                    id="address"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    v-model.trim="form.address"
                    rows="3"
                    autocomplete="street-address"
                ></textarea>
                <InputError class="mt-2" :message="form.errors.address" />
            </div>

            <!-- Password -->
            <div>
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <!-- Confirm Password -->
            <div>
                <InputLabel
                    for="password_confirmation"
                    value="Confirm Password"
                />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <!-- Action -->
            <div class="flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900"
                >
                    Already registered?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
