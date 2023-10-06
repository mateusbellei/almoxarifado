<template>
  <div>
    <UModal v-model="localIsOpen">
      <UCard :ui="{ divide: 'divide-y divide-gray-100 dark:divide-gray-800' }">
        <template #header>
          <h1 class="text-primary font-medium">Editar Estoque</h1>
        </template>

        <UForm class="space-y-4" :validate="validate" :state="state" @submit="submit">
          <!-- Informações atuais para visualização -->
          <div class="space-y-2">
            <div class="flex gap-2">
              <h1 class="text-primary">Produto Atual:</h1>
              <h1>{{ product ? product.produto : 'Loading...' }}</h1>
            </div>
            <div class="flex gap-2">
              <h1 class="text-primary">Estoque Atual:</h1>
              <h1>{{ product ? product.estoque : 'Loading...' }}</h1>
            </div>
            <div class="flex gap-2">
              <h1 class="text-primary">Validade Atual:</h1>
              <h1>{{ product ? product.validade : 'Loading...' }}</h1>
            </div>
            <div class="flex gap-2">
              <h1 class="text-primary">Última Atualização Por:</h1>
              <h1>{{ product ? product.updated_by : 'Loading...' }}</h1>
            </div>
          </div>

          <!-- Campos para edição -->
          <UFormGroup label="Entrada Estoque" name="entrada">
            <UInput v-model="state.entrada" type="number" />
          </UFormGroup>
          <UFormGroup label="Saída Estoque" name="saida">
            <UInput v-model="state.saida" type="number" />
          </UFormGroup>
          <UFormGroup label="Nova Validade" name="validade">
            <UInput v-model="state.validade" type="date" />
          </UFormGroup>
          <div class="flex justify-between space-x-1">
            <UButton color="red" label="Cancelar" @click="closeModal" />
            <UButton label="Salvar" type="submit" />
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
    </UModal>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, defineProps, defineEmits } from 'vue';
import { useAuthStore } from '~/stores/authStore';
import type { FormError, FormSubmitEvent } from '@nuxt/ui/dist/runtime/types';

const auth = useAuthStore();

// Props
const props = defineProps({
  modelValue: Boolean,
  product: {
    type: Object,
    required: true,
    default: () => ({ produto: '', estoque: '', validade: '', updated_by: '' })
  }
});

console.log('props product', props.product)


// State
const state = ref({
  entrada: undefined,
  saida: undefined,
  validade: new Date().toISOString().substr(0, 10), // Default to current date
});

const successMessage = ref('');
const errorMessage = ref('');

// Modal control
const localIsOpen = ref(props.modelValue);
watch(() => props.modelValue, newVal => localIsOpen.value = newVal);

// Emits
const emit = defineEmits(["update:modelValue", "stock-updated", "close"]);
const updateModelValue = (value: any) => emit("update:modelValue", value);

// Methods
const closeModal = () => {
  updateModelValue(false);
  emit("close");
}

const validate = (state: any): FormError[] => {
  const errors = [];
  if (!state.entrada && !state.saida) {
    errors.push({ path: 'entrada', message: 'Entrada ou saída é necessária' });
  }
  
  return errors;
}

async function submit(event: FormSubmitEvent<any>) {
  try {
    const { data, error }: { data: any, error: any } = await useFetch(`${import.meta.env.VITE_BASE_API_URL}/product/${props.product.id}/update-estoque`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${auth.token}`
      },
      body: JSON.stringify(event.data)
    });

    if (data) {
      successMessage.value = 'Estoque atualizado com sucesso!';
      errorMessage.value = '';
      state.value.entrada = undefined;
      state.value.saida = undefined;
      emit("stock-updated");
      successMessage.value = '';
      setTimeout(() => {
        closeModal();
      }, 1000);
    } else if (error) {
      errorMessage.value = error.value.data.message || 'Erro ao atualizar o estoque';
      successMessage.value = '';
    }
  } catch (error) {
    errorMessage.value = 'Erro ao atualizar o estoque';
  }
}

// Watchers
watch(localIsOpen, updateModelValue);
</script>