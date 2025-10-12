<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import SiteLayout from '@/Layouts/SiteLayout.vue';

const props = defineProps({
    cart: { type: Object, required: true },
    total: { type: Number, required: true },
});

const address = ref('');

// util tampilan
function money(n) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
    }).format(Number(n || 0));
}

function submit() {
    if (!address.value || address.value.trim().length < 5) {
        return window.Swal?.fire({
            icon: 'warning',
            title: 'Alamat belum lengkap',
            text: 'Minimal 5 karakter.',
        });
    }
    router.post(
        route('checkout.store'),
        { address_text: address.value },
        {
            onSuccess: () => {
                window.Swal?.fire({
                    icon: 'success',
                    title: 'Pesanan dibuat!',
                    timer: 1400,
                    showConfirmButton: false,
                });
                router.visit(route('orders.index'));
            },
            onError: (errs) => {
                window.Swal?.fire({
                    icon: 'error',
                    title: 'Checkout gagal',
                    text: Object.values(errs)[0] || 'Coba lagi.',
                });
            },
        },
    );
}
</script>

<template>
    <SiteLayout>
        <Head title="Checkout" />
        <section class="mx-auto max-w-3xl px-4 py-8">
            <h1 class="mb-4 text-2xl font-semibold">Checkout</h1>

            <div class="rounded-xl border bg-white p-6">
                <h2 class="mb-3 font-medium">Ringkasan Keranjang</h2>

                <div class="divide-y">
                    <div
                        v-for="it in props.cart.items"
                        :key="it.id"
                        class="flex items-center justify-between py-3"
                    >
                        <div>
                            <p class="font-medium">{{ it.product.name }}</p>
                            <p class="text-sm text-gray-600">x{{ it.qty }}</p>
                        </div>
                        <p>{{ money(it.qty * it.product.price) }}</p>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <span class="font-semibold">Total</span>
                    <span class="text-lg font-bold">{{
                        money(props.total)
                    }}</span>
                </div>

                <div class="mt-6">
                    <label class="mb-1 block text-sm font-medium"
                        >Alamat Pengiriman</label
                    >
                    <textarea
                        v-model="address"
                        rows="4"
                        placeholder="Tuliskan alamat lengkap Anda…"
                        class="w-full rounded-md border px-3 py-2"
                    ></textarea>
                </div>

                <button class="btn-primary mt-6 w-full" @click="submit">
                    Buat Pesanan
                </button>

                <p class="mt-2 text-center text-xs text-gray-500">
                    Dengan menekan “Buat Pesanan”, Anda menyetujui syarat &
                    ketentuan yang berlaku.
                </p>
            </div>
        </section>
    </SiteLayout>
</template>
