<template>
    <form @submit.prevent="handleSubmit" class="p-4">
        <!-- Usuario (solo para admin) -->
        <div v-if="$page.props.auth.user.isAdmin && usuarios" class="field mb-4">
            <label for="usuario_id" class="block text-900 font-medium mb-2">
                Usuario <span class="text-red-500">*</span>
            </label>
            <Dropdown
                id="usuario_id"
                v-model="form.usuario_id"
                :options="usuarios"
                optionLabel="name"
                optionValue="id"
                placeholder="Seleccionar usuario"
                class="w-full"
                :class="{ 'p-invalid': form.errors.usuario_id }"
                filter
            />
            <small v-if="form.errors.usuario_id" class="p-error">{{ form.errors.usuario_id }}</small>
        </div>

        <!-- Fecha -->
        <div class="field mb-4">
            <label for="fecha" class="block text-900 font-medium mb-2">
                Fecha <span class="text-red-500">*</span>
            </label>
            <Calendar
                id="fecha"
                v-model="form.fecha"
                placeholder="Seleccionar fecha"
                class="w-full"
                :class="{ 'p-invalid': form.errors.fecha }"
                :minDate="minDate"
                showIcon
                dateFormat="dd/mm/yy"
                @date-select="onDateChange"
            />
            <small v-if="form.errors.fecha" class="p-error">{{ form.errors.fecha }}</small>
        </div>

        <!-- Hora -->
        <div class="field mb-4">
            <label for="hora" class="block text-900 font-medium mb-2">
                Hora <span class="text-red-500">*</span>
            </label>
            <Dropdown
                id="hora"
                v-model="form.hora"
                :options="availableHours"
                placeholder="Seleccionar hora"
                class="w-full"
                :class="{ 'p-invalid': form.errors.hora }"
                :loading="loadingHours"
                :disabled="!form.fecha"
            />
            <small v-if="form.errors.hora" class="p-error">{{ form.errors.hora }}</small>
            <small v-if="!form.fecha" class="text-color-secondary">
                Primero selecciona una fecha
            </small>
        </div>

        <!-- Tipo de Servicio -->
        <div class="field mb-4">
            <label for="tipo_servicio" class="block text-900 font-medium mb-2">
                Tipo de Servicio
            </label>
            <Dropdown
                id="tipo_servicio"
                v-model="form.tipo_servicio"
                :options="tiposServicio"
                placeholder="Seleccionar tipo de servicio"
                class="w-full"
                :class="{ 'p-invalid': form.errors.tipo_servicio }"
            />
            <small v-if="form.errors.tipo_servicio" class="p-error">{{ form.errors.tipo_servicio }}</small>
        </div>

        <!-- Información del Vehículo -->
        <div class="grid">
            <div class="col-12 md:col-4">
                <div class="field mb-4">
                    <label for="vehiculo_marca" class="block text-900 font-medium mb-2">
                        Marca del Vehículo
                    </label>
                    <InputText
                        id="vehiculo_marca"
                        v-model="form.vehiculo_marca"
                        placeholder="Ingrese la marca"
                        class="w-full"
                        :class="{ 'p-invalid': form.errors.vehiculo_marca }"
                    />
                    <small v-if="form.errors.vehiculo_marca" class="p-error">{{ form.errors.vehiculo_marca }}</small>
                </div>
            </div>
            
            <div class="col-12 md:col-4">
                <div class="field mb-4">
                    <label for="vehiculo_modelo" class="block text-900 font-medium mb-2">
                        Modelo del Vehículo
                    </label>
                    <InputText
                        id="vehiculo_modelo"
                        v-model="form.vehiculo_modelo"
                        placeholder="Ingrese el modelo"
                        class="w-full"
                        :class="{ 'p-invalid': form.errors.vehiculo_modelo }"
                    />
                    <small v-if="form.errors.vehiculo_modelo" class="p-error">{{ form.errors.vehiculo_modelo }}</small>
                </div>
            </div>
            
            <div class="col-12 md:col-4">
                <div class="field mb-4">
                    <label for="vehiculo_placa" class="block text-900 font-medium mb-2">
                        Placa del Vehículo
                    </label>
                    <InputMask
                        id="vehiculo_placa"
                        v-model="form.vehiculo_placa"
                        mask="aaa-999"
                        placeholder="AAA-999"
                        class="w-full text-uppercase"
                        pattern="^[a-z]{3}-\d{3}$"
                        :class="{ 'p-invalid': form.errors.vehiculo_placa }"
                    />
                    <small v-if="form.errors.vehiculo_placa" class="p-error">{{ form.errors.vehiculo_placa }}</small>
                </div>
            </div>
        </div>

        <!-- Descripción -->
        <div class="field mb-4">
            <label for="descripcion" class="block text-900 font-medium mb-2">
                Descripción <span class="text-red-500">*</span>
            </label>
            <Textarea
                id="descripcion"
                v-model="form.descripcion"
                placeholder="Describe el problema o servicio requerido..."
                class="w-full"
                :class="{ 'p-invalid': form.errors.descripcion }"
                rows="4"
                maxlength="500"
            />
            <small v-if="form.errors.descripcion" class="p-error">{{ form.errors.descripcion }}</small>
            <small class="text-color-secondary">
                {{ form.descripcion?.length || 0 }}/500 caracteres
            </small>
        </div>

        <!-- Estado (solo para admin en edición) -->
        <div v-if="$page.props.auth.user.isAdmin && cita" class="field mb-4">
            <label for="estado" class="block text-900 font-medium mb-2">Estado</label>
            <Dropdown
                id="estado"
                v-model="form.estado"
                :options="estadoOptions"
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
                @click="$inertia.visit(route('dashboard'))"
            />
            <Button
                type="submit"
                :label="cita ? 'Actualizar Cita' : 'Crear Cita'"
                :loading="form.processing"
                :disabled="form.processing"
            />
        </div>
    </form>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import axios from 'axios'
