<template>
    <NavBar :active="'dashboard'" />

    <div class="p-4 sm:ml-64">
        <h1>Hello</h1>
    </div>
</template>

<script>
import NavBar from '../../components/admin/navbar.vue'

export default {
    name: "Admin Dashboard",
    components: {
        NavBar
    }
}
</script>