<template>
    <GuestLayout>

        <Head title="Iniciar Sesión" />

        <div
            class="min-h-screen flex items-center justify-center py-10 px-4 bg-gradient-to-br from-primary-50 to-blue-100">
            <!-- Animación de entrada -->
            <div class="w-full max-w-md animate-fade-in">
                <Card class="shadow-2xl rounded-2xl overflow-hidden border-0 backdrop-blur-sm bg-white/90">
                    <template #header>
                        <div class="text-center bg-[var(--p-primary-color)] p-4 rounded-t-lg">
                            <h1 class="text-2xl font-bold text-white">
                                <i class="pi pi-sign-in mr-2"></i>
                                Sistema de Citas
                            </h1>
                            <p class="text-sm text-white">Accede a tu cuenta para continuar</p>
                        </div>
                    </template>

                    <template #content>
                        <div class="p-4 space-y-3">
                            <!-- Mensaje de estado -->
                            <Message v-if="status" severity="success" :closable="false" class="mb-4">
                                {{ status }}
                            </Message>

                            <form @submit.prevent="submit" class="space-y-6">
                                <!-- Campo Email -->
                                <div class="space-y-2">
                                    <label for="email" class="block text-gray-800 font-semibold text-sm">
                                        <i class="pi pi-envelope mr-2 text-primary-600"></i>
                                        Correo Electrónico
                                        <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <InputText id="email" v-model="form.email" type="email"
                                        placeholder="Ingresa tu correo electrónico" class="w-full h-12" :class="{
                                            'p-invalid border-red-300': form.errors.email,
                                            'border-green-300': form.email && !form.errors.email
                                        }" required autofocus autocomplete="username" />
                                    <Transition name="fade">
                                        <small v-if="form.errors.email" class="p-error flex items-center">
                                            <i class="pi pi-exclamation-triangle mr-1"></i>
                                            {{ form.errors.email }}
                                        </small>
                                    </Transition>
                                </div>

                                <!-- Campo Contraseña -->
                                <div class="space-y-2">
                                    <label for="password" class="block text-gray-800 font-semibold text-sm">
                                        <i class="pi pi-lock mr-2 text-primary-600"></i>
                                        Contraseña
                                        <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <Password id="password" v-model="form.password" placeholder="Ingresa tu contraseña"
                                        :inputClass="['h-12 w-full']" :class="{'p-invalid': form.errors.password }" class="w-full"
                                        :feedback="false" toggleMask required autocomplete="current-password" />
                                    <Transition name="fade">
                                        <small v-if="form.errors.password" class="p-error flex items-center">
                                            <i class="pi pi-exclamation-triangle mr-1"></i>
                                            {{ form.errors.password }}
                                        </small>
                                    </Transition>
                                </div>

                                <!-- Recordarme -->
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center">
                                        <Checkbox id="remember" v-model="form.remember" :binary="true" class="mr-2" />
                                        <label for="remember" class="text-sm text-gray-700 cursor-pointer">
                                            Mantener sesión iniciada
                                        </label>
                                    </div>

                                    <Link v-if="canResetPassword" :href="route('password.request')"
                                        class="text-sm text-primary-600 hover:text-primary-700 hover:underline transition-colors">
                                    ¿Olvidaste tu contraseña?
                                    </Link>
                                </div>

                                <!-- Botón de login -->
                                <Button type="submit" label="Iniciar Sesión" icon="pi pi-sign-in"
                                    class="w-full h-12 text-base font-semibold" :loading="form.processing"
                                    :disabled="form.processing" loadingIcon="pi pi-spinner pi-spin" />
                            </form>

                            <!-- Divisor -->
                            <div class="relative my-3">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-gray-200"></div>
                                </div>
                                <div class="relative flex justify-center text-sm">
                                    <span class="px-2 bg-white text-gray-500">o</span>
                                </div>
                            </div>

                            <!-- Link de registro -->
                            <div class="text-center">
                                <p class="text-sm text-gray-600 mb-3">
                                    ¿Primera vez aquí?
                                </p>
                                <Link :href="route('register')"
                                    class="inline-flex items-center justify-center w-full h-10 px-4 text-sm font-medium text-primary-600 bg-primary-50 border border-primary-200 rounded-lg hover:bg-primary-100 hover:text-primary-700 transition-colors">
                                <i class="pi pi-user-plus mr-2"></i>
                                Crear cuenta nueva
                                </Link>
                            </div>
                        </div>
                    </template>
                </Card>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

// PrimeVue Components
import Card from 'primevue/card';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Message from 'primevue/message';

defineProps({
    canResetPassword: {
        type: Boolean,
        default: true
    },
    status: {
        type: String,
        default: null
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<style scoped>
/* Animaciones personalizadas */
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes spin-slow {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}

.animate-fade-in {
    animation: fade-in 0.6s ease-out;
}

.animate-spin-slow {
    animation: spin-slow 8s linear infinite;
}

/* Transiciones */
.fade-enter-active,
.fade-leave-active {
    transition: all 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}

/* Estilos mejorados para inputs */
:deep(.p-inputtext) {
    transition: all 0.3s ease;
}

:deep(.p-inputtext:focus) {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

:deep(.p-button) {
    transition: all 0.3s ease;
}

:deep(.p-button:hover:not(:disabled)) {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
}

/* Mejoras para el header del card */
:deep(.p-card-header) {
    padding: 0;
}

:deep(.p-card-content) {
    padding: 0;
}
</style>