// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  devtools: { enabled: true },

  modules: [
    "@nuxt/ui",
    "@nuxt/eslint",
    "@nuxt/fonts",
    "@nuxt/icon",
    "@pinia/nuxt",
  ],

  css: ["~/assets/css/main.css"],

  // Configuration pour éviter les erreurs TLS en développement
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || "https://127.0.0.1:8000",
    },
  },

  // Configuration SSR pour éviter les problèmes de TLS
  ssr: true,

  // Configuration pour le développement
  devServer: {
    https: false, // Force HTTP en développement
  },

  nitro: {
    // Configuration pour le développement
    devProxy: {
      "/api": {
        target: "https://127.0.0.1:8000",
        changeOrigin: true,
        secure: false, // Ignore les certificats SSL auto-signés
        ws: true,
      },
    },
    // Désactiver HTTPS pour éviter les erreurs TLS
    experimental: {
      wasm: true,
    },
  },

  future: {
    compatibilityVersion: 4,
  },

  compatibilityDate: "2024-11-27",
});
