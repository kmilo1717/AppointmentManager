<template>
    <AuthenticatedLayout>
        <Head title="Dashboard" />

        <!-- Statistics Cards -->
        <div class="grid mb-4">
            <div class="col-12 md:col-3">
                <Card>
                    <template #content>
                        <div class="flex align-items-center">
                            <div class="flex-1">
                                <div class="text-2xl font-bold text-primary">{{ stats.total }}</div>
                                <div class="text-color-secondary">Total Citas</div>
                            </div>
                            <i class="pi pi-calendar text-4xl text-primary"></i>
                        </div>
                    </template>
                </Card>
            </div>
            
            <div class="col-12 md:col-3">
                <Card>
                    <template #content>
                        <div class="flex align-items-center">
                            <div class="flex-1">
                                <div class="text-2xl font-bold text-orange-500">{{ stats.pendientes }}</div>
                                <div class="text-color-secondary">Pendientes</div>
                            </div>
                            <i class="pi pi-clock text-4xl text-orange-500"></i>
                        </div>
                    </template>
                </Card>
            </div>
            
            <div class="col-12 md:col-3">
                <Card>
                    <template #content>
                        <div class="flex align-items-center">
                            <div class="flex-1">
                                <div class="text-2xl font-bold text-green-500">{{ stats.confirmadas }}</div>
                                <div class="text-color-secondary">Confirmadas</div>
                            </div>
                            <i class="pi pi-check text-4xl text-green-500"></i>
                        </div>
                    </template>
                </Card>
            </div>
            
            <div class="col-12 md:col-3">
                <Card>
                    <template #content>
                        <div class="flex align-items-center">
                            <div class="flex-1">
                                <div class="text-2xl font-bold text-red-500">{{ stats.canceladas }}</div>
                                <div class="text-color-secondary">Canceladas</div>
                            </div>
                            <i class="pi pi-times text-4xl text-red-500"></i>
                        </div>
                    </template>
                </Card>
            </div>
        </div>

        <!-- Filters -->
        <Card class="mb-4">
            <template #content>
                <form @submit.prevent="applyFilters" class="flex flex-wrap gap-3 align-items-end">
                    <div class="field">
                        <label for="estado" class="block text-sm font-medium mb-1">Estado</label>
                        <Dropdown
                            id="estado"
                            v-model="filterForm.estado"
                            :options="estadoOptions"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Todos los estados"
                            class="w-full md:w-14rem"
                        />
                    </div>
                    
                    <div class="field">
                        <label for="fecha" class="block text-sm font-medium mb-1">Fecha</label>
                        <Calendar
                            id="fecha"
                            v-model="filterForm.fecha"
                            placeholder="Seleccionar fecha"
                            class="w-full md:w-14rem"
                            showIcon
                            dateFormat="dd/mm/yy"
                        />
                    </div>

                    <div v-if="$page.props.auth.user.isAdmin" class="field">
                        <label for="usuario_id" class="block text-sm font-medium mb-1">Usuario</label>
                        <Dropdown
                            id="usuario_id"
                            v-model="filterForm.usuario_id"
                            :options="usuarios"
                            optionLabel="name"
                            optionValue="id"
                            placeholder="Todos los usuarios"
                            class="w-full md:w-14rem"
                            filter
                        />
                    </div>

                    <div class="field">
                        <Button
                            type="submit"
                            label="Filtrar"
                            icon="pi pi-search"
                            class="mr-2"
                        />
                        <Button
                            type="button"
                            label="Limpiar"
                            icon="pi pi-filter-slash"
                            severity="secondary"
                            @click="clearFilters"
                        />
                    </div>
                </form>
            </template>
        </Card>

        <!-- Citas Table -->
        <Card>
            <template #content>
                <DataTable
                    :value="citas.data"
                    paginator
                    :rows="citas.per_page"
                    :totalRecords="citas.total"
                    :lazy="true"
                    responsiveLayout="scroll"
                    emptyMessage="No hay citas registradas"
                >
                    <Column v-if="$page.props.auth.user.isAdmin" field="usuario.name" header="Usuario" sortable>
                        <template #body="{ data }">
                            <div class="flex align-items-center gap-2">
                                <Avatar :label="data.usuario.name.charAt(0)" size="small" />
                                <div>
                                    <div class="font-medium">{{ data.usuario.name }}</div>
                                    <div class="text-sm text-color-secondary">{{ data.usuario.email }}</div>
                                </div>
                            </div>
                        </template>
                    </Column>

                    <Column field="fecha" header="Fecha" sortable>
                        <template #body="{ data }">
                            <div class="flex align-items-center gap-2">
                                <i class="pi pi-calendar text-primary"></i>
                                <span>{{ formatDate(data.fecha) }}</span>
                            </div>
                        </template>
                    </Column>

                    <Column field="hora" header="Hora" sortable>
                        <template #body="{ data }">
                            <div class="flex align-items-center gap-2">
                                <i class="pi pi-clock text-primary"></i>
                                <span>{{ data.hora }}</span>
                            </div>
                        </template>
                    </Column>

                    <Column field="tipo_servicio" header="Servicio">
                        <template #body="{ data }">
                            <Tag v-if="data.tipo_servicio" :value="data.tipo_servicio" />
                            <span v-else class="text-color-secondary">No especificado</span>
                        </template>
                    </Column>

                    <Column field="descripcion" header="Descripción">
                        <template #body="{ data }">
                            <span class="text-color-secondary">
                                {{ data.descripcion.length > 50 ? data.descripcion.substring(0, 50) + '...' : data.descripcion }}
                            </span>
                        </template>
                    </Column>

                    <Column field="estado" header="Estado" sortable>
                        <template #body="{ data }">
                            <Tag
                                :value="getEstadoLabel(data.estado)"
                                :severity="getEstadoSeverity(data.estado)"
                            />
                        </template>
                    </Column>

                    <Column header="Acciones" style="width: 150px">
                        <template #body="{ data }">
                            <div class="flex gap-1">
                                <Button
                                    icon="pi pi-eye"
                                    severity="info"
                                    size="small"
                                    v-tooltip="'Ver'"
                                    @click="$inertia.visit(route('citas.show', data.id))"
                                />
                                <Button
                                    v-if="canEdit(data)"
                                    icon="pi pi-pencil"
                                    severity="warning"
                                    size="small"
                                    v-tooltip="'Editar'"
                                    @click="$inertia.visit(route('citas.edit', data.id))"
                                />
                                <Button
                                    v-if="canCancel(data)"
                                    icon="pi pi-times"
                                    severity="danger"
                                    size="small"
                                    v-tooltip="'Cancelar'"
                                    @click="cancelCita(data)"
                                />
                                <Button
                                    v-if="$page.props.auth.user.isAdmin"
                                    icon="pi pi-trash"
                                    severity="danger"
                                    size="small"
                                    v-tooltip="'Eliminar'"
                                    @click="deleteCita(data)"
                                />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </template>
        </Card>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { Head, useForm, router, usePage } from '@inertiajs/vue3'
