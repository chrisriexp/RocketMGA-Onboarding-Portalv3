<template>
    <NavBar :active="'dashboard'" :role="role" />

    <div id="loading" :class="loading ? '' : 'hidden'" class="w-full grid justify-items-center z-10 fixed">
        <loading class="mt-64" />
    </div>

    <div class="sm:ml-64 p-10 h-screen bg-custom-bg">
        <div class="w-full flow-root">
            <div class="float-left grid gap-2">
                <p class="text-3xl text-custom-gray">Welcome back <span>{{ first_name }}</span>!</p>
                <p class="text-custom-gray font-light">Here is some important information for you:</p>
            </div>

            <div class="float-right w-fit rounded-lg bg-white p-2">
                <p>{{ moment(date).format("dddd, MMMM Do YYYY, h:mm a") }}</p>
            </div>
        </div>

        <div class="flow-root gap-12 mt-12">
            <!--Notifications-->
            <div class="float-left grid gap-6 w-[65%] h-fit p-6 rounded-lg border-[1px] border-custom-gray border-opacity-20 bg-white">
                <div class="flow-root w-full">
                    <p class="float-left text-xl text-custom-gray font-semibold">Notifications</p>
                    <p @click="notificationPopup = true" class="flex gap-4 float-right text-md my-auto text-custom-gray hover:text-custom-red hover:cursor-pointer">View all<ArrowRightCircleIcon class="h-6 my-auto" /></p>
                </div>

                <!--Dynamic Notification-->
                <div v-for="(notification, index) in quickNotifications" :key="index" @click="gotoAgency(notification.rocket_id)" class="w-full rounded-xl bg-custom-gray bg-opacity-5 p-4 hover:cursor-pointer">
                    <div class="flex gap-6">
                        <div v-if="!notification.read" class="h-[10px] w-[10px] rounded-full bg-custom-red my-auto"></div>
                        <div v-else class="h-[10px] w-[10px] rounded-full bg-transparent my-auto"></div>

                        <p class="text-custom-gray text-md font-medium"><span class="text-custom-red">{{ notification.agency_name }}</span> has completed their onboarding application!</p>
                    </div>
                </div>
            </div>

            <!--Notification Popup-->
            <div :class="!notificationPopup ? 'hidden' : ''" class="w-[800px] h-fit grid gap-6 shadow-newdrop bg-white border-custom-gray border-opacity-20 p-6 rounded-lg z-20 absolute ml-[20%] mt mt-[-3%]">
                <div class="flow-root w-full">
                    <p class="float-left text-xl text-custom-gray font-semibold">Notifications</p>
                    <p @click="notificationPopup = false" class="flex gap-4 float-right text-md my-auto text-custom-gray hover:text-custom-red hover:cursor-pointer">Close<XCircleIcon class="h-6 my-auto" /></p>
                </div>

                <!--Dynamic Notification-->
                <div class="h-[400px] overflow-y-scroll scrollbar">
                    <div class="h-fit grid gap-6">
                        <div v-for="(notification, index) in notifications" :key="index" @click="gotoAgency(notification.rocket_id, true)" class="w-full h-fit rounded-xl bg-custom-gray bg-opacity-5 p-4 hover:cursor-pointer">
                            <div class="flex gap-6">
                                <div v-if="!notification.read" class="h-[10px] w-[10px] rounded-full bg-custom-red my-auto"></div>
                                <div v-else class="h-[10px] w-[10px] rounded-full bg-transparent my-auto"></div>

                                <p class="text-custom-gray text-md font-medium"><span class="text-custom-red">{{ notification.agency_name }}</span> has completed their onboarding application!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--Analytics-->
            <div class="float-right grid gap-8 w-[30%] h-fit p-6 rounded-lg border-[1px] border-custom-gray border-opacity-20 bg-white">
                <p class="text-xl text-custom-gray font-semibold">Analytics</p>

                <!--Agents Currently Onboarding-->
                <div class="flow-root w-full border-b-2 border-custom-gray pb-2 text-custom-gray">
                    <p class="float-left flex gap-4"><UserCircleIcon class="h-6" />Agents Onboarding</p>
                    <p class="float-right px-2">{{ agents_onboarding }}</p>
                </div>

                <!--Agents Under Review-->
                <div class="flow-root w-full border-b-2 border-custom-gray pb-2 text-custom-gray">
                    <p class="float-left flex gap-4"><ClipboardDocumentCheckIcon class="h-6" />Agents Under Review</p>
                    <p class="float-right px-2">{{ agents_review }}</p>
                </div>

                <!--Agents Completed-->
                <div class="flow-root w-full border-b-2 border-custom-gray pb-2 text-custom-gray">
                    <p class="float-left flex gap-4"><CheckBadgeIcon class="h-6" />Agents Apointed</p>
                    <p class="float-right px-2">{{ agents_appointed }}</p>
                </div>

                <!--Conversion %-->
                <div class="flow-root w-full border-b-2 border-custom-gray pb-2 text-custom-gray">
                    <p class="float-left flex gap-4"><ReceiptPercentIcon class="h-6" />Conversion %</p>
                    <p class="float-right px-2">{{ conversion }}%</p>
                </div>
            </div>
        </div>

        <!--Agents Quick View-->
        <div class="grid gap-4 w-full h-fit mt-12 p-6 rounded-lg border-[1px] border-custom-gray border-opacity-20 bg-white text-sm">
            <div class="flow-root w-full">
                <p class="float-left text-xl text-custom-gray font-semibold">Agents Onboarding</p>
                <router-link to="/admin/agents" class="flex gap-4 float-right text-md my-auto text-custom-gray hover:text-custom-red hover:cursor-pointer">View all<ArrowRightCircleIcon class="h-6 my-auto" /></router-link>
            </div>

            <div class="grid grid-cols-5 gap-6 w-full h-fit pb-2 border-b-2 border-custom-gray border-opacity-20 text-custom-gray font-medium pl-2">
                <p>Agency Name</p>
                <p>Principal Agent</p>
                <p>Phone</p>
                <p>Email</p>
                <p>Stage</p>
            </div>

            <div v-for="(agent, index) in agents" :key="index" @click="gotoAgency(agent.rocket_id, false)" class="grid grid-cols-5 gap-6 w-full h-fit p-2 text-custom-gray border-2 border-opacity-20 border-custom-gray rounded-md hover:cursor-pointer hover:inner-border-2">
                <p>{{ agent.agency_name }}</p>
                <p>{{ agent.agent_name }}</p>
                <input v-model="agent.phone" disabled v-mask="'(###) ###-####'" class="bg-white  h-fit hover:cursor-pointer">
                <p>{{ agent.email }}</p>

                <span v-if="agent.appointed" class="flex gap-2 my-auto"><div class="w-[8px] h-[8px] my-auto rounded-full bg-custom-red"></div> Agency Appointed</span>
                <span v-else-if="agent.approved" class="flex gap-2 my-auto"><div class="w-[8px] h-[8px] my-auto rounded-full bg-custom-red"></div> Agency in Training</span>
                <span v-else-if="agent.completed" class="flex gap-2 my-auto"><div class="w-[8px] h-[8px] my-auto rounded-full bg-custom-red"></div> Agency Under Review</span>
                <span v-else-if="agent.stage == 'agency'" class="flex gap-2 my-auto"><div class="w-[8px] h-[8px] my-auto rounded-full bg-custom-red"></div> Agency Information</span>
                <span v-else-if="agent.stage == 'carrier'" class="flex gap-2 my-auto"><div class="w-[8px] h-[8px] my-auto rounded-full bg-custom-red"></div> Carrier Information</span>
                <span v-else-if="agent.stage == 'entity'" class="flex gap-2 my-auto"><div class="w-[8px] h-[8px] my-auto rounded-full bg-custom-red"></div> Entity Information</span>
                <span v-else-if="agent.stage == 'eo'" class="flex gap-2 my-auto"><div class="w-[8px] h-[8px] my-auto rounded-full bg-custom-red"></div> E&O Information</span>
                <span v-else-if="agent.stage == 'agreement'" class="flex gap-2 my-auto"><div class="w-[8px] h-[8px] my-auto rounded-full bg-custom-red"></div> Agreement</span>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment'
