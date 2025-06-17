import { defineStore } from "pinia"
import { ref } from "vue"
import axios from "axios"

export const useCitasStore = defineStore("citas", () => {
  const citas = ref([])
  const loading = ref(false)
  const statistics = ref({})

  const fetchCitas = async (filters = {}) => {
    loading.value = true
    try {
      const response = await axios.get("/api/citas", { params: filters })
      if (response.data.success) {
        citas.value = response.data.data.data || response.data.data
        return { success: true, data: response.data.data }
      }
      return { success: false, message: response.data.message }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || "Error al cargar citas",
      }
    } finally {
      loading.value = false
    }
  }

  const createCita = async (citaData) => {
    loading.value = true
    try {
      const response = await axios.post("/api/citas", citaData)
      if (response.data.success) {
        return { success: true, data: response.data.data }
      }
      return { success: false, message: response.data.message }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || "Error al crear cita",
        errors: error.response?.data?.errors || {},
      }
    } finally {
      loading.value = false
    }
  }

  const updateCita = async (id, citaData) => {
    loading.value = true
    try {
      const response = await axios.put(`/api/citas/${id}`, citaData)
      if (response.data.success) {
        return { success: true, data: response.data.data }
      }
      return { success: false, message: response.data.message }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || "Error al actualizar cita",
        errors: error.response?.data?.errors || {},
      }
    } finally {
      loading.value = false
    }
  }

  const deleteCita = async (id) => {
    loading.value = true
    try {
      const response = await axios.delete(`/api/citas/${id}`)
      if (response.data.success) {
        return { success: true }
      }
      return { success: false, message: response.data.message }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || "Error al eliminar cita",
      }
    } finally {
      loading.value = false
    }
  }

  const cancelCita = async (id) => {
    loading.value = true
    try {
      const response = await axios.patch(`/api/citas/${id}/cancel`)
      if (response.data.success) {
        return { success: true, data: response.data.data }
      }
      return { success: false, message: response.data.message }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || "Error al cancelar cita",
      }
    } finally {
      loading.value = false
    }
  }

  const getAvailableHours = async (fecha) => {
    try {
      const response = await axios.get("/api/citas/available-hours", {
        params: { fecha },
      })
      if (response.data.success) {
        return { success: true, data: response.data.data }
      }
      return { success: false, message: response.data.message }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || "Error al cargar horarios",
      }
    }
  }

  const fetchStatistics = async () => {
    try {
      const response = await axios.get("/api/citas/admin/statistics")
      if (response.data.success) {
        statistics.value = response.data.data
        return { success: true, data: response.data.data }
      }
      return { success: false, message: response.data.message }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || "Error al cargar estadísticas",
      }
    }
  }

  return {
    citas,
    loading,
    statistics,
    fetchCitas,
    createCita,
    updateCita,
    deleteCita,
    cancelCita,
    getAvailableHours,
    fetchStatistics,
  }
})
