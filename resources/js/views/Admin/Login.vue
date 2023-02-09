<template>
    <!--Background Image-->
    <div class="w-full bottom-0 fixed z-0">
        <img src="../../../assets/1-red.jpg" alt="Abstract Background Image">
    </div>

    <form @submit.prevent="login" class="grid gap-6 w-[400px] h-fit mx-auto mt-64 px-16 py-8 bg-white shadow-newdrop border-custom-gray border-[1px] border-opacity-20 rounded-lg z-10 relative">
        <h1 class="text-center text-custom-dark-blue text-xl font-medium">Admin Login</h1>

        <textInput @inputUpdate="inputChange" :inputValue="form.email" :id="'email'" :label="'Email'" :placeholderText="'john@doe.com'" :email=true />
        <textInput @inputUpdate="inputChange" :inputValue="form.password" :id="'password'" :label="'Password'" :placeholderText="'*******'" :password=true />

        <input type="submit" class="mt-4 bg-custom-dark-blue text-white rounded-md p-2 hover:cursor-pointer">
    </form>

    <Footer class="bottom-0 absolute" />
</template>

<script>
import Footer from '../../components/footer.vue'
import textInput from '../../components/textInput.vue'

export default {
    name: "Admin Login",
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
        const token = localStorage.getItem('token')

        if(token){
            await axios.post('/api/token/validate', {"admin": true})
            .then(response => {
                if(response.data.valid){
                    this.$router.push({name: "AdminDashboard"})
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
                    if(response.data.role == 'user'){
                        this.form.email = ''
                        this.form.password = ''

                        this.$alert({
                            title: 'Authorization Error',
                            text: "This is not an admin user",
                            type: 'error'
                        })

                        return
                    }

                    if(response.data.success){
                        localStorage.setItem('token', response.data.token)
                        this.$router.push({name: 'AdminDashboard'})
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