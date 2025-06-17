<template>
    <GuestLayout>
        <Head title="Registro" />

        <div class="w-full mx-auto mt-4" style="min-width: 400px !important;">
            <Card class="shadow-4">
                <template #header>
                    <div class="text-center bg-primary">
                        <h1 class="text-3xl font-bold text-white m-0">
                            <i class="pi pi-user-plus mr-2"></i> Registro
                        </h1>
                        <p class="text-primary-50">Crea tu nueva cuenta</p>
                    </div>
                </template>

                <template #content>
                    <form @submit.prevent="submit" class="p-fluid p-4">

                        <div class="field mb-3">
                            <label for="name" class="block text-900 font-medium mb-2">Usuario *</label>
                            <InputText
                                id="name"
                                v-model="form.name"
                                placeholder="Nombre de usuario"
                                class="w-full"
                                :class="{ 'p-invalid': form.errors.name }"
                                autocomplete="username"
                            />
                            <small v-if="form.errors.name" class="p-error">{{ form.errors.name }}</small>
                        </div>

                        <div class="field mb-3">
                            <label for="nombre" class="block text-900 font-medium mb-2">Nombre completo *</label>
                            <InputText
                                id="nombre"
                                v-model="form.nombre"
                                placeholder="Tu nombre completo"
                                class="w-full"
                                :class="{ 'p-invalid': form.errors.nombre }"
                            />
                            <small v-if="form.errors.nombre" class="p-error">{{ form.errors.nombre }}</small>
                        </div>

                        <div class="field mb-3">
                            <label for="email" class="block text-900 font-medium mb-2">Correo electrónico *</label>
                            <InputText
                                id="email"
                                v-model="form.email"
                                placeholder="Correo electrónico"    
                                type="email"
                                class="w-full"
                                :class="{ 'p-invalid': form.errors.email }"
                                autocomplete="email"
                            />
                            <small v-if="form.errors.email" class="p-error">{{ form.errors.email }}</small>
                        </div>

                        <div class="field mb-3">
                            <label for="telefono" class="block text-900 font-medium mb-2">Teléfono</label>
                            <InputText
                                id="telefono"
                                v-model="form.telefono"
                                placeholder="3001234567"
                                class="w-full"
                                :class="{ 'p-invalid': form.errors.telefono }"
                            />
                            <small v-if="form.errors.telefono" class="p-error">{{ form.errors.telefono }}</small>
                        </div>

                        <div class="field mb-3">
                            <label for="password" class="block text-900 font-medium mb-2">Contraseña *</label>
                            <Password
                                id="password"
                                v-model="form.password"
                                placeholder="Crea una contraseña segura"
                                class="w-full"
                                :class="{ 'p-invalid': form.errors.password }"
                                toggleMask
                                autocomplete="new-password"
                            />
                            <small v-if="form.errors.password" class="p-error">{{ form.errors.password }}</small>
                        </div>

                        <div class="field mb-3">
                            <label for="password_confirmation" class="block text-900 font-medium mb-2">Confirmar contraseña *</label>
                            <Password
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                placeholder="Repite tu contraseña"
                                class="w-full"
                                :class="{ 'p-invalid': form.errors.password_confirmation }"
                                toggleMask
                                :feedback="false"
                                autocomplete="new-password"
                            />
                            <small v-if="form.errors.password_confirmation" class="p-error">
                                {{ form.errors.password_confirmation }}
                            </small>
                        </div>

                        <Button
                            type="submit"
                            label="Registrarse"
                            icon="pi pi-user-plus"
                            class="w-full mb-3"
                            :loading="form.processing"
                            :disabled="form.processing"
                        />

                        <div class="text-center">
                            <Link
                                :href="route('login')"
                                class="text-primary hover:underline text-sm"
                            >
                                ¿Ya tienes cuenta? Inicia sesión aquí
                            </Link>
                        </div>
                    </form>
                </template>
            </Card>
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

const form = useForm({
    name: '',
    nombre: '',
    email: '',
    telefono: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>
