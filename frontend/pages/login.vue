<template>
  <Head>
    <Title>Login</Title>
  </Head>
  <Body>
    <section v-if="auth.isLoading">
      <Spinner v-if="auth.isLoading"/>
    </section>
    <section v-else>
      <div class="h-screen flex items-center justify-center">
        <UCard class="w-96">
          <template #header>
            <h2 class="text-2xl text-primary">Login</h2>
          </template>
          <UForm
            class="space-y-4"
            :validate="validate"
            :state="state"
            @submit="submit"
          >
            <UFormGroup label="Usuário" name="name">
              <UInput v-model="state.name" />
            </UFormGroup>
            <UFormGroup label="Senha" name="password">
              <UInput v-model="state.password" type="password" />
            </UFormGroup>
            <div class="flex justify-between items-center">
              <UButton type="submit">
                Login
              </UButton>
              <div class="flex flex-col space-y-2 text-right text-xs">
                <NuxtLink to="/register" class="text-primary underline text-xs">
                  Registrar novo usuário
                </NuxtLink>
              </div>
            </div>
          </UForm>
          <template #footer>
            <!-- Mensagem de Sucesso -->
            <UBadge v-if="successMessage" color="green">
              {{ successMessage }}
            </UBadge>

            <!-- Mensagem de Erro -->
            <UBadge v-if="errorMessage" color="red">
              {{ errorMessage }}
            </UBadge>
          </template>
        </UCard>
      </div>
    </section>
  </Body>
  
</template>

<script setup lang="ts">
import { ref } from 'vue'
import type { FormError, FormSubmitEvent } from '@nuxt/ui/dist/runtime/types'
import { useAuthStore } from '~/stores/authStore' // Importe a store

const auth = useAuthStore() // Use a store

definePageMeta({
  middleware: [
    'auth',
  ]
});

const state = ref({
  name: undefined,
  password: undefined,
})

let successMessage = ref('')
let errorMessage = ref('')

const validate = (state: any): FormError[] => {
  const errors = []
  if (!state.name) errors.push({ path: 'name', message: 'Required' })
  if (!state.password) errors.push({ path: 'password', message: 'Required' })
  
  return errors
}

async function submit(event: FormSubmitEvent<any>) {
  try {
    const { data, error }: { data: any, error: any } = await useFetch(`${import.meta.env.VITE_BASE_API_URL}/login`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(event.data)
    })

    if (data.value) {
      auth.setToken(data.value.token) // Use a ação setToken da store
      successMessage.value = data.value.message
      errorMessage.value = ''
      return navigateTo('/estoque')
    } else if (error) {
      errorMessage.value = error.value.data.message
      successMessage.value = ''
    }

  } catch (error) {
    console.error(error)
  }
}
</script>
