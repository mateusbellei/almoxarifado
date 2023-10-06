<template>
  <div class="h-screen flex items-center justify-center">
    <UCard class="w-96">
      <template #header>
        <h2 class="text-2xl text-primary">Criar Usuário</h2>
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
        <UFormGroup label="Confirmar Senha" name="password_confirmation">
          <UInput v-model="state.password_confirmation" type="password" />
        </UFormGroup>
        <UFormGroup label="Código de Criação" name="secret_word">
          <UInput v-model="state.secret_word" type="password"/>
        </UFormGroup>
        <div class="flex justify-between items-center">
          <UButton type="submit">
            Register
          </UButton>
          <NuxtLink to="/login" class="text-primary underline text-xs">
            Já tem uma conta? Login
          </NuxtLink>
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
</template>

<script setup lang="ts">
import { ref } from 'vue'
import type { FormError, FormSubmitEvent } from '@nuxt/ui/dist/runtime/types'
const state = ref({
  name: undefined,
  secret_word: undefined,
  password: undefined,
  password_confirmation: undefined
})

let successMessage = ref('')
let errorMessage = ref('')

const validate = (state: any): FormError[] => {
  const errors = []
  if (!state.name) errors.push({ path: 'name', message: 'Required' })
  if (!state.secret_word) errors.push({ path: 'secret_word', message: 'Required' })
  if (!state.password) errors.push({ path: 'password', message: 'Required' })
  if (!state.password_confirmation) errors.push({ path: 'password_confirmation', message: 'Required' })
  if (state.password !== state.password_confirmation) errors.push({ path: 'password_confirmation', message: 'Passwords do not match' })
  
  return errors
}
async function submit (event: FormSubmitEvent<any>) {
  try {
      const {data, error}: {data: any, error: any} = await useFetch(`${import.meta.env.VITE_BASE_API_URL}/register`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(event.data)
    })

    if (data.value) {
      successMessage.value = data.value.message;
      errorMessage.value = '';
      setTimeout(() => {
        navigateTo('/login')
      }, 3000)
    } else if (error.value?.data.errors) {
      errorMessage.value = Object.values(error.value.data.errors).flat().join(',');
      successMessage.value = '';
    }

  } catch (error) {
    console.error(error)
  }
}

</script>
