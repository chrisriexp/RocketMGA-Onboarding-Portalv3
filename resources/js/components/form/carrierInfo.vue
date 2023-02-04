<template>
    <div class="grid gap-6 w-[680px] px-16 py-6 mx-auto mt-16 bg-white rounded-lg border-[1px] border-custom-gray border-opacity-20 shadow-newdrop z-0 relative">
        <!--Progress Bar-->
        <ProgressBar :width="'35%'" />

        <!--Header-->
        <div class="grid text-sm">
            <h2 class="text-center text-custom-dark-blue text-2xl font-semibold">Carrier and State Information</h2>
            <p class="text-center text-custom-dark-blue">This will allow you to utilize your existing codes within<br/>the comparative rater</p>
        </div>

        <form @submit.prevent="next" class="grid gap-6 w-full">
            <!--No Carriers Selected Disclaimer-->
            <div :class="carrierPopup ? '' : 'hidden'" class="grid z-10 ml-24 fixed w-[400px] h-[200px] rounded-xl bg-white border-2 border-custom-gray border-opacity-40 shadow-newdrop">
                <p class="text-center text-md text-custom-gray my-auto">We see that you have <span class="text-red-400">not</span> entered any NFIP or Private Flood carrier. Are you sure you want to continue?</p>
                <div class="flex gap-6 w-fit mx-auto">
                    <button @click="carrierDisYes" type="button" class="h-fit text-md border-2 rounded border-custom-gray border-opacity-40 px-4">Yes</button>
                    <button @click="carrierDisNo" type="button" class="h-fit text-md border-2 text-white rounded border-red-400 bg-red-400 px-4">No</button>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-8">
                <div class="grid text-custom-dark-blue ">
                    <p class="text-lg font-medium">Flood Insurance Carriers</p>
                    <p class="text-sm">Both NFIP and Private</p>
                </div>

                <div class="grid text-custom-dark-blue ">
                    <p class="text-lg font-medium">Appointed States</p>
                    <div class="flow-root">
                        <p @click="addAllStates" class="float-left text-sm text-custom-blue hover:cursor-pointer">Add all states</p>
                        <p @click="removeAllStates" class="float-right text-sm text-custom-blue hover:cursor-pointer">Remove all states</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div class="grid gap-2 h-fit">
                    <div v-for="(item, index) in form.carriers" :key="index" class="flex gap-4 h-fit">
                        <input
                            v-model="item.name"
                            type='text'
                            placeholder="Carrier Name"
                            class="w-full h-fit text-sm p-[6px] rounded-md border-2 border-custom-gray border-opacity-20 focus:border-custom-blue focus:ring-0"
                        >

                        <TrashIcon v-if="form.carriers.length > 1" @click="deleteCarrier(index)" class="h-8 my-auto text-custom-red hover:cursor-pointer" />
                    </div>

                    <button @click="addCarrier" type="button" class="w-fit px-4 py-2 text-sm text-white font-semibold bg-custom-blue rounded-lg uppercase">Add Carrier</button>
                </div>

                <div class="grid gap-2 h-fit">
                    <div v-for="(item, index) in form.additional_states" :key="index" class="flex gap-4">
                        <v-select
                            placeholder='Select State'
                            id="stateSelect"
                            @input="stateInput(index)"
                            ref="selectRef"
                            code="code"
                            v-model="form.additional_states[index]"
                            :options="options"
                            label="name"
                            class="w-full text-sm rounded-md"
                        ></v-select>

                        <TrashIcon v-if="form.additional_states.length > 1" @click="deleteState(index)" class="h-8 text-custom-red hover:cursor-pointer" />
                    </div>

                    <button @click="addState" type="button" class="w-fit px-4 py-2 text-sm text-white font-semibold bg-custom-blue rounded-lg uppercase">Add State</button>
                </div>
            </div>

            <div class="flex gap-12 w-full">
                <button @click="back" type="button" class="w-[65%] bg-custom-gray bg-opacity-40 rounded-lg py-2 uppercase text-white font-bold text-sm hover:cursor-pointer">back</button>
                <input type="submit" class="w-full bg-custom-blue rounded-lg py-2 uppercase text-white font-bold text-sm hover:cursor-pointer" value="next">
            </div>
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
                    this.form.additional_states = [state]
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
        stateInput(value, index){
            console.log(value+index)
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

            await axios.post('/api/onboarding/carrierInfo', {
                'additional_states': JSON.stringify(this.form.additional_states)
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
                // this.$emit('next')
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