import { useConfirm } from 'primevue/useconfirm'
import { useToast } from 'primevue/usetoast'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// PrimeVue Components
import Card from 'primevue/card'
import Button from 'primevue/button'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Tag from 'primevue/tag'
import Dropdown from 'primevue/dropdown'
import Calendar from 'primevue/calendar'
import Avatar from 'primevue/avatar'

const props = defineProps({
    citas: Object,
    stats: Object,
    filters: Object,
    usuarios: Array,
})

const confirm = useConfirm()
const toast = useToast()

const filterForm = reactive({
    estado: props.filters.estado || null,
    fecha: props.filters.fecha ? new Date(props.filters.fecha) : null,
    usuario_id: props.filters.usuario_id || null,
})

const estadoOptions = [
    { label: 'Pendiente', value: 'pendiente' },
    { label: 'Confirmada', value: 'confirmada' },
    { label: 'Cancelada', value: 'cancelada' }
]

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
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

const canEdit = (cita) => {
    const user = usePage().props.auth.user
    return user.isAdmin || (user.id === cita.usuario_id && cita.estado === 'pendiente')
}

const canCancel = (cita) => {
    const user = usePage().props.auth.user
    return (user.isAdmin || user.id === cita.usuario_id) && cita.estado !== 'cancelada'
}

const applyFilters = () => {
    const params = {}
    
    if (filterForm.estado) params.estado = filterForm.estado
    if (filterForm.fecha) params.fecha = filterForm.fecha.toISOString().split('T')[0]
    if (filterForm.usuario_id) params.usuario_id = filterForm.usuario_id
    
    router.get(route('dashboard'), params, {
        preserveState: true,
        preserveScroll: true,
    })
}

const clearFilters = () => {
    filterForm.estado = null
    filterForm.fecha = null
    filterForm.usuario_id = null
    
    router.get(route('dashboard'), {}, {
        preserveState: true,
        preserveScroll: true,
    })
}

const cancelCita = (cita) => {
    confirm.require({
        message: '¿Estás seguro de que deseas cancelar esta cita?',
        header: 'Confirmar Cancelación',
        icon: 'pi pi-exclamation-triangle',
        accept: () => {
            router.patch(route('citas.cancel', cita.id), {}, {
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

const deleteCita = (cita) => {
    confirm.require({
        message: '¿Estás seguro de que deseas eliminar esta cita? Esta acción no se puede deshacer.',
        header: 'Confirmar Eliminación',
        icon: 'pi pi-exclamation-triangle',
        accept: () => {
            router.delete(route('citas.destroy', cita.id), {
                onSuccess: () => {
                    toast.add({
                        severity: 'success',
                        summary: 'Cita Eliminada',
                        detail: 'La cita ha sido eliminada exitosamente',
                        life: 3000
                    })
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
