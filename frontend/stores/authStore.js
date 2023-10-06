import { defineStore } from 'pinia'
import { setStorageItem, getStorageItem, removeStorageItem } from '~/utils/local_storage';

const TOKEN_KEY = 'EnfAuthToken';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: getStorageItem(TOKEN_KEY) || null,
    isLoading: true,
  }),
  getters: {
    isAuthenticated: (state) => !!state.token
  },
  actions: {
    async setToken(token) {
      this.token = token;
      setStorageItem(TOKEN_KEY, token);
      this.isLoading = false;
    },
    async loadToken() {
      this.token = getStorageItem(TOKEN_KEY);
      if (this.token) {
        this.isLoading = false;
      } else if (process.client) {
        this.isLoading = false;
      }
    },
    async clearToken() {
      this.token = null;
      removeStorageItem(TOKEN_KEY);
      this.isLoading = false;
    },
    async logout() {
      await this.clearToken();
    }
  }
});

