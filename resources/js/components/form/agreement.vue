<template>
    <div class="h-fit grid gap-2 justify-items-center px-6 md:px-0">
        <!--Favicon Image-->
        <!-- <img src="../../../assets/favicon.png" alt="Rocket Favicon" class="h-[53px] md:h-[64px] mt-12"> -->

        <!--Agreement Header-->
        <div class="grid h-fit mt-6 text-center text-custom-dark-blue">
            <h2 class="text-[24px] md:text-[32px] font-semibold">Agreement Signing</h2>
            <p class="text-[14px] md:text-[16px] opacity-70">Contratulations! Sign your agreement and you are done!</p>
        </div>

        <form @submit.prevent="submitAgreement" class="grid gap-4 w-full md:w-[600px]">
            <div id="submitLoading" class="my-8 mx-auto h-[600px] hidden">
                <loading />
            </div>

            <div v-if="!form.document_embed" class="my-8 mx-auto h-[600px]">
                <loading />
            </div>

            <iframe v-else id="agreement" :src="'https://app.pandadoc.com/s/' + form.document_embed" height="660" class="w-full"></iframe>

            <!--Next Button-->
            <input :disabled="submitDisabled" type="submit" class="h-[48px] mt-2 bg-custom-orange rounded-md py-2 uppercase text-white font-medium text-[18px] border-l-[5px] border-b-[6px] border-[#F4B983] active:border-custom-orange cursor-pointer shadow-newdrop active:shadow-none disabled:opacity-40 disabled:cursor-not-allowed" value="next">
            <!--Back Button-->
            <button :disabled="backDisabled" @click="back" type="button" class="w-fit mx-auto mb-8 md:mb-0 text-[16px] text-custom-blue font-medium underline" >Back</button>
        </form>
    </div>
</template>

<script>
import ProgressBar from '../progressBar.vue'
import loading from '../loading-red.vue'
import moment from 'moment';
import states from '../../../assets/states.json'

