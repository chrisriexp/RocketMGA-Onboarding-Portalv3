<template>
    <!--Background Image-->
    <div class="w-full bottom-0 fixed z-0">
        <img src="../../assets/1.jpg" alt="Abstract Background Image">
    </div>

    <form @submit.prevent="resetPassword" class="grid gap-4 w-fit h-fit mx-auto mt-48 px-16 py-8 bg-white shadow-newdrop border-custom-gray border-[1px] border-opacity-20 rounded-lg z-10 relative">
        <h1 class="text-center text-custom-dark-blue text-xl">Reset Password</h1>

        <textInput :isdisabled="verifedEmail" @inputUpdate="inputChange" :id="'email'" :label="'Email'" :placeholderText="'johndoe@gmail.com'" :email=true />
        <textInput v-if="codeSent && !verifedEmail" @inputUpdate="inputChange" :id="'verification_code'" :label="'Verification Code'" :placeholderText="'000000'" />
        <textInput v-if="verifedEmail" @inputUpdate="inputChange" :id="'password'" :label="'Password'" :placeholderText="'*******'" :password=true />
        <textInput v-if="verifedEmail" @inputUpdate="inputChange" :id="'confirm_password'" :label="'Confirm Password'" :placeholderText="'*******'" :password=true />


        <div v-if="!verifedEmail" class="w-full">
            <button v-if="!codeSent" @click="sendCode" class="mt-4 bg-custom-dark-blue text-white rounded-md p-2 hover:cursor-pointer w-full">Send Code</button>
            <button v-else @click="verifyCode" class="mt-4 bg-custom-dark-blue text-white rounded-md p-2 hover:cursor-pointer w-full">Verify Email</button>
        </div>
        <input v-else type="submit" class="mt-4 bg-custom-dark-blue text-white rounded-md p-2 hover:cursor-pointer">

        <router-link to="/register" class="text-sm text-custom-dark-blue text-center">Don't have an account? <span class="underline">Register</span></router-link>
    </form>

    <Footer class="bottom-0 absolute" />
</template>

<script>
import Footer from '../components/footer.vue'
import textInput from '../components/textInput.vue'

export default {
    name: "Reset Password",
    data() {
        return {
            verifedEmail: false,
            codeSent: false,
            emailCode: '',
            verification_code: '',
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
            this.emailCode = Math.floor(Math.random()*90000).toString()
            this.codeSent = true
            console.log(this.emailCode)
        },
        verifyCode(){
            if(this.verification_code == this.emailCode){
                this.verifedEmail = true
            } else {
                this.$alert({
                    title: 'Verification Code Error:',
                    text: 'The verification code you entered is invalid.',
                    type: 'error'
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
                            type: 'error'
                        })
                    })
                }
                return
            })

            if(valid){
               await axios.post('/api/resetPassword', this.form)
               .then(response => {
                    this.$router.push({name: 'Home'})
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