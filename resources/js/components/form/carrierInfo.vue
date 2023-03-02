<template>
    <div class="h-fit grid gap-4 justify-items-center px-6 md:px-0">
        <!--Favicon Image-->
        <img src="../../../assets/favicon.png" alt="Rocket Favicon" class="h-[53px] md:h-[64px] mt-12">

        <!--Carrier Information Header-->
        <div class="grid w-full md:w-[502px] h-fit text-center text-custom-dark-blue">
            <h2 class="text-[20px] md:text-[32px] font-semibold">Carrier and State Information</h2>
            <p class="text-[14px] md:text-[16px] opacity-70">This will allow you to utilize your existing codes within the comparative rater</p>
        </div>

        <!--Carrier Information Form-->
        <form @submit.prevent="next" class="grid gap-2 w-full md:w-[502px] mt-[-10px] relative">
            <!--No Carriers Selected Disclaimer-->
            <div :class="carrierPopup ? '' : 'hidden'" class="grid z-10 absolute mt-[25%] md:ml-[-10%] w-full md:w-[120%] h-[200px] rounded-xl bg-white border-2 border-custom-gray border-opacity-40 shadow-newdrop px-4">
                <p class="text-center text-[15px] text-custom-gray my-auto">We see that you have <span class="text-red-400">not</span> entered any NFIP or Private Flood carriers. You must accurately list your current flood companies to continue.</p>
                <div class="flex gap-6 w-fit h-fit mx-auto">
                    <button @click="carrierDisYes" type="button" class="h-fit text-[15px] text-center border-2 rounded border-custom-gray border-opacity-40 px-2">No current flood appointments</button>
                    <button @click="carrierDisNo" type="button" class="h-fit text-[15px] border-2 text-white rounded border-red-400 bg-red-400 px-2">Add flood carriers</button>
                </div>
            </div>

            <!--Flood Insurance Carriers-->
            <div class="w-full h-fit md:h-[270px] grid rounded-md md:border-[1px] border-custom-gray border-opacity-20 px-4 py-4">
                <div class="grid h-fit">
                    <div class="grid h-fit">
                        <p class="text-[20px] text-custom-dark-blue font-semibold">Flood Insurance Carriers</p>
                        <p class="text-[16px] text-custom-dark-blue opacity-70">Both NFIP and Private</p>
                    </div>

                    <div class="w-full grid gap-4 md:gap-0 md:flow-root mt-6">
                        <div class="grid gap-2 h-fit w-full md:w-[200px] float-left">
                            <div class="grid w-full h-[48px] border-custom-gray border-[1px] opacity-20 rounded-md relative">
                                <p class="px-4 h-fit text-[15px] my-auto">Carrier Name</p>
                            </div>

                            <!--Add Carrier Button-->
                            <button @click="addCarrier" type="button" class="h-[40px] bg-custom-blue text-white border-l-[4px] border-b-[5px] border-[#6B92CB] active:border-custom-blue rounded-md shadow-newdrop">Add Carrier</button>
                        </div>

                        <!--Dynamic Carriers-->
                        <div class="grid h-fit md:h-[155px] float-right overflow-y-scroll scrollbar">
                            <div class="grid h-fit gap-4">
                                <div v-for="(item, index) in form.carriers" :key="index" class="w-full md:w-[248px] flow-root">
                                    <input
                                        v-model="item.name"
                                        type='text'
                                        placeholder="Carrier Name"
                                        class="w-[240px] md:w-[200px] h-[48px] md:h-[40px] text-[15px] text-custom-dark-blue p-[7px] rounded-[5px] border-[1px] border-custom-dark-blue focus:border-custom-dark-blue focus:ring-0 float-left"
                                    >

                                    <TrashIcon v-if="form.carriers.length > 1" @click="deleteCarrier(index)" class="h-[32px] text-custom-red hover:cursor-pointer float-right" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--Additional Licensed States-->
            <div class="w-full h-fit md:h-[270px] grid rounded-md md:border-[1px] border-custom-gray border-opacity-20 px-4 py-4">
                <div class="grid h-fit">
                    <div class="grid h-fit">
                        <p class="text-[20px] text-custom-dark-blue font-semibold">Appointed States</p>
                        <div class="w-full grid grid-cols-2">
                            <p @click="addAllStates" class="w-fit text-[16px] text-custom-blue hover:cursor-pointer">Add all states</p>
                            <p @click="removeAllStates" class="w-fit text-[16px] ml-[-20px] text-custom-blue hover:cursor-pointer">Remove all states</p>
                        </div>
                    </div>

                    <div class="w-full grid gap-4 md:gap-0 md:flow-root mt-6">
                        <div class="grid gap-2 h-fit w-full md:w-[200px] float-left">
                            <div class="grid w-full h-[48px] border-custom-gray border-[1px] opacity-20 rounded-md relative">
                                <p class="px-4 h-fit text-[15px] my-auto">Select State</p>
                            </div>

                            <!--Add State Button-->
                            <button @click="addState" type="button" class="h-[40px] bg-custom-blue text-white border-l-[4px] border-b-[5px] border-[#6B92CB] active:border-custom-blue rounded-md shadow-newdrop">Add State</button>
                        </div>

                        <!--Dynamic States-->
                        <div class="grid h-fit md:h-[155px] float-right md:overflow-y-scroll scrollbar">
                            <div class="grid h-fit gap-4">
                                <div v-for="(item, index) in form.additional_states" :key="index" class="w-full md:w-[248px] flow-root">
                                    <v-select
                                        placeholder='Select State'
                                        id="stateSelect"
                                        ref="selectRef"
                                        code="code"
                                        v-model="form.additional_states[index]"
                                        :options="options"
                                        label="name"
                                        class="relative w-[240px] md:w-[200px] h-fit text-[15px] text-custom-dark-blue bg-custom-blue rounded-md border-2 border-transparent float-left"
                                    ></v-select>

                                    <TrashIcon v-if="form.additional_states.length > 1" @click="deleteState(index)" class="h-[32px] text-custom-red hover:cursor-pointer float-right" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--Next Button-->
            <input :disabled="carrierPopup" type="submit" class="h-[48px] mt-2 bg-custom-orange rounded-md py-2 uppercase text-white font-medium text-[18px] border-l-[5px] border-b-[6px] border-[#F4B983] active:border-custom-orange cursor-pointer shadow-newdrop active:shadow-none disabled:opacity-40 disabled:cursor-not-allowed" value="next">
            <!--Back Button-->
            <button @click="back" type="button" class="w-fit mx-auto mb-8 md:mb-0 text-[16px] text-custom-blue font-medium underline" >Back</button>
        </form>
    </div>
