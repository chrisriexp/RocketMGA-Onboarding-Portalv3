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
            data: {}
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
            this.appointedStates.forEach(state => {
                if(!statesStr){
                    statesStr = state.name
                } else {
                    statesStr = `${statesStr},${state.name}`
                }
            })

            //Get Zip+4

            //Email Carriers and Max
            //Email to Dual
            emailjs.init(this.apiData.publicKey)
            emailjs.send(this.apiData.serviceID, this.apiData.dualTemplate, {
                agencyName: this.data.agency_name,
                agentName: this.onboardingInformation.first_name + " " + this.onboardingInformation.last_name,
                agentEmail: this.onboardingInformation.email,
                agentPhone: this.onboardingInformation.phone,
                agencyFullAddress: this.onboardingInformation.address
            })

            //Email to Palomar
            emailjs.init(this.apiData.publicKey)
            emailjs.send(this.apiData.serviceID, this.apiData.palomarTemplate, {
                agencyName: this.onboardingInformation.agency_name,
                agentName: this.onboardingInformation.first_name + " " + this.onboardingInformation.last_name,
                agentEmail: this.onboardingInformation.email,
                agentPhone: this.onboardingInformation.phone,
                agencyFullAddress: this.onboardingInformation.address
            })

            //Sub Agent Welcome Email
            emailjs.init(this.apiData.publicKey)
            emailjs.send(this.apiData.serviceID, this.apiData.onboardingConfirmation, {
                agencyName: this.onboardingInformation.agency_name,
                toEmail: this.onboardingInformation.email,
            })

            //Email to Max
            emailjs.init(this.apiData.publicKey)
            emailjs.send(this.apiData.serviceID, this.apiData.maxTemplate, {
                agentName: this.onboardingInformation.first_name + " " + this.onboardingInformation.last_name,
                agencyName: this.onboardingInformation.agency_name,
                agencyDBAname: this.onboardingInformation.dba_name,
                agencyType: this.onboardingInformation.agency_type,
                agentLicense: this.onboardingInformation.agent_license,
                agentLicenseEff: this.onboardingInformation.agent_license_eff,
                agentLicenseExp: this.onboardingInformation.agent_license_exp,
                NPN: this.onboardingInformation.agent_npn,
                rocketMGAid: this.onboardingInformation.rocketMGA_id,
                agencyEmail: this.onboardingInformation.email,
                agencyPhone: this.onboardingInformation.phone,
                agencyFullAddress: this.onboardingInformation.address,
                additionalStates: statesStr
            })

            //Update database submitted and stage
            await axios.put('/api/user/update/' + this.userID, {
                "user": {
                    "stage": "Submitted",
                    "submitted": true
                }
            })

            //Create new completed Notification for Backend
            await axios.post('/api/notification/add', {
                "agency": this.onboardingInformation.agency_name,
                "agency_id": this.userID
            })

            //Send Flow and Palomar Credentials to Appointed Agents Table
            await axios.post('/api/appointed/add', {
                "rocketMGA_id": this.onboardingInformation.rocketMGA_id,
                "flow": this.onboardingInformation.email,
                "palomar": this.onboardingInformation.email
            })

            //Check and Add to Zoho
            await axios.post('https://shielded-ridge-03597.herokuapp.com/https://hooks.zapier.com/hooks/catch/14170682/bjht2i9/', {
                "agency_name": this.onboardingInformation.agency_name,
                "agent_name": this.onboardingInformation.first_name + " " + this.onboardingInformation.last_name,
                "phone": this.onboardingInformation.phone.replace(/[() -]/g, ''),
                "email": this.onboardingInformation.email,
                "adress": this.onboardingInformation.address_1 + " " + this.onboardingInformation.address_2,
                "city": this.onboardingInformation.city,
                "state": this.onboardingInformation.state,
                "zip": this.onboardingInformation.zip,
                "npn": this.onboardingInformation.agent_npn,
                "source": "Onboarding Portal"
            })

            //Send Data to Wright Sheet
            await axios.post('https://shielded-ridge-03597.herokuapp.com/https://hooks.zapier.com/hooks/catch/14170682/bjh4r3i/',  {
                "name": this.onboardingInformation.agency_name,
                "dba_name": this.onboardingInformation.dba_name,
                "phone": this.onboardingInformation.phone,
                "email": this.onboardingInformation.email,
                "agency_type": this.onboardingInformation.agency_type,
                "address1": this.onboardingInformation.address_1,
                "address2": this.onboardingInformation.address_2,
                "city": this.onboardingInformation.city,
                "state": this.onboardingInformation.state,
                "zip": this.onboardingInformation.zip,
                "agent_name": this.onboardingInformation.first_name + " " + this.onboardingInformation.last_name,
                "agent_license": this.onboardingInformation.agent_license,
                "license_eff": this.onboardingInformation.agent_license_eff,
                "license_exp": this.onboardingInformation.agent_license_exp,
                "npn": this.onboardingInformation.agent_npn,
                "week": week
            })

            //Send Data to Aon Sheet
            await axios.post('https://shielded-ridge-03597.herokuapp.com/https://hooks.zapier.com/hooks/catch/14170682/bjtgf41', {
                "name": this.onboardingInformation.agency_name,
                "phone": this.onboardingInformation.phone,
                "email": this.onboardingInformation.email,
                "address": this.onboardingInformation.address_1 + " " + this.onboardingInformation.address_2,
                "city": this.onboardingInformation.city,
                "state": this.onboardingInformation.state,
                "zip": this.onboardingInformation.zip,
                "additional_states": statesStr,
                "first_name": this.onboardingInformation.first_name,
                "last_name": this.onboardingInformation.last_name,
                "week": week
            })

            this.neptuneAPI()

            this.submitted = true

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