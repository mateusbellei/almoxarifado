export default defineNuxtConfig({
  devtools: { enabled: true },

  ssr: true,

  modules: [
    '@nuxt/ui',
    '@nuxtjs/tailwindcss',
    '@pinia/nuxt',
  ],
})