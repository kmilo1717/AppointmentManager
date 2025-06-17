import { defineStore } from "pinia"
import { ref, computed } from "vue"
import axios from "axios"

export const useAuthStore = defineStore("auth", () => {
  const user = ref(null)
  const token = ref(localStorage.getItem("token"))
  const loading = ref(false)

  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const isAdmin = computed(() => user.value?.rol === "admin")
  const isCliente = computed(() => user.value?.rol === "cliente")

  // Configure axios defaults
  if (token.value) {
    axios.defaults.headers.common["Authorization"] = `Bearer ${token.value}`
  }

  const login = async (credentials) => {
    loading.value = true
    try {
      const response = await axios.post("/api/auth/login", credentials)

      if (response.data.success) {
        const { token: authToken, user: userData } = response.data.data

        token.value = authToken
        user.value = userData
        localStorage.setItem("token", authToken)
        axios.defaults.headers.common["Authorization"] = `Bearer ${authToken}`

        return { success: true }
      }

      return { success: false, message: response.data.message }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || "Error al iniciar sesión",
      }
    } finally {
      loading.value = false
    }
  }

  const register = async (userData) => {
    loading.value = true
    try {
      const response = await axios.post("/api/auth/register", userData)

      if (response.data.success) {
        const { token: authToken, user: newUser } = response.data.data

        token.value = authToken
        user.value = newUser
        localStorage.setItem("token", authToken)
        axios.defaults.headers.common["Authorization"] = `Bearer ${authToken}`

        return { success: true }
      }

      return { success: false, message: response.data.message }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || "Error al registrarse",
        errors: error.response?.data?.errors || {},
      }
    } finally {
      loading.value = false
    }
  }

  const logout = async () => {
    try {
      await axios.post("/api/auth/logout")
    } catch (error) {
      console.error("Error al cerrar sesión:", error)
    } finally {
      token.value = null
      user.value = null
      localStorage.removeItem("token")
      delete axios.defaults.headers.common["Authorization"]
    }
  }

  const checkAuth = async () => {
    if (!token.value) return false

    try {
      const response = await axios.get("/api/auth/me")
      if (response.data.success) {
        user.value = response.data.data.user
        return true
      }
    } catch (error) {
      console.error("Error al verificar autenticación:", error)
      logout()
    }

    return false
  }

  return {
    user,
    token,
    loading,
    isAuthenticated,
    isAdmin,
    isCliente,
    login,
    register,
    logout,
    checkAuth,
  }
})
