<template>
    <NavBar :active="'profile'" :role="role" />

    <div class="sm:ml-64 p-10 h-screen bg-custom-bg">
        <div class="w-full h-fit grid grid-cols-3 gap-8 p-4">
            <div class="h-fit grid gap-4 justify-items-center shadow-newdrop py-8 text-center rounded-xl bg-white">
                <p class="p-6 lg:p-10 rounded-3xl bg-custom-yellow w-fit text-2xl lg:text-5xl font-bold text-custom-gray text-opacity-40">{{ initals }}</p>
                <p class="text-custom-dark-blue text-lg lg:text-2xl font-medium">{{ data.name }}</p>
                <p class="flex gap-2 text-white bg-custom-red p-2 rounded-md text-md align-middle"><BriefcaseIcon class="h-6" />{{ role }}</p>
            </div>

            <div class="h-fit grid col-span-2 gap-4 t text-sm">
                <div class="flex border-custom-gray border-[1px] border-opacity-20 p-4 rounded-md  bg-white">
                    <p class="w-[15%] flex gap-2 font-medium text-custom-gray text-opacity-80"><UserIcon class="h-4 my-auto" />Name:</p>
                    <p class="col-span-2 font-bold text-custom-dark-blue">{{ data.name }}</p>
                </div>

                <div class="flex border-custom-gray border-[1px] border-opacity-20 p-4 rounded-md  bg-white">
                    <p class="w-[15%] flex gap-2 font-medium text-custom-gray text-opacity-80"><IdentificationIcon class="h-4 my-auto" />Rocket ID:</p>
                    <p class="col-span-2 font-bold text-custom-dark-blue">{{ data.rocket_id }}</p>
                </div>

                <div class="flex border-custom-gray border-[1px] border-opacity-20 p-4 rounded-md  bg-white">
                    <p class="w-[15%] flex gap-2 font-medium text-custom-gray text-opacity-80"><AtSymbolIcon class="h-4 my-auto" />Email:</p>
                    <p class="col-span-2 font-bold text-custom-dark-blue">{{ data.email }}</p>
                </div>

                <div class="w-full grid grid-cols-2 gap-4">
                    <div class="grid gap-4 h-fit">
                        <button @click="changeEmail = !changeEmail" class="w-fit flex gap-2 text-white bg-custom-red p-2 rounded-md text-md font-medium">
                            <LockClosedIcon v-if="!changeEmail" class="h-4 my-auto" />
                            <LockOpenIcon v-else class="h-4 my-auto" />
                            Update Email
                        </button>

                        <form @submit.prevent="updateEmail" v-if="changeEmail" class="grid gap-4">
                            <textInput @inputUpdate="emailInputChange" :inputValue="emailForm.email" :id="'email'" :label="'Email'" :email=true />
                            <textInput @inputUpdate="emailInputChange" :inputValue="emailForm.confirm_email" :id="'confirm_email'" :label="'Confirm Email'" :email=true />
                            <textInput @inputUpdate="emailInputChange" :inputValue="emailForm.password" :id="'password'" :label="'Password'" />
                            <input type="submit" value="change" class="uppercase text-sm font-bold text-white bg-custom-red rounded-md p-2 hover:cursor-pointer">
                        </form>
                    </div>

                    <div class="grid gap-4 h-fit">
                        <button @click="changePassword = !changePassword" class="w-fit flex gap-2 text-white bg-custom-red p-2 rounded-md text-md font-medium">
                            <LockClosedIcon v-if="!changePassword" class="h-4 my-auto" />
                            <LockOpenIcon v-else class="h-4 my-auto" />
                            Update Password
                        </button>

                        <form @submit.prevent="updatePassword" v-if="changePassword" class="grid gap-4">
                            <textInput @inputUpdate="passwordInputChange" :inputValue="passwordForm.password" :id="'password'" :label="'Password'" />
                            <textInput @inputUpdate="passwordInputChange" :inputValue="passwordForm.confirm_password" :id="'confirm_password'" :label="'Confirm Password'" />
                            <input type="submit" value="change" class="uppercase text-sm font-bold text-white bg-custom-red rounded-md p-2 hover:cursor-pointer">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { UserIcon, ExclamationTriangleIcon, IdentificationIcon, AtSymbolIcon, LockOpenIcon, LockClosedIcon, BriefcaseIcon } from '@heroicons/vue/24/solid'
import NavBar from '../../components/admin/navbar.vue'
import textInput from '../../components/textInput.vue'

export default {
    name: "Admin Profile",
    data() {
        return {
            changeEmail: false,
            changePassword: false,
            role: '',
            initals: '',
            data: {
                name: '',
                email: '',
                rocket_id: '',
            },
            emailForm: {
                email: '',
                confirm_email: '',
                password: ''
            },
            passwordForm: {
                email: '',
                password: '',
                confirm_password: ''
            },
            emailErrors: [
                {
                    name: 'email',
                    errors: []
                },
                {
                    name: 'confirm_email',
                    errors: []
                }
            ],
            passwordErrors: [
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
    async created() {
        this.role = this.$route.meta.role
        await axios.get('/api/user')
        .then(response => {
            const keys = Object.keys(this.data)

            keys.forEach(key => {
                this.data[key] = response.data[key]
            })
        })

        const names = this.data.name.split(" ")
        const i1 = Array.from(names[0])[0]
        const i2 = Array.from(names[1])[0]
        this.initals = i1+i2
    },
    methods: {
        emailInputChange(id, value, errors){
            this.emailForm[id] = value
            
            if(errors){
                this.emailErrors.forEach(error => {
                    if(error.name == id){
                        error.errors = errors
                    }
                })                    
            } else {
                this.emailErrors.forEach(error => {
                    if(error.name == id){
                        error.errors = []
                    }
                })
            }
        },
        passwordInputChange(id, value, errors){
            this.passwordForm[id] = value
            
            if(errors){
                this.passwordErrors.forEach(error => {
                    if(error.name == id){
                        error.errors = errors
                    }
                })                    
            } else {
                this.passwordErrors.forEach(error => {
                    if(error.name == id){
                        error.errors = []
                    }
                })
            }
        },
        async updateEmail(){
            let valid = true

            this.emailErrors.forEach(item => {
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

            const emailKeys = Object.keys(this.emailForm)

            emailKeys.forEach(key => {
                if(this.emailForm[key] == ''){
                    valid = false
                    this.$alert({
                        title: 'Validation Error',
                        text: `${key.replace("_", " ")} is a required field`,
                        type: 'warn'
                    })
                }
            })

            if(valid){
                await axios.post('/api/resetEmail', this.emailForm)
               .then(response => {
                    location.reload()
                    this.$alert({
                        title: 'Email Reset',
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
        },
        async updatePassword(){
            this.passwordForm.email = this.data.email
            let valid = true

            this.passwordErrors.forEach(item => {
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

            const passwordKeys = Object.keys(this.passwordForm)

            passwordKeys.forEach(key => {
                if(this.passwordForm[key] == ''){
                    valid = false
                    this.$alert({
                        title: 'Validation Error',
                        text: `${key.replace("_", " ")} is a required field`,
                        type: 'warn'
                    })
                }
            })

            if(valid){
                await axios.post('/api/resetPassword', this.passwordForm)
               .then(response => {
                    location.reload()
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
        NavBar,
        textInput,
        UserIcon,
        ExclamationTriangleIcon,
        IdentificationIcon,
        AtSymbolIcon,
        LockOpenIcon,
        LockClosedIcon,
        BriefcaseIcon
    }
}
</script>