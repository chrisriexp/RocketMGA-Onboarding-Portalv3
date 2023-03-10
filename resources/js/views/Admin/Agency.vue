<template>
    <NavBar :role="role" />

    <div id="loading" :class="loading ? '' : 'hidden'" class="w-full grid justify-items-center z-10 fixed">
        <loading class="mt-64" />
    </div>

    <div class="sm:ml-64 p-10 h-full bg-custom-bg">
        <!--Header-->
        <div class="grid gap-4 h-fit pb-4 border-b-2 border-custom-gray border-opacity-20">
            <div class="flow-root w-full">
                <div class="float-left flex gap-4 w-fit h-fit font-bold text-2xl text-custom-gray ">
                    <p class="my-auto">{{ data.agency_name }} - {{ rocket_id }}</p>
                    <button v-if="!update" @click="update = !update" class="text-sm px-2 rounded-md bg-custom-red text-white disabled:opacity-40" :disabled="update">Update Information</button>
                    
                    <button v-if="update" @click="saveInfo" class="text-sm px-2 rounded-md bg-custom-dark-blue text-white disabled:opacity-40" :disabled="!update">Save</button>
                    <button v-if="update" @click="cancelUpdate" class="text-sm px-2 rounded-md bg-custom-red text-white">Cancel</button>

                    <button v-if="!data.appointed && data.approved" class="text-sm px-2 rounded-md bg-custom-dark-blue text-white">Finalize Appointment</button>
                </div>

                <div class="float-right w-fit h-fit my-auto rounded-lg bg-white p-2 text-sm">
                    <p><span class="font-medium">Created:</span> {{ moment(data.created_at).format("dddd, MMMM Do YYYY, h:mm a") }}</p>
                </div>
            </div>

            <div class="w-full flex gap-4 text-md text-custom-gray">
                <div class="flex gap-2 w-fit h-fit justify-items-center">
                    <p class="font-medium h-fit my-auto">DBA -</p>
                    <textInput @inputUpdate="inputChange" :inputValue="data.dba_name" :id="'dba_name'" :required=false :isdisabled=!update />
                </div>
                
                <div class="flex gap-2 w-fit h-fit justify-items-center">
                    <p class="font-medium h-fit my-auto">Principal Agent -</p>
                    <textInput @inputUpdate="inputChange" :inputValue="data.agent_name" :id="'agent_name'" :isdisabled=!update />
                </div>

                <p class="flex gap-2"><span class="font-medium my-auto">Stage </span>
                    <span v-if="data.appointed" class="flex gap-2 my-auto"><div class="w-2 h-2 my-auto rounded-full bg-custom-red"></div> Agency Appointed</span>
                    <span v-else-if="data.approved" class="flex gap-2 my-auto"><div class="w-2 h-2 my-auto rounded-full bg-custom-red"></div> Agency in Training</span>
                    <span v-else-if="data.completed" class="flex gap-2 my-auto"><div class="w-2 h-2 my-auto rounded-full bg-custom-red"></div> Agency Under Review</span>
                    <span v-else-if="data.stage == 'agency'" class="flex gap-2 my-auto"><div class="w-2 h-2 my-auto rounded-full bg-custom-red"></div> Agency Information</span>
                    <span v-else-if="data.stage == 'carrier'" class="flex gap-2 my-auto"><div class="w-2 h-2 my-auto rounded-full bg-custom-red"></div> Carrier Information</span>
                    <span v-else-if="data.stage == 'entity'" class="flex gap-2 my-auto"><div class="w-2 h-2 my-auto rounded-full bg-custom-red"></div> Entity Information</span>
                    <span v-else-if="data.stage == 'eo'" class="flex gap-2 my-auto"><div class="w-2 h-2 my-auto rounded-full bg-custom-red"></div> E&O Information</span>
                    <span v-else-if="data.stage == 'agreement'" class="flex gap-2 my-auto"><div class="w-2 h-2 my-auto rounded-full bg-custom-red"></div> Agreement</span>
                </p>

                <div class="flex gap-2 w-fit h-fit justify-items-center">
                    <p class="font-medium h-fit my-auto">Rocket Rep -</p>
                    <v-select style="min-width: 180px;"
                        code="code"
                        v-model="data.rocket_rep"
                        :options="repOptions"
                        label="name"
                        class="text-sm font-medium rounded-xl w-fit bg-custom-red disabled:text-custom-gray"
                        :disabled="!update"
                    ></v-select>
                </div>
            </div>

            <p class="text-md font-light text-custom-gray">Here is some more important information for you:</p>
        </div>

        <div class="flex mt-8 w-full justify-items-center text-sm text-custom-gray rounded-xl border-[1px] border-custom-gray border-opacity-20 text-center bg-white">
            <p v-if="!data.approved && data.completed" @click="active = 'approval'" id="approval" :class="active == 'approval' ? 'text-white bg-custom-red' : ''" class="w-full p-2 rounded-xl font-bold uppercase hover:cursor-pointer">Approval Checklist</p>
            <p @click="active = 'agency'" :class="active == 'agency' ? 'text-white bg-custom-red' : ''" class="w-full p-2 rounded-xl font-bold uppercase hover:cursor-pointer">Agency</p>
            <p @click="active = 'appointments'" :class="active == 'appointments' ? 'text-white bg-custom-red' : ''" class="w-full p-2 rounded-xl font-bold uppercase hover:cursor-pointer">Appointments</p>
            <p @click="active = 'documents'" :class="active == 'documents' ? 'text-white bg-custom-red' : ''" class="w-full p-2 rounded-xl font-bold uppercase hover:cursor-pointer">Documents</p>
            <p @click="active = 'logins'" :class="active == 'logins' ? 'text-white bg-custom-red' : ''" class="w-full p-2 rounded-xl font-bold uppercase hover:cursor-pointer">Logins</p>
        </div>

        <ApprovalCheckList @loading="loading = !loading" @change="change" v-if="active == 'approval'" :data="data" :update="update" :rocket_id="rocket_id" />
        <AgencyInfo @change="change" v-if="active == 'agency'" :data="data" :update="update" :rocket_id="rocket_id" />
        <AppointmentInfo @loading="loading = !loading" @change="change" v-if="active == 'appointments'" :data="data" :update="update" :rocket_id="rocket_id" />
        <Documents @loading="loading = !loading" @change="change" v-if="active == 'documents'" :data="data" :update="update" />
        <Logins @loading="loading = !loading" @change="change" v-if="active == 'logins'" :data="data" :update="update" :rocket_id="rocket_id" />
    </div>
