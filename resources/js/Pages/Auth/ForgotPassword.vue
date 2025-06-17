<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, useForm, Link } from '@inertiajs/vue3'

// PrimeVue components
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import Message from 'primevue/message'

defineProps({
    status: String,
})

const form = useForm({
    email: '',
})

const submit = () => {
    form.post(route('password.email'))
}
</script>

<template>
    <GuestLayout>
        <div class="bg-gray-400">

            <Head title="Restablecer Contraseña" />

            <div class="w-full max-w-30rem mx-auto mt-6 p-4 shadow-2 border-round surface-card">
                <h2 class="text-center text-2xl font-bold text-primary mb-3">
                    ¿Olvidaste tu contraseña?
                </h2>

                <p class="text-center text-600 mb-4">
                    Ingresa tu correo y te enviaremos un enlace para restablecerla.
                </p>

                <Message v-if="status" severity="success" class="mb-3">
                    {{ status }}
                </Message>

                <form @submit.prevent="submit" class="p-fluid">
                    <div class="field mb-4">
                        <label for="email" class="block text-900 font-medium mb-2">Correo electrónico</label>
                        <InputText id="email" v-model="form.email" type="email" class="w-full"
                            :class="{ 'p-invalid': form.errors.email }" required autocomplete="username" />
                        <small v-if="form.errors.email" class="p-error">{{ form.errors.email }}</small>
                    </div>

                    <Button type="submit" label="Enviar enlace" icon="pi pi-envelope" class="mb-3 w-full"
                        :loading="form.processing" :disabled="form.processing" />

                    <div class="text-center">
                        <Link :href="route('login')" class="text-primary hover:underline text-sm">
                        ← Volver al inicio de sesión
                        </Link>
                    </div>
                </form>
            </div>
        </div>

    </GuestLayout>
</template>
