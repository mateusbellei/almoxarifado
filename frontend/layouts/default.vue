<template>
  <div>
    <Navbar class="z-[9999]" />
    <div class="pt-12 lg:pt-16">
      <slot/>
    </div>
  </div>
</template>

<script setup lang="ts">
useHead({
  titleTemplate: (titleChunk): string => {
    return titleChunk ? `Almoxarifado Online - ${titleChunk}` : 'Almoxarifado Online'
  },
});
</script>
