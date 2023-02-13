<template>
    <div v-if="data.approved" class="w-full h-fit grid gap-4  mt-4 p-6 bg-white rounded-md border-custom-gray border-opacity-20 border-[1px]">
        <div class="flow-root w-full">
            <button class="float-left w-fit flex gap-2 px-6 py-2 bg-custom-dark-blue text-white rounded-md">Update MGA Agent Portal <PaperAirplaneIcon class="h-6" /></button>
            <button @click="saveUIPs" class="float-right w-fit flex gap-2 px-6 py-2 bg-custom-dark-blue text-white rounded-md">Save</button>
        </div>

        <div class="w-full grid gap-4 text-custom-dark-blue text-md">
            <div class="grid grid-cols-8 gap-4 font-medium text-lg pb-2 border-b-2 border-custom-dark-blue">
                <p>Aon</p>
                <p>Beyond</p>
                <p>Dual</p>
                <p>Flow</p>
                <p>Neptune</p>
                <p>Palomar</p>
                <p>Sterling</p>
                <p>Wright</p>
            </div>

            <div class="grid grid-cols-8 gap-4">
                <div v-for="(carrier, index) in uips" :key="index" class="grid col text-[11px] text-center h-fit">
                    <div class="w-full grid grid-cols-2 border border-custom-gray rounded-md h-fit">
                        <button @click="carrier.direct = true" :class="carrier.direct ? 'bg-custom-dark-blue text-white' : ''" class="col rounded-tl-md rounded-bl-md border-r-2 disabled:bg-opacity-40">Direct</button>
                        <button @click="carrier.direct = false" :class="!carrier.direct ? 'bg-custom-dark-blue text-white' : ''" class="col rounded-tr-md rounded-br-md disabled:bg-opacity-40">Rocket MGA</button>
                    </div>

                    <div v-if="!carrier.direct" class="grid gap-4 h-fit text-md">
                        <div v-for="(uip, uipIndex) in carrier" :key="uipIndex">
                            <div v-if="uipIndex != 'direct'" class="grid">
                                <p class="text-left uppercase font-medium">{{ uipIndex.replace("_", " ") }}</p>
                                <textInput @inputUpdate="uipUpdate" :inputValue="uip" :index="uipIndex" />
                            </div>
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
            <h2 class="text-xl text-custom-gray">Appointed Carriers</h2>

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
import { ExclamationTriangleIcon, TrashIcon, PlusCircleIcon, PaperAirplaneIcon } from '@heroicons/vue/24/solid'

export default {
    name: "Appointment Info",
    props: {
        rocket_id: String,
        data: Object,
        update: Boolean
    },
    data() {
        return {
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
            uips: []
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
                const carriers = ['aon', 'beyond', 'dual', 'flow', 'neptune', 'palomar', 'sterling', 'wright']

                carriers.forEach(carrier => {
                    this.uips.push(JSON.parse(response.data.appointments[carrier]))
                })
            })
        }
    },
    watch: {

    },
    methods: {
        async saveUIPs() {
            this.$emit('loading')

            let updates = {}

            const carriers = ['aon', 'beyond', 'dual', 'flow', 'neptune', 'palomar', 'sterling', 'wright']

            let i = 0
            carriers.forEach(carrier => {
                updates[carrier] = this.uips[i]
                i += 1
            })

            await axios.post('/api/appointment', {
                "rocket_id": this.rocket_id,
                "update": updates
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
            console.log(id+" "+value+" "+index)
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
        PaperAirplaneIcon
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