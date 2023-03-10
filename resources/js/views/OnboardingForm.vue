<template>
    <div class="w-full h-full md:w-screen md:h-screen grid md:grid-cols-5 bg-white">
        <!--Side Bar Navigation-->
        <NavBar class="grid md:col-span-2" :step="step" />
        
        <div class="grid md:col-span-3 relative">
            <div v-if="!completed">
                <agencyInfo v-if="step == 0" @next="next" />
                <carrierInfo v-else-if="step == 1" @back="back" @next="next" />
                <entityInfo v-else-if="step == 2" @back="back" @next="next" />
                <eoInfo v-else-if="step == 3" @back="back" @next="next" />
                <agreement v-else-if="step == 4" @back="back" @agreementCompleted="submit" :data="this.data" />
                <loadingFinalizeAppointment v-else-if="step == 5" />
            </div>

            <congrats v-else />

            <!--Footer-->
            <Footer />
        </div>
    </div>
</template>

<script>
import NavBar from '../components/navbar.vue'
import Footer from '../components/footer.vue'
import loading from '../components/loading.vue'

import agencyInfo from '../components/form/agencyInfo.vue'
import carrierInfo from '../components/form/carrierInfo.vue'
import entityInfo from '../components/form/entityInfo.vue'
import eoInfo from '../components/form/eoInfo.vue'
import agreement from '../components/form/agreement.vue'
import loadingFinalizeAppointment from '../components/form/loadingFinalizeAppointment.vue'
import congrats from '../components/form/congrats.vue'

import emailjs from '@emailjs/browser';

export default {
    name: "Onboarding From",
    data() {
        return {
            completed: false,
            steps: [
                'agencyInfo',
                'carrierInfo',
                'entityInfo', 
                'eoInfo', 
                'agreement'
            ],
            step: 0,
            data: {},
            api: {
                apiKey: '861fd4711f09ecbcf3f10f7a5cd449e22453abf3',
                // apiKey: '8135da5570abd90097a2bcc0dbbce76d1decd484', //SandBox,
                serviceID: 'service_nf9yozb',
                publicKey: 'h29zXRTKkaswfKPkp',
                dualTemplate: 'template_n43poh8',
                palomarTemplate: 'template_lsue7c3',
                // dualTemplate: 'template_93696xs', //Testing
                // palomarTemplate: 'template_asfertergfd', //Testing
                maxTemplate: 'template_ajztr2i',
                onboardingConfirmation: 'template_ymmtc35',
                neptuneError: 'template_utc3pnh'
            },
        }
    },
    async created() {
        await axios.get('/api/onboarding/check')
        .then(response => {
            if(response.data.message.completed){
                this.completed = true
                this.step = 5
            } else {
                this.data = response.data.message
            }
        })
    },
    methods: {
        next(){
            this.step += 1
        },
        back() {
            this.step -= 1
        },
        async submit() {
            this.completed = true
        }
    },
    components: {
        NavBar,
        Footer,
        agencyInfo,
        carrierInfo,
        entityInfo,
        eoInfo,
        agreement,
        congrats,
        loadingFinalizeAppointment,
        loading
    }
}
</script>