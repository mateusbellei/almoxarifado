<template>
  <div class="h-screen bg-cover bg-center relative bg-gray-100 dark:bg-gray-900">
    <div class="absolute inset-0 opacity-50"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
      <div class="flex space-x-4">
        <nuxt-link to="/login" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Entrar</nuxt-link>
        <nuxt-link to="/register" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Registrar</nuxt-link>
      </div>
    </div>
  </div>
</template>
