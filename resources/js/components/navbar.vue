<template>
    <div class="w-full grid relative bg-custom-light-blue-bg bg-opacity-30">
        <!--Background Image-->
        <div class="hidden md:block absolute top-0 right-0 z-0">
            <img src="../../assets/portal_left_bg.png" alt="Abstract Background Image" class="">
        </div>

        <div class="z-10 grid w-fit h-fit mx-auto gap-10 px-6 md:px-0">
            <div class="w-full flow-root">
                <button @click="logout" class="md:hidden w-fit mt-12 flex gap-4 py-2 px-8 bg-white text-[16px] text-custom-dark-blue font-semibold rounded-md border-l-[4px] border-b-[5px] border-custom-gray border-opacity-5 active:border-white active:border-opacity-100 shadow-newdrop float-right"><ArrowLeftOnRectangleIcon class="h-[24px]" />Logout</button>
            </div>
            
            <!--Rocket MGA Logo-->
            <img src="../../assets/RocketMGALogo.png" alt="Rocket MGA Logo" class="mx-auto md:mx-0 h-[36px] md:mt-48">

            <div class="flex gap-6 h-fit w-fit mb-12 md:mb-0">
                <img :src="`images/${step}-progress.png`" alt="Progress Bar" class="hidden md:block">
                <img :src="`images/${step}-progress-mobile.png`" alt="Progress Bar" class="md:hidden">
            </div>

            <button @click="logout" class="hidden w-fit mt-12 md:flex gap-4 py-2 px-8 bg-white text-[16px] text-custom-dark-blue font-semibold rounded-md border-l-[4px] border-b-[5px] border-custom-gray border-opacity-5 active:border-white active:border-opacity-100 shadow-newdrop"><ArrowLeftOnRectangleIcon class="h-[24px]" />Logout</button>
        </div>
    </div>
</template>

<script>
import { ArrowLeftOnRectangleIcon } from '@heroicons/vue/24/solid'

export default {
    name: "NavBar",
    props: {
        step: Number
    },
    methods: {
        async logout(){
            await axios.get('/api/logout')
            .then(response => {
                localStorage.removeItem('tokens')
                this.$router.push({name: 'Login'})
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