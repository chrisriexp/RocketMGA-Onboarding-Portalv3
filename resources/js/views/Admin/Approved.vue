<template>
    <NavBar :active="'approved'" :role="role" />

    <div id="loading" :class="loading ? '' : 'hidden'" class="w-full grid justify-items-center z-10 fixed">
        <loading class="mt-64" />
    </div>

    <div class="sm:ml-64 p-10 h-screen bg-custom-bg">
        <div class="grid gap-2 text-custom-gray">
            <h1 class="font-semibold text-2xl">Agents Approved</h1>
            <p>Below is the list of approved agents.</p>
        </div>

        <div class="w-fit grid grid-cols-2 border text-md border-custom-gray rounded-md h-fit mt-8">
            <button @click=changeView(true) :class="appointed ? 'bg-custom-gray text-white' : ''" class="px-2 col rounded-tl-sm rounded-bl-sm disabled:bg-opacity-40">Appointed Agents</button>
            <button @click=changeView(false) :class="!appointed ? 'bg-custom-gray text-white' : ''" class="px-2 col rounded-tr-sm rounded-br-sm disabled:bg-opacity-40">Not Appointed</button>
        </div>

        <div class="mt-6 grid grid-cols-4 gap-6 text-custom-gray font-medium pb-2 border-b-2 border-custom-gray border-opacity-20 pl-2">
            <p>Agency Name</p>
            <p>Principal Agent</p>
            <p>Phone</p>
            <p>Email</p>
        </div>

        <div class="h-[600px] mt-8 overflow-y-scroll scrollbar">
            <div class="h-fit grid gap-4">
                <div v-for="(agent, index) in agentsView" :key="index" @click="gotoAgency(agent.rocket_id)" class="grid grid-cols-4 gap-6 text-sm text-custom-gray bg-white border-custom-gray border-opacity-20 border-[1px] p-2 rounded-md hover:inner-border-2 hover:cursor-pointer">
                    <p>{{ agent.agency_name }}</p>
                    <p>{{ agent.agent_name }}</p>
                    <input v-model="agent.phone" disabled v-mask="'(###) ###-####'" class="bg-white hover:cursor-pointer">
                    <p>{{ agent.email }}</p>
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
    name: 'AdminApproved',
    data(){
        return {
            role: '',
            loading: true,
            appointed: false,
            agents: [],
            agentsView:[]
        }
    },
    async created(){
        this.role = this.$route.meta.role

        await axios.get('/api/onboarding/approved')
        .then(response => {
            const keys = Object.keys(response.data.agents)

            keys.forEach(key => {
                this.agents.push(response.data.agents[key])
            })
        })

        this.agents.forEach(agent => {
            if(!agent.appointed){
                this.agentsView.push(agent)
            }
        })

        this.loading = false
    },
    methods: {
        gotoAgency(id){
            this.$router.push({name: 'AgencyView', params:{rocket_id: id}})
        },
        changeView(isAppointed){
            this.appointed = isAppointed

            this.agentsView = []

            if(this.appointed){
                this.agents.forEach(agent => {
                    if(agent.appointed){
                        this.agentsView.push(agent)
                    }
                })
            } else {
                this.agents.forEach(agent => {
                    if(!agent.appointed){
                        this.agentsView.push(agent)
                    }
                })
            }
        }
    },
    directives: {mask},
    components: {
        NavBar,
        loading
    }
}
</script>