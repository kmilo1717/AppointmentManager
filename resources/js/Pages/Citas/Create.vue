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
import { useToast } from 'primevue/usetoast'

const toast = useToast()

defineProps({
    usuarios: Array,
})

const handleSubmit = (form) => {
    
    form.post(route('citas.store'), {
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Cita Creada',
                detail: 'La cita ha sido creada exitosamente',
                life: 3000
            })
        },
        onError: (errors) => {
            toast.add({
                severity: 'error',
                summary: 'Error al crear la cita',
                detail: 'Por favor, corrige los errores y vuelve a intentarlo.',
                life: 3000
            })
        }
    })
}
</script>
