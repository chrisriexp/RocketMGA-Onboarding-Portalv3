<template>
    <div class="w-full h-full md:w-screen md:h-screen grid md:grid-cols-2 bg-white">
        <div class="grid relative bg-custom-light-blue-bg bg-opacity-30">
            <!--Background Image-->
            <div class="hidden md:block absolute top-0 right-0 z-0">
                <img src="../../assets/portal_left_bg.png" alt="Abstract Background Image" class="">
            </div>

            <div class="z-10 grid w-fit h-fit mx-auto gap-10 px-6 md:px-0">
                <!--Rocket MGA Logo-->
                <img src="../../assets/RocketMGALogo.png" alt="Rocket MGA Logo" class="mx-auto md:mx-0 h-[36px] mt-24 md:mt-48">
                
                <!--Heading-->
                <div class="grid h-fit">
                    <p class="text-[12px] md:text-[16px] text-custom-dark-blue opacity-70">Welcome to,</p>

                    <h1 class="text-custom-blue text-[24px] md:text-[32px] font-semibold">Onboarding Portal</h1>
                </div>

                <!--Portal Information-->
                <div class="grid gap-6 h-fit w-full md:w-[537px]">
                    <p class="text-[14px] md:text-[16px] text-custom-dark-blue">To start the onboarding process you will need to create an account, during the application will require the following information</p>
                    
                    <ul class="list-disc ml-20 text-[14px] md:text-[16px] text-custom-blue">
                        <li>Agent License</li>
                        <li>Agency License</li>
                        <li>Agency E&O</li>
                        <li>Agency Carrier Appointments</li>
                    </ul>

                    <p class="flex gap-4 text-[14px] md:text-[16px] text-custom-dark-blue"><ExclamationCircleIcon class="h-[34px]" />The final step of this application will be signing our Agency Appointment Agreement</p>
                </div>

                <!--Login Disclaimer-->
                <p class="w-full md:w-[527px] m-0 md:mt-12 text-[16px] text-custom-dark-blue">Have you already started your onboarding process? <router-link to="Login" class="text-custom-blue font-medium">Login Now</router-link> to continue your application</p>
            </div>
        </div>

        <div class="h-fit grid gap-12 justify-items-center px-6 md:px-0">

            <!--Favicon Image-->
            <img src="../../assets/favicon.png" alt="Rocket Favicon" class="h-[53px] md:h-[64px] mt-12 md:mt-24">

            <!--Create Account Header-->
            <div class="grid h-fit text-center text-custom-dark-blue">
                <h2 class="text-[24px] md:text-[32px] font-semibold">Create Account</h2>
                <p class="text-[14px] md:text-[16px] opacity-70">Please enter your information</p>
            </div>

            <!--Create Account Form-->
            <form @submit.prevent="register" class="grid gap-6 w-full md:w-[400px] mb-16 md:mb-0">

                <textInput @inputUpdate="inputChange" :inputValue="form.name" :id="'name'" :label="'Name'" :placeholderText="'John Doe'" />
                <textInput @inputUpdate="inputChange" :inputValue="form.email" :id="'email'" :label="'Email'" :placeholderText="'john@doe.com'" :email=true />
                <textInput @inputUpdate="inputChange" :inputValue="form.password" :id="'password'" :label="'Password'" :placeholderText="'*******'" :password=true />
                <textInput @inputUpdate="inputChange" :inputValue="form.confirm_password" :id="'confirm_password'" :label="'Confirm Password'" :placeholderText="'*******'" :password=true />

                <input :disabled="registerDisabled" type="submit" value="Create" class="mt-4 bg-custom-dark-blue text-white rounded-md p-2 border-l-[4px] border-b-[5px] border-[#2c4174] active:border-custom-dark-blue cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
            </form>
        </div>
    </div>
</template>

<script>
import Footer from '../components/footer.vue'
import textInput from '../components/textInput.vue'
import { ExclamationCircleIcon } from '@heroicons/vue/24/outline'

export default {
    name: "Create Account",
    data() {
        return {
            form: {
                registerDisabled: false,
                name: '',
                email: '',
                password: '',
                confirm_password: ''
            },
            errors: [
                {
                    name: 'name',
                    errors: []
                },
                {
                    name: 'email',
                    errors: []
                },
                {
                    name: 'password',
                    errors: []
                },
                {
                    name: 'confirm_password',
                    errors: []
                }
            ]
        }
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
        async register(){
            //Disable register button
            this.registerDisabled = true

            let valid  = true

            this.errors.forEach(item => {
                if(item.errors.length > 0){
                    valid = false
                    item.errors.forEach(error => {
                        this.$alert({
                            title: item.name.toUpperCase()+" ERROR",
                            text: error,
                            type: 'error'
                        })
                    })
                }
                return
            })

            if(valid){
                await axios.post('/api/register', this.form)
                .then(response => {
                    this.$alert({
                        title: 'Registered',
                        text: response.data.message,
                        type: 'success'
                    })

                    setTimeout(() => {
                        axios.post('/api/login', {
                            "email": this.form.email,
                            "password": this.form.password
                        })
                        .then(response => {
                            localStorage.setItem('token', response.data.token)
                            this.$router.push({name: 'OnboardingForm'})
                        })
                    }, 500)
                })
                .catch(error => {
                    this.form.name = ''
                    this.form.email = ''
                    this.form.password = ''
                    this.form.confirm_password = ''

                    if(error.response.status == 401){
                        this.$alert({
                            title: 'Registration Error',
                            text: error.response.data.message,
                            type: 'error'
                        })
                    } else {
                        const keys  = Object.keys(error.response.data.message)

                        keys.forEach(key => {
                            error.response.data.message[key].forEach(error => {
                                this.$alert({
                                    title: 'Registration Error',
                                    text: error,
                                    type: 'error'
                                })
                            })
                        })
                    }               
                })
            }
        }
    },
    components: {
        Footer,
        textInput,
        ExclamationCircleIcon
    }
}
</script>