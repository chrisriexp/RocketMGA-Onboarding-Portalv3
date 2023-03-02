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

                <!--Login Illustration-->
                <img src="../../assets/login_image.png" alt="Login Illustration">
            </div>
        </div>

        <div class="grid relative">
            <div class="h-fit grid gap-12 justify-items-center px-6 md:px-0">
                <!--Favicon Image-->
                <img src="../../assets/favicon.png" alt="Rocket Favicon" class="h-[53px] md:h-[64px] mt-12 md:mt-24">

                <!--Create Account Header-->
                <div class="grid h-fit text-center text-custom-dark-blue">
                    <h2 class="text-[24px] md:text-[32px] font-semibold">Login</h2>
                    <p class="text-[14px] md:text-[16px] opacity-70">Please enter your information</p>
                </div>

                <form @submit.prevent="login" class="grid gap-6 w-full md:w-[400px] mb-16 md:mb-0">

                    <textInput @inputUpdate="inputChange" :inputValue="form.email" :id="'email'" :label="'Email'" :placeholderText="'john@doe.com'" :email=true />
                    <textInput @inputUpdate="inputChange" :inputValue="form.password" :id="'password'" :label="'Password'" :placeholderText="'*******'" :password=true />

                    <!--Forgot Password-->
                    <div class="w-full flow-rootm mt-[-15px]">
                        <router-link to="/reset-password" class="text-[14px] md:text-[16px] underline text-custom-blue float-right">Forgot Password</router-link>
                    </div>

                    <input type="submit" value="Login" class="mt-4 bg-custom-dark-blue text-white rounded-md p-2 border-l-[4px] border-b-[5px] border-[#2c4174] active:border-custom-dark-blue hover:cursor-pointer shadow-newdrop">

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
    async created() {
        const accessToken = localStorage.getItem('token');
        if (accessToken) {
            axios.post('/api/token/validate')
            .then(response => {

                if (response.data.valid) {
                    this.$router.push({name: 'OnboardingForm'})
                }
            })
            .catch(error => {
                console.error(error);
            });
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