<template>
    <div class="grid gap-6 w-[680px] px-16 py-6 mx-auto mt-16 bg-white rounded-lg border-[1px] border-custom-gray border-opacity-20 shadow-newdrop z-0 relative">
        <!--Progress Bar-->
        <ProgressBar :width="'85'" />

        <!--Header-->
        <h2 class="text-center text-custom-dark-blue text-2xl font-semibold">E&O Information</h2>

        <form @submit.prevent="next" class="grid gap-4 w-full">
            <div class="w-full grid gap-6">
                <fileUpload v-if="checkRun" @fileUploaded="fileUploaded" :type="'eo'" :inputValue="form.eo" :label="'E&O Document'" />
            </div>

            <div class="w-full grid grid-cols-2 gap-6">
                <textInput @inputUpdate="inputChange" :inputValue="form.eo_exp" :id="'eo_exp'" :label="'E&O Exp'" :date=true />
                <textInput @inputUpdate="inputChange" :inputValue="form.eo_policy" :id="'eo_policy'" :label="'E&O Policy Number'" />
            </div>

            <div class="w-full grid grid-cols-2 gap-6">
                <textInput @inputUpdate="inputChange" :inputValue="form.eo_limit" :id="'eo_limit'" :label="'E&O Limit'" />
                <textInput @inputUpdate="inputChange" :inputValue="form.eo_insurer" :id="'eo_insurer'" :label="'E&O Insurer'" />
            </div>

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