import { route } from 'ziggy-js';

// PrimeVue Components
import Calendar from 'primevue/calendar'
import Dropdown from 'primevue/dropdown'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'
import Button from 'primevue/button'
import InputMask from 'primevue/inputmask'

const props = defineProps({
    cita: {
        type: Object,
        default: null
    },
    usuarios: {
        type: Array,
        default: null
    }
})

const emit = defineEmits(['submit'])

const loadingHours = ref(false)
const availableHours = ref([])

const form = useForm({
    usuario_id: props.cita?.usuario_id || null,
    fecha: props.cita ? new Date(props.cita.fecha) : null,
    hora: props.cita?.hora || '',
    tipo_servicio: props.cita?.tipo_servicio || '',
    vehiculo_marca: props.cita?.vehiculo_marca || '',
    vehiculo_modelo: props.cita?.vehiculo_modelo || '',
    vehiculo_placa: props.cita?.vehiculo_placa || '',
    descripcion: props.cita?.descripcion || '',
    estado: props.cita?.estado || 'pendiente'
})

const minDate = computed(() => {
    const tomorrow = new Date()
    tomorrow.setDate(tomorrow.getDate() + 1)
    return tomorrow
})

const tiposServicio = [
    'Mantenimiento',
    'Reparación',
    'Diagnóstico',
    'Cambio de Aceite',
    'Revisión de Frenos',
    'Alineación',
    'Balanceo',
    'Otros'
]

const estadoOptions = [
    { label: 'Pendiente', value: 'pendiente' },
    { label: 'Confirmada', value: 'confirmada' },
    { label: 'Cancelada', value: 'cancelada' }
]

const onDateChange = async () => {
    if (!form.fecha) return

    loadingHours.value = true
    try {
        const dateStr = form.fecha.toISOString().split('T')[0]
        
        const response = await axios.get(route('citas.available-hours'), {
            params: { fecha: dateStr, citaId: props.cita?.id || null }
        })

        if (response.data.success) {
            availableHours.value = response.data.data

            
            if (form.hora && !availableHours.value.includes(form.hora)) {
                form.hora = ''
            }
        }

    } catch (error) {
        console.error('Error loading available hours:', error)
    } finally {
        loadingHours.value = false
    }
}

const handleSubmit = () => {

    form.fecha = form.fecha ? form.fecha.toISOString().split('T')[0] : null

    emit('submit', form)
}

// Watch date changes
watch(() => form.fecha, onDateChange)

watch(() => form.errors, (errors) => {
  console.log('Cambios en los errores del form:', errors);
});

onMounted(() => {
    if (form.fecha) {
        onDateChange()
    }
})
</script>
