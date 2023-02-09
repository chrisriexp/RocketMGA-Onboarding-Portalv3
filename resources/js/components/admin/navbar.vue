<template>
    <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-8 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0" aria-label="Sidebar">
        <div class="h-full px-4 pb-4 overflow-y-auto bg-white">
            <div class="grid gap-2 h-fit">
                <img src="../../../assets/logo-red.svg" alt="RocketMGA Logo" class="pb-4 border-b-2 mb-6">
                <router-link :to="{name: ''}" :class="active == 'profile' ? 'text-white bg-custom-red' : ''" class="flex gap-4 align-middle font-medium rounded-lg py-2 px-2 hover:bg-custom-gray hover:bg-opacity-10 hover:text-custom-red"><UserCircleIcon class="h-6 my-auto" />Profile</router-link>
                <router-link :to="{name: 'AdminDashboard'}" :class="active == 'dashboard' ? 'text-white bg-custom-red' : ''" class="flex gap-4 align-middle font-medium rounded-lg py-2 px-2 hover:bg-custom-gray hover:bg-opacity-10 hover:text-custom-red"><HomeIcon class="h-6 my-auto" />Dashboard</router-link>
                <router-link :to="{name: ''}" :class="active == 'agents' ? 'text-white bg-custom-red' : ''" class="flex gap-4 align-middle font-medium rounded-lg py-2 px-2 hover:bg-custom-gray hover:bg-opacity-10 hover:text-custom-red"><UserGroupIcon class="h-6 my-auto" />Agents</router-link>
                <router-link :to="{name: ''}" :class="active == 'documents' ? 'text-white bg-custom-red' : ''" class="flex gap-4 align-middle font-medium rounded-lg py-2 px-2 hover:bg-custom-gray hover:bg-opacity-10 hover:text-custom-red"><DocumentTextIcon class="h-6 my-auto" />Documents</router-link>
                <router-link :to="{name: ''}" :class="active == 'review' ? 'text-white bg-custom-red' : ''" class="flex gap-4 align-middle font-medium rounded-lg py-2 px-2 hover:bg-custom-gray hover:bg-opacity-10 hover:text-custom-red"><ClipboardDocumentCheckIcon class="h-6 my-auto" />Under Review</router-link> 
                <router-link :to="{name: ''}" :class="active == 'appointed' ? 'text-white bg-custom-red' : ''" class="flex gap-4 align-middle font-medium rounded-lg py-2 px-2 hover:bg-custom-gray hover:bg-opacity-10 hover:text-custom-red"><CheckBadgeIcon class="h-6 my-auto" />Appointed</router-link>  
                <button @click="logout" class="flex gap-4 align-middle font-medium rounded-lg py-2 px-2 hover:bg-custom-gray hover:bg-opacity-10 hover:text-custom-red"><ArrowLeftOnRectangleIcon class="h-6 my-auto" />Sign Out</button>             
            </div>

            <div class="bottom-2 fixed text-custom-gray text-sm mx-auto w-fit pl-4">
                <p>© 2023 <a target="_blank" href="https://rocketmga.com">ROCKETMGA.COM</a></p>
                
            </div>
        </div>
    </aside>

    <!-- <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg md:hidden">
        <span>Open sidebar</span>
    </button> -->
</template>

<script>
import { HomeIcon, ArrowLeftOnRectangleIcon, UserCircleIcon, UserGroupIcon, DocumentTextIcon, ClipboardDocumentCheckIcon, CheckBadgeIcon } from '@heroicons/vue/24/outline'

export default {
    name: "Nav Bar",
    methods: {
        async logout(){
            await axios.get('/api/logout')
            .then(response => {
                localStorage.removeItem('token')
                this.$router.push({name: 'AdminLogin'})
                this.$alert({
                    title: 'Logout',
                    text: response.data.message,
                    type: 'success'
                })
            })
        }
    },
    props: {
        active: String
    },
    components: {
        HomeIcon,
        UserCircleIcon,
        UserGroupIcon,
        DocumentTextIcon,
        ClipboardDocumentCheckIcon,
        CheckBadgeIcon,
        ArrowLeftOnRectangleIcon
    }
}
</script>