export default {
    name: "Agreement",
    props: {
        data: Object
    },
    data() {
        return {
            email: '',
            backDisabled: true, 
            submitDisabled: true,
            api: {
                apiKey: '861fd4711f09ecbcf3f10f7a5cd449e22453abf3',
                // apiKey: '8135da5570abd90097a2bcc0dbbce76d1decd484', //SandBox
                templateID: '3gbM422FzFxVjXyWJ484hm',
            },
            form: {
                document_id: '',
                document_embed: ''
            },
            agreement: {
                rocket_id: '',
                agency_name: '',
                dba_name: '',
                phone: '',
                email: '',
                address1: '',
                address2: '',
                city: '',
                state: '',
                zip: '',
                cityStateZip: '',
                agent_name: '',
                first_name: '',
                last_name: '',
                today: '',
                ddmm: '',
                spelledState: '',
                beyondEntityType: '',
                sterlingEntityType: '',
                w9EntityType: '',
                w9LLCType: '',
                agency_tax_id: '',
                tax1: '',
                tax2: '',
                tax3: '',
                tax4: '',
                tax5: '',
                tax6: '',
                tax7: '',
                tax8: '',
                tax9: '',
                haveBeyond: false,
                haveFlow: false,
                haveSterling: false,
                agent_license: '',
                agency_license: '',
                eo_insurer: '',
                eo_policy: '',
                eo_limit: '',
                eo_exp: ''
            }
        }
    },
    async created(){
        await axios.get('/api/onboarding/check')
        .then(response => {
            // Set Email
            this.email = response.data.message.email
            
            //Set Document and Embed ID if exists
            const keys = Object.keys(this.form)

            keys.forEach(key => {
                this.form[key] = response.data.message[key]
            })

            if(!this.form.document_id){
                //Set all values received
                const keys = Object.keys(this.agreement)

                keys.forEach(key => {
                    if(Object.hasOwn(response.data.message, key) && response.data.message[key] != null){
                        this.agreement[key] = response.data.message[key]
                    }
                })

                //Set cityStateZip
                this.agreement.cityStateZip = `${this.agreement.city}, ${this.agreement.state} ${this.agreement.zip}`

                //Set Spelled State
                states.forEach(state => {
                    if(state.code == response.data.message.state){
                        this.agreement.spelledState = state.name
                    }
                })

                //Set Name
                const name = response.data.message.agent_name.split(" ")
                this.agreement.first_name = name[0]
                this.agreement.last_name = name[1]

                //Agency Types
                const agency_type = JSON.parse(response.data.message.agency_type).code
                
                if (agency_type === "Sole") {
                    this.agreement.w9EntityType = "Sole";
                    this.agreement.beyondEntityType = "Sole";
                    this.agreement.sterlingEntityType = "Sole";
                } else if (agency_type === "C-Corp") {
                    this.agreement.w9EntityType = "C Corp";
                    this.agreement.beyondEntityType = "Corp";
                    this.agreement.sterlingEntityType = "Corp";
                } else if (agency_type === "S-Corp") {
                    this.agreement.w9EntityType = "S Corp";
                    this.agreement.beyondEntityType = "Corp";
                    this.agreement.sterlingEntityType = "Corp";
                } else if (agency_type === "Partner") {
                    this.agreement.w9EntityType = "Partner";
                    this.agreement.beyondEntityType = "Partner";
                    this.agreement.sterlingEntityType = "Partner";
                } else if (agency_type === "Trust") {
                    this.agreement.w9EntityType = "Trust";
                    this.agreement.beyondEntityType = "Partner";
                    this.agreement.sterlingEntityType = "Partner";
                } else if (agency_type === "LCC-C") {
                    this.agreement.w9EntityType = "LLC";
                    this.agreement.w9LLCType = "C";
                    this.agreement.beyondEntityType = "LLC";
                    this.agreement.sterlingEntityType = "Corp";
                } else if (agency_type === "LLC-S") {
                    this.agreement.w9EntityType = "LLC";
                    this.agreement.w9LLCType = "S";
                    this.agreement.beyondEntityType = "LLC";
                    this.agreement.sterlingEntityType = "Corp";
                } else if (agency_type === "LLC-P") {
                    this.agreement.w9EntityType = "LLC";
                    this.agreement.w9LLCType = "P";
                    this.agreement.beyondEntityType = "LLC";
                    this.agreement.sterlingEntityType = "Partner";
                }

                //Tax ID
                const tax = response.data.message.agency_tax_id.replace("-", '').split("")

                let i = 1
                tax.forEach(item => {
                    if(i === 1){this.agreement.tax1 = item} else if(i === 2){this.agreement.tax2 = item} else if(i === 3){this.agreement.tax3 = item} else if(i === 4){this.agreement.tax4 = item} else if(i === 5){this.agreement.tax5 = item} else if(i === 6){this.agreement.tax6 = item} else if(i === 7){this.agreement.tax7 = item} else if(i === 8){this.agreement.tax8 = item} else if (i === 9){this.agreement.tax9 = item}
                    i += 1
                })
            } else {
                this.backDisabled = false
                this.submitDisabled = false
            }
        })

        if(!this.form.document_id){
            //Set Dates
            const date = new Date()
            this.agreement.today = moment(date).format('MM/DD/YYYY')
            this.agreement.ddmm = moment(date).format('MM/DD')

            this.createDocument()
        } else if(this.form.document_id && !this.form.document_embed){
            const myHeaders = {
                headers: {'Authorization': `API-Key ${this.api.apiKey}`, 'Content-Type': 'application/json'}
            }

            await axios.get(`https://api.pandadoc.com/public/v1/documents/${this.form.document_id}`, myHeaders)
            .then(response => {
                if(response.data.status == 'document.sent'){
                    this.getEmbedID()
                }else if(response.data.status == 'document.draft'){
                    this.sendDocument()
                }
            })
        }
    },
    methods: {
        back(){
            this.$emit('back')
        },
        async createDocument() {
            const address = `${this.agreement.address1} ${String(this.agreement.address2)}`

            const myHeaders = {
                headers: {'Authorization': `API-Key ${this.api.apiKey}`, 'Content-Type': 'application/json'}
            }

            const raw = {
                "name": `RocketMGA - ${this.agreement.agency_name} Onboarding`,
                "template_uuid": `${this.api.templateID}`,
                "recipients": [
                    {
                    "email": this.agreement.email,
                    "first_name": this.agreement.first_name,
                    "last_name": this.agreement.last_name,
                    "role": "Client"
                    }
                ],
                "fields": {
                    "agency_name": {
                        "value": this.agreement.agency_name,
                        "role": "Client"
                    },
                    "dba_name": {
                        "value": this.agreement.dba_name,
                        "role": "Client"
                    },
                    "phone": {
                        "value": this.agreement.phone,
                        "role": "Client"
                    },
                    "email": {
                        "value": this.agreement.email,
                        "role": "Client"
                    },
                    "address": {
                        "value": address,
                        "role": "Client"
                    },
                    "cityStateZip": {
                        "value": this.agreement.cityStateZip,
                        "role": "Client"
                    },
                    "agent_name": {
                        "value": this.agreement.agent_name,
                        "role": "Client"
                    },
                    "today": {
                        "value": this.agreement.today,
                        "role": "Client"
                    },
                    "spelledState": {
                        "value": this.agreement.spelledState,
                        "role": "Client"
                    },
                    "w9EntityType": {
                        "value": this.agreement.w9EntityType,
                        "role": "Client"
                    },
                    "w9LLCType": {
                        "value": this.agreement.w9LLCType,
                        "role": "Client"
                    },
                    "agency_tax_id": {
                        "value": this.agreement.agency_tax_id,
                        "role": "Client"
                    },
                    "agency_license": {
                        "value": this.agreement.agency_license,
                        "role": "Client"
                    },
                    "agent_license": {
                        "value": this.agreement.agent_license,
                        "role": "Client"
                    }
                },
                "tokens": [
                    {
                        "name": 'RMGAid',
                        "value": this.agreement.rocket_id
                    },
                    {
                        "name": "haveBeyond",
                        "value": this.agreement.haveBeyond
                    },
                    {
                        "name": "haveFlow",
                        "value": this.agreement.haveFlow
                    },
                    {
                        "name": "haveSterling",
                        "value": this.agreement.haveSterling
                    },
                    {
                        "name": "tax1",
                        "value": this.agreement.tax1
                    },
                    {
                        "name": "tax2",
                        "value": this.agreement.tax2
                    },
                    {
                        "name": "tax3",
                        "value": this.agreement.tax3
                    },
                    {
                        "name": "tax4",
                        "value": this.agreement.tax4
                    },
                    {
                        "name": "tax5",
                        "value": this.agreement.tax5
                    },
                    {
                        "name": "tax6",
                        "value": this.agreement.tax6
                    },
                    {
                        "name": "tax7",
                        "value": this.agreement.tax7
                    },
                    {
                        "name": "tax8",
                        "value": this.agreement.tax8
                    },
                    {
                        "name": "tax9",
                        "value": this.agreement.tax9
                    }
                ],
                "content_placeholders": [
                    {
                        "block_id": "beyondAgreement",
                        "content_library_items": [
                            {
                                "id": "Z6U3JC9wLheLUFR4AtZjwP",
                                "fields": {
                                    "agency_name": {
                                        "value": this.agreement.agency_name,
                                        "role": "Client"
                                    },
                                    "dba_name": {
                                        "value": this.agreement.dba_name,
                                        "role": "Client"
                                    },
                                    "agent_name": {
                                        "value": this.agreement.agent_name,
                                        "role": "Client"
                                    },
                                    "phone": {
                                        "value": this.agreement.phone,
                                        "role": "Client"
                                    },
                                    "email": {
                                        "value": this.agreement.email,
                                        "role": "Client"
                                    },
                                    "address": {
                                        "value": address,
                                        "role": "Client"
                                    },
                                    "cityStateZip": {
                                        "value": this.agreement.cityStateZip,
                                        "role": "Client"
                                    },
                                    "today": {
                                        "value": this.agreement.today,
                                        "role": "Client"
                                    },
                                    "beyondEntityType": {
                                        "value": this.agreement.beyondEntityType,
                                        "role": "Client"
                                    },
                                    "agency_tax_id": {
                                        "value": this.agreement.agency_tax_id,
                                        "role": "Client"
                                    },
                                    "eo_exp": {
                                        "value": this.agreement.eo_exp,
                                        "role": "Client"
                                    },
                                    "eo_policy": {
                                        "value": this.agreement.eo_policy,
                                        "role": "Client"
                                    },
                                    "eo_limit": {
                                        "value": this.agreement.eo_limit,
                                        "role": "Client"
                                    },
                                    "eo_insurer": {
                                        "value": this.agreement.eo_insurer,
                                        "role": "Client"
                                    }
                                }
                            }
                        ]
                    },
                    {
                        "block_id": "flowAgreement",
                        "content_library_items": [
                            {
                                "id": "BgKg4bfJZ4xqAgyMBRpNmT",
                                "fields": {
                                    "agency_name": {
                                        "value": this.agreement.agency_name,
                                        "role": "Client"
                                    },
                                    "dba_name": {
                                        "value": this.agreement.dba_name,
                                        "role": "Client"
                                    },
                                    "agent_name": {
                                        "value": this.agreement.agent_name,
                                        "role": "Client"
                                    },
                                    "agent_license": {
                                        "value": this.agreement.agent_license,
                                        "role": "Client"
                                    },
                                    "phone": {
                                        "value": this.agreement.phone,
                                        "role": "Client"
                                    },
                                    "email": {
                                        "value": this.agreement.email,
                                        "role": "Client"
                                    },
                                    "address": {
                                        "value": address,
                                        "role": "Client"
                                    },
                                    "cityStateZip": {
                                        "value": this.agreement.cityStateZip,
                                        "role": "Client"
                                    },
                                    "spelledState": {
                                        "value": this.agreement.spelledState,
                                        "role": "Client"
                                    },
                                    "today": {
                                        "value": this.agreement.today,
                                        "role": "Client"
                                    },
                                    "ddmm": {
                                        "value": this.agreement.ddmm,
                                        "role": "Client"
                                    },
                                    "agency_tax_id": {
                                        "value": this.agreement.agency_tax_id,
                                        "role": "Client"
                                    },
                                    "w9EntityType": {
                                        "value": this.agreement.w9EntityType,
                                        "role": "Client"
                                    }
                                }
                            }
                        ]
                    },
                    {
                        "block_id": "sterlingAgreement",
                        "content_library_items": [
                            {
                                "id": "4owrMGnKtvqSWEJ9YLVkeN",
                                "fields": {
                                    "agency_name": {
                                        "value": this.agreement.agency_name,
                                        "role": "Client"
                                    },
                                    "agent_name": {
                                        "value": this.agreement.agent_name,
                                        "role": "Client"
                                    },
                                    "today": {
                                        "value": this.agreement.today,
                                        "role": "Client"
                                    },
                                    "sterlingEntityType": {
                                        "value": this.agreement.sterlingEntityType,
                                        "role": "Client"
                                    },
                                    "agency_tax_id": {
                                        "value": this.agreement.agency_tax_id,
                                        "role": "Client"
                                    }
                                }
                            }
                        ]
                    }
                ]
            };

            await axios.post('https://api.pandadoc.com/public/v1/documents', raw, myHeaders)
            .then(response => {
                this.form.document_id = response.data.id
            })

            //Update database with document id
            await axios.post('/api/onboarding/update', {
                "data": this.form
            })

            //Send Document
            setTimeout(() =>{
                this.sendDocument()
            }, 7000);
        },
        async sendDocument() {
            const myHeaders = {
                headers: {'Authorization': `API-Key ${this.api.apiKey}`, 'Content-Type': 'application/json'}
            }

            const raw = {
                "message": "Welcome to RocketMGA - Almost There!",
                "silent": true
            }
            
            await axios.post(`https://api.pandadoc.com/public/v1/documents/${this.form.document_id}/send`, raw, myHeaders)
            .then(response => {
                if(response.status == 200){
                    this.getEmbedID()
                } else {
                    return this.sendDocument()
                }
            })
            .catch(error => {
                if(error.status === 409){
                    setTimeout(() => {
                        return this.sendDocument()
                    }, 2000);
                }
            })
        },
        async getEmbedID() {
            const myHeaders = {
                headers: {'Authorization': `API-Key ${this.api.apiKey}`, 'Content-Type': 'application/json'}
            }

            const raw = {
                "recipient": `${this.email}`,
                "lifetime": 9999999
            }

            await axios.post(`https://api.pandadoc.com/public/v1/documents/${this.form.document_id}/session`, raw, myHeaders)
            .then(response => {
                this.form.document_embed = response.data.id
            })

            //Update database with embed id
            await axios.post('/api/onboarding/update', {
                "data": this.form
            })

            //Enabled Submit and Back Buttons
            this.submitDisabled = false;
            this.backDisabled = false;
        },
        async submitAgreement() {
            //Disable Submit and Back Button
            this.submitDisabled = true;
            this.backDisabled = true;

            //Show Loading Spinner
            document.getElementById('submitLoading').classList.remove('hidden')

            //Drop Agreement Opcity
            document.getElementById('agreement').classList.add('opacity-40')

            //Variable checking document completed status
            let documentCompleted = false

            //Get Agreement Document Status
            const statusHeaders = {
                headers: {'Authorization': `API-Key ${this.api.apiKey}`}
            }

            await axios.get(`https://api.pandadoc.com/public/v1/documents/${this.form.document_id}`, statusHeaders)
            .then(response => {
                if(response.data.status === 'document.completed'){
                    documentCompleted = true
                }
            })

            if(!documentCompleted){
                //Enable Submit and Back Button
                this.submitDisabled = false;
                this.backDisabled = false;
                this.$alert({
                    title: 'Validation Error',
                    text: 'Agreement not complete, please finalize and try again. ',
                    type: 'warn',
                    duration: 5000
                })

                //Hide Loading Spinner
                document.getElementById('submitLoading').classList.add('hidden')

                //Fix Agreement Opcity
                document.getElementById('agreement').classList.remove('opacity-40')
            }else {
                this.$emit('agreementCompleted')
            }
        }
    },
    components: {
        ProgressBar,
        loading
    }
}
</script>