<template>
  <nav
    class="fixed top-0 left-0 right-0 flex justify-between items-center p-4 bg-transparent transition-all ease-in-out duration-300"
    :class="{ 'bg-opacity-90': isScrolled }">
    <NuxtLink to="/" class="flex items-center gap-2 text-2xl font-bold text-blue-500">
      <img src="logo.png" alt="Logo" class="w-8 h-8" />
      Almoxarifado Online
    </NuxtLink>

    <div class="flex space-x-1 lg:space-x-6">
      <ClientOnly v-if="isAuthenticated">
        <UButton @click="logout" color="red">
          Deslogar
        </UButton>
      </ClientOnly>

      <ClientOnly>
        <UButton :icon="isDark ? 'i-heroicons-moon-20-solid' : 'i-heroicons-sun-20-solid'"
          class="bg-gray-300 hover:bg-gray-400 text-gray-900" @click="toggleTheme" />
      </ClientOnly>
      <button class="lg:hidden">
        <!-- Ícone do menu hamburger aqui -->
      </button>
    </div>
  </nav>
</template>

<script lang="ts">
import { defineComponent, ref, computed, onMounted, onUnmounted } from 'vue';
import { useAuthStore } from '~/stores/authStore' // Importe a store

const auth = useAuthStore() // Use a store

// Estado para controlar se o modal está aberto ou fechado
const isOpen = ref(false);

// Função para abrir o modal
const openModal = () => {
  isOpen.value = true;
}

// Função para fechar o modal
const closeModal = () => {
  isOpen.value = false;
}

async function logout() {
  try {
    const headers = {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${auth.token}`  // Adicionando o token ao cabeçalho
    };

    const { data, error } = await useFetch(`${import.meta.env.VITE_BASE_API_URL}/logout`, {
      method: 'POST',
      headers: headers
    });

    if (data) {
      auth.logout();
      return navigateTo('/login')
    } else if (error) {
      console.error("Erro ao deslogar:", error);
      return navigateTo('/login')
    }
  } catch (error) {
    console.error(error);
  }
}

export default defineComponent({
  name: 'Navbar',
  setup() {
    const store = useAuthStore();
    const isAuthenticated = computed(() => auth.isAuthenticated);

    const colorMode = useColorMode();
    const isDark = computed({
      get() {
        return colorMode.value === 'dark';
      },
      set(val) {
        colorMode.preference = val ? 'dark' : 'light';
      }
    });

    const toggleTheme = () => {
      isDark.value = !isDark.value;
    }

    const isScrolled = ref(false);

    const handleScroll = () => {
      if (window.scrollY > 10) {
        isScrolled.value = true;
      } else {
        isScrolled.value = false;
      }
    }

    onMounted(() => {
      window.addEventListener('scroll', handleScroll);
    });

    onUnmounted(() => {
      window.removeEventListener('scroll', handleScroll);
    });

    return {
      isAuthenticated,
      isDark,
      toggleTheme,
      isScrolled,
      logout,
      isOpen,
      openModal,
      closeModal,
    }
  }
});
</script>