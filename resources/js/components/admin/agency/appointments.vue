<template>
    <div v-if="data.approved" class="w-full h-fit grid gap-4 mt-4 bg-white rounded-md border-custom-gray border-opacity-20 border-[1px]">
        <div @click="showUIP = !showUIP" :class="showUIP ? ''  : ''" class="w-full flow-root justify-items-center hover:cursor-pointer p-0">
            <div class="float-left flex gap-6 ml-6 mt-[14px]">
                <p class="my-auto text-custom-gray font-medium text-lg">Appointment UIP Info</p>
                <button @click="saveUIPs" class="float-left w-fit flex gap-2 px-4 py-[2px] bg-custom-gray bg-opacity-40 text-white rounded-md my-auto hover:bg-opacity-100">Save <PaperAirplaneIcon class="h-6" /></button>
            </div>

            <div class="float-right my-auto h-fit w-fit p-4">
                <ChevronUpIcon v-if="!showUIP" class="h-6 text-custom-gray" />
                <ChevronDownIcon v-else class="h-6 text-custom-gray" />
            </div>
        </div>

        <div v-if="showUIP" class="w-full grid gap-4 p-4">
            <div v-for="(value, key, index) in uips" :key="index" class="w-full flex gap-4 rounded-md p-2 border-2 border-custom-gray border-opacity-20">
                <p class="w-36 text-sm my-auto text-custom-gray">{{ key }}</p>

                <div class="w-fit grid grid-cols-2 border text-sm border-custom-gray rounded-md h-fit my-auto">
                    <button @click="value.direct = true" :class="value.direct ? 'bg-custom-gray text-white' : ''" class="px-2 col rounded-tl-sm rounded-bl-sm disabled:bg-opacity-40">Direct</button>
                    <button @click="value.direct = false" :class="!value.direct ? 'bg-custom-gray text-white' : ''" class="px-2 col rounded-tr-sm rounded-br-sm disabled:bg-opacity-40">Rocket MGA</button>
                </div>

                <div v-if="!value.direct" class="flex gap-8 h-fit text-sm my-auto">
                    <div v-for="(uip, uipIndex) in value" :key="uipIndex">
                        <div v-if="uipIndex != 'direct'" class="flex gap-2">
                            <p v-if="key != 'Beyond Flood'" class="text-left font-light my-auto">{{ uipIndex }}</p>
                            <p v-else class="text-left font-light my-auto">bf_id</p>
                            <textInput @inputUpdate="uipUpdate" :id="key" :inputValue="uip" :index="uipIndex" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="w-full h-fit grid gap-8 my-6 p-6 bg-white rounded-md border-custom-gray border-opacity-20 border-[1px]">
        <div class="grid w-fit h-fit">
            <h2 class="font-medium text-2xl text-custom-gray">Appointment Information</h2>
            <p class="text-md font-light text-custom-gray">Below is the agency's appointment information..</p>
        </div>

        <div v-if="!data.approved" class="w-full p-6 rounded-md bg-custom-dark-blue text-white text-medium h-24 text-xl font-semibold">
            <p class="flex gap-6"><ExclamationTriangleIcon class="h-10" /><span class="my-auto">Approve agency information to create carrier UIPs..</span></p>
        </div>

        <div class="grid gap-4 w-fit h-fit">
            <h2 class="text-xl text-custom-gray">Direct Appointments/Not Rocket MGA</h2>

            <div class="flex gap-6 w-full pb-4 overflow-x-scroll scrollbar">
                <div v-for="(carrier, index) in form.carriers" :key="index"  class="flex gap-2">
                    <textInput @inputUpdate="inputChange" :index="index" :inputValue="carrier.name" :isdisabled=!update style="max-width: 180px;" />
                    <button @click="remove('carriers', index)" :disabled="!update" class="text-custom-red disabled:text-[#f89393]"><TrashIcon class="h-6 my-auto" /></button>
                </div>

                <button @click="add('carriers')" :disabled="!update" class="text-custom-red disabled:text-[#f89393]"><PlusCircleIcon class="h-6 my-auto" /></button>
            </div>
        </div>

        <div class="grid gap-4 w-fit h-fit">
            <h2 class="text-xl text-custom-gray">Appointed States</h2>

            <div class="flex gap-6 w-full h-[150px] pb-4 overflow-x-scroll scrollbar">
                <div v-for="(state, index) in form.additional_states" :key="index"  class="flex gap-2 h-fit">
                    <v-select style="min-width: 180px;"
                        code="code"
                        v-model="form.additional_states[index]"
                        :options="options"
                        label="name"
                        class="h-fit text-sm font-medium rounded-xl w-fit bg-custom-red disabled:text-custom-gray"
                        :disabled="!update"
                    ></v-select>
                    <button @click="remove('state', index)" :disabled="!update" class="text-custom-red disabled:text-[#f89393]"><TrashIcon class="h-6 my-auto" /></button>
                </div>

                <button @click="add('state')" :disabled="!update" class="text-custom-red disabled:text-[#f89393] h-fit mt-[5px]"><PlusCircleIcon class="h-6 my-auto" /></button>
            </div>
        </div>
    </div>
