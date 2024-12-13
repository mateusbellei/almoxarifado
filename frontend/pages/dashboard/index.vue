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
    <section v-else>Dashboard</section>
  </Body>
</template>
