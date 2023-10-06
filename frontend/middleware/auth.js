import { useAuthStore } from '~/stores/authStore'

export default defineNuxtRouteMiddleware((to) => {
  const auth = useAuthStore();
  auth.loadToken();

  if (auth.isLoading) {
    return;
  } 

  if (to.name === 'login' && auth.isAuthenticated) {
    return navigateTo('/estoque', { replace: true })
  }
  
  if (to.name !== 'login' && !auth.isAuthenticated) {
    return navigateTo('/login', { replace: true })
  }
});
