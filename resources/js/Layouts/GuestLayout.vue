<template>
    <Toast />
    <ConfirmDialog />
    <div class="min-h-screen flex flex-column sm:justify-center items-center bg-gray-100 bg-no-repeat bg-cover"
        style="background-image: url('/images/login-bg.jpg');">
        <div v-if="$slots.header" class="w-full max-w-md mb-4">
            <slot name="header" />
        </div>
        <div class="">
            <slot />
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>
