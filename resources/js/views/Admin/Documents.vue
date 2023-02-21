<template>
    <NavBar :active="'documents'" :role="role" />

    <div id="loading" :class="loading ? '' : 'hidden'" class="w-full grid justify-items-center z-10 fixed">
        <loading class="mt-64" />
    </div>

    <div class="sm:ml-64 p-10 h-screen bg-custom-bg">
        <div class="grid gap-2 text-custom-gray">
            <h1 class="font-semibold text-2xl">Agent Documents</h1>
            <p>Below you can find the full list of agents and their uploaded documents and rocket agreement.</p>
        </div>

        <div class="mt-12 grid grid-cols-6 gap-6 text-custom-gray font-medium pb-2 border-b-2 border-custom-gray border-opacity-20 pl-2">
            <p>Agency Name</p>
            <p>Agency License</p>
            <p>Agent License</p>
            <p>E&O</p>
            <p>Rocket Agreement</p>
            <p>Agency Logo</p>
        </div>

        <div class="h-[600px] mt-8 overflow-y-scroll scrollbar">
            <div class="h-fit grid gap-4">
                <div v-for="(agent, index) in agents" :key="index" class="grid grid-cols-6 gap-6 text-sm text-custom-gray bg-white border-custom-gray border-opacity-20 border-[1px] p-2 rounded-md hover:inner-border-2">
                    <p @click="gotoAgency(agent.rocket_id)" class="hover:cursor-pointer">{{ agent.agency_name }}</p>

                    <a v-if="agent.agency_license_file != null" target="_blank" :href="agent.agency_license_file" class="text-custom-red">Click to View</a>
                    <a v-else target="_blank" :href="agent.agency_license_file" class="text-custom-gray opacity-40">File not uploaded</a>

                    <a v-if="agent.agent_license_file != null" target="_blank" :href="agent.agent_license_file" class="text-custom-red">Click to View</a>
                    <a v-else target="_blank" :href="agent.agent_license_file" class="text-custom-gray opacity-40">File not uploaded</a>

                    <a v-if="agent.eo != null" target="_blank" :href="agent.eo" class="text-custom-red">Click to View</a>
                    <a v-else target="_blank" :href="agent.eo" class="text-custom-gray opacity-40">File not uploaded</a>

                    <button @click="downloadAgreement(agent.rocket_id, agent.agency_name, agent.document_id)" v-if="agent.document_id != null" :disabled="agreement_disabled" class="w-fit text-custom-red pl-2">Click to Download</button>
                    <a v-else target="_blank" :href="agent.document_id" class="text-custom-gray opacity-40 pl-2">Document not created</a>

                    <a v-if="agent.agency_logo != null" target="_blank" :href="agent.agency_logo" class="text-custom-red pl-2">Click to View</a>
                    <a v-else target="_blank" :href="agent.agency_logo" class="text-custom-gray opacity-40 pl-2">File not uploaded</a>
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
    name: 'AdminDocuments',
    data(){
        return {
            role: '',
            loading: true,
            agents:[],
            agreement_disabled: false,
            api: {
                // apiKey: '8135da5570abd90097a2bcc0dbbce76d1decd484'
                apiKey: '8135da5570abd90097a2bcc0dbbce76d1decd484', //SandBox
            }
        }
    },
    async created(){
        this.role = this.$route.meta.role

        await axios.get('/api/onboarding/documents')
        .then(response => {
            const keys = Object.keys(response.data.agents)

            keys.forEach(key => {
                this.agents.push(response.data.agents[key])
            })
        })

        const files = ['agency_license_file', 'agent_license_file', 'eo', 'agency_logo']

        this.agents.forEach(agent => {
            files.forEach(file => {
                if(agent[file] != null){
                    axios.get('/api/file/' + agent[file])
                    .then(response => {
                        // this[file] = "https://onboarding.rocketmga.com" + response.data.path
                        agent[file] = "http://localhost:8000" + response.data.path
                    })
                }
            })
        })

        this.loading = false
    },
    methods: {
        gotoAgency(id){
            this.$router.push({name: 'AgencyView', params:{rocket_id: id}})
        },
        async downloadAgreement(rocket_id, agency, id){
            //Disable Agreement Button
            this.agreement_disabled = true
            //Show Loading Spinner
            this.loading = true

            const myHeaders = {
                headers: {'Authorization': `API-Key ${this.api.apiKey}`, 'Content-Type': 'application/pdf'},
                responseType: 'blob'
            }
            await axios.get(`https://api.pandadoc.com/public/v1/documents/${id}/download`, myHeaders)
            .then(response => {
                const url = window.URL.createObjectURL(new Blob([response.data], {type:"application/pdf"}));
                const link = document.createElement('a');
                link.href = url;
                window.open(url);
                link.setAttribute('download', `${agency}-${rocket_id}.pdf`);
                document.body.appendChild(link);
                link.click();
            })
            //Hide Loading Spinner
            this.loading = false
            //Enable Agreement Button
            this.agreement_disabled = false
        },
    },
    directives: {mask},
    components: {
        NavBar,
        loading
    }
}
</script>