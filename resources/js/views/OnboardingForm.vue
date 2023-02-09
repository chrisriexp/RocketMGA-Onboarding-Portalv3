<template>
    <!--Background Image-->
    <div class="w-full bottom-0 fixed z-0">
        <img src="../../assets/1.jpg" alt="Abstract Background Image">
    </div>

    <div class="z-10 relative mb-24">
        <NavBar />

        <div v-if="!completed">
            <agencyInfo v-if="step == 0" @next="next" />
            <carrierInfo v-else-if="step == 1" @back="back" @next="next" />
            <entityInfo v-else-if="step == 2" @back="back" @next="next" />
            <eoInfo v-else-if="step == 3" @back="back" @next="next" />
            <agreement v-else-if="step == 4" @back="back" @submit="submit" />
            
            <div v-else-if="step == 5" class="grid gap-8 justify-items-center w-fit h-fit mx-auto mt-24">
                <loading />
                <h2 class="text-center text-2xl text-custom-dark-blue font-medium">Please wait while we finalize your appointment..</h2>
            </div>
        </div>

        <div v-else class="">
            <congrats class="" />
        </div>
    </div>

    <Footer :class="step == 1 || step == 3 || step == 5 || completed ? 'absolute' : 'relative'" class="bottom-0" />
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
import congrats from '../components/form/congrats.vue'

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
                serviceID: 'service_nf9yozb',
                publicKey: 'h29zXRTKkaswfKPkp',
                dualTemplate: 'template_n43poh8',
                palomarTemplate: 'template_lsue7c3',
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
            const names = this.data.agent_name.split(" ")
            const first_name = names[0]
            const last_name = names[1]

            this.step += 1

            //Add Aon and Wright Data to Google Sheets
            //Get the date of the beginning of the week
            let date = new Date();
            let day = date.getDay() || 7;
            if( day != 1 ){
                date.setHours(-24 * (day));
            };

            let date_last = new Date();
            let day_last = date_last.getDay() || 7;
            if( day_last != 1 ){
                date_last.setHours(-24 * (day_last - 6));
            };

            const weekStart = this.formatDate(date)
            const weekEnd = this.formatDate(date_last)
            const week = weekStart + " - " + weekEnd

            //Create Additional States String
            let statesStr = ""
            this.data.additional_states.forEach(state => {
                if(!statesStr){
                    statesStr = state.name
                } else {
                    statesStr = `${statesStr},${state.name}`
                }
            })

            //Get Zip+4

            //Email Carriers and Max
            //Email to Dual
            emailjs.init(this.api.publicKey)
            emailjs.send(this.api.serviceID, this.api.dualTemplate, {
                agencyName: this.data.agency_name,
                agentName: this.data.agent_name,
                agentEmail: this.data.email,
                agentPhone: this.data.phone,
                agencyFullAddress: this.data.address
            })

            //Email to Palomar
            emailjs.init(this.api.publicKey)
            emailjs.send(this.api.serviceID, this.api.palomarTemplate, {
                agencyName: this.data.agency_name,
                agentName: this.data.agent_name,
                agentEmail: this.data.email,
                agentPhone: this.data.phone,
                agencyFullAddress: this.data.address
            })

            //Sub Agent Welcome Email
            emailjs.init(this.api.publicKey)
            emailjs.send(this.api.serviceID, this.api.onboardingConfirmation, {
                agencyName: this.data.agency_name,
                toEmail: this.data.email,
            })

            //Email to Max
            emailjs.init(this.api.publicKey)
            emailjs.send(this.api.serviceID, this.api.maxTemplate, {
                agentName: this.data.agent_name,
                agencyName: this.data.agency_name,
                agencyDBAname: this.data.dba_name,
                agencyType: this.data.agency_type,
                agentLicense: this.data.agent_license,
                agentLicenseEff: this.data.agent_license_eff,
                agentLicenseExp: this.data.agent_license_exp,
                NPN: this.data.agent_npn,
                rocketMGAid: this.data.rocket_id,
                agencyEmail: this.data.email,
                agencyPhone: this.data.phone,
                agencyFullAddress: this.data.address,
                additionalStates: statesStr
            })

            //Update database submitted and stage
            await axios.post('/api/onboarding/update',{
                "data": {
                    "stage": "Submitted",
                    "completed": true
                }
            })

            // //Create new completed Notification for Backend
            // await axios.post('/api/notification/add', {
            //     "agency": this.data.agency_name,
            //     "agency_id": this.userID
            // })

            // //Send Flow and Palomar Credentials to Appointed Agents Table
            // await axios.post('/api/appointed/add', {
            //     "rocket_id": this.data.rocket_id,
            //     "flow": this.data.email,
            //     "palomar": this.data.email
            // })

            //Check and Add to Zoho
            await axios.post('https://shielded-ridge-03597.herokuapp.com/https://hooks.zapier.com/hooks/catch/14170682/bjht2i9/', {
                "agency_name": this.data.agency_name,
                "agent_name": this.data.agent_name,
                "phone": this.data.phone.replace(/[() -]/g, ''),
                // "email": this.data.email,
                "email": "chrisriexp@gmail.com",
                "adress": this.data.address_1 + " " + this.data.address_2,
                "city": this.data.city,
                "state": this.data.state,
                "zip": this.data.zip,
                "npn": this.data.agent_npn,
                "source": "Onboarding Portal"
            })

            //Send Data to Wright Sheet
            await axios.post('https://shielded-ridge-03597.herokuapp.com/https://hooks.zapier.com/hooks/catch/14170682/bjh4r3i/',  {
                "name": this.data.agency_name,
                "dba_name": this.data.dba_name,
                "phone": this.data.phone,
                "email": this.data.email,
                "agency_type": this.data.agency_type,
                "address1": this.data.address_1,
                "address2": this.data.address_2,
                "city": this.data.city,
                "state": this.data.state,
                "zip": this.data.zip,
                "agent_name": this.data.agent_name,
                "agent_license": this.data.agent_license,
                "license_eff": this.data.agent_license_eff,
                "license_exp": this.data.agent_license_exp,
                "npn": this.data.agent_npn,
                "week": week
            })

            //Send Data to Aon Sheet
            await axios.post('https://shielded-ridge-03597.herokuapp.com/https://hooks.zapier.com/hooks/catch/14170682/bjtgf41', {
                "name": this.data.agency_name,
                "phone": this.data.phone,
                "email": this.data.email,
                "address": this.data.address_1 + " " + this.data.address_2,
                "city": this.data.city,
                "state": this.data.state,
                "zip": this.data.zip,
                "additional_states": statesStr,
                "first_name": first_name,
                "last_name": last_name,
                "week": week
            })

            // this.neptuneAPI()

            // this.submitted = true

            console.log('submit')
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
        loading
    }
}
</script>