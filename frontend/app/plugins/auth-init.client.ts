import { useAuthStore } from '~/store/auth';

export default defineNuxtPlugin(async () => {
  const authStore = useAuthStore();
  
  // Initialiser l'auth au chargement de l'app côté client
  if (!authStore.user) {
    try {
      await authStore.fetchUser();
      console.log('🔐 User auto-login attempt:', authStore.user ? 'Success' : 'No session');
    } catch (error) {
      console.log('🔐 No active session found');
    }
  }
});