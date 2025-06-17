<template>
    <AuthenticatedLayout>
        <Head title="Editar Cita" />

        <template #header>
            <div class="flex justify-content-between align-items-center">
                <h1 class="text-3xl font-bold text-900 m-0">Editar Cita</h1>
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
                    :cita="cita"
                    :usuarios="usuarios"
                    @submit="handleSubmit"
                />
            </template>
        </Card>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import CitaForm from '@/Components/CitaForm.vue'
import Card from 'primevue/card'
import Button from 'primevue/button'

const props = defineProps({
    cita: Object,
    usuarios: Array,
})

const handleSubmit = (data) => {
    const form = useForm(data)
    
    form.put(route('citas.update', props.cita.id), {
        onSuccess: () => {
            // Redirect handled by controller
        }
    })
}
</script>
