<template>
    <div class="w-full border-b-2 border-b-custom-gray border-opacity-20 bg-white">
        <div class="w-[80%] mx-auto py-4 flow-root">
            <router-link to="/"><img src="../../assets/RocketMGALogo.png" alt="RocketMGA Logo" class="h-[30px] float-left hover:cursor-pointer"></router-link>

            <button @click="logout" class="text-custom-dark-blue font-medium align-middle flex gap-4 hover:text-custom-red mt-[4px] float-right"><ArrowLeftOnRectangleIcon class="h-6" /> Logout</button>
        </div>
    </div>
</template>

<script>
import { ArrowLeftOnRectangleIcon } from '@heroicons/vue/24/solid'

export default {
    name: "NavBar",
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
    },
    components: {
        ArrowLeftOnRectangleIcon
    }
}
</script>