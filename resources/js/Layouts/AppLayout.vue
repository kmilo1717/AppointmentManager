<template>
    <div class="layout-wrapper">
        <Toast />
        <ConfirmDialog />
        
        <!-- Navigation Bar -->
        <Menubar :model="menuItems" class="border-noround " >
            <template #start>
                <div class="flex align-items-center gap-2">
                    <i class="pi pi-wrench text-2xl text-primary"></i>
                    <span class="font-bold text-xl">Taller de Vehículos</span>
                </div>
            </template>
            
            <template #end>
                <div class="flex align-items-center gap-2">
                    <Avatar :label="authStore.user?.nombre?.charAt(0)" class="mr-2" shape="circle" />
                    <span class="font-medium hidden md:inline">{{ authStore.user?.nombre }}</span>
                    <Button 
                        icon="pi pi-sign-out" 
                        severity="secondary" 
                        text 
                        @click="logout"
                        v-tooltip="'Cerrar Sesión'"
                    />
                </div>
            </template>
        </Menubar>

        <!-- Main Content -->
        <div class="layout-main">
            <div class="layout-content">
                <!-- Page Header -->
                <div v-if="$slots.header" class="content-section mb-4">
                    <Card>
                        <template #content>
                            <slot name="header" />
                        </template>
                    </Card>
                </div>
                
                <!-- Page Content -->
                <div class="content-section">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/Stores/authStore'
import { useToast } from 'primevue/usetoast'

// PrimeVue Components
import Toast from 'primevue/toast'
import ConfirmDialog from 'primevue/confirmdialog'
import Menubar from 'primevue/menubar'
import Avatar from 'primevue/avatar'
import Button from 'primevue/button'
import Card from 'primevue/card'

const authStore = useAuthStore()
const toast = useToast()

const menuItems = computed(() => {
    const items = []
    
    if (authStore.isCliente) {
        items.push(
            {
                label: 'Mis Citas',
                icon: 'pi pi-calendar',
                command: () => window.location.href = '/dashboard'
            },
            {
                label: 'Nueva Cita',
                icon: 'pi pi-plus',
                command: () => window.location.href = '/citas/create'
            }
        )
    }
    
    if (authStore.isAdmin) {
        items.push(
            {
                label: 'Dashboard Admin',
                icon: 'pi pi-chart-bar',
                command: () => window.location.href = '/admin/dashboard'
            },
            {
                label: 'Gestión de Citas',
                icon: 'pi pi-calendar',
                command: () => window.location.href = '/admin/citas'
            },
            {
                label: 'Nueva Cita',
                icon: 'pi pi-plus',
                command: () => window.location.href = '/citas/create'
            }
        )
    }
    
    return items
})

const logout = async () => {
    await authStore.logout()
    toast.add({
        severity: 'success',
        summary: 'Sesión Cerrada',
        detail: 'Has cerrado sesión exitosamente',
        life: 3000
    })
    window.location.href = '/login'
}
</script>

<style scoped>
.layout-wrapper {
    min-height: 100vh;
    background-color: #f8f9fa;
}

.layout-main {
    padding: 2rem;
}

.layout-content {
    max-width: 1200px;
    margin: 0 auto;
}

.content-section {
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .layout-main {
        padding: 1rem;
    }
}
</style>
