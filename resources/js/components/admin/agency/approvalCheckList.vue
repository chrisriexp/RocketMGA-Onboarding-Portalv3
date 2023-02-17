<template>
    <div class="grid gap-8 my-12">
        <div class="flow-root gap-8 w-full pb-4 border-b-2 border-custom-gray border-opacity-20">
            <div class="float-left grid">
                <h2 class="flex gap-2 text-custom-orange font-medium text-2xl"><ExclamationTriangleIcon class="h-10" /><span class="my-auto">Application Approval Required!!</span></h2>
                <p class="text-md text-custom-gray font-light">Please validate the below items to activate the agency appointment..</p>
            </div>

            <button @click="approveAgency" class="float-right px-4 py-2 flex gap-2 bg-custom-dark-blue text-white text-xl rounded-md h-fit my-auto">
                <CheckBadgeIcon class="h-8" />
                <p class="my-auto">Approve Agency</p>
            </button>
        </div>

        <div class="grid gap-2">
            <div :class="valid.license && valid.license_file ? 'text-green-400' : 'text-custom-red'" class="flex gap-4 w-fit h-fit">
                <CheckCircleIcon class="h-10"/>
                <p class="text-2xl my-auto">Agency License</p>
            </div>

            <div :class="valid.license ? 'text-green-400' : 'text-custom-red'" class="flex gap-4 w-fit h-fit ml-24">
                <button @click="approve('license')" :disabled="this.valid.license"><CheckCircleIcon class="h-6 my-auto"/></button>
                <p class="text-lg my-auto">License Number - </p>
                <textInput @inputUpdate="inputChange" :inputValue="form.agency_license" :id="'agency_license'" :isdisabled=!update />
            </div>

            <div :class="valid.license_file ? 'text-green-400' : 'text-custom-red'" class="flex gap-4 w-fit h-fit ml-24">
                <button @click="approve('license_file')" :disabled="this.valid.license_file"><CheckCircleIcon class="h-6 my-auto"/></button>
                <a target="_blank" :href="agency_license_file" class="flex gap-2 my-auto text-lg">View License File</a>
            </div>
        </div>

        <div class="grid gap-2">
            <div :class="valid.eo_file && valid.eo_exp && valid.eo_limit ? 'text-green-400' : 'text-custom-red'" class="flex gap-4 w-fit h-fit">
                <CheckCircleIcon class="h-10"/>
                <p class="text-2xl my-auto">Agency E&O</p>
            </div>

            <div :class="valid.eo_exp ? 'text-green-400' : 'text-custom-red'" class="flex gap-4 w-fit h-fit ml-24">
                <button @click="approve('eo_exp')" :disabled="this.valid.eo_exp"><CheckCircleIcon class="h-6 my-auto"/></button>
                <p class="text-lg my-auto">E&O Exp Date - </p>
                <textInput @inputUpdate="inputChange" :inputValue="form.eo_exp" :id="'eo_exp'" :isdisabled=!update :date=true />
            </div>

            <div :class="valid.eo_limit ? 'text-green-400' : 'text-custom-red'" class="flex gap-4 w-fit h-fit ml-24">
                <button @click="approve('eo_limit')" :disabled="this.valid.eo_limit"><CheckCircleIcon class="h-6 my-auto"/></button>
                <p class="text-lg my-auto">E&O Limit - </p>
                <textInput @inputUpdate="inputChange" :inputValue="form.eo_limit" :id="'eo_limit'" :isdisabled=!update />
            </div>

            <div :class="valid.eo_file ? 'text-green-400' : 'text-custom-red'" class="flex gap-4 w-fit h-fit ml-24">
                <button @click="approve('eo_file')" :disabled="this.valid.eo_file"><CheckCircleIcon class="h-6 my-auto"/></button>
                <a target="_blank" :href="eo" class="flex gap-2 my-auto text-lg">View E&O File</a>
            </div>
        </div>

        <div class="grid gap-2">
            <div :class="valid.agreement ? 'text-green-400' : 'text-custom-red'" class="flex gap-4 w-fit h-fit">
                <button @click="approve('agreement')" :disabled="this.valid.agreement"><CheckCircleIcon class="h-10"/></button>
                <button @click="downloadAgreement" :disabled="agreement_disabled" class="text-2xl my-auto disabled:opacity-40">View Executed Contracts</button>
            </div>
        </div>
    </div>
</template>

<script>
import textInput from '../textInput.vue'
import { ExclamationTriangleIcon, CheckCircleIcon, EyeIcon, CheckBadgeIcon } from '@heroicons/vue/24/solid'
import axios from 'axios'
import emailjs from '@emailjs/browser';

