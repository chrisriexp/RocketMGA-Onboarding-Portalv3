<template>
    <div class="w-full h-fit grid gap-8 my-6 p-6 bg-white rounded-md border-custom-gray border-opacity-20 border-[1px]">
        <div v-if="this.data.completed" class="grid gap-6 w-fit h-fit">
            <button @click="downloadAgreement" :disabled="agreement_disabled" class="flex gap-8 px-6 w-fit group text-white rounded-xl bg-custom-red font-medium text-xl disabled:bg-opacity-40">
                <span class="my-auto">Rocket MGA Agreement</span>
                <FolderArrowDownIcon class="h-10" />
            </button>
        </div>

        <div v-else class="w-full px-2 bg-custom-orange bg-opacity-60 rounded-md text-sm text-white">
            <p class="flex gap-2"><ExclamationCircleIcon class="h-6 my-auto"/><span class="my-auto">This agency has not completed their application yet.</span></p>
        </div>
    </div>
    
    <div class="w-full h-fit grid gap-8 my-6 p-6 bg-white rounded-md border-custom-gray border-opacity-20 border-[1px]">
        <div class="grid gap-6 w-fit h-fit">
            <a target="_blank" :href="this.agency_license_file" class="flex gap-8 px-6 w-fit group text-white rounded-xl bg-custom-red font-medium text-xl">
                <span class="my-auto">Agency License</span>
                <FolderArrowDownIcon class="h-10" />
            </a>

            <div class="flex gap-2 w-fit h-fit justify-items-center">
                <p class="font-medium h-fit my-auto">License Number -</p>
                <textInput @inputUpdate="inputChange" :inputValue="form.agency_license" :id="'agency_license'" :isdisabled=!update />
            </div>
        </div>
    </div>

    <div class="w-full h-fit grid gap-4 my-6 p-6 bg-white rounded-md border-custom-gray border-opacity-20 border-[1px]">
        <div class="grid gap-6 w-fit h-fit">
            <a target="_blank" :href="this.agent_license_file" class="flex gap-8 px-6 w-fit group text-white rounded-xl bg-custom-red font-medium text-xl">
                <span class="my-auto">Agent License</span>
                <FolderArrowDownIcon class="h-10" />
            </a>

            <div class="w-full flex gap-4 text-md text-custom-gray">
                <div class="flex gap-2 w-fit h-fit justify-items-center">
                    <p class="font-medium h-fit my-auto">License Number -</p>
                    <textInput @inputUpdate="inputChange" :inputValue="form.agent_license" :id="'agent_license'" :isdisabled=!update />
                </div>

                <div v-if="!this.data.wright" class="flex gap-2 w-fit h-fit justify-items-center">
                    <p class="font-medium h-fit my-auto">License Eff Date -</p>
                    <textInput @inputUpdate="inputChange" :inputValue="form.agent_license_eff" :id="'agent_license_eff'" :isdisabled=!update :date=true />
                </div>
                
                <div v-if="!this.data.wright" class="flex gap-2 w-fit h-fit justify-items-center">
                    <p class="font-medium h-fit my-auto">License Exp Date -</p>
                    <textInput @inputUpdate="inputChange" :inputValue="form.agent_license_exp" :id="'agent_license_exp'" :isdisabled=!update :date=true />
                </div>

                <div v-if="!this.data.wright" class="flex gap-2 w-fit h-fit justify-items-center">
                    <p class="font-medium h-fit my-auto">Agent NPN -</p>
                    <textMask @inputUpdate="inputChange" :inputValue="form.agent_npn" :id="'agent_npn'" :maskText="'#######'" :isdisabled=!update />
                </div>
            </div>
        </div>

        <div v-if="this.data.wright" class="w-full px-2 bg-custom-orange bg-opacity-60 rounded-md text-sm text-white">
            <p class="flex gap-2"><ExclamationCircleIcon class="h-6 my-auto"/><span class="my-auto">This agency is already appointed with Wright so we have not collected the following information: Agent NPN, License Eff Date and License Exp Date.</span></p>
        </div>
    </div>

    <div class="w-full h-fit grid gap-8 my-6 p-6 bg-white rounded-md border-custom-gray border-opacity-20 border-[1px]">
        <div class="grid gap-6 w-fit h-fit">
            <a target="_blank" :href="this.eo" class="flex gap-8 px-6 w-fit group text-white rounded-xl bg-custom-red font-medium text-xl">
                <span class="my-auto">Agency E&O</span>
                <FolderArrowDownIcon class="h-10" />
            </a>

            <div class="w-full flex gap-4 text-md text-custom-gray">
                <div class="flex gap-2 w-fit h-fit justify-items-center">
                    <p class="font-medium h-fit my-auto">Policy Number -</p>
                    <textInput @inputUpdate="inputChange" :inputValue="form.eo_policy" :id="'eo_policy'" :isdisabled=!update />
                </div>

                <div class="flex gap-2 w-fit h-fit justify-items-center">
                    <p class="font-medium h-fit my-auto">Exp Date -</p>
                    <textInput @inputUpdate="inputChange" :inputValue="form.eo_exp" :id="'eo_exp'" :isdisabled=!update :date=true />
                </div>

                <div class="flex gap-2 w-fit h-fit justify-items-center">
                    <p class="font-medium h-fit my-auto">Limit -</p>
                    <textInput @inputUpdate="inputChange" :inputValue="form.eo_limit" :id="'eo_limit'" :isdisabled=!update />
                </div>

                <div class="flex gap-2 w-fit h-fit justify-items-center">
                    <p class="font-medium h-fit my-auto">Insurer -</p>
                    <textInput @inputUpdate="inputChange" :inputValue="form.eo_insurer" :id="'eo_insurer'" :isdisabled=!update />
                </div>
            </div>
        </div>
    </div>

    <div class="w-full h-fit grid gap-8 my-6 p-6 bg-white rounded-md border-custom-gray border-opacity-20 border-[1px]">
        <div v-if="this.form.agency_logo != null" class="grid gap-6 w-fit h-fit">
            <a target="_blank" :href="this.agency_logo" class="flex gap-8 px-6 w-fit group text-white rounded-xl bg-custom-red font-medium text-xl">
                <span class="my-auto">Agency Logo</span>
                <FolderArrowDownIcon class="h-10" />
            </a>
        </div>

        <div v-else class="w-full px-2 bg-custom-orange bg-opacity-60 rounded-md text-sm text-white">
            <p class="flex gap-2"><ExclamationCircleIcon class="h-6 my-auto"/><span class="my-auto">This agency did not upload their logo.</span></p>
        </div>
    </div>
