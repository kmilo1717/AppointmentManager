<template>
  <div>
    <div class="flex justify-content-between align-items-center mb-4">
      <h1 class="text-3xl font-bold text-900">Panel de Administración</h1>
      <div class="flex gap-2">
        <Button
          label="Nueva Cita"
          icon="pi pi-plus"
          @click="createAppointment"
        />
        <Button
          label="Exportar"
          icon="pi pi-download"
          severity="secondary"
          @click="exportData"
        />
      </div>
    </div>

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
                <div class="text-2xl font-bold text-blue-500">{{ stats.scheduled }}</div>
                <div class="text-color-secondary">Programadas</div>
              </div>
              <i class="pi pi-clock text-4xl text-blue-500"></i>
            </div>
          </template>
        </Card>
      </div>
      
      <div class="col-12 md:col-3">
        <Card>
          <template #content>
            <div class="flex align-items-center">
              <div class="flex-1">
                <div class="text-2xl font-bold text-green-500">{{ stats.completed }}</div>
                <div class="text-color-secondary">Completadas</div>
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
                <div class="text-2xl font-bold text-red-500">{{ stats.cancelled }}</div>
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
        <div class="flex flex-wrap gap-3 align-items-center">
          <div class="field">
            <label for="user-filter" class="block text-sm font-medium mb-1">Usuario</label>
            <InputText
              id="user-filter"
              v-model="filters.user"
              placeholder="Buscar por usuario"
              class="w-full md:w-14rem"
              @input="debounceSearch"
            />
          </div>
          
          <div class="field">
            <label for="status-filter" class="block text-sm font-medium mb-1">Estado</label>
            <Dropdown
              id="status-filter"
              v-model="filters.status"
              :options="statusOptions"
              optionLabel="label"
              optionValue="value"
              placeholder="Todos los estados"
              class="w-full md:w-14rem"
              @change="loadAppointments"
            />
          </div>
          
          <div class="field">
            <label for="date-range" class="block text-sm font-medium mb-1">Rango de Fechas</label>
            <Calendar
              id="date-range"
              v-model="filters.dateRange"
              selectionMode="range"
              placeholder="Seleccionar rango"
              class="w-full md:w-14rem"
              @date-select="loadAppointments"
              showIcon
            />
          </div>

          <div class="field">
            <Button
              label="Limpiar"
              icon="pi pi-filter-slash"
              severity="secondary"
              @click="clearFilters"
            />
          </div>
        </div>
      </template>
    </Card>

    <!-- Appointments Table -->
    <Card>
      <template #content>
        <DataTable
          :value="appointments"
          :loading="loading"
          paginator
          :rows="15"
          :rowsPerPageOptions="[10, 15, 25, 50]"
          responsiveLayout="scroll"
          emptyMessage="No hay citas registradas"
          sortMode="multiple"
        >
          <Column field="user.name" header="Usuario" sortable>
            <template #body="{ data }">
              <div class="flex align-items-center gap-2">
                <Avatar :label="data.user.name.charAt(0)" size="small" />
                <div>
                  <div class="font-medium">{{ data.user.name }}</div>
                  <div class="text-sm text-color-secondary">{{ data.user.email }}</div>
                </div>
              </div>
            </template>
          </Column>

          <Column field="date" header="Fecha" sortable>
            <template #body="{ data }">
              <div class="flex align-items-center gap-2">
                <i class="pi pi-calendar text-primary"></i>
                <span>{{ formatDate(data.date) }}</span>
              </div>
            </template>
          </Column>

          <Column field="time" header="Hora" sortable>
            <template #body="{ data }">
              <div class="flex align-items-center gap-2">
                <i class="pi pi-clock text-primary"></i>
                <span>{{ data.time }}</span>
              </div>
            </template>
          </Column>

          <Column field="description" header="Descripción">
            <template #body="{ data }">
              <span class="text-color-secondary">
                {{ data.description || 'Sin descripción' }}
              </span>
            </template>
          </Column>

          <Column field="status" header="Estado" sortable>
            <template #body="{ data }">
              <Tag
                :value="getStatusLabel(data.status)"
                :severity="getStatusSeverity(data.status)"
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
                  v-tooltip="'Ver Detalles'"
                  @click="viewAppointment(data)"
                />
                <Button
                  icon="pi pi-pencil"
                  severity="warning"
                  size="small"
                  v-tooltip="'Editar'"
                  @click="editAppointment(data)"
                />
                <Button
                  icon="pi pi-trash"
                  severity="danger"
                  size="small"
                  v-tooltip="'Eliminar'"
                  @click="deleteAppointment(data)"
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
      class="w-full max-w-md"
    >
      <AppointmentForm
        v-if="selectedAppointment"
        :appointment="selectedAppointment"
        :is-admin="true"
        @save="handleSave"
        @cancel="showEditDialog = false"
      />
    </Dialog>

    <!-- View Dialog -->
    <Dialog
      v-model:visible="showViewDialog"
      modal
      header="Detalles de la Cita"
      class="w-full max-w-md"
    >
      <div v-if="selectedAppointment" class="p-4">
        <div class="field mb-3">
          <label class="font-medium text-color-secondary">Usuario:</label>
          <div class="mt-1">{{ selectedAppointment.user?.name }}</div>
        </div>
        
        <div class="field mb-3">
          <label class="font-medium text-color-secondary">Email:</label>
          <div class="mt-1">{{ selectedAppointment.user?.email }}</div>
        </div>
        
        <div class="field mb-3">
          <label class="font-medium text-color-secondary">Fecha:</label>
          <div class="mt-1">{{ formatDate(selectedAppointment.date) }}</div>
        </div>
        
        <div class="field mb-3">
          <label class="font-medium text-color-secondary">Hora:</label>
          <div class="mt-1">{{ selectedAppointment.time }}</div>
        </div>
        
        <div class="field mb-3">
          <label class="font-medium text-color-secondary">Estado:</label>
          <div class="mt-1">
            <Tag
              :value="getStatusLabel(selectedAppointment.status)"
              :severity="getStatusSeverity(selectedAppointment.status)"
            />
          </div>
        </div>
        
        <div class="field mb-3">
          <label class="font-medium text-color-secondary">Descripción:</label>
          <div class="mt-1">{{ selectedAppointment.description || 'Sin descripción' }}</div>
        </div>
      </div>
    </Dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useConfirm } from 'primevue/useconfirm'