</template>

<script>
import NavBar from '../../components/admin/navbar.vue'
import loading from '../../components/loading-red.vue'
import {  } from '@heroicons/vue/24/outline'
import moment from 'moment'
import textInput from '../../components/admin/textInput.vue'
import reps from '../../../assets/reps.json'
import AgencyInfo from '../../components/admin/agency/agencyInfo.vue'
import AppointmentInfo from '../../components/admin/agency/appointments.vue'
import Documents from '../../components/admin/agency/documents.vue'
import ApprovalCheckList from '../../components/admin/agency/approvalCheckList.vue'
import Logins from '../../components/admin/agency/logins.vue'

export default {
    name: "Agency View",
    data() {
        return {
            role: '',
            rocket_id: '',
            loading: true,
            update: false,
            active: 'approval',
            repOptions: reps,
            data: {
                agency_name: '',
                dba_name: '',
                agent_name: '',
                phone: '',
                email: '',
                address: '',
                address1: '',
                address2: '',
                city: '',
                state: '',
                zip: '',
                additional_states: [],
                carriers: [],
                agency_license: '',
                agency_license_file: '',
                agency_tax_id: '',
                agent_license: '',
                agent_license_eff: '',
                agent_license_exp: '',
                agent_license_file: '',
                agent_npn: '',
                eo_policy: '',
                eo_exp: '',
                eo_limit: '',
                eo_insurer: '',
                eo: '',
                agency_type: {},
                agency_logo: '',
                stage: '',
                document_id: '',
                rocket_rep: {},
                completed: false,
                approved: false,
                uip_created: false,
                appointed: false,
                created_at: '',
                aon: false, 
                beyond: false,
                dual: false,
                flow: false,
                neptune: false,
                palomar: false,
                sterling: false,
                wright: false,
                agency_logo: '',
                document_id: '',
                note: '',
                agreement: '',
                flow_package: '',
                beyond_package: '',
                sterling_package: ''
            },
            errors: [
                {
                    name: 'agency_name',
                    errors: []
                },
                {
                    name: 'agent_name',
                    errors: []
                },
                {
                    name: 'phone',
                    errors: []
                },
                {
                    name: 'email',
                    errors: []
                }
            ]
        }
    },
    async created() {
        this.moment = moment
        this.role = this.$route.meta.role
        this.rocket_id = this.$route.params.rocket_id

        await axios.post('/api/onboarding', {"rocket_id": this.rocket_id})
        .then(response => {
            const keys = Object.keys(this.data)

            keys.forEach(key => {
                if(key == 'additional_states' || key == 'carriers' || key == 'agency_type' || key == 'rocket_rep'){
                    this.data[key] = JSON.parse(response.data[key])
                } else {
                    if(response.data[key] == 0) {
                        this.data[key] = false
                    } else if(response.data[key] == 1) {
                        this.data[key] = true
                    } else {
                        this.data[key] = response.data[key]
                    }
                }
            })
        })

        this.active = 'agency'
        setTimeout(() => {
            this.active = 'approval'

            if(this.data.approved == true || !this.data.completed){
                this.active = 'agency'
            }
        }, 100);

        this.loading = false 
    },
    methods: {
        inputChange(id, value, errors){
            this.data[id] = value
            
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
        },
        async change(id, value, errors){
            this.data[id] = value

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
        },
        async cancelUpdate(){
            this.loading = true 

            await axios.post('/api/onboarding', {"rocket_id": this.rocket_id})
            .then(response => {
                const keys = Object.keys(this.data)

                keys.forEach(key => {
                    if(key == 'additional_states' || key == 'carriers' || key == 'agency_type' || key == 'rocket_rep'){
                        this.data[key] = JSON.parse(response.data[key])
                    } else {
                        if(response.data[key] == 0) {
                            this.data[key] = false
                        } else if(response.data[key] == 1) {
                            this.data[key] = true
                        } else {
                            this.data[key] = response.data[key]
                        }
                    }
                })
            })

            this.update = false
            this.loading = false

            this.$alert({
                title: 'Agency Information Update',
                text: 'Update cancelled successfully.',
                type: 'warn'
            })
        },
        async saveInfo(){
            this.loading = true

            let valid = true

            this.errors.forEach(item => {
                if(item.errors.length > 0){
                    valid = false

                    item.errors.forEach(error => {
                        this.$alert({
                            title: 'Validation Error',
                            text: error,
                            type: 'warn'
                        })
                    })
                }
            })

            if(valid){
                await axios.post('/api/onboarding', {'rocket_id': this.rocket_id, 'update': this.data})
                .then(response => {
                    this.$alert({
                        title: 'Information Updated',
                        text: 'Agency information updated successfully.',
                        type: 'success'
                    })
                })
                .catch(error => {
                    console.log(error.response)
                    const keys = Object.keys(error.response.data.message)

                    keys.forEach(key => {
                        error.response.data.message[key].forEach(error => {
                            this.$alert({
                                title: 'Validation Error',
                                text: error,
                                type: 'warn'
                            })
                        })
                    })
                })
            } else {
                this.$alert({
                    title: 'Update error',
                    text: 'Fields not valid, updates not saved.',
                    type: 'warn'
                })
            }

            this.update = !this.update
            this.loading = false
        }
    },
    components: {
        NavBar,
        loading,
        textInput,
        AgencyInfo,
        AppointmentInfo,
        Documents,
        ApprovalCheckList,
        Logins
    }
}
</script>