</template>

<script>
import textInput from '../textInput.vue'
import textMask from '../textMask.vue'
import { FolderArrowDownIcon, ExclamationCircleIcon } from '@heroicons/vue/24/outline'
import axios from 'axios'

export default {
    name: "Agency Documents",
    props: {
        data: Object,
        update: Boolean
    },
    data() {
        return {
            agreement_disabled: false,
            agency_license_file: '',
            agent_license_file: '',
            eo: '',
            agency_logo: '',
            api: {
                apiKey: '8135da5570abd90097a2bcc0dbbce76d1decd484'
            },
            form: {
                document_id: '',
                agent_license_file: '',
                agent_license: '',
                agent_license_eff: '',
                agent_license_exp: '',
                agent_npn: '',
                agency_license: '',
                agency_license_file: '',
                eo: '',
                eo_policy: '',
                eo_limit: '',
                eo_exp: '',
                eo_insurer: '',
                agency_logo: ''
            },
            errors: [
                {
                    name: 'agent_license',
                    errors: []
                },
                {
                    name: 'agent_license_eff',
                    errors: []
                },
                {
                    name: 'agent_license_exp',
                    errors: []
                },
                {
                    name: 'agent_npn',
                    errors: []
                },
                {
                    name: 'agency_license',
                    errors: []
                },
                {
                    name: 'eo_policy',
                    errors: []
                },
                {
                    name: 'eo_limit',
                    errors: []
                },
                {
                    name: 'eo_exp',
                    errors: []
                }
            ]
        }
    },
    async created(){
        const keys = Object.keys(this.form)
        keys.forEach(key => {
            this.form[key] = this.data[key]
        })

        const files = ['agency_license_file', 'agent_license_file', 'eo', 'agency_logo']

        files.forEach(file => {
            axios.get('/api/file/' + this.form[file])
            .then(response => {
                this[file] = "https://onboarding.rocketmga.com" + response.data.path
            })
        })
    },
    watch: {

    },
    methods: {
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
        textMask,
        FolderArrowDownIcon,
        ExclamationCircleIcon
    }
}
</script>