import NavBar from '../../components/admin/navbar.vue'
import loading from '../../components/loading-red.vue'
import { ArrowRightCircleIcon, UserCircleIcon, ClipboardDocumentCheckIcon, CheckBadgeIcon, ReceiptPercentIcon, XCircleIcon } from '@heroicons/vue/24/outline'
import {mask} from 'vue-the-mask'


export default {
    name: "Admin Dashboard",
    data() {
        return {
            notificationPopup: false,
            loading: true,
            role: '',
            first_name: '',
            date: '',
            agents_onboarding: '',
            agents_review: '',
            agents_appointed: '',
            conversion: '',
            quickNotifications: [],
            notifications: [],
            agents: []
        }
    },
    async created() {
        this.role = this.$route.meta.role
        this.moment = moment
        this.date = new Date()

        await axios.get('/api/user')
        .then(response => {
            const names = response.data.name.split(" ")

            this.first_name = names[0]
        })

        await axios.get('/api/notification')
        .then(response => {
            const keys = Object.keys(response.data)
            
            keys.forEach(key => {
                this.notifications.push(response.data[key])
            })

            this.quickNotifications = this.notifications.slice(0, 3)
        })

        await axios.get('/api/onboarding/dashboard')
        .then(response => {
            const keys = Object.keys(response.data.agents)

            keys.forEach(key => {
                this.agents.push(response.data.agents[key])
            })

            this.agents_onboarding = response.data.onboarding
            this.agents_review = response.data.review
            this.agents_appointed = response.data.appointed
            this.conversion = response.data.appointed / response.data.totalCount * 100
        })

        this.loading = false
    },
    methods: {
        async gotoAgency(id, notification){
            if(notification){
                await axios.post('/api/notification/read', {
                    "rocket_id": id
                })
            }

            this.$router.push({name: 'AgencyView', params:{rocket_id: id}})
        }
    },
    directives: {mask},
    components: {
        NavBar,
        ArrowRightCircleIcon,
        UserCircleIcon,
        ClipboardDocumentCheckIcon,
        CheckBadgeIcon,
        ReceiptPercentIcon,
        loading,
        XCircleIcon
    }
}
</script>