export default {
    name: 'Approval Check List',
    props: {
        rocket_id: String,
        data: Object,
        update: Boolean
    },
    data() {
        return {
            agreement_disabled: false,
            api: {
                apiKey: '8135da5570abd90097a2bcc0dbbce76d1decd484',
                serviceID: 'service_nf9yozb',
                publicKey: 'h29zXRTKkaswfKPkp',
                trainingInvite: 'template_b3uxk2g'
            },
            valid: {},
            agency_license_file: '',
            eo: '',
            form: {
                eo: '',
                eo_exp: '',
                eo_limit: '',
                agency_license: '',
                agency_license_file: '',
                document_id: '' 
            },
            errors: [
                {
                    name: 'eo_exp',
                    errors: []
                },
                {
                    name: 'eo_limit',
                    errors: []
                },
                {
                    name: 'agency_license',
                    errors: []
                }
            ],
            appointments: {
                aon: false,
                beyond: false,
                dual: false,
                flow: false,
                neptune: false,
                palomar: false,
                sterling: false,
                wright: false
            }
        }
    },
    async created(){
        const keys = Object.keys(this.form)
        keys.forEach(key => {
            this.form[key] = this.data[key]
        })

        const appointmenKeys = Object.keys(this.appointments)
        appointmenKeys.forEach(key => {
            this.appointments[key] = this.data[key]

            if(this.appointments[key] == 0){
                this.appointments[key] = false
            } else if(this.appointments[key] == 1){
                this.appointments[key] = true
            }
        })

        await axios.post('/api/approval', {"rocket_id": this.rocket_id})
        .then(response => {
            this.valid = response.data
        })

        const validKeys = Object.keys(this.valid)

        validKeys.forEach(key => {
            if(this.valid[key] == 1){
                this.valid[key] = true
            } else if(this.valid[key] == 0) {
                this.valid[key] = false
            }
        })

        const files = ['agency_license_file', 'eo']

        files.forEach(file => {
            axios.get('/api/file/' + this.form[file])
            .then(response => {
                this[file] = "http://localhost:8000" + response.data.path
            })
        })
    },
    mounted() {
        const keys = Object.keys(this.form)
        keys.forEach(key => {
            this.form[key] = this.data[key]
        })
    },
    methods: {
        async approveAgency() {
            this.$emit('loading')
            let approved = true

            await axios.post('/api/approval', {
                "rocket_id": this.rocket_id,
                "valid": true
            })
            .then(response => {
                this.$alert({
                    title: 'Approval Confirmed',
                    text: response.data.message,
                    type: 'success',
                    duration: 3000
                })
            })
            .catch(error => {
                this.$alert({
                    title: 'Approval Error',
                    text: error.response.data.message,
                    type: 'error'
                })

                approved = false
            })

            if(approved){
                const carriers = ['AON Edge', 'Beyond Flood', 'Dual Flood', 'Flow Flood', 'Neptune', 'Palomar', 'Sterling', 'Wright']
                const checkCarriers= ['aon', 'beyond', 'dual', 'flow', 'neptune', 'palomar', 'sterling', 'wright']
                let i = 0

                await axios.post('/api/onboarding', {
                    "rocket_id": this.rocket_id,
                    "approved": true
                })

                let updates = {}

                await axios.post('/api/appointment', {"rocket_id": this.rocket_id})
                await axios.post('/api/appointment', {"rocket_id": this.rocket_id})
                .then(response => {
                    carriers.forEach(carrier => {
                        let check = checkCarriers[i]
                        let details = JSON.parse(response.data.appointments[carrier])

                        if(this.appointments[check] == true){
                            details.direct = true
                            updates[carrier] = details
                        } else if (carrier == 'Flow Flood' || carrier == 'Palomar' && this.appointments[check] == false){
                            if(carrier == 'Flow Flood'){
                                details.email = this.data.email
                            } else if (carrier == 'Palomar'){
                                details.AgentId = this.data.email
                            }

                            updates[carrier] = details
                        }

                        i += 1
                    })

                    i = 0
                })

                let logins = {}
                await axios.post('/api/logins', {"rocket_id": this.rocket_id})
                .then(response => {
                    carriers.forEach(carrier => {
                        let check = checkCarriers[i]
                        let details = JSON.parse(response.data.logins[carrier])

                        if(this.appointments[check] == true){
                            details.direct = true
                            logins[carrier] = details
                        }

                        i += 1
                    })

                    i = 0
                })

                await axios.post('/api/logins', {
                    "rocket_id": this.rocket_id,
                    "update": logins
                })


                await axios.post('/api/appointment', {
                    "rocket_id": this.rocket_id,
                    "update": updates
                })

                //Carrier Data
                let carrier_data = []
                let carrier_uips = []

                await axios.post('/api/appointment', {
                    "rocket_id": this.rocket_id
                })
                .then(response => {
                    carriers.forEach(carrier => {
                        const carrier_keys = Object.keys(JSON.parse(response.data.appointments[carrier]))

                        carrier_keys.forEach(key => {
                            if(key != 'direct'){
                                carrier_uips.push({
                                    name: key,
                                    value: JSON.parse(response.data.appointments[carrier])[key]
                                })
                            }
                        })

                        if(carrier == 'Wright'){
                            const wright = [' - NFIP', ' - Hiscox', ' - ResiFlood']

                            wright.forEach(product => {
                                carrier_data.push({
                                    direct: JSON.parse(response.data.appointments[carrier]).direct,
                                    name: carrier+product,
                                    uip_fields: carrier_uips
                                })
                            })

                            carrier_uips = []

                        }else {
                            carrier_data.push({
                                direct: JSON.parse(response.data.appointments[carrier]).direct,
                                name: carrier,
                                uip_fields: carrier_uips
                            })

                            carrier_uips = []
                        }
                    })
                })

                await axios.post('/api/onboarding', {
                    "rocket_id": this.rocket_id,
                    "uip_created": true
                })

                //Create Agency in Rocket MGA System
                let states = []

                this.data.additional_states.forEach(state => {
                    if(state.code == this.data.state){
                        states.push({name: state.code, license_number: this.data.agency_license})
                    } else {
                        states.push({name: state.code, license_number: null})
                    }
                })

                const agencyInfo = {
                    "company_name": this.data.agency_name,
                    "rocket_id": this.rocket_id,
                    "dba_name": this.data.dba_name,
                    "principal_agent": this.data.agent_name,
                    //Carriers
                    "carriers_list": carrier_data,
                    "phone": this.data.phone,
                    "email": this.data.email,
                    "address1": this.data.address1,
                    "address2": this.data.address2,
                    "city": this.data.city,
                    "state": this.data.state,
                    "zip": this.data.zip,
                    "agency_license": this.data.agency_license,
                    "agency_tax_id": this.data.agency_tax_id,
                    "agency_type": this.data.agency_type.code,
                    "agent_license": this.data.agent_license,
                    "agent_license_eff": this.data.agent_license_eff,
                    "agent_license_exp": this.data.agent_license_exp,
                    "agent_npn": this.data.agent_npn,
                    "eo_insurer": this.data.eo_insurer,
                    "eo_limit": this.data.eo_limit,
                    "eo_policy": this.data.eo_policy,
                    "eo_exp": this.data.eo_exp,
                    //Appointed States
                    "appointed_states": states
                }

                await axios.post('https://backend.agentportal.rocketmga.com/api/services/client/new-request', agencyInfo)

                //Send Training Calendly Invite
                emailjs.init(this.api.publicKey)
                emailjs.send(this.api.serviceID, this.api.trainingInvite, {
                    toEmail: this.data.email,
                    agencyName: this.data.agency_name
                })


                location.reload()
            } else{
                this.$emit('loading')
            }
        },
        async approve(name){
            await axios.post('/api/approval', {
                "rocket_id": this.rocket_id,
                "update": {
                    [name]: true
                }
            })
            .then(response => {
                this.$alert({
                    title: 'Update Success',
                    text: response.data.message,
                    type: 'success'
                })
            })

            await axios.post('/api/approval', {"rocket_id": this.rocket_id})
            .then(response => {
                this.valid = response.data
            })  

            const validKeys = Object.keys(this.valid)

            validKeys.forEach(key => {
                if(this.valid[key] == 1){
                    this.valid[key] = true
                } else if(this.valid[key] == 0) {
                    this.valid[key] = false
                }
            })
        },
        inputChange(id, value, errors){
            this.form[id] = value
            
            if(errors){
                this.errors.forEach(error => {
                    if(error.name == id){
                        error.errors = errors
                    }
                })                    
            } else {
                this.errors.forEach(error => {
                    if(error.name == id){
                        error.errors = []
                    }
                })
            }

            this.$emit('change', id, value, errors)
        },
        async downloadAgreement(){
            //Disable Agreement Button
            this.agreement_disabled = true
            //Show Loading Spinner
            this.$emit('loading') 

            const myHeaders = {
                headers: {'Authorization': `API-Key ${this.api.apiKey}`, 'Content-Type': 'application/pdf'},
                responseType: 'blob'
            }
            await axios.get(`https://api.pandadoc.com/public/v1/documents/${this.form.document_id}/download`, myHeaders)
            .then(response => {
                const url = window.URL.createObjectURL(new Blob([response.data], {type:"application/pdf"}));
                const link = document.createElement('a');
                link.href = url;
                window.open(url);
                link.setAttribute('download', `${this.data.agency_name}-${this.data.rocket_id}.pdf`);
                document.body.appendChild(link);
                link.click();
            })
            //Hide Loading Spinner
            this.$emit('loading')
            //Enable Agreement Button
            this.agreement_disabled = false
        },
    },
    components: {
        textInput,
        ExclamationTriangleIcon,
        CheckCircleIcon,
        EyeIcon,
        CheckBadgeIcon
    }
}
</script>