<template>
    <div class="w-full h-fit grid gap-8 my-6 p-6 bg-white rounded-md border-custom-gray border-opacity-20 border-[1px]">
        <div v-if="!data.approved" class="w-full p-6 rounded-md bg-custom-dark-blue text-white text-medium h-24 text-xl font-semibold">
            <p class="flex gap-6"><ExclamationTriangleIcon class="h-10" /><span class="my-auto">Approve agency information to adjust carrier logins..</span></p>
        </div>

        <div v-else class="grid gap-6">
            <div class="flex gap-6">
                <p class="my-auto text-custom-gray font-medium text-lg">Agency Carrier Logins</p>
                <button @click="saveLogins" class="float-left w-fit flex gap-2 px-4 py-[2px] bg-custom-gray bg-opacity-40 text-white rounded-md my-auto hover:bg-opacity-100">Save <PaperAirplaneIcon class="h-6" /></button>
            </div>

            <div class="grid grid-cols-4 gap-8 text-md font-light text-custom-gray pb-2 border-b-2 border-custom-gray border-opacity-20">
                <p>Carrier</p>
                <p>Appointment</p>
                <p>Username</p>
                <p>Password</p>
            </div>

            <div v-for="(value, key, index) in logins" :key="index" class="grid grid-cols-4 gap-8 text-sm text-custom-gray font-medium">
                <p class="my-auto">{{ key }}</p>

                <div class="w-fit grid grid-cols-2 border text-sm border-custom-gray rounded-md h-fit my-auto">
                    <button @click="value.direct = true" :class="value.direct ? 'bg-custom-gray text-white' : ''" class="px-2 col rounded-tl-sm rounded-bl-sm disabled:bg-opacity-40">Direct</button>
                    <button @click="value.direct = false" :class="!value.direct ? 'bg-custom-gray text-white' : ''" class="px-2 col rounded-tr-sm rounded-br-sm disabled:bg-opacity-40">Rocket MGA</button>
                </div>

                <div v-if="!value.direct" class="flex gap-8 h-fit text-sm my-auto">
                    <textInput @inputUpdate="loginUpdate" :id="key" :inputValue="value.username" :index="'username'" />
                </div>

                <div v-if="!value.direct" class="flex gap-8 h-fit text-sm my-auto">
                    <textInput @inputUpdate="loginUpdate" :id="key" :inputValue="value.password" :index="'password'" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import textInput from '../textInput.vue'
import textMask from '../textMask.vue'
import { PaperAirplaneIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline'
import axios from 'axios'

export default {
    name: "Agency Logins",
    props: {
        rocket_id: String,
        data: Object,
        update: Boolean
    },
    data() {
        return {
            logins: {}
        }
    },
    async created(){
        this.$emit('loading')

        await axios.post('/api/logins', {"rocket_id": this.rocket_id})
        .then(response => {
            const keys = Object.keys(response.data.logins)

            keys.forEach(carrier => {
                if(carrier != 'rocket_id' && carrier != 'created_at' && carrier != 'updated_at'){
                    this.logins[carrier] = JSON.parse(response.data.logins[carrier])
                }
            })
        })

        this.$emit('loading')
    },
    watch: {

    },
    methods: {
        loginUpdate(id, value, errors, index){
            this.logins[id][index] = value
        },
        async saveLogins(){
            await axios.post('/api/logins', {
                "rocket_id": this.rocket_id,
                "update": this.logins
            })
            .then(response => {
                this.$alert({
                    title: 'Agency Logins Update',
                    text: response.data.message,
                    type: 'success'
                })
            })
        }
    },
    components: {
        textInput,
        textMask,
        PaperAirplaneIcon,
        ExclamationTriangleIcon
    }
}
</script>