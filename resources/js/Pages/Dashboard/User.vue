<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-content-between align-items-center">
                <h1 class="text-3xl font-bold text-900 m-0">Mis Citas</h1>
                <Button
                    label="Nueva Cita"
                    icon="pi pi-plus"
                    @click="$inertia.visit('/citas/create')"
                />
            </div>
        </template>

        <!-- Statistics Cards -->
        <div class="grid mb-4">
            <div class="col-12 md:col-4">
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
            
            <div class="col-12 md:col-4">
                <Card>
                    <template #content>
                        <div class="flex align-items-center">
                            <div class="flex-1">
                                <div class="text-2xl font-bold text-blue-500">{{ stats.pendientes }}</div>
                                <div class="text-color-secondary">Pendientes</div>
                            </div>
                            <i class="pi pi-clock text-4xl text-blue-500"></i>
                        </div>
                    </template>
                </Card>
            </div>
            
            <div class="col-12 md:col-4">
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
        </div>

        <!-- Filters -->
        <Card class="mb-4">
            <template #content>
                <div class="flex flex-wrap gap-3 align-items-center">
                    <div class="field">
                        <label for="status-filter" class="block text-sm font-medium mb-1">Estado</label>
                        <Dropdown
                            id="status-filter"
                            v-model="filters.estado"
                            :options="estadoOptions"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Todos los estados"
                            class="w-full md:w-14rem"
                            @change="loadCitas"
                        />
                    </div>
                    
                    <div class="field">
                        <label for="date-filter" class="block text-sm font-medium mb-1">Fecha</label>
                        <Calendar
                            id="date-filter"
                            v-model="filters.fecha"
                            placeholder="Seleccionar fecha"
                            class="w-full md:w-14rem"
                            @date-select="loadCitas"
                            showIcon
                            dateFormat="dd/mm/yy"
                        />
                    </div>

                    <div class="field">
                        <Button
                            label="Limpiar Filtros"
                            icon="pi pi-filter-slash"
                            severity="secondary"
                            @click="clearFilters"
                        />
                    </div>
                </div>
            </template>
        </Card>

        <!-- Citas Table -->
        <Card>
            <template #content>
                <DataTable
                    :value="citas"
                    :loading="citasStore.loading"
                    paginator
                    :rows="10"
                    :rowsPerPageOptions="[5, 10, 20]"
                    responsiveLayout="scroll"
                    emptyMessage="No tienes citas programadas"
                >
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
                                {{ data.descripcion || 'Sin descripción' }}
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

                    <Column header="Acciones" style="width: 120px">
                        <template #body="{ data }">
                            <div class="flex gap-2">
                                <Button
                                    v-if="data.estado === 'pendiente'"
                                    icon="pi pi-pencil"
                                    severity="info"
                                    size="small"
                                    v-tooltip="'Editar'"
                                    @click="editCita(data)"
                                />
                                <Button
                                    v-if="data.estado !== 'cancelada'"
                                    icon="pi pi-times"
                                    severity="danger"
                                    size="small"
                                    v-tooltip="'Cancelar'"
                                    @click="cancelCita(data)"
                                />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </template>
        </Card>

        <!-- Edit Dialog -->
        <Dialog
            v-model:visible="showEditDialog"
            modal
            header="Editar Cita"
            class="w-full max-w-2xl"
        >
            <CitaForm
                v-if="selectedCita"
                :cita="selectedCita"
                @save="handleSave"
                @cancel="showEditDialog = false"
            />
        </Dialog>
    </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useConfirm } from 'primevue/useconfirm'
import { useToast } from 'primevue/usetoast'
import { useCitasStore } from '@/stores/citas'
import AppLayout from '@/Layouts/AppLayout.vue'
import CitaForm from '@/Components/CitaForm.vue'

// PrimeVue Components
import Card from 'primevue/card'
import Button from 'primevue/button'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Tag from 'primevue/tag'
import Dropdown from 'primevue/dropdown'
import Calendar from 'primevue/calendar'
import Dialog from 'primevue/dialog'

const confirm = useConfirm()
const toast = useToast()
const citasStore = useCitasStore()

const citas = ref([])
const showEditDialog = ref(false)
const selectedCita = ref(null)

const filters = reactive({
    estado: null,
    fecha: null
})

const estadoOptions = [
    { label: 'Pendiente', value: 'pendiente' },
    { label: 'Confirmada', value: 'confirmada' },
    { label: 'Cancelada', value: 'cancelada' }
]

const stats = computed(() => {
    const total = citas.value.length
    const pendientes = citas.value.filter(c => c.estado === 'pendiente').length
    const confirmadas = citas.value.filter(c => c.estado === 'confirmada').length
    const canceladas = citas.value.filter(c => c.estado === 'cancelada').length
    
    return { total, pendientes, confirmadas, canceladas }
})

const loadCitas = async () => {
    const filterParams = {}
    if (filters.estado) filterParams.estado = filters.estado
    if (filters.fecha) filterParams.fecha = filters.fecha.toISOString().split('T')[0]

    const result = await citasStore.fetchCitas(filterParams)
    if (result.success) {
        citas.value = result.data.data || result.data
    } else {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: result.message,
            life: 3000
        })
    }
}

const clearFilters = () => {
    filters.estado = null
    filters.fecha = null
    loadCitas()
}

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

const editCita = (cita) => {
    selectedCita.value = { ...cita }
    showEditDialog.value = true
}

const cancelCita = (cita) => {
    confirm.require({
        message: '¿Estás seguro de que deseas cancelar esta cita?',
        header: 'Confirmar Cancelación',
        icon: 'pi pi-exclamation-triangle',
        accept: async () => {
            const result = await citasStore.cancelCita(cita.id)
            if (result.success) {
                toast.add({
                    severity: 'success',
                    summary: 'Cita Cancelada',
                    detail: 'La cita ha sido cancelada exitosamente',
                    life: 3000
                })
                loadCitas()
            } else {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: result.message,
                    life: 3000
                })
            }
        }
    })
}

const handleSave = () => {
    showEditDialog.value = false
    loadCitas()
}

onMounted(() => {
    loadCitas()
    useConfirm()
    useToast()
})
</script>
