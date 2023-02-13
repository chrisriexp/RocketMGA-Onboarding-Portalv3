<template>
    <div class="w-full h-fit grid gap-8 my-6 p-6 bg-white rounded-md border-custom-gray border-opacity-20 border-[1px]">
        <div class="grid w-fit h-fit">
            <h2 class="font-medium text-2xl text-custom-gray">Agency Information</h2>
            <p class="text-md font-light text-custom-gray">Below is some general agency information..</p>
        </div>

        <div class="w-full flex gap-4 text-md text-custom-gray">
            <div class="flex gap-2 w-fit h-fit justify-items-center">
                <p class="font-medium h-fit my-auto">Agency Tax ID -</p>
                <textMask @inputUpdate="inputChange" :inputValue="form.agency_tax_id" :id="'agency_tax_id'" :maskText="'###-##-####'" :isdisabled=!update />
            </div>

            <div class="flex gap-2 w-fit h-fit justify-items-center">
                <p class="font-medium h-fit my-auto">Agency Type -</p>
                <v-select style="min-width: 280px;"
                    code="code"
                    v-model="form.agency_type"
                    :options="options"
                    label="name"
                    class="text-sm font-medium rounded-xl w-fit bg-custom-red disabled:text-custom-gray"
                    :disabled="!update"
                    @option:selected="selectChange"
                ></v-select>
            </div>
        </div>

        <div class="w-full flex gap-4 text-md text-custom-gray">
            <div class="flex gap-2 w-fit h-fit justify-items-center">
                <p class="font-medium h-fit my-auto">Agency Phone -</p>
                <textMask @inputUpdate="inputChange" :inputValue="form.phone" :id="'phone'" :maskText="'(###) ###-####'" :isdisabled=!update />
            </div>

            <div class="flex gap-2 w-fit h-fit justify-items-center">
                <p class="font-medium h-fit my-auto">Agency Email -</p>
                <textInput @inputUpdate="inputChange" :inputValue="form.email" :id="'email'" :isdisabled=!update :email=true />
            </div>
        </div>

        <!--Google Autocomplete-->
        <div class="grid w-full mx-auto gap-2 text-custom-gray">
            <p class="font-medium h-fit my-auto">Agency Address -</p>
            <vue-google-autocomplete
            ref="addressAutocomplete"
            id="map"
            classname="form-control"
            placeholder="Please type your address"
            v-on:placechanged="getAddressData"
            country="us"
            class="w-full p-[5px] rounded-xl inner-border-[1px] border-none focus:ring-0 disabled:bg-custom-gray disabled:bg-opacity-5"
            :disabled="!update"
            >
            </vue-google-autocomplete>
        </div>

        <div class="w-full flex gap-4 text-md text-custom-gray">
                <textInput @inputUpdate="inputChange" :inputValue="form.address1" :id="'address1'" :label="'Address 1'" :isdisabled=true />
                <textInput @inputUpdate="inputChange" :inputValue="form.address2" :id="'address2'" :label="'Address 2'" :isdisabled=!update :required=false />
        </div>

        <div class="w-full flex gap-4 text-md text-custom-gray">
            <textInput @inputUpdate="inputChange" :inputValue="form.city" :id="'city'" :label="'City'" :isdisabled=true />
            <textInput @inputUpdate="inputChange" :inputValue="form.state" :id="'state'" :label="'State'" :isdisabled=true />
            <textInput @inputUpdate="inputChange" :inputValue="form.zip" :id="'zip'" :label="'Zip'" :isdisabled=true />
        </div>
    </div>
</template>

<script>
import textInput from '../textInput.vue'
import textMask from '../textMask.vue'
import agenctTypes from '../../../../assets/agencyTypes.json'
import VueGoogleAutocomplete from 'vue-google-autocomplete'

export default {
    name: "Agency Info",
    props: {
        data: Object,
        update: Boolean
    },
    data() {
        return {
            options: agenctTypes,
            form: {
                address: '',
                address1: '',
                address2: '',
                city: '',
                state: '',
                zip: '',
                phone: '',
                email: '',
                agency_tax_id: '',
                agency_type: {}
            },
            errors: [
                {
                    name: 'phone',
                    errors: []
                },
                {
                    name: 'email',
                    errors: []
                },
                {
                    name: 'agency_tax_id',
                    errors: []
                }
            ]
        }
    },
    async created(){
        const keys = Object.keys(this.form)
        keys.forEach(key => {
            this.form[key] = this.data[key]
        })
    },
    watch: {

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

            this.$emit('change', id, value, errors)
        },
        selectChange() {
            this.$emit('change', 'agency_type', this.form.agency_type)
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

            const fields = ['address', 'address1', 'address2', 'city', 'state', 'zip']

            fields.forEach(field => {
                this.$emit('change', field, this.form[field])
            })
        },
    },
    components: {
        textInput,
        textMask,
        VueGoogleAutocomplete
    }
}
</script>

<style scoped>
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
    --vs-dropdown-max-height: 350px;
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