<script setup>
import { useAuthStore } from "~/stores/authStore"; // Importe a store

const auth = useAuthStore(); // Use a store

definePageMeta({
  middleware: ["auth"],
});
</script>

<template>
  <Head>
    <Title>Dashboard</Title>
  </Head>

  <Body>
    <section v-if="auth.isLoading">
      <Spinner />
    </section>
    <section v-else class="flex justify-center items-center mx-auto">
      <div class="grid grid-cols-1 lg:grid-cols-3 mt-16 lg:mt-32 gap-8">
        <NuxtLink to="/dashboard/estoque">
          <UCard
            class="flex flex-col text-center hover:text-primary hover:cursor-pointer"
          >
            <UIcon name="i-heroicons-clipboard-document-list" class="h-8 w-8" />
            <h1 class="font-medium text-lg">Almoxarifado</h1>
          </UCard>
        </NuxtLink>
        <NuxtLink to="/dashboard/agendas">
          <UCard
            class="flex flex-col text-center hover:text-primary hover:cursor-pointer"
          >
            <UIcon name="i-heroicons-calendar" class="h-8 w-8" />
            <h1 class="font-medium text-lg">Agendas</h1>
          </UCard>
        </NuxtLink>
      </div>
    </section>
  </Body>
</template>
