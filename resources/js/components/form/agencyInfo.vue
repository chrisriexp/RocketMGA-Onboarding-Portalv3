<template>
    <div class="grid gap-6 w-[680px] px-16 py-6 mx-auto mt-16 bg-white rounded-lg border-[1px] border-custom-gray border-opacity-20 shadow-newdrop z-0 relative">
        <!--Progress Bar-->
        <ProgressBar :width="'0%'" />

        <!--Header-->
        <h2 class="text-center text-custom-dark-blue text-2xl font-semibold">Agency Information</h2>

        <form @submit.prevent="next" class="grid gap-4 w-full">
            <div class="w-full grid grid-cols-2 gap-6">
                <textInput @inputUpdate="inputChange" :inputValue="form.agent_name" :id="'agent_name'" :label="'Agent Name'" :placeholderText="'John Doe'" />
                <textInput @inputUpdate="inputChange" :inputValue="form.agency_name" :id="'agency_name'" :label="'Agency Name'" :placeholderText="'ABC Insurance'" />
            </div>

            <div class="w-full grid grid-cols-2 gap-6">
                <!--Phone Number-->
                <div class="grid gap-2 h-fit">
                    <label class="text-md text-custom-dark-blue font-medium">Phone Number <span class="text-custom-red">*</span></label>
                    <input
                        v-model="v$.form.phone.$model"
                        type='text'
                        v-mask="['(###) ###-####']"
                        placeholder="(555) 555-5555"
                        :class="v$.form.phone.$error ? 'border-custom-red focus:border-custom-red' : 'border-custom-gray border-opacity-20 focus:border-custom-blue'"
                        class="w-full p-2 rounded-md border-2 focus:ring-0"
                    >
                    <div class="input-errors" v-for="(error, index) of v$.form.phone.$errors" :key="index">
                        <div class="error-msg text-sm text-red-400">{{ error.$message }}</div>
                    </div>
                </div>

                <textInput @inputUpdate="inputChange" :inputValue="form.email" :id="'email'" :label="'Email Address'" :placeholderText="'john@doe.com'" :email=true />
            </div>


            <div class="w-full grid grid-cols-2 gap-6">
                <textInput @inputUpdate="inputChange" :inputValue="form.dba_name" :id="'dba_name'" :label="'DBA Name'" :placeholderText="'ABCIns'" :required=false />
            </div>

            <hr>

            <!--Google Autocomplete-->
            <div class="grid w-full mx-auto gap-4">
                <vue-google-autocomplete
                ref="addressAutocomplete"
                id="map"
                classname="form-control"
                placeholder="Please type your address"
                v-on:placechanged="getAddressData"
                country="us"
                class="p-2 rounded-md border-2 border-custom-gray border-opacity-20 focus:border-custom-blue focus:ring-0"
                >
                </vue-google-autocomplete>
            </div>

            <div class="w-full grid grid-cols-2 gap-6">
                <textInput class="opacity-40" @inputUpdate="inputChange" :inputValue="form.address1" :id="'address1'" :label="'Address 1'" :isdisabled=true />
                <textInput @inputUpdate="inputChange" :inputValue="form.address2" :id="'address2'" :label="'Address 2'" :required=false />
            </div>

            <div class="w-full grid grid-cols-3 gap-4">
                <textInput class="opacity-40" @inputUpdate="inputChange" :inputValue="form.city" :id="'city'" :label="'City'" :isdisabled=true />
                <textInput class="opacity-40" @inputUpdate="inputChange" :inputValue="form.state" :id="'state'" :label="'State'" :isdisabled=true />
                <textInput class="opacity-40" @inputUpdate="inputChange" :inputValue="form.zip" :id="'zip'" :label="'Zip'" :isdisabled=true />
            </div>

            <input type="submit" class="bg-custom-blue rounded-lg py-2 uppercase text-white font-bold text-sm hover:cursor-pointer" value="next">
        </form>
    </div>
