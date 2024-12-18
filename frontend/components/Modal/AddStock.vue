<template>
  <div>
    <UModal v-model="localIsOpen">
      <UCard :ui="{ divide: 'divide-y divide-gray-100 dark:divide-gray-800' }">
        <template #header>
          <h1 class="text-primary font-medium">Adicionar Estoque</h1>
        </template>
        <UForm
          class="space-y-4"
          :validate="validate"
          :state="state"
          @submit="submit"
        >
          <UFormGroup label="Produto" name="produto">
            <UInput v-model="state.produto" />
          </UFormGroup>
          <UFormGroup label="Unidade de Medida" name="unidade_medida">
            <USelectMenu v-model="state.unidade_medida" :options="unidades" />
          </UFormGroup>
          <UFormGroup label="Estoque" name="estoque">
            <UInput v-model="state.estoque" />
          </UFormGroup>
          <UFormGroup label="Validade" name="validade">
            <UInput v-model="state.validade" type="date" />
          </UFormGroup>
          <UFormGroup label="Atualizado por" name="updated_by">
            <UInput
              v-model="state.updated_by"
              placeholder="Digite o seu nome..."
            />
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
import { ref, watch, defineProps, defineEmits } from "vue";
import type { FormError, FormSubmitEvent } from "@nuxt/ui/dist/runtime/types";
import { useAuthStore } from "~/stores/authStore";

const auth = useAuthStore();
const toast = useToast();

// Props
const props = defineProps({
  modelValue: Boolean,
});

// State
const state = ref({
  produto: "",
  unidade_medida: undefined,
  estoque: undefined,
  validade: new Date().toISOString().substr(0, 10),
  updated_by: "",
});

const successMessage = ref("");
const errorMessage = ref("");

// Units
const unidades = [
  "Unidade",
  "Pacote",
  "Rolo",
  "Caixa",
  "Bloco",
  "Maço",
  "Metro",
  "Frasco",
  "Tubo",
  "Galão",
];

// Modal control
const localIsOpen = ref(props.modelValue);
watch(
  () => props.modelValue,
  (newVal) => (localIsOpen.value = newVal)
);

// Emits
const emit = defineEmits(["update:modelValue", "product-added"]);
const updateModelValue = (value: any) => emit("update:modelValue", value);

// Methods
const closeModal = () => updateModelValue(false);

const validate = (state: any): FormError[] => {
  const errors = [];
  if (!state.produto) errors.push({ path: "produto", message: "Required" });
  if (!state.unidade_medida)
    errors.push({ path: "unidade_medida", message: "Required" });
  if (!state.estoque) errors.push({ path: "estoque", message: "Required" });
  if (!state.validade || new Date(state.validade) < new Date())
    errors.push({ path: "validade", message: "Data inválida" });
  if (!state.updated_by)
    errors.push({ path: "updated_by", message: "Required" });

  if (state.estoque < 0)
    errors.push({ path: "estoque", message: "Não pode ser negativo" });
  return errors;
};

async function submit(event: FormSubmitEvent<any>) {
  try {
    const { data, error }: { data: any; error: any } = await useFetch(
      `${import.meta.env.VITE_BASE_API_URL}/product`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${auth.token}`,
        },
        body: JSON.stringify(event.data),
      }
    );

    if (data.value) {
      console.log(data.value);
      toast.add({
        title: `Produto "${state.value.produto}" adicionado com sucesso!`,
        description: `Estoque: ${state.value.estoque}, Unidade: ${state.value.unidade_medida}, Validade: ${state.value.validade}`,
      });

      state.value.produto = "";
      state.value.unidade_medida = undefined;
      state.value.estoque = undefined;
      state.value.validade = new Date().toISOString().substr(0, 10);
      successMessage.value = data.value.message;
      errorMessage.value = "";
      emit("product-added");
      setTimeout(() => {
        closeModal();
      }, 1000);
    } else if (error) {
      console.error(error.value.data.message);
      errorMessage.value = error.value.data.message;
      successMessage.value = "";
    }
  } catch (error) {
    console.error(error);
  }
}

// Watchers
watch(localIsOpen, updateModelValue);

// Passar nome do produto sempre pra todas letras maiusculas caso o valor seja diferente de undefined
watch(
  () => state.value.produto,
  (newVal: string | undefined) => {
    if (newVal) {
      state.value.produto = newVal.toUpperCase();
    }
  }
);
</script>
