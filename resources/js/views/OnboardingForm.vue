<template>
    <!--Background Image-->
    <div class="w-full bottom-0 fixed z-0">
        <img src="../../assets/1.jpg" alt="Abstract Background Image">
    </div>

    <div class="z-10 relative mb-24">
        <NavBar />

        <agencyInfo v-if="step == 0" @next="next" />
        <carrierInfo v-else-if="step == 1" @back="back" @next="next" />
    </div>

    <Footer :class="step == 1 ? 'absolute' : 'relative'" class="bottom-0" />
</template>

<script>
import NavBar from '../components/navbar.vue'
import Footer from '../components/footer.vue'

import agencyInfo from '../components/form/agencyInfo.vue'
import carrierInfo from '../components/form/carrierInfo.vue'

export default {
    name: "Onboarding From",
    data() {
        return {
            steps: [
                'agencyInfo',
                'carrierInfo',
                'entityInfo', 
                'eoInfo', 
                'agreement'
            ],
            step: 0
        }
    },
    methods: {
        next(){
            this.step += 1
        },
        back() {
            this.step -= 1
        }
    },
    components: {
        NavBar,
        Footer,
        agencyInfo,
        carrierInfo
    }
}
</script>