// https://nuxt.com/docs/api/configuration/nuxt-config
import tailwindcss from "@tailwindcss/vite";

export default defineNuxtConfig({
  compatibilityDate: "2025-07-15",
  devtools: { enabled: true },
  modules: ["@nuxt/fonts", "@nuxt/icon", "@nuxtjs/color-mode"],

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
