export default defineNuxtPlugin(() => {
  // S'assurer que toutes les requêtes API se font côté client uniquement
  if (import.meta.client) {
    console.log('🚀 API client plugin loaded');
  }
});