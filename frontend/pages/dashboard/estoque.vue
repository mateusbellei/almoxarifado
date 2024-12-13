<template>
  <Head>
    <Title>Almoxarifado</Title>
  </Head>

  <Body>
    <section v-if="auth.isLoading">
      <Spinner />
    </section>
    <section v-else>
      <div class="px-4 mt-8">
        {{ products }}
        <div class="flex flex-col gap-4 lg:gap-0 lg:flex-row justify-between">
          <div class="flex items-center gap-2">
            <UIcon
              name="i-heroicons-magnifying-glass"
              class="w-6 h-6 hover:bg-blue-600 cursor-pointer"
              @click="fetchProducts"
            />
            <UInput
              v-model="searchQuery"
              @keyup.enter="fetchProducts"
              placeholder="Pesquisar produto..."
              class="lg:w-96"
              type="text"
            />
          </div>
          <div class="flex items-center gap-2 mt-4">
            <div>
              <p class="text-xs">Ordenar Por:</p>
              <USelectMenu
                v-model="sortOption"
                :options="[
                  { value: 'produto', label: 'Nome' },
                  { value: 'estoque', label: 'Estoque' },
                  { value: 'validade', label: 'Validade' },
                ]"
                placeholder="Ordenar por..."
                class="w-48"
                @change="updateSortOption"
              />
            </div>
            <div>
              <p class="text-xs">Ordem:</p>
              <USelectMenu
                v-model="sortOrder"
                :options="[
                  { value: 'asc', label: 'Crescente' },
                  { value: 'desc', label: 'Decrescente' },
                ]"
                placeholder="Ordem..."
                class="w-48"
                @change="updateSortOrder"
              />
            </div>
          </div>
          <UButton
            @click="openModal"
            class="bg-blue-500 text-white hover:bg-blue-600"
          >
            + Adicionar Estoque
          </UButton>
        </div>
        <div class="flex flex-col w-full">
          <div class="flex border-b-[1px] mt-4 rounded-lg">
            <div class="px-4 py-2 w-1/5 font-bold">Nome</div>
            <div class="px-4 py-2 w-1/5 font-bold">Unidade Medida</div>
            <div class="px-4 py-2 w-1/5 font-bold">Estoque</div>
            <div class="px-4 py-2 w-1/5 font-bold">Validade</div>
            <div class="px-4 py-2 w-1/5 font-bold">Ações</div>
          </div>
          <div
            class="flex border-b-[1px]"
            v-for="product in products"
            :key="product.id"
          >
            <div class="px-4 py-2 w-1/5">{{ product.produto }}</div>
            <div class="px-4 py-2 w-1/5">
              {{
                product
                  ? product.unidade_medida.charAt(0).toUpperCase() +
                    product.unidade_medida.slice(1)
                  : "Loading..."
              }}
            </div>
            <div class="px-4 py-2 w-1/5">{{ product.estoque }}</div>
            <div class="px-4 py-2 w-1/5">
              {{ product.validade }}
            </div>
            <div class="px-4 py-2 w-1/5 flex space-x-2">
              <!-- Adicionada coluna Ações -->
              <UButton @click="openEditModal(product)">Editar</UButton>
              <UButton color="red" @click="deleteProduct(product.id)"
                >Excluir</UButton
              >
            </div>
          </div>
        </div>
        <div class="mt-4 flex items-center justify-end space-x-1">
          <UButton
            v-for="link in links"
            :key="link.label"
            @click="changePage(link.url)"
            class="px-4 py-2"
            v-html="link.label"
          >
          </UButton>
          <p>Página: {{ page }}</p>
        </div>
      </div>
    </section>
    <ModalAddStock
      v-model="isOpen"
      @product-added="fetchProducts"
      @close="closeModal"
    />
    <ModalEditStock
      v-model="isEditOpen"
      :product="selectedProduct"
      @stock-updated="fetchProducts"
      @close="closeEditModal"
    />
  </Body>
</template>

<script setup>
import { ref, watch } from "vue";
import { useAuthStore } from "~/stores/authStore"; // Importe a store

const auth = useAuthStore(); // Use a store

definePageMeta({
  middleware: ["auth"],
});

const products = ref({});
const links = ref({});
const searchQuery = ref("");
const page = ref(1);
const pageCount = ref(30);

const sortOption = ref("produto"); // Opção padrão: Nome
const sortOrder = ref("asc"); // Ordem padrão: Crescente

const isOpen = ref(false);
const isEditOpen = ref(false);

const selectedProduct = ref(null);

// Função para abrir o modal
const openModal = () => {
  isOpen.value = true;
};

// Função para fechar o modal
const closeModal = () => {
  isOpen.value = false;
};

async function openEditModal(product) {
  try {
    const response = await fetch(
      `${import.meta.env.VITE_BASE_API_URL}/product/${product.id}`,
      {
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${auth.token}`,
        },
      }
    );

    if (response.ok) {
      const updatedProduct = await response.json();
      selectedProduct.value = updatedProduct.produto; // Atualize esta linha de acordo com a estrutura do seu objeto de resposta
      isEditOpen.value = true;
    } else {
      console.error(
        "Erro ao buscar o produto específico:",
        await response.text()
      );
    }
  } catch (error) {
    console.error("Erro ao buscar o produto específico:", error);
  }
}

const closeEditModal = () => {
  selectedProduct.value = null;
  isEditOpen.value = false;
};

async function fetchProducts() {
  const headers = {
    "Content-Type": "application/json",
    Authorization: `Bearer ${auth.token}`,
  };

  try {
    const response = await fetch(
      `${import.meta.env.VITE_BASE_API_URL}/product?name=${encodeURIComponent(
        searchQuery.value
      )}&page=${page.value}&perPage=${pageCount.value}&sort=${
        sortOption.value
      }&order=${sortOrder.value}`,
      { headers }
    );

    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`);
    }

    const data = await response.json();
    products.value = data.produtos.data;
    links.value = data.produtos.links;
  } catch (error) {
    console.error("Erro ao buscar produtos:", error);
  }
}

fetchProducts();

watch([page], fetchProducts);
watch(selectedProduct, (newVal) => {
  if (newVal) {
    isEditOpen.value = true;
  }
});
watch([sortOption, sortOrder], () => {
  fetchProducts();
});

const changePage = (url) => {
  if (!url || typeof url !== "string" || !url.startsWith("http")) return;

  const urlObj = new URL(url);
  const newPage = urlObj.searchParams.get("page");
  if (newPage) {
    page.value = newPage;

    // Recarregar produtos mantendo a ordenação
    fetchProducts();
  }
};

function updateSortOption(option) {
  sortOption.value = option.value; // Extrai apenas o campo 'value'
}

function updateSortOrder(option) {
  sortOrder.value = option.value; // Extrai apenas o campo 'value'
}

async function deleteProduct(productId) {
  const confirmation = window.confirm(
    "Tem certeza que deseja excluir este produto?"
  );

  if (!confirmation) {
    return; // Se o usuário cancelar, simplesmente retorne e não faça nada.
  }

  const headers = {
    "Content-Type": "application/json",
    Authorization: `Bearer ${auth.token}`,
  };

  try {
    const response = await fetch(
      `${import.meta.env.VITE_BASE_API_URL}/product/${productId}`,
      {
        method: "DELETE",
        headers: headers,
      }
    );

    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`);
    }

    console.log("Produto excluído com sucesso!");
    fetchProducts(); // Atualize a lista após a exclusão.
  } catch (error) {
    console.error("Erro ao excluir produto:", error);
  }
}
</script>
