// https://nuxt.com/docs/api/configuration/nuxt-config

//
export default defineNuxtConfig({
  compatibilityDate: "2025-05-15",
  devtools: { enabled: true },
  modules: ["@nuxtjs/tailwindcss", "@nuxtjs/color-mode", "@nuxt/eslint"],

  app: {
    head: {
      title: "Kanban",
      htmlAttrs: {
        lang: "en",
      },
    },
  },

  nitro: {
    devProxy: {
      "/api": {
        target: "http://127.0.01:8000",
        changeOrigin: true,
      },
    },
  },

  colorMode: {
    preference: "system",
    fallback: "light",
    classSuffix: "",
    storage: "cookie",
    storageKey: "color-mode",
  },

  tailwindcss: {
    config: {
      darkMode: "class",
    },
  },
});
