export default defineNuxtPlugin(() => {
  // Configuration SSL côté serveur pour le développement
  if (process.server && process.dev) {
    // Désactiver la vérification des certificats SSL en développement
    process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0';
  }
});