<template>
    <div class="h-fit grid gap-8 justify-items-center px-6 md:px-0">
        <!--Favicon Image-->
        <img src="../../../assets/favicon.png" alt="Rocket Favicon" class="h-[53px] md:h-[64px] mt-12">

        <!--E&O Information Header-->
        <div class="grid h-fit text-center text-custom-dark-blue">
            <h2 class="text-[24px] md:text-[32px] font-semibold">E&O Information</h2>
        </div>

        <!--E&O Information Form-->
        <form @submit.prevent="next" class="grid gap-4 w-full md:w-[502px]">
            <div class="w-full">
                <fileUpload v-if="checkRun" @fileUploaded="fileUploaded" :type="'eo'" :inputValue="form.eo" :label="'E&O Document'" class="w-full" />
            </div>

            <div class="w-full grid gap-4 md:gap-0 md:flow-root">
                <textInput @inputUpdate="inputChange" :inputValue="form.eo_exp" :id="'eo_exp'" :label="'E&O Exp'" :date=true class="md:w-[239px] md:float-left" />
                <textInput @inputUpdate="inputChange" :inputValue="form.eo_policy" :id="'eo_policy'" :label="'E&O Policy Number'" class="md:w-[239px] md:float-right" />
            </div>

            <div class="w-full grid gap-4 md:gap-0 md:flow-root">
                <textMask @inputUpdate="inputChange" :inputValue="form.eo_limit" :id="'eo_limit'" :label="'E&O Limit'" class="md:w-[239px] md:float-left" :maskText="'#########'" />
                <textInput @inputUpdate="inputChange" :inputValue="form.eo_insurer" :id="'eo_insurer'" :label="'E&O Insurer'" class="md:w-[239px] md:float-right" />
            </div>

            <!--Next Button-->
            <input type="submit" class="h-[48px] mt-2 bg-custom-orange rounded-md py-2 uppercase text-white font-medium text-[18px] border-l-[5px] border-b-[6px] border-[#F4B983] active:border-custom-orange cursor-pointer shadow-newdrop active:shadow-none disabled:opacity-40 disabled:cursor-not-allowed" value="next">
            <!--Back Button-->
            <button @click="back" type="button" class="w-fit mx-auto mb-8 md:mb-0 text-[16px] text-custom-blue font-medium underline" >Back</button>
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
            form: {
                eo: '',
                eo_exp: '',
                eo_policy: '',
                eo_limit: '',
                eo_insurer: ''
            },
            errors: [
                {
                    name: 'eo_exp',
                    errors: []
                },
                {
                    name: 'eo_policy',
                    errors: []
                },
                {
                    name: 'eo_limit',
                    errors: []
                },
                {
                    name: 'eo_insurer',
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
                if(!response.data.message[key] == null || !response.data.message[key] == ''){
                    this.form[key] = response.data.message[key]
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

            if(!this.form.eo){
                valid = false
                this.$alert({
                    title: 'Validation Error',
                    text: 'please upload you agency e&o document.',
                    type: 'warn'
                })
            }

            if(valid){
                await axios.post('/api/onboarding/update', {
                    'step': 3,
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