import { useToast } from 'primevue/usetoast'
import { apiService } from '../services/api'
import AppointmentForm from './AppointmentForm.vue'

// PrimeVue Components
import Card from 'primevue/card'
import Button from 'primevue/button'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Tag from 'primevue/tag'
import Dropdown from 'primevue/dropdown'
import Calendar from 'primevue/calendar'
import Dialog from 'primevue/dialog'
import InputText from 'primevue/inputtext'
import Avatar from 'primevue/avatar'

const confirm = useConfirm()
const toast = useToast()

const appointments = ref([])
const loading = ref(false)
const showEditDialog = ref(false)
const showViewDialog = ref(false)
const selectedAppointment = ref(null)

const stats = reactive({
  total: 0,
  scheduled: 0,
  completed: 0,
  cancelled: 0
})

const filters = reactive({
  user: '',
  status: null,
  dateRange: null
})

const statusOptions = [
  { label: 'Programada', value: 'scheduled' },
  { label: 'Completada', value: 'completed' },
  { label: 'Cancelada', value: 'cancelled' }
]

let searchTimeout = null

const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    loadAppointments()
  }, 500)
}

const loadAppointments = async () => {
  loading.value = true
  try {
    const params = {}
    if (filters.user) params.user = filters.user
    if (filters.status) params.status = filters.status
    if (filters.dateRange && filters.dateRange.length === 2) {
      params.start_date = filters.dateRange[0].toISOString().split('T')[0]
      params.end_date = filters.dateRange[1].toISOString().split('T')[0]
    }

    const response = await apiService.get('/admin/appointments', { params })
    appointments.value = response.data.appointments
    
    // Update stats
    stats.total = appointments.value.length
    stats.scheduled = appointments.value.filter(a => a.status === 'scheduled').length
    stats.completed = appointments.value.filter(a => a.status === 'completed').length
    stats.cancelled = appointments.value.filter(a => a.status === 'cancelled').length
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Error al cargar las citas',
      life: 3000
    })
  } finally {
    loading.value = false
  }
}

const clearFilters = () => {
  filters.user = ''
  filters.status = null
  filters.dateRange = null
  loadAppointments()
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('es-ES', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const getStatusLabel = (status) => {
  const statusMap = {
    scheduled: 'Programada',
    completed: 'Completada',
    cancelled: 'Cancelada'
  }
  return statusMap[status] || status
}

const getStatusSeverity = (status) => {
  const severityMap = {
    scheduled: 'info',
    completed: 'success',
    cancelled: 'danger'
  }
  return severityMap[status] || 'info'
}

const createAppointment = () => {
  selectedAppointment.value = null
  showEditDialog.value = true
}

const viewAppointment = (appointment) => {
  selectedAppointment.value = appointment
  showViewDialog.value = true
}

const editAppointment = (appointment) => {
  selectedAppointment.value = { ...appointment }
  showEditDialog.value = true
}

const deleteAppointment = (appointment) => {
  confirm.require({
    message: '¿Estás seguro de que deseas eliminar esta cita? Esta acción no se puede deshacer.',
    header: 'Confirmar Eliminación',
    icon: 'pi pi-exclamation-triangle',
    accept: async () => {
      try {
        await apiService.delete(`/admin/appointments/${appointment.id}`)
        toast.add({
          severity: 'success',
          summary: 'Cita Eliminada',
          detail: 'La cita ha sido eliminada exitosamente',
          life: 3000
        })
        loadAppointments()
      } catch (error) {
        toast.add({
          severity: 'error',
          summary: 'Error',
          detail: 'Error al eliminar la cita',
          life: 3000
        })
      }
    }
  })
}

const handleSave = () => {
  showEditDialog.value = false
  loadAppointments()
}

const exportData = async () => {
  try {
    const response = await apiService.get('/admin/appointments/export', {
      responseType: 'blob'
    })
    
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `citas_${new Date().toISOString().split('T')[0]}.csv`)
    document.body.appendChild(link)
    link.click()
    link.remove()
    
    toast.add({
      severity: 'success',
      summary: 'Exportación Exitosa',
      detail: 'Los datos han sido exportados correctamente',
      life: 3000
    })
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Error al exportar los datos',
      life: 3000
    })
  }
}

onMounted(() => {
  loadAppointments()
})
</script>
