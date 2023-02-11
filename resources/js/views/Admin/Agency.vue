<template>
    <NavBar :role="role" />

    <div id="loading" :class="loading ? '' : 'hidden'" class="w-full grid justify-items-center z-10 fixed">
        <loading class="mt-64" />
    </div>

    <div class="sm:ml-64 p-10 h-screen bg-custom-bg">
        <div class="flow-root w-full">
            <div class="float-left w-fit h-fit font-bold text-2xl text-custom-gray ">
                <p>{{ data.agency_name }} - {{ rocket_id }}</p>
            </div>

            <div class="float-right w-fit h-fit my-auto rounded-lg bg-white p-2">
                <p><span class="font-medium">Created:</span> {{ moment(data.created_at).format("dddd, MMMM Do YYYY, h:mm a") }}</p>
            </div>
        </div>
    </div>
</template>

<script>
import NavBar from '../../components/admin/navbar.vue'
import loading from '../../components/loading-red.vue'
import {  } from '@heroicons/vue/24/outline'
import moment from 'moment'

export default {
    name: "Agency View",
    data() {
        return {
            role: '',
            rocket_id: '',
            loading: true,
            data: {}
        }
    },
    async created() {
        this.moment = moment
        this.role = this.$route.meta.role
        this.rocket_id = this.$route.params.rocket_id

        await axios.post('/api/onboarding', {"rocket_id": this.rocket_id})
        .then(response => {
            this.data = response.data
        })

        console.log(this.data)

        this.loading = false 
    },
    components: {
        NavBar,
        loading
    }
}
</script>