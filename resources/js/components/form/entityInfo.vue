<template>
    <div class="h-fit grid gap-4 justify-items-center px-6 md:px-0">
        <!--Favicon Image-->
        <img src="../../../assets/favicon.png" alt="Rocket Favicon" class="h-[53px] md:h-[64px] mt-12">

        <!--Entity Information Header-->
        <div class="grid h-fit text-center text-custom-dark-blue">
            <h2 class="text-[24px] md:text-[32px] font-semibold">Entity and Licensing Information</h2>
        </div>

        <!--Entity Information Form-->
        <form @submit.prevent="next" class="grid gap-4 w-full md:w-[502px]">
            <!--Agent License and NPN-->
            <div class="w-full grid gap-4 md:gap-0 md:flow-root">
                <textInput @inputUpdate="inputChange" :inputValue="form.agent_license" :id="'agent_license'" :label="'Agent License'" :placeholderText="'Z000000'" class="md:w-[239px] md:float-left" />
                <textMask @inputUpdate="inputChange" :inputValue="form.agent_npn" :id="'agent_npn'" :label="'Agent NPN'" :placeholderText="'00000000'" :maskText="'########'" class="agent_npn md:w-[239px] md:float-right" />
            </div>

            <!--Agent License Eff and Exp Date-->
            <div class="w-full grid gap-4 md:gap-0 md:flow-root">
                <textInput @inputUpdate="inputChange" :inputValue="form.agent_license_eff" class="agent_license_eff md:w-[239px] md:float-left" :id="'agent_license_eff'" :label="'Agent License Issue Date'" :date=true />
                <textInput @inputUpdate="inputChange" :inputValue="form.agent_license_exp" class="agent_license_exp md:w-[239px] md:float-right" :id="'agent_license_exp'" :label="'Agent License Exp Date'" :date=true />
            </div>

            <hr>

            <!--Agency License and Tax ID-->
            <div class="w-full grid gap-4 md:gap-0 md:flow-root">
                <textInput @inputUpdate="inputChange" :inputValue="form.agency_license" :id="'agency_license'" :label="'Agency License'" :placeholderText="'A000000'" class="md:w-[239px] md:float-left" />
                <textMask @inputUpdate="inputChange" :inputValue="form.agency_tax_id" :id="'agency_tax_id'" :label="'Agency Tax ID'" :placeholderText="'000-00-0000'" :maskText="'###-##-####'" class="md:w-[239px] md:float-right" />
            </div>

            <!--Agency Type-->
            <div class="w-full">
                <div class="grid gap-2">
                    <label class="text-[16px] text-custom-dark-blue font-medium">Agency Type <span class="text-custom-red">*</span></label>
                    <v-select
                        placeholder='Agency Type'
                        id="stateagency_typeSelect"
                        ref="selectRef"
                        code="code"
                        v-model="form.agency_type"
                        :options="options"
                        label="name"
                        class="w-full text-[15px] rounded-md border-2 bg-custom-blue  border-transparent p-0"
                    ></v-select>
                </div>
            </div>

            <hr>

            <!--Agent and Agency License File Upload-->
            <div class="w-full grid gap-4 md:gap-0 md:flow-root">
                <fileUpload v-if="checkRun" @fileUploaded="fileUploaded" :type="'agency_license_file'" :inputValue="form.agency_license_file" :label="'Agency License'" class="md:w-[239px] md:float-left" />
                <fileUpload v-if="checkRun" @fileUploaded="fileUploaded" :type="'agent_license_file'" :inputValue="form.agent_license_file" :label="'Agent License'" class="md:w-[239px] md:float-right" />
            </div>

            <div class="w-full">
                <fileUpload v-if="checkRun" @fileUploaded="fileUploaded" :type="'agency_logo'" :inputValue="form.agency_logo" :label="'Company Logo'" :required=false class="w-full" />
            </div>

            <!--Next Button-->
            <input type="submit" class="h-[48px] bg-custom-orange rounded-md py-2 uppercase text-white font-medium text-[18px] border-l-[5px] border-b-[6px] border-[#F4B983] active:border-custom-orange cursor-pointer shadow-newdrop active:shadow-none disabled:opacity-40 disabled:cursor-not-allowed" value="next">
            <!--Back Button-->
            <button @click="back" type="button" class="w-fit mx-auto mt-[-10px] mb-8 md:mb-0 text-[16px] text-custom-blue font-medium underline" >Back</button>
        </form>
    </div>
