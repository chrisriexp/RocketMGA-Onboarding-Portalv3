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
            <agreement v-else-if="step == 4" @back="back" @agreementCompleted="submit" />
            
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
        padTo2Digits(num) {
            return num.toString().padStart(2, '0');
        },
        formatDate(date) {
            return [
                this.padTo2Digits(date.getMonth() + 1),
                this.padTo2Digits(date.getDate()),
                date.getFullYear(),
            ].join('/');
        },
        async submit() {
            await axios.get('/api/onboarding/check')
            .then(response => {
                this.data = response.data.message
            })

            this.step += 1

            //Names
            const names = this.data.agent_name.split(" ")
            const first_name = names[0]
            const last_name = names[1]

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

            

            JSON.parse(this.data.additional_states).forEach(state => {
                if(!statesStr){
                    statesStr = state.code
                } else {
                    statesStr = `${statesStr},${state.code}`
                }
            })

            //Save Rocket Agreement
            let agreementFile = ''

            const myHeaders = {
                headers: {
                    'Authorization': `API-Key ${this.api.apiKey}`,
                    'Content-Type': 'application/pdf',
                    'Content-Disposition': 'attachment; filename="Agreement.pdf"'
                },
                responseType: 'blob'
            };

            await axios.get(`https://shielded-ridge-03597.herokuapp.com/https://api.pandadoc.com/public/v1/documents/${this.data.document_id}/download`, myHeaders)
            .then(response => {

                const pdfFile = new File([response.data], 'Agreement.pdf', { type: 'application/pdf' });
                const agreementData = new FormData();
                agreementData.append('type', 'agreement');
                agreementData.append('file', pdfFile);

                axios.post('/api/upload', agreementData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(response => {
                    const id = response.data.id
                    console.log(id)
                    //Save Agreement ID
                    axios.post('/api/onboarding/update', {
                        'data': {
                            'agreement': id
                        }
                    })
                })
                .catch(error => {
                    console.log(error.response.data);
                });
            });

            //Get Zip+4

            //Email Carriers and Max
            if(!this.data.dual){
                //Email to Dual
                emailjs.init(this.api.publicKey)
                emailjs.send(this.api.serviceID, this.api.dualTemplate, {
                    agencyName: this.data.agency_name,
                    agentName: this.data.agent_name,
                    agentEmail: this.data.email,
                    agentPhone: this.data.phone,
                    agencyFullAddress: this.data.address
                })
            }

            //Email to Palomar
            if(!this.data.palomar){
                emailjs.init(this.api.publicKey)
                emailjs.send(this.api.serviceID, this.api.palomarTemplate, {
                    agencyName: this.data.agency_name,
                    agentName: this.data.agent_name,
                    agentEmail: this.data.email,
                    agentPhone: this.data.phone,
                    agencyFullAddress: this.data.address
                })
            }

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
                agencyType: JSON.parse(this.data.agency_type).name,
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
                    "stage": "submitted",
                    "completed": true
                }
            })

            //Create new completed Notification for Backend
            await axios.post('/api/notification', {
                "agency_name": this.data.agency_name
            })

            //Creat Approval
            await axios.get('/api/approval/check')

            //Check and Add to Zoho
            await axios.post('https://shielded-ridge-03597.herokuapp.com/https://hooks.zapier.com/hooks/catch/14170682/bjht2i9/', {
                "agency_name": this.data.agency_name,
                "agent_name": this.data.agent_name,
                "phone": this.data.phone,
                "email": this.data.email,
                "adress": this.data.address1 + " " + this.data.address2,
                "city": this.data.city,
                "state": this.data.state,
                "zip": this.data.zip,
                "npn": this.data.agent_npn,
                "source": "Onboarding Portal"
            })

            //Send Data to Wright Sheet
            if(!this.data.wright){
                await axios.post('https://shielded-ridge-03597.herokuapp.com/https://hooks.zapier.com/hooks/catch/14170682/bjh4r3i/',  {
                    "name": this.data.agency_name,
                    "dba_name": this.data.dba_name,
                    "phone": this.data.phone,
                    "email": this.data.email,
                    "agency_type": JSON.parse(this.data.agency_type).code,
                    "address1": this.data.address1,
                    "address2": this.data.address2,
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
            }

            //Send Data to Aon Sheet
            if(!this.data.aon){
                await axios.post('https://shielded-ridge-03597.herokuapp.com/https://hooks.zapier.com/hooks/catch/14170682/bjtgf41', {
                    "name": this.data.agency_name,
                    "phone": this.data.phone,
                    "email": this.data.email,
                    "address": this.data.address1 + " " + this.data.address2,
                    "city": this.data.city,
                    "state": this.data.state,
                    "zip": this.data.zip,
                    "additional_states": statesStr,
                    "first_name": first_name,
                    "last_name": last_name,
                    "week": week
                })
            }

            //Create Agency in Logins Table
            await axios.get('/api/logins/check')

            // if(!this.data.neptune){
            //     await this.neptuneAPI()
            // }

            this.completed = true
        },
        async neptuneAPI(){
            let token = ''
            let type = 0

            const agency_type = JSON.parse(this.data.agency_type).code

            if(agency_type == 'Sole'){
                type = 1
            } else if(agency_type == 'LLC-C'){
                type = 6
            } else if(agency_type == 'LLC-S'){
                type = 7
            } else if(agency_type == 'LLC-P'){
                type = 8
            } else if(agency_type == 'C-Corp'){
                type = 2
            } else if(agency_type == 'S-Corp'){
                type = 3
            } else if(agency_type == 'Partner'){
                type = 4
            } else if(agency_type == 'Trust'){
                type = 5
            }

            await axios.get('https://api.neptuneflood.com/api/v5/auth/getToken', {
                auth: {
                    username: 'KQUsJAVf1wXezeLHsoXuJGMF53h1IJZ9qPakP/wQcmpsHVGb5W4UjMKVp4pQD9Pj9TyVofphOJkDa+ZdI19p4gnGsVgHAX5xAtxFUZE+s3A=',
                    password: '433309b9-fbfa-4d62-bb7f-672aab463834'
                }
            })
            .then(response => {
                token = response.data
            })

            const neptuneHeaders = {
                headers: {'Authorization': token}
            }

            await axios.post('https://shielded-ridge-03597.herokuapp.com/https://api.neptuneflood.com/api/v4/agency/sub', {
                "name": this.data.agent_name,
                "title": "Principal",
                "company": this.data.agency_name,
                "type": type,
                "taxId": this.data.agency_tax_id,
                "addr1": this.data.address1,
                "addr2": this.data.address2,
                "city": this.data.city,
                "state": this.data.state,
                "zip": this.data.zip,
                "mailingAddr1": this.data.address1,
                "mailingAddr2": this.data.address2,
                "mailingCity": this.data.city,
                "mailingState": this.data.state,
                "mailingZip": this.data.zip,
                "isMailingAddressUserVerified": true,
                "email": this.data.email,
                "phone": this.data.phone,
                
                "eoInsurer": this.data.eo_insurer,
                "eoPolicyNumber": this.data.eo_policy,
                "eoLimit": this.data.eo_limit,
                "eoExpiration": this.data.eo_exp
            }, neptuneHeaders)
            .then(response => {
                console.log(response)
                const self = this;
                if(response.data.payload.agencyInfo.agencyNo){
                    // //Send Neptune Credentials to Appointed Agents Table
                    // axios.put('/api/appointed/update/'+self.data.rocket_id, {
                    //     "neptune": response.data.payload.agencyInfo.agencyNo
                    // })
                }

                if(response.data.payload.errors){
                    let neptuneErrors = ''
                    let i = 0
                    response.data.payload.errors.forEach(error=> {
                        if(neptuneErrors == ''){
                            neptuneErrors = 'ERROR-'+i+': '+error.message
                        } else {
                            neptuneErrors = neptuneErrors+', ERROR-'+i+': '+error.message
                        }

                        i += 1
                    })

                    emailjs.init(self.api.publicKey)
                    emailjs.send(self.api.serviceID, self.api.neptuneError, {
                        agencyName: self.data.agency_name,
                        agentName: self.data.agent_name,
                        phone: self.data.phone,
                        email: self.data.email,
                        address: self.data.address1+ " " +self.data.address2+ ", " +self.data.city+ ", " +self.data.state+" "+self.data.zip,
                        errors: neptuneErrors
                    })
                }
            })
            .catch(error => {
                const self = this;
                emailjs.init(self.api.publicKey)
                emailjs.send(self.api.serviceID, this.api.neptuneError, {
                    agencyName: self.data.agency_name,
                    agentName: self.data.agent_name,
                    phone: self.data.phone,
                    email: self.data.email,
                    address: self.data.address1+ " " +self.data.address2+ ", " +self.data.city+ ", " +self.data.state+" "+self.data.zip,
                    errors: 'ERROR: '+error.message
                })
            })

            return
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