<style>
:root {
    /* Search Input */
    --vs-search-input-bg: #FD0000;

    /* Font */
    --vs-font-size: 1rem;
    --vs-line-height: 1.4;

    /* Disabled State */
    --vs-state-disabled-bg: #f89393;
    --vs-state-disabled-color: #4c4c4c;
    --vs-state-disabled-controls-color: #4c4c4c;
    --vs-state-disabled-cursor: not-allowed;

    /* Borders */
    --vs-border-color: var(--vs-colors--lightest);
    --vs-border-width: 0px;
    --vs-border-style: border-none;
    --vs-border-radius: 10px;

    /* Actions: house the component controls */
    --vs-actions-padding: 4px 6px 0 3px;

    /* Component Controls: Clear, Open Indicator */
    --vs-controls-color: #fff;
    --vs-controls-size: 1;
    --vs-controls--deselect-text-shadow: 0 1px 0 #fff;

    /* Selected */
    --vs-selected-bg: #FD0000;
    --vs-selected-color: #fff;
    --vs-selected-padding: 5px;
    --vs-selected-border-color: var(--vs-border-color);
    --vs-selected-border-style: border-none;
    --vs-selected-border-width: border-none;

    /* Dropdown */
    --vs-dropdown-bg: #fff;
    --vs-dropdown-color: inherit;
    --vs-dropdown-z-index: 1000;
    --vs-dropdown-min-width: 160px;
    --vs-dropdown-max-height: 350px;
    --vs-dropdown-box-shadow: 0px 3px 6px 0px var(--vs-colors--darkest);

    /* Options */
    --vs-dropdown-option-bg: #000;
    --vs-dropdown-option-color: var(--vs-dropdown-color);
    --vs-dropdown-option-padding: 3px 20px;

    /* Active State */
    --vs-dropdown-option--active-bg: #FD0000;
    --vs-dropdown-option--active-color: #fff;

    /* Transitions */
    --vs-transition-timing-function: cubic-bezier(1, -0.115, 0.975, 0.855);
    --vs-transition-duration: 150ms;
}
</style>