</template>

<script>
import textInput from '../textInput.vue'
import textMask from '../textMask.vue'
import states from '../../../../assets/states.json'
import { ExclamationTriangleIcon, TrashIcon, PlusCircleIcon, PaperAirplaneIcon, ChevronUpIcon, ChevronDownIcon } from '@heroicons/vue/24/solid'

export default {
    name: "Appointment Info",
    props: {
        rocket_id: String,
        data: Object,
        update: Boolean
    },
    data() {
        return {
            showUIP: false,
            options: states,
            form: {
                additional_states: [
                    {
                        name: null,
                        code: null
                    }
                ],
                carriers: [
                    {
                        name: ''
                    }
                ]
            },
            uips: {}
        }
    },
    async created(){
        const keys = Object.keys(this.form)
        keys.forEach(key => {
            this.form[key] = this.data[key]
        })

        if(this.data.approved){
            await axios.post('/api/appointment', {"rocket_id": this.rocket_id})
            .then(response => {
                const carriers = ['AON Edge', 'Beyond Flood', 'Dual Flood', 'Flow Flood', 'Neptune', 'Palomar', 'Sterling', 'Wright']

                carriers.forEach(carrier => {
                    this.uips[carrier] = JSON.parse(response.data.appointments[carrier])
                })
            })
        }
    },
    watch: {

    },
    methods: {
        async saveUIPs() {
            this.$emit('loading')

            await axios.post('/api/appointment', {
                "rocket_id": this.rocket_id,
                "update": this.uips
            })
            .then(response => {
                this.$alert({
                    title: 'UIP Update',
                    text: response.data.message,
                    type: 'success'
                })
            })

            this.$emit('loading')
        },
        add(id){
            if(id == 'carriers'){
                this.form[id].push({name: ''})
            } else if(id == 'state'){
                this.form.additional_states.push({name: null, code: null})
            }
        },
        remove(id, index){
            if(id == 'carriers'){
                this.form.carriers.splice(index, 1)

                this.$emit('change', 'carriers', this.form.carriers)
            } else if(id == 'state'){
                this.form.additional_states.splice(index, 1)

                this.$emit('change', 'additional_states', this.form.additional_states)
            }
        },
        uipUpdate(id, value, errors, index){
            this.uips[id][index] = value
        },
        inputChange(id, value, errors, index){
            this.form.carriers[index].name = value

            this.$emit('change', 'carriers', this.form.carriers)
        },
        selectChange() {
            this.$emit('change', 'agency_type', this.form.agency_type)
        },
    },
    components: {
        textInput,
        textMask,
        ExclamationTriangleIcon,
        TrashIcon,
        PlusCircleIcon,
        PaperAirplaneIcon,
        ChevronUpIcon,
        ChevronDownIcon
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
    --vs-dropdown-max-height: 100px;
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