</template>

<script>
import ProgressBar from '../progressBar.vue'
import { TrashIcon } from '@heroicons/vue/24/solid'
import states from '../../../assets/states.json'

export default {
    name: "Carrier Info",
    data() {
        return {
            carrierPopup: false,
            state: '',
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
            }
        }
    },
    async created() {
        await axios.get('/api/onboarding/check')
        .then(response => {
            this.state = response.data.message.state

            states.forEach(state => {
                if(response.data.message.state == state.code){
                    this.form.additional_states.push(state)
                }
            })

            if(response.data.message.additional_states != null){
                this.form.additional_states = JSON.parse(response.data.message.additional_states)
            }

            if(response.data.message.carriers != null){
                this.form.carriers = JSON.parse(response.data.message.carriers)
            }
            
        })
    },
    methods: {
        back(){
            this.$emit('back')
        },
        addCarrier(){
            this.form.carriers.push({name: ''})
        },
        deleteCarrier(index){
            this.form.carriers.splice(index, 1)
        },
        addState(){
            this.form.additional_states.push({name: null, code: null})
        },
        deleteState(index){
            this.form.additional_states.splice(index, 1)
        },
        addAllStates(){
            this.form.additional_states = []

            states.forEach(state => {
                if(state.code == this.state){
                    this.form.additional_states.push(state)
                }
            })

            states.forEach(state => {
                if(state.code != this.state){
                    this.form.additional_states.push(state)
                }
            })
        },
        removeAllStates(){
            this.form.additional_states = []

            states.forEach(state => {
                if(state.code == this.state){
                    this.form.additional_states.push(state)
                    return
                }
            })
        },
        async carrierDisYes(){
            this.carrierPopup = false

            await axios.post('/api/onboarding/update', {
                'step': 1,
                'data': {
                    'additional_states': JSON.stringify(this.form.additional_states),
                    'carriers': JSON.stringify(this.form.carriers)
                }
            })
            .then(response => {
                this.$emit('next')
            })
            return
        },
        carrierDisNo(){
            this.carrierPopup = false
            return
        },
        async next(){
            if(this.form.carriers.length == 1 && this.form.carriers[0].name == ''){
                this.carrierPopup = true
                return
            }

            await axios.post('/api/onboarding/update', {
                'step': 1,
                'data': {
                    'additional_states': JSON.stringify(this.form.additional_states),
                    'carriers': JSON.stringify(this.form.carriers)
                }
            })
            .then(response => {
                this.$emit('next')
            })
            .catch(error => {
                console.log(error)
            })
        }
    },
    components: {
        ProgressBar,
        TrashIcon
    }
}
</script>

<style scoped>
:root {

    /* Search Input */
    --vs-search-input-bg: #5080C7;

    /* Font */
    --vs-font-size: 1rem;
    --vs-line-height: 1.4;

    /* Disabled State */
    --vs-state-disabled-bg: #93b3f8;
    --vs-state-disabled-color: #fff;
    --vs-state-disabled-controls-color: #fff;
    --vs-state-disabled-cursor: not-allowed;

    /* Borders */
    --vs-border-color: var(--vs-colors--lightest);
    --vs-border-width: 0px;
    --vs-border-style: border-none;
    --vs-border-radius: 4px;

    /* Actions: house the component controls */
    --vs-actions-padding: 4px 6px 0 3px;

    /* Component Controls: Clear, Open Indicator */
    --vs-controls-color: #fff;
    --vs-controls-size: 1;
    --vs-controls--deselect-text-shadow: 0 1px 0 #fff;

    /* Selected */
    --vs-selected-bg: #5080C7;
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
    --vs-dropdown-max-height: 350px;
    --vs-dropdown-box-shadow: 0px 3px 6px 0px var(--vs-colors--darkest);

    /* Options */
    --vs-dropdown-option-bg: #000;
    --vs-dropdown-option-color: var(--vs-dropdown-color);
    --vs-dropdown-option-padding: 3px 20px;

    /* Active State */
    --vs-dropdown-option--active-bg: #5080C7;
    --vs-dropdown-option--active-color: #fff;

    /* Transitions */
    --vs-transition-timing-function: cubic-bezier(1, -0.115, 0.975, 0.855);
    --vs-transition-duration: 150ms;
}
</style>