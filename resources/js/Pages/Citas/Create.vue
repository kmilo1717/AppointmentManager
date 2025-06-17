<template>
    <AuthenticatedLayout>
        <Head title="Nueva Cita" />

        <template #header>
            <div class="flex justify-content-between align-items-center">
                <h1 class="text-3xl font-bold text-900 m-0">Nueva Cita</h1>
                <Button
                    label="Volver"
                    icon="pi pi-arrow-left"
                    severity="secondary"
                    @click="$inertia.visit(route('dashboard'))"
                />
            </div>
        </template>

        <Card>
            <template #content>
                <CitaForm 
                    :usuarios="usuarios"
                    @submit="handleSubmit"
                />
            </template>
        </Card>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import CitaForm from '@/Components/CitaForm.vue'
import Card from 'primevue/card'
import Button from 'primevue/button'
import { route } from 'ziggy-js';

defineProps({
    usuarios: Array,
})

const handleSubmit = (data) => {
    const form = useForm(data)
    
    form.post(route('citas.store'), {
        onSuccess: () => {
            // Redirect handled by controller
        }
    })
}
</script>
