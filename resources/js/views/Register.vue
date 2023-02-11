<template>
    <!--Background Image-->
    <div class="w-full bottom-0 fixed z-0">
        <img src="../../assets/1.jpg" alt="Abstract Background Image">
    </div>

    <form @submit.prevent="register" class="grid gap-6 w-[400px] h-fit mx-auto mt-32 px-16 py-8 bg-white shadow-newdrop border-custom-gray border-[1px] border-opacity-20 rounded-lg z-10 relative">
        <h1 class="text-center text-custom-dark-blue text-xl font-medium">Register</h1>

        <textInput @inputUpdate="inputChange" :inputValue="form.name" :id="'name'" :label="'Name'" :placeholderText="'John Doe'" />
        <textInput @inputUpdate="inputChange" :inputValue="form.email" :id="'email'" :label="'Email'" :placeholderText="'john@doe.com'" :email=true />
        <textInput @inputUpdate="inputChange" :inputValue="form.password" :id="'password'" :label="'Password'" :placeholderText="'*******'" :password=true />
        <textInput @inputUpdate="inputChange" :inputValue="form.confirm_password" :id="'confirm_password'" :label="'Confirm Password'" :placeholderText="'*******'" :password=true />

        <input type="submit" class="mt-4 bg-custom-dark-blue text-white rounded-md p-2 hover:cursor-pointer">

        <router-link to="/" class="text-sm text-custom-dark-blue text-center mt-[-15px]">Already have an account? <span class="underline">Login</span></router-link>
    </form>

    <Footer class="bottom-0 absolute" />
</template>

<script>
import Footer from '../components/footer.vue'
import textInput from '../components/textInput.vue'

export default {
    name: "Register",
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
                        this.$router.push({name: "Home"})
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
        textInput
    }
}
</script>