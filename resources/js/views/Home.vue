<template>
    <!--Background Image-->
    <div class="w-full bottom-0 fixed z-0">
        <img src="../../assets/1.jpg" alt="Abstract Background Image">
    </div>

    <div class="w-fit flex gap-8 h-fit mx-auto mt-32 px-12 py-8 bg-white shadow-newdrop border-custom-gray border-[1px] border-opacity-20 rounded-lg z-10 relative">
        <div class="w-[600px] border-r-2 border-opacity-20 pr-8">
            <div class="h-fit grid gap-6 text-md">
                <img src="../../assets/RocketMGALogo.png" alt="Rocket MGA logo" class="h-[35px]">
                <h2 class="text-custom-blue font-medium text-2xl">Onboarding Portal</h2>
                <div class="font-light">
                    <p>To start the onboarding process you will need to create an account, during the application we will require the following information:</p>
                    <ul class="list-disc mt-2 pl-8 text-custom-blue">
                        <li>Agent License</li>
                        <li>Agency License</li>
                        <li>Agency E&O</li>
                        <li>Agency Carrier Appointments</li>
                    </ul>

                    <p class="flex gap-4 text-custom-dark-blue mt-4"><ExclamationCircleIcon class="h-6 opacity-60" />The final step of this application will be signing our Agency Appointment Agreement</p>

                    <p class="mt-8 text-custom-gray">Have you already started your onboarding process? <router-link to="/login" class="text-custom-blue font-normal underline">Login now</router-link> to continue your application.</p>
                </div>
            </div>
        </div>
        
        <form @submit.prevent="register" class="grid gap-6 w-[400px]">
            <h1 class="text-center text-custom-dark-blue text-xl font-medium">Create Account</h1>

            <textInput @inputUpdate="inputChange" :inputValue="form.name" :id="'name'" :label="'Name'" :placeholderText="'John Doe'" />
            <textInput @inputUpdate="inputChange" :inputValue="form.email" :id="'email'" :label="'Email'" :placeholderText="'john@doe.com'" :email=true />
            <textInput @inputUpdate="inputChange" :inputValue="form.password" :id="'password'" :label="'Password'" :placeholderText="'*******'" :password=true />
            <textInput @inputUpdate="inputChange" :inputValue="form.confirm_password" :id="'confirm_password'" :label="'Confirm Password'" :placeholderText="'*******'" :password=true />

            <input type="submit" class="mt-4 bg-custom-dark-blue text-white rounded-md p-2 hover:cursor-pointer">
        </form>
    </div>

    <Footer class="bottom-0 absolute" />
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
                        this.$router.push({name: "Login"})
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