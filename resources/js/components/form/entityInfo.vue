<template>
    <div class="grid gap-6 w-[680px] px-16 py-6 mx-auto mt-16 bg-white rounded-lg border-[1px] border-custom-gray border-opacity-20 shadow-newdrop z-0 relative">
        <!--Progress Bar-->
        <ProgressBar :width="'65'" />

        <!--Header-->
        <h2 class="text-center text-custom-dark-blue text-2xl font-semibold">Entity and Licensing Information</h2>

        <form @submit.prevent="next" class="grid gap-4 w-full">
            <div class="w-full grid grid-cols-2 gap-6">
                <textInput @inputUpdate="inputChange" :inputValue="form.agent_license" :id="'agent_license'" :label="'Agent License'" :placeholderText="'Z000000'" />
                <textMask @inputUpdate="inputChange" :inputValue="form.agent_npn" class="agent_npn" :id="'agent_npn'" :label="'Agent NPN'" :placeholderText="'0000000'" :maskText="'#######'" />
            </div>

            <div class="w-full grid grid-cols-2 gap-6">
                <textInput @inputUpdate="inputChange" :inputValue="form.agent_license_eff" class="agent_license_eff" :id="'agent_license_eff'" :label="'Agent License Issue Date'" :date=true />
                <textInput @inputUpdate="inputChange" :inputValue="form.agent_license_exp" class="agent_license_exp" :id="'agent_license_exp'" :label="'Agent License Exp Date'" :date=true />
            </div>

            <hr>

            <div class="w-full grid grid-cols-2 gap-6">
                <textInput @inputUpdate="inputChange" :inputValue="form.agency_license" :id="'agency_license'" :label="'Agency License'" :placeholderText="'A000000'" />
                <textMask @inputUpdate="inputChange" :inputValue="form.agency_tax_id" :id="'agency_tax_id'" :label="'Agency Tax ID'" :placeholderText="'000-00-0000'" :maskText="'###-##-####'" />
            </div>

            <div>
                <div class="grid gap-2">
                    <label class="text-md text-custom-dark-blue font-medium">Agency Type <span class="text-custom-red">*</span></label>
                    <v-select
                        placeholder='Agency Type'
                        id="stateagency_typeSelect"
                        ref="selectRef"
                        code="code"
                        v-model="form.agency_type"
                        :options="options"
                        label="name"
                        class="w-full text-sm rounded-md border-2 border-transparent p-0"
                    ></v-select>
                </div>
            </div>

            <hr>

            <div v-if="checkRun" class="w-full grid grid-cols-2 gap-6">
                <fileUpload @fileUploaded="fileUploaded" :type="'agency_license_file'" :inputValue="form.agency_license_file" :label="'Agency License'" />
                <fileUpload @fileUploaded="fileUploaded" :type="'agent_license_file'" :inputValue="form.agent_license_file" :label="'Agent License'" />
            </div>

            <fileUpload :type="'agency_logo'" :inputValue="form.agency_logo" :label="'Company Logo'" :required=false />

            <div class="flex gap-12 w-full">
                <button @click="back" type="button" class="w-[65%] bg-custom-gray bg-opacity-40 rounded-lg py-2 uppercase text-white font-bold text-sm hover:cursor-pointer">back</button>
                <input type="submit" class="w-full bg-custom-orange  rounded-lg py-2 uppercase text-white font-bold text-sm hover:cursor-pointer" value="next">
            </div>
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