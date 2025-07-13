export default defineNuxtPlugin(() => {
  // Configuration pour éviter les erreurs TLS en développement côté client
  if (process.client && process.dev) {
    // Désactiver la vérification TLS pour le développement
    // Cette configuration ne s'applique que côté client en développement
    const originalFetch = window.fetch;
    window.fetch = async (input, init = {}) => {
      // Pour les requêtes vers notre API en développement
      if (typeof input === 'string' && input.includes('127.0.0.1:8000')) {
        return originalFetch(input, {
          ...init,
          // Ajouter des headers pour éviter les problèmes CORS
          headers: {
            'Content-Type': 'application/json',
            ...init.headers,
          },
        });
      }
      return originalFetch(input, init);
    };
  }
});