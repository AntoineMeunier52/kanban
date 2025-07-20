// https://nuxt.com/docs/api/configuration/nuxt-config
import tailwindcss from "@tailwindcss/vite";

export default defineNuxtConfig({
  compatibilityDate: "2025-07-15",
  devtools: { enabled: true },
  modules: ["@nuxt/fonts", "@nuxt/icon", "@nuxtjs/color-mode"],

  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || "https://127.0.0.1:8000",
    },
  },

  nitro: {
    devProxy: {
      "^/api/.*": {
        target: "https://127.0.0.1:8000",
        changeOrigin: true,
        //prependPath: true,
        secure: false,
      },
    },
  },

  css: ["~/assets/css/main.css"],

  vite: {
    plugins: [tailwindcss()],
  },

  colorMode: {
    preference: "light",
    fallback: "light",
    storage: "cookie",
  },

  icon: {
    mode: "css",
    cssLayer: "base",
  },

  fonts: {
    fontshare: {
      id: ["Stardom"],
    },
  },
});
