<template>
    <div class="layout-wrapper">
        <Toast />
        <ConfirmDialog />
        
        <!-- Navigation Bar -->
        <Menubar :model="menuItems" class="border-noround p-4 bg-white shadow-md">
            <template #start>
                <div class="flex align-items-center gap-2">
                    <i class="pi pi-wrench text-2xl text-primary"></i>
                    <span class="font-bold text-xl">Taller de Vehículos</span>
                </div>
            </template>
            
            <template #end>
                <div class="flex align-items-center gap-2">
                    <Avatar :label="$page.props.auth.user.name.charAt(0)" class="mr-2" shape="circle" />
                    <span class="font-medium hidden md:inline">{{ $page.props.auth.user.name }}</span>
                    <Menu ref="menu" :model="userMenuItems" popup />
                    <Button 
                        icon="pi pi-user" 
                        severity="secondary" 
                        text 
                        @click="toggleUserMenu"
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
import { ref, computed } from 'vue'
import { usePage, router, useRemember } from '@inertiajs/vue3'
import { useToast } from 'primevue/usetoast'

// PrimeVue Components
import Toast from 'primevue/toast'
import ConfirmDialog from 'primevue/confirmdialog'
import Menubar from 'primevue/menubar'
import Avatar from 'primevue/avatar'
import Button from 'primevue/button'
import Card from 'primevue/card'
import Dropdown from 'primevue/dropdown'
import Menu from 'primevue/menu'


const page = usePage()
const toast = useToast()
const menu = ref()

const menuItems = computed(() => {
    const items = []
    
    items.push({
        label: 'Dashboard',
        icon: 'pi pi-home',
        command: () => router.visit(route('dashboard'))
    })
    
    items.push({
        label: 'Nueva Cita',
        icon: 'pi pi-plus',
        command: () => router.visit(route('citas.create'))
    })
    
    return items
})

const userMenuItems = [
/*     {
        label: 'Perfil',
        icon: 'pi pi-user',
        route: 'profile.edit'
    },
    {
        separator: true
    }, */
    {
        label: 'Cerrar Sesión',
        icon: 'pi pi-sign-out',
        command: logout
    }
]

const toggleUserMenu = (event) => {
    menu.value.toggle(event)
}

function logout() {
    router.post(route('logout'), {}, {
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Sesión Cerrada',
                detail: 'Has cerrado sesión exitosamente',
                life: 3000
            })
        }
    })
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
