<template>
    <!--Background Image-->
    <div class="w-full bottom-0 fixed z-0">
        <img src="../../assets/1.jpg" alt="Abstract Background Image">
    </div>

    <form @submit.prevent="login" class="grid gap-4 w-fit h-fit mx-auto mt-64 px-16 py-8 bg-white shadow-newdrop border-custom-gray border-[1px] border-opacity-20 rounded-lg z-10 relative">
        <h1 class="text-center text-custom-dark-blue text-xl font-medium">Login</h1>

        <textInput @inputUpdate="inputChange" :id="'email'" :label="'Email'" :placeholderText="'johndoe@gmail.com'" :email=true />
        <textInput @inputUpdate="inputChange" :id="'password'" :label="'Password'" :placeholderText="'*******'" :password=true />

        <input type="submit" class="mt-4 bg-custom-dark-blue text-white rounded-md p-2 hover:cursor-pointer">

        <div class="flow-root w-full">
            <router-link to="/register" class="text-sm underline text-custom-dark-blue float-left">Create Account</router-link>
            <router-link to="/reset-password" class="text-sm underline text-custom-dark-blue float-right">Forgot Password</router-link>
        </div>
    </form>

    <Footer class="bottom-0 absolute" />
</template>

<script>
import Footer from '../components/footer.vue'
import textInput from '../components/textInput.vue'

export default {
    name: "Home",
    data() {
        return {
            form: {
                email: '',
                password: ''
            },
            errors: [
                {
                    name: 'email',
                    errors: []
                },
                {
                    name: 'password',
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
        async login(){
            let valid = true

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
                await axios.post('/api/login', this.form)
                .then(response => {
                    if(response.data.success){
                        localStorage.setItem('token', response.data.token)
                        this.$router.push({name: 'OnboardingForm'})
                        this.$alert({
                            title: 'Login',
                            text: response.data.message,
                            type: 'success'
                        })
                    }
                })
                .catch(error => {
                    this.form.email = ''
                    this.form.password = ''

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