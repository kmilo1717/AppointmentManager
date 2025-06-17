<template>
  <form @submit.prevent="handleSubmit" class="p-4">
    <!-- User Selection (Admin only) -->
    <div v-if="isAdmin" class="field mb-4">
      <label for="user" class="block text-900 font-medium mb-2">Usuario *</label>
      <Dropdown
        id="user"
        v-model="form.user_id"
        :options="users"
        optionLabel="name"
        optionValue="id"
        placeholder="Seleccionar usuario"
        class="w-full"
        :class="{ 'p-invalid': errors.user_id }"
        filter
        :loading="loadingUsers"
        @focus="loadUsers"
      />
      <small v-if="errors.user_id" class="p-error">{{ errors.user_id }}</small>
    </div>

    <!-- Date -->
    <div class="field mb-4">
      <label for="date" class="block text-900 font-medium mb-2">Fecha *</label>
      <Calendar
        id="date"
        v-model="form.date"
        placeholder="Seleccionar fecha"
        class="w-full"
        :class="{ 'p-invalid': errors.date }"
        :minDate="minDate"
        :disabledDates="disabledDates"
        showIcon
        dateFormat="dd/mm/yy"
        @date-select="onDateChange"
      />
      <small v-if="errors.date" class="p-error">{{ errors.date }}</small>
    </div>

    <!-- Time -->
    <div class="field mb-4">
      <label for="time" class="block text-900 font-medium mb-2">Hora *</label>
      <Dropdown
        id="time"
        v-model="form.time"
        :options="availableTimes"
        placeholder="Seleccionar hora"
        class="w-full"
        :class="{ 'p-invalid': errors.time }"
        :loading="loadingTimes"
        :disabled="!form.date"
      />
      <small v-if="errors.time" class="p-error">{{ errors.time }}</small>
      <small v-if="!form.date" class="text-color-secondary">
        Primero selecciona una fecha
      </small>
    </div>

    <!-- Description -->
    <div class="field mb-4">
      <label for="description" class="block text-900 font-medium mb-2">Descripción</label>
      <Textarea
        id="description"
        v-model="form.description"
        placeholder="Describe el motivo de tu cita..."
        class="w-full"
        :class="{ 'p-invalid': errors.description }"
        rows="4"
        maxlength="500"
      />
      <small v-if="errors.description" class="p-error">{{ errors.description }}</small>
      <small class="text-color-secondary">
        {{ form.description?.length || 0 }}/500 caracteres
      </small>
    </div>

    <!-- Status (Admin only for editing) -->
    <div v-if="isAdmin && appointment" class="field mb-4">
      <label for="status" class="block text-900 font-medium mb-2">Estado</label>
      <Dropdown
        id="status"
        v-model="form.status"
        :options="statusOptions"
        optionLabel="label"
        optionValue="value"
        placeholder="Seleccionar estado"
        class="w-full"
      />
    </div>

    <!-- Actions -->
    <div class="flex gap-2 justify-content-end">
      <Button
        type="button"
        label="Cancelar"
        severity="secondary"
        @click="$emit('cancel')"
      />
      <Button
        type="submit"
        :label="appointment ? 'Actualizar' : 'Crear Cita'"
        :loading="loading"
        :disabled="loading"
      />
    </div>
  </form>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { useToast } from 'primevue/usetoast'
import { apiService } from '@services/api'

// PrimeVue Components
import Calendar from 'primevue/calendar'
import Dropdown from 'primevue/dropdown'
import Textarea from 'primevue/textarea'
import Button from 'primevue/button'

