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
                    <p class="flex gap-4 float-right text-md my-auto text-custom-gray hover:text-custom-red hover:cursor-pointer">View all<ArrowRightCircleIcon class="h-6 my-auto" /></p>
                </div>

                <!--Dynamic Notification-->
                <div class="w-full rounded-xl bg-custom-gray bg-opacity-5 p-4">
                    <div class="flex gap-6">
                        <div class="h-[10px] w-[10px] rounded-full bg-custom-red my-auto"></div>

                        <p class="text-custom-gray text-md font-medium"><span class="text-custom-red">Agency Name</span> has completed their onboarding application!</p>
                    </div>
                </div>
            </div>

            <!--Analytics-->
            <div class="float-right grid gap-8 w-[30%] h-fit p-6 rounded-lg border-[1px] border-custom-gray border-opacity-20 bg-white">
                <p class="text-xl text-custom-gray font-semibold">Analytics</p>

                <!--Agents Currently Onboarding-->
                <div class="flow-root w-full border-b-2 border-custom-gray pb-2 text-custom-gray">
                    <p class="float-left flex gap-4"><UserCircleIcon class="h-6" />Agents Onboarding</p>
                    <p class="float-right">{{ agents_onboarding }}</p>
                </div>

                <!--Agents Under Review-->
                <div class="flow-root w-full border-b-2 border-custom-gray pb-2 text-custom-gray">
                    <p class="float-left flex gap-4"><ClipboardDocumentCheckIcon class="h-6" />Agents Under Review</p>
                    <p class="float-right">{{ agents_review }}</p>
                </div>

                <!--Agents Completed-->
                <div class="flow-root w-full border-b-2 border-custom-gray pb-2 text-custom-gray">
                    <p class="float-left flex gap-4"><CheckBadgeIcon class="h-6" />Agents Apointed</p>
                    <p class="float-right">{{ agents_completed }}</p>
                </div>

                <!--Conversion %-->
                <div class="flow-root w-full border-b-2 border-custom-gray pb-2 text-custom-gray">
                    <p class="float-left flex gap-4"><ReceiptPercentIcon class="h-6" />Conversion %</p>
                    <p class="float-right">{{ conversion }}%</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment'
import NavBar from '../../components/admin/navbar.vue'
import loading from '../../components/loading-red.vue'
import { ArrowRightCircleIcon, UserCircleIcon, ClipboardDocumentCheckIcon, CheckBadgeIcon, ReceiptPercentIcon } from '@heroicons/vue/24/outline'


export default {
    name: "Admin Dashboard",
    data() {
        return {
            loading: true,
            role: '',
            first_name: '',
            date: '',
            agents_onboarding: '',
            agents_review: '',
            agents_completed: '',
            conversion: '',
            quickNotifications: [],
            notifications: []
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
            console.log(response)
        })

        this.loading = false
    },
    components: {
        NavBar,
        ArrowRightCircleIcon,
        UserCircleIcon,
        ClipboardDocumentCheckIcon,
        CheckBadgeIcon,
        ReceiptPercentIcon,
        loading
    }
}
</script>