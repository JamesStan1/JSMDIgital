<template>
  <div class="app-wrapper">
    <NavBar v-if="!$route.meta.isDashboard" />
    <main>
      <RouterView />
    </main>
    <FooterSection v-if="!$route.meta.isDashboard" />
  </div>
</template>

<script setup>
import NavBar from '@/components/NavBar.vue'
import FooterSection from '@/components/FooterSection.vue'
</script>

<style>
.app-wrapper {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

main {
  flex: 1;
}
</style>
