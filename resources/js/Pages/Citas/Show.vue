<template>
    <AuthenticatedLayout>

        <Head title="Detalles de la Cita" />

        <template #header>
            <div class="flex justify-content-between align-items-center">
                <h1 class="text-3xl font-bold text-900 m-0">Detalles de la Cita</h1>
                <div class="flex gap-2">
                    <Button v-if="canEdit" label="Editar" icon="pi pi-pencil"
                        @click="$inertia.visit(route('citas.edit', cita.id))" />
                    <Button label="Volver" icon="pi pi-arrow-left" severity="secondary"
                        @click="$inertia.visit(route('dashboard'))" />
                </div>
            </div>
        </template>

        <div class="grid">
            <!-- Información Principal -->
            <div class="col-12 lg:col-8">
                <Card class="mb-4">
                    <template #header>
                        <div class="p-3 bg-[var(--p-primary-color)] rounded-t-lg">
                            <h2 class="text-2xl font-bold text-white m-0">
                                <i class="pi pi-calendar mr-2"></i>
                                Información de la Cita
                            </h2>
                        </div>
                    </template>

                    <template #content>
                        <div class="grid p-3">
                            <div class="col-12 md:col-6">
                                <div class="field mb-4">
                                    <label class="font-medium text-color-secondary mb-2 block">Fecha:</label>
                                    <div class="flex align-items-center gap-2">
                                        <i class="pi pi-calendar text-primary"></i>
                                        <span class="text-xl font-medium">{{ formatDate(cita.fecha) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 md:col-6">
                                <div class="field mb-2">
                                    <label class="font-medium text-color-secondary mb-2 block">Hora:</label>
                                    <div class="flex align-items-center gap-2">
                                        <i class="pi pi-clock text-primary"></i>
                                        <span class="text-xl font-medium">{{ cita.hora }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 md:col-6">
                                <div class="field mb-2">
                                    <label class="font-medium text-color-secondary mb-2 block">Estado:</label>
                                    <Tag :value="getEstadoLabel(cita.estado)" :severity="getEstadoSeverity(cita.estado)"
                                        class="text-lg" />
                                </div>
                            </div>

                            <div class="col-12 md:col-6">
                                <div class="field mb-2">
                                    <label class="font-medium text-color-secondary mb-2 block">Tipo de Servicio:</label>
                                    <div class="flex align-items-center gap-2">
                                        <i class="pi pi-wrench text-primary"></i>
                                        <span class="text-lg">{{ cita.tipo_servicio || 'No especificado' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="field mb-1">
                                    <label class="font-medium text-color-secondary mb-2 block">Descripción:</label>
                                    <div class="p-3 bg-gray-50 border-round">
                                        <p class="m-0 text-color line-height-3">
                                            {{ cita.descripcion || 'Sin descripción' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </Card>
            </div>

            <!-- Información del Usuario y Acciones -->
            <div class="col-12 lg:col-4">
                <!-- Información del Usuario (Solo para Admin) -->
                <Card v-if="$page.props.auth.user.isAdmin && cita.usuario" class="mb-4">
                    <template #header>
                        <div class="p-4 bg-green-500">
                            <h2 class="text-xl font-bold text-white m-0">
                                <i class="pi pi-user mr-2"></i>
                                Cliente
                            </h2>
                        </div>
                    </template>

                    <template #content>
                        <div class="p-4 text-center">
                            <Avatar :label="cita.usuario.name.charAt(0)" size="xlarge" class="mb-3" shape="circle" />
                            <h3 class="text-xl font-bold mb-2">{{ cita.usuario.name }}</h3>
                            <p class="text-color-secondary mb-2">{{ cita.usuario.email }}</p>
                            <p v-if="cita.usuario.telefono" class="text-color-secondary">
                                <i class="pi pi-phone mr-1"></i>
                                {{ cita.usuario.telefono }}
                            </p>
                        </div>
                    </template>
                </Card>

                <!-- Acciones Rápidas -->
                <Card v-if="canEdit || canCancel || $page.props.auth.user.isAdmin" class="mb-4">
                    <template #header>
                        <div class="p-3 bg-[var(--p-gray-300)] rounded-t-lg">
                            <h2 class="text-xl font-bold text-white m-0">
                                <i class="pi pi-cog mr-2"></i>
                                Acciones
                            </h2>
                        </div>
                    </template>

                    <template #content>
                        <div class="p-3 flex flex-column gap-3">
                            <Button v-if="canEdit" label="Editar Cita" icon="pi pi-pencil" class="w-full"
                                @click="$inertia.visit(route('citas.edit', cita.id))" />

                            <Button v-if="canCancel" label="Cancelar Cita" icon="pi pi-times" severity="warning"
                                class="w-full" @click="cancelCita" />

                            <Button v-if="$page.props.auth.user.isAdmin && cita.estado === 'pendiente'"
                                label="Confirmar Cita" icon="pi pi-check" severity="success" class="w-full"
                                @click="confirmCita" />

                            <Button v-if="$page.props.auth.user.isAdmin" label="Eliminar Cita" icon="pi pi-trash"
                                severity="danger" class="w-full" @click="deleteCita" />
                        </div>
                    </template>
                </Card>
                <!-- Información del Vehículo -->
                <Card v-if="hasVehicleInfo" class="mb-4">
                    <template #header>
                        <div class="p-3 bg-[var(--p-blue-500)] rounded-t-lg">
                            <h2 class="text-2xl font-bold text-white m-0">
                                <i class="pi pi-car mr-2"></i>
                                Información del Vehículo
                            </h2>
                        </div>
                    </template>

                    <template #content>
                        <div class="grid pt-2 px-2">
                            <div class="col" v-if="cita.vehiculo_marca">
                                <label class="font-medium text-color-secondary block">Marca:</label>
                                <div class="flex align-items-center gap-2">
                                    <i class="pi pi-tag text-blue-500"></i>
                                    <span class="text-lg">{{ cita.vehiculo_marca }}</span>
                                </div>
                            </div>

                            <div class="col" v-if="cita.vehiculo_modelo">
                                <label class="font-medium text-color-secondary block">Modelo:</label>
                                <div class="flex align-items-center gap-2">
                                    <i class="pi pi-tag text-blue-500"></i>
                                    <span class="text-lg">{{ cita.vehiculo_modelo }}</span>
                                </div>
                            </div>

                        </div>
                        <div class="grid pt-2 px-2">
                            <div class="col" v-if="cita.vehiculo_placa">
                                <label class="font-medium text-color-secondary mb-2 block">Placa:</label>
                                <div class="flex align-items-center gap-2">
                                    <i class="pi pi-hashtag text-blue-500"></i>
                                    <span class="text-lg font-mono">{{ cita.vehiculo_placa }}</span>
                                </div>
                            </div>
                        </div>
                    </template>
                </Card>
                <!-- Información Adicional -->
                <Card>
                    <template #header>
                        <div class="p-3 bg-[var(--p-gray-800)] rounded-t-lg">
                            <h2 class="text-xl font-bold text-white m-0">
                                <i class="pi pi-info-circle mr-2"></i>
                                Información Adicional
                            </h2>
                        </div>
                    </template>

                    <template #content>
                        <div class="p-2">
                            <div class="field mb-3">
                                <label class="font-medium text-color-secondary mb-1 block">Creada:</label>
                                <span class="text-sm">{{ formatDateTime(cita.created_at) }}</span>
                            </div>

                            <div class="field mb-3">
                                <label class="font-medium text-color-secondary mb-1 block">Última actualización:</label>
                                <span class="text-sm">{{ formatDateTime(cita.updated_at) }}</span>
                            </div>

                        </div>
                    </template>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js';
import { useConfirm } from 'primevue/useconfirm'
import { useToast } from 'primevue/usetoast'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// PrimeVue Components
import Card from 'primevue/card'
import Button from 'primevue/button'
import Tag from 'primevue/tag'
import Avatar from 'primevue/avatar'

const props = defineProps({
    cita: Object,
})

const page = usePage()
const confirm = useConfirm()
const toast = useToast()

const canEdit = computed(() => {
    const user = page.props.auth.user
    return user.isAdmin || (user.id === props.cita.usuario_id && props.cita.estado === 'pendiente')
})

const canCancel = computed(() => {
    const user = page.props.auth.user
    return (user.isAdmin || user.id === props.cita.usuario_id) && props.cita.estado !== 'cancelada'
})

const hasVehicleInfo = computed(() => {
    return props.cita.vehiculo_marca || props.cita.vehiculo_modelo || props.cita.vehiculo_placa
})

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const formatDateTime = (dateString) => {
    return new Date(dateString).toLocaleString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const getEstadoLabel = (estado) => {
    const estadoMap = {
        pendiente: 'Pendiente',
        confirmada: 'Confirmada',
        cancelada: 'Cancelada'
    }
    return estadoMap[estado] || estado
}

const getEstadoSeverity = (estado) => {
    const severityMap = {
        pendiente: 'warning',
        confirmada: 'success',
        cancelada: 'danger'
    }
    return severityMap[estado] || 'info'
}

const cancelCita = () => {
    confirm.require({
        message: '¿Estás seguro de que deseas cancelar esta cita?',
        header: 'Confirmar Cancelación',
        icon: 'pi pi-exclamation-triangle',
        accept: () => {
            router.patch(route('citas.cancel', props.cita.id), {}, {
                onSuccess: () => {
                    toast.add({
                        severity: 'success',
                        summary: 'Cita Cancelada',
                        detail: 'La cita ha sido cancelada exitosamente',
                        life: 3000
                    })
                },
                onError: () => {
                    toast.add({
                        severity: 'error',
                        summary: 'Error',
                        detail: 'Error al cancelar la cita',
                        life: 3000
                    })
                }
            })
        }
    })
}

const confirmCita = () => {
    confirm.require({
        message: '¿Estás seguro de que deseas confirmar esta cita?',
        header: 'Confirmar Cita',
        icon: 'pi pi-question-circle',
        accept: () => {
            router.put(route('citas.update', props.cita.id), {
                estado: 'confirmada'
            }, {
                onSuccess: () => {
                    toast.add({
                        severity: 'success',
                        summary: 'Cita Confirmada',
                        detail: 'La cita ha sido confirmada exitosamente',
                        life: 3000
                    })
                },
                onError: () => {
                    toast.add({
                        severity: 'error',
                        summary: 'Error',
                        detail: 'Error al confirmar la cita',
                        life: 3000
                    })
                }
            })
        }
    })
}

const deleteCita = () => {
    confirm.require({
        message: '¿Estás seguro de que deseas eliminar esta cita? Esta acción no se puede deshacer.',
        header: 'Confirmar Eliminación',
        icon: 'pi pi-exclamation-triangle',
        accept: () => {
            router.delete(route('citas.destroy', props.cita.id), {
                onSuccess: () => {
                    toast.add({
                        severity: 'success',
                        summary: 'Cita Eliminada',
                        detail: 'La cita ha sido eliminada exitosamente',
                        life: 3000
                    })
                    // Redirect to dashboard after deletion
                    router.visit(route('dashboard'))
                },
                onError: () => {
                    toast.add({
                        severity: 'error',
                        summary: 'Error',
                        detail: 'Error al eliminar la cita',
                        life: 3000
                    })
                }
            })
        }
    })
}
</script>

<style scoped>
.field {
    margin-bottom: 1rem;
}

.field:last-child {
    margin-bottom: 0;
}
</style>
