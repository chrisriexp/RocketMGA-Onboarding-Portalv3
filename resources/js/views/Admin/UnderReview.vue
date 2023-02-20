<template>
    <NavBar :active="'review'" :role="role" />

    <div id="loading" :class="loading ? '' : 'hidden'" class="w-full grid justify-items-center z-10 fixed">
        <loading class="mt-64" />
    </div>

    <div class="sm:ml-64 p-10 h-screen bg-custom-bg">
        <div class="grid gap-2 text-custom-gray">
            <h1 class="font-semibold text-2xl">Agents Under Review</h1>
            <p>Below is the list of agents that have completed their onboarding application and are now under review.</p>
        </div>

        <div class="mt-12 grid grid-cols-5 gap-6 text-custom-gray font-medium pb-2 border-b-2 border-custom-gray border-opacity-20 pl-2">
            <p>Agency Name</p>
            <p>Principal Agent</p>
            <p>Phone</p>
            <p>Email</p>
            <p>Stage</p>
        </div>

        <div class="h-[600px] mt-8 overflow-y-scroll scrollbar">
            <div class="h-fit grid gap-4">
                <div v-for="(agent, index) in agents" :key="index" @click="gotoAgency(agent.rocket_id)" class="grid grid-cols-5 gap-6 text-sm text-custom-gray bg-white border-custom-gray border-opacity-20 border-[1px] p-2 rounded-md hover:inner-border-2 hover:cursor-pointer">
                    <p>{{ agent.agency_name }}</p>
                    <p>{{ agent.agent_name }}</p>
                    <input v-model="agent.phone" disabled v-mask="'(###) ###-####'" class="bg-white hover:cursor-pointer">
                    <p>{{ agent.email }}</p>
                    <span class="flex gap-2 my-auto"><div class="w-[8px] h-[8px] my-auto rounded-full bg-custom-red"></div> Agency Under Review</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import NavBar from '../../components/admin/navbar.vue'
import loading from '../../components/loading-red.vue'
import {mask} from 'vue-the-mask'

export default {
    name: 'AdminReview',
    data(){
        return {
            role: '',
            loading: true,
            agents:[]
        }
    },
    async created(){
        this.role = this.$route.meta.role

        await axios.get('/api/onboarding/review')
        .then(response => {
            const keys = Object.keys(response.data.agents)

            keys.forEach(key => {
                this.agents.push(response.data.agents[key])
            })
        })

        this.loading = false
    },
    methods: {
        gotoAgency(id){
            this.$router.push({name: 'AgencyView', params:{rocket_id: id}})
        }
    },
    directives: {mask},
    components: {
        NavBar,
        loading
    }
}
</script>