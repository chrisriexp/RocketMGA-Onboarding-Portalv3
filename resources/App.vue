<script setup>
import { RouterView } from 'vue-router'
</script>

<template>
  <RouterView />
    <notifications />
</template>

<style>
/**Font from google font */
#app {
  font-family: 'Montserrat', sans-serif;
}

body {
  background-color: #F5F5F5;
}

@media (-webkit-device-pixel-ratio: 1.25) {
    :root {
      zoom: 0.8;
    }
}

/* Chrome, Safari, Edge, Opera */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
/* Firefox */
input[type="number"] {
    -moz-appearance: textfield;
}
</style>