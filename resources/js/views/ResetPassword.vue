<template>
    <div class="w-full h-full md:w-screen md:h-screen grid md:grid-cols-2 bg-white">
        <div class="grid relative bg-custom-light-blue-bg bg-opacity-30">
            <!--Background Image-->
            <div class="hidden md:block absolute top-0 right-0 z-0">
                <img src="../../assets/portal_left_bg.png" alt="Abstract Background Image" class="">
            </div>

            <div class="z-10 grid justify-items-center w-fit h-fit mx-auto gap-10 px-6 md:px-0">
                <!--Rocket MGA Logo-->
                <img src="../../assets/RocketMGALogo.png" alt="Rocket MGA Logo" class="mx-auto md:mx-0 h-[36px] mt-24 md:mt-48">

                <!--Reset Password Illustration-->
                <img v-if="!verifedEmail" src="../../assets/reset_image.png" alt="Reset Illustration">
                <img v-else src="../../assets/change_image.png" alt="Reset Illustration">
            </div>
        </div>

        <div class="grid relative">
            <div class="h-fit grid gap-12 justify-items-center px-6 md:px-0">
                <!--Favicon Image-->
                <img src="../../assets/favicon.png" alt="Rocket Favicon" class="h-[53px] md:h-[64px] mt-12 md:mt-24">

                <!--Create Account Header-->
                <div class="grid h-fit text-center text-custom-dark-blue">
                    <h2 class="text-[24px] md:text-[32px] font-semibold">Reset Password</h2>
                    <p v-if="verifedEmail && codeSent" class="text-[14px] md:text-[16px] opacity-70">Please enter new password</p>
                    <p v-else-if="!verifedEmail && codeSent" class="text-[14px] md:text-[16px] opacity-70">Please enter verification code</p>
                    <p v-else-if="!verifedEmail && !codeSent" class="text-[14px] md:text-[16px] opacity-70">Please enter your email</p>
                </div>
                

                <form @submit.prevent="resetPassword" class="w-full md:w-[400px] grid gap-6 mb-16 md:mb-0">
                    <textInput :isdisabled="verifedEmail" @inputUpdate="inputChange" :inputValue="form.email" :id="'email'" :label="'Email'" :placeholderText="'john@doe.com'" :email=true />
                    <textInput v-if="codeSent && !verifedEmail" @inputUpdate="inputChange" :id="'verification_code'" :label="'Verification Code'" :placeholderText="'000000'" />
                    <textInput v-if="verifedEmail" @inputUpdate="inputChange" :id="'password'" :label="'Password'" :placeholderText="'*******'" :password=true />
                    <textInput v-if="verifedEmail" @inputUpdate="inputChange" :id="'confirm_password'" :label="'Confirm Password'" :placeholderText="'*******'" :password=true />


                    <div v-if="!verifedEmail" class="w-full">
                        <button v-if="!codeSent" type="button" @click="sendCode" class="w-full mt-4 bg-custom-dark-blue text-white rounded-md p-2 border-l-[4px] border-b-[5px] border-[#2c4174] active:border-custom-dark-blue hover:cursor-pointer">Send Code</button>
                        <button v-else type="button" @click="verifyCode" class="w-full mt-4 bg-custom-dark-blue text-white rounded-md p-2 border-l-[4px] border-b-[5px] border-[#2c4174] active:border-custom-dark-blue hover:cursor-pointer">Verify Email</button>
                    </div>
                    <input v-else type="submit" value="Change Password" class="mt-4 bg-custom-dark-blue text-white rounded-md p-2 border-l-[4px] border-b-[5px] border-[#2c4174] active:border-custom-dark-blue hover:cursor-pointer">

                    <div class="w-full grid justify-items-center mt-[-15px]">
                        <router-link to="/" class="text-[14px] md:text-[16px] text-custom-dark-blue opacity-70 float-left">Don't have an account? <span class="text-custom-blue">Create Account</span></router-link>
                    </div>
                </form>
            </div>

            <Footer />
        </div>
    </div>
</template>

<script>
import Footer from '../components/footer.vue'
import textInput from '../components/textInput.vue'

import emailjs from '@emailjs/browser';

export default {
    name: "Reset Password",
    data() {
        return {
            verifedEmail: false,
            codeSent: false,
            emailCode: '',
            verification_code: '',
            api: {
                serviceID: 'service_nf9yozb',
                publicKey: 'h29zXRTKkaswfKPkp',
                verificationCode: 'template_771e0me'
            },
            form: {
                email: '',
                password: '',
                confirm_password: ''
            },
            errors: [
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
            if(id == 'verification_code'){
                this.verification_code = value
                return
            }

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
        sendCode(){
            let valid = true

            this.errors.forEach(item => {
                if(item.errors.length > 0){
                    valid = false
                    item.errors.forEach(error => {
                        this.$alert({
                            title: item.name.toUpperCase()+" ERROR",
                            text: error,
                            type: 'warn'
                        })
                    })
                }
                return
            })

            if(this.form.email == null || this.form.email == ''){
                valid = false
                this.$alert({
                    title: 'Email Valdation',
                    text: 'Please enter a valid email.',
                    type: 'warn'
                })
                return
            }

            if(valid){
                this.emailCode = Math.floor(Math.random()*90000).toString()

                emailjs.init(this.api.publicKey)
                emailjs.send(this.api.serviceID, this.api.verificationCode, {
                    toEmail: this.form.email,
                    verificationCodeEmail: this.emailCode,
                })

                this.codeSent = true
            }
        },
        verifyCode(){
            if(this.verification_code == this.emailCode){
                this.verifedEmail = true
            } else {
                this.$alert({
                    title: 'Verification Code Error:',
                    text: 'The verification code you entered is invalid.',
                    type: 'warn'
                })

                this.codeSent = false
            }
        },
        async resetPassword(){
            let valid  = true

            this.errors.forEach(item => {
                if(item.errors.length > 0){
                    valid = false
                    item.errors.forEach(error => {
                        this.$alert({
                            title: item.name.toUpperCase()+" ERROR",
                            text: error,
                            type: 'warn'
                        })
                    })
                }
                return
            })

            if(valid){
               await axios.post('/api/resetPassword', this.form)
               .then(response => {
                    this.$router.push({name: 'Login'})
                    this.$alert({
                        title: 'Password Reset',
                        text: response.data.message,
                        type: 'success'
                    })
               })
               .catch(error => {
                    if(error.response.status == 400){
                        const keys = Object.keys(error.response.data.message)

                        keys.forEach(key => {
                            error.response.data.message[key].forEach(error => {
                                this.$alert({
                                    title: 'Login Error',
                                    text: error,
                                    type: 'error'
                                })
                            })
                        })

                    } else if(error.response.status == 401){
                        this.$alert({
                            title: 'Login Error',
                            text: error.response.data.message,
                            type: 'error'
                        })
                        setTimeout(() => {
                            location.reload()
                        }, 500)
                        
                    }
                })
            }
        }
    },
    components: {
        Footer,
        textInput
    }
}
</script>