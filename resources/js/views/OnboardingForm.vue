<template>
    <h1>Onboarding Form</h1>
    <button @click="logout">Logout</button>
</template>

<script>
import axios from 'axios';


export default {
    name: "Onboarding From",
    methods: {
        async logout(){
            await axios.get('/api/logout')
            .then(response => {
                localStorage.removeItem('tokens')
                this.$router.push({name: 'Home'})
                this.$alert({
                    title: 'Logout',
                    text: response.data.message,
                    type: 'success'
                })
            })
        }
    }
}
</script>