</template>

<script>
import ProgressBar from '../progressBar.vue'
import textInput from '../textInput.vue'
import textMask from '../textMask.vue'
import fileUpload from '../fileUpload.vue'

export default {
    name: "Entity Info",
    data() {
        return {
            checkRun: false,
            options: [
                {
                    name: 'Individual/Sole Proprietor',
                    code: 'Sole'
                },
                {
                    name: 'LLC - C Corporation',
                    code: 'LLC-C'
                },
                {
                    name: 'LLC - S Corporation',
                    code: 'LLC-S'
                },
                {
                    name: 'LLC - Partnership',
                    code: 'LLC-P'
                },
                {
                    name: 'C Corporation',
                    code: 'C-Corp'
                },
                {
                    name: 'S Corporation',
                    code: 'S-Corp'
                },
                {
                    name: 'Partnership',
                    code: 'Partner'
                },
                {
                    name: 'Trust/Estate',
                    code: 'Trust'
                }
            ],
            wright: false,
            form: {
                agent_npn: '',
                agent_license: '',
                agent_license_eff: '',
                agent_license_exp: '',
                agent_license_file: '',
                agency_license: '',
                agency_license_file: '',
                agency_tax_id: '',
                agency_type: '',
                agency_logo: ''
            },
            errors: [
                {
                    name: 'agent_npn',
                    errors: []
                },
                {
                    name: 'agent_license',
                    errors: []
                },
                {
                    name: 'agent_license_eff',
                    errors: []
                },
                {
                    name: 'agent_license_exp',
                    errors: []
                },
                {
                    name: 'agency_license',
                    errors: []
                },
                {
                    name: 'agency_tax_id',
                    errors: []
                },
                {
                    name: 'agency_type',
                    errors: []
                }
                
            ]
        }
    },
    async created() {
        await axios.get('/api/onboarding/check')
        .then(response => {
            this.wright = response.data.message.wright

            if(this.wright){
                let items = ['agent_npn', 'agent_license_eff', 'agent_license_exp']

                items.forEach(item => {
                    const element = document.getElementsByClassName(item)

                    if(element.length > 0){
                        element[0].classList.add('hidden');
                    }
                })
            }

            const keys = Object.keys(this.form)

            keys.forEach(key => {
                if(!response.data.message[key] == null || !response.data.message[key] == ''){
                    if(key == 'agency_type'){
                        const obj = JSON.parse(response.data.message[key])
                        this.form[key] = obj.code
                    } else {
                        this.form[key] = response.data.message[key]
                    }
                }
            })

            this.checkRun = true
        })
    },
    methods: {
        back(){
            this.$emit('back')
        },
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
        fileUploaded(key, value){
            this.form[key] = value
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

            if(!this.form.agency_license_file){
                valid = false
                this.$alert({
                    title: 'Validation Error',
                    text: 'please upload you agency license.',
                    type: 'warn'
                })
            } else if(!this.form.agent_license_file){
                valid = false
                this.$alert({
                    title: 'Validation Error',
                    text: 'please upload you agent license.',
                    type: 'warn'
                })
            }

            if(valid){
                let agency_type = this.form.agency_type

                if(typeof agency_type == 'string'){
                    this.options.forEach(option => {
                        if(option.code == agency_type){
                            agency_type = option
                        }
                    })
                }

                this.form.agency_type = agency_type

                await axios.post('/api/onboarding/update', {
                    'step': 2,
                    'wright': this.wright,
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
        textMask,
        fileUpload
    }
}
</script>

<style>
:root {
    /* Search Input */
    --vs-search-input-bg: #5080C7;

    /* Font */
    --vs-font-size: 1rem;
    --vs-line-height: 1.4;

    /* Disabled State */
    --vs-state-disabled-bg: #f89393;
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