</template>

<script>
import { useVuelidate } from '@vuelidate/core'
import { required, minLength, maxLength, helpers} from '@vuelidate/validators'
import {mask} from 'vue-the-mask'
import VueGoogleAutocomplete from 'vue-google-autocomplete'
import ProgressBar from '../progressBar.vue'
import textInput from '../textInput.vue'

export default {
    setup() {
        return {
            v$: useVuelidate()
        }
    },
    name: "Agency Info",
    data() {
        return {
            form: {
                agent_name: '',
                agency_name: '',
                dba_name: '',
                phone: '',
                email: '',
                address: '',
                address1: '',
                address2: '',
                city: '',
                state: '',
                zip: ''
            },
            errors: [
                {
                    name: 'agent_name',
                    errors: []
                },
                {
                    name: 'agency_name',
                    errors: []
                },
                {
                    name: 'phone',
                    errors: []
                },
                {
                    name: 'email',
                    errors: []
                }
                
            ]
        }
    },
    async created() {
        await axios.get('/api/onboarding/check')
        .then(response => {
            const keys = Object.keys(this.form)

            keys.forEach(key => {
                if(response.data.message[key] != null || response.data.message[key] != ''){
                    this.form[key] = response.data.message[key]
                }
            })
        })
    },
    methods: {
        inputChange(id, value, errors){
            this.form[id] = value
            
            if(errors){
                this.errors.forEach(error => {
                    if(error.name == id){
                        error.errors = errors
                    }
                })                    
            } else {
                this.errors.forEach(error => {
                    if(error.name == id){
                        error.errors = []
                    }
                })
            }
        },
        getAddressData: function (addressData, placeResultData, map) {
            this.form.address1 = `${addressData.street_number} ${addressData.route}`
            this.form.city = addressData.locality
            this.form.state = addressData.administrative_area_level_1
            this.form.zip = addressData.postal_code

            if(addressData.subpremise === undefined){
                this.form.address2 = ''
            } else {
                this.form.address2 = addressData.subpremise
            }

            this.form.address = this.form.address1 + " " + this.form.address2 + ", " + this.form.city + ", " + this.form.state + " " + this.form.zip
        },
        async next(){
            let valid = true

            this.errors.forEach(item => {
                if(item.errors.length > 0){
                    valid = false

                    item.errors.forEach(error => {
                        this.$alert({
                            title: 'Validation Error',
                            text: error,
                            type: 'warn'
                        })
                    })
                }
            })

            if(this.v$.form.$error){
                valid = false
                this.v$.form.$errors.forEach(error => {
                    console.log(error.$message)
                    this.$alert({
                        title: 'Validation Error',
                        text: error.$message,
                        type: 'warn'
                    })
                })
            }

            if(this.form.address == null){
                valid = false

                this.$alert({
                    title: 'Address Validation Error',
                    text: 'Please enter your address.',
                    type: 'warn'
                })
            }

            if(valid){
                await axios.post('/api/onboarding/update', {
                    'step': 0,
                    'data': this.form
                })
                .then(response => {
                    this.$emit('next')
                })
                .catch(error => {
                    console.log(error.response)
                    const keys = Object.keys(error.response.data.message)

                    keys.forEach(key => {
                        error.response.data.message[key].forEach(error => {
                            this.$alert({
                                title: 'Validation Error',
                                text: error,
                                type: 'warn'
                            })
                        })
                    })
                })
            }
        }
    },
    components: {
        ProgressBar,
        textInput,
        VueGoogleAutocomplete
    },
    directives: {mask},
    validations () {
        return {
            form: {
                phone: {required, minLengthValue: helpers.withMessage('Please enter a 10 digit phone number.', minLength(14)), maxLengthValue: helpers.withMessage('Please enter a 10 digit phone number.', maxLength(14))}
            }
        }
    },
}
</script>