const props = defineProps({
  appointment: {
    type: Object,
    default: null
  },
  isAdmin: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['save', 'cancel'])

const toast = useToast()

const loading = ref(false)
const loadingUsers = ref(false)
const loadingTimes = ref(false)
const users = ref([])
const availableTimes = ref([])
const errors = ref({})

const form = reactive({
  user_id: null,
  date: null,
  time: '',
  description: '',
  status: 'scheduled'
})

const minDate = computed(() => {
  const tomorrow = new Date()
  tomorrow.setDate(tomorrow.getDate() + 1)
  return tomorrow
})

const disabledDates = ref([])

const statusOptions = [
  { label: 'Programada', value: 'scheduled' },
  { label: 'Completada', value: 'completed' },
  { label: 'Cancelada', value: 'cancelled' }
]

// Initialize form with appointment data if editing
const initializeForm = () => {
  if (props.appointment) {
    form.user_id = props.appointment.user_id
    form.date = new Date(props.appointment.date)
    form.time = props.appointment.time
    form.description = props.appointment.description || ''
    form.status = props.appointment.status || 'scheduled'
  }
}

// Load users for admin
const loadUsers = async () => {
  if (!props.isAdmin || users.value.length > 0) return
  
  loadingUsers.value = true
  try {
    const response = await apiService.get('/admin/users')
    users.value = response.data.users
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Error al cargar usuarios',
      life: 3000
    })
  } finally {
    loadingUsers.value = false
  }
}

// Load available times when date changes
const onDateChange = async () => {
  if (!form.date) return
  
  loadingTimes.value = true
  try {
    const dateStr = form.date.toISOString().split('T')[0]
    const response = await apiService.get(`/appointments/available-times/${dateStr}`)
    availableTimes.value = response.data.times
    
    // Reset time if current selection is not available
    if (form.time && !availableTimes.value.includes(form.time)) {
      form.time = ''
    }
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Error al cargar horarios disponibles',
      life: 3000
    })
  } finally {
    loadingTimes.value = false
  }
}

// Load disabled dates
const loadDisabledDates = async () => {
  try {
    const response = await apiService.get('/appointments/disabled-dates')
    disabledDates.value = response.data.dates.map(date => new Date(date))
  } catch (error) {
    console.error('Error loading disabled dates:', error)
  }
}

const validateForm = () => {
  errors.value = {}
  
  if (props.isAdmin && !form.user_id) {
    errors.value.user_id = 'Selecciona un usuario'
  }
  
  if (!form.date) {
    errors.value.date = 'Selecciona una fecha'
  }
  
  if (!form.time) {
    errors.value.time = 'Selecciona una hora'
  }
  
  if (form.description && form.description.length > 500) {
    errors.value.description = 'La descripción no puede exceder 500 caracteres'
  }
  
  return Object.keys(errors.value).length === 0
}

const handleSubmit = async () => {
  if (!validateForm()) return
  
  loading.value = true
  try {
    const data = {
      ...form,
      date: form.date.toISOString().split('T')[0]
    }
    
    if (!props.isAdmin) {
      delete data.user_id
      delete data.status
    }
    
    if (props.appointment) {
      const endpoint = props.isAdmin 
        ? `/admin/appointments/${props.appointment.id}`
        : `/appointments/${props.appointment.id}`
      await apiService.put(endpoint, data)
      
      toast.add({
        severity: 'success',
        summary: 'Cita Actualizada',
        detail: 'La cita ha sido actualizada exitosamente',
        life: 3000
      })
    } else {
      // Create new appointment
      const endpoint = props.isAdmin 
        ? '/admin/appointments'
        : '/appointments'
      await apiService.post(endpoint, data)
      
      toast.add({
        severity: 'success',
        summary: 'Cita Creada',
        detail: 'La cita ha sido creada exitosamente',
        life: 3000
      })
    }
    
    emit('save')
  } catch (error) {
    const message = error.response?.data?.message || 'Error al procesar la cita'
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: message,
      life: 5000
    })
  } finally {
    loading.value = false
  }
}

// Watch date changes
watch(() => form.date, onDateChange)

onMounted(() => {
  initializeForm()
  loadDisabledDates()
  if (props.isAdmin) {
    loadUsers()
  }
  if (form.date) {
    onDateChange()
  }
})
</script>
