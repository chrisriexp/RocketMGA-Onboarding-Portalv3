<template>
    <NavBar :active="'users'" :role="role" />

    <div id="loading" :class="loading ? '' : 'hidden'" class="w-full grid justify-items-center z-10 fixed">
        <loading class="mt-64" />
    </div>

    <div class="sm:ml-64 p-10 h-screen bg-custom-bg">
        <div class="w-full flow-root">
            <div class="float-left grid gap-2 text-custom-gray">
                <h1 class="font-semibold text-2xl">Admin Users</h1>
                <p>Below you can find the full list admin users.</p>
            </div>

            <button @click="createUser = true" :disabled="loading" class="float-right w-fit flex gap-2 px-6 py-2 rounded-md bg-custom-red text-white">Create User<PlusCircleIcon class="h-6" /></button>
        </div>

        <form @submit.prevent="createAdminUser" v-if="createUser" class="w-[400px] h-fit grid gap-4 px-10 py-4 ml-[25%] rounded-md bg-white shadow-newdrop border-[1px] border-custom-gray border-opacity-20 z-10 absolute">
            <div class="w-full flow-root">
                <p @click="createUser = false" class="flex gap-2 float-right text-md my-auto text-custom-gray hover:text-custom-red hover:cursor-pointer">Close<XCircleIcon class="h-6 my-auto" /></p>
            </div>

            <div class="grid gap-2">
                <p class="text-md text-custom-dark-blue font-medium">Role <span class="text-custom-red">*</span></p>
                <v-select style="min-width: 200px;"
                    code="code"
                    v-model="newUser.role"
                    :options="options"
                    label="name"
                    class="text-sm font-medium rounded-xl w-fit bg-custom-red disabled:text-custom-gray"
                ></v-select>  
            </div>

            <textInput @inputUpdate="newInputChange" :inputValue="newUser.name" :id="'name'" :label="'Name'" />
            <textInput @inputUpdate="newInputChange" :inputValue="newUser.email" :id="'email'" :label="'Email'" :email=true />
            <textInput @inputUpdate="newInputChange" :inputValue="newUser.password" :id="'password'" :label="'Password'" />
            <textInput @inputUpdate="newInputChange" :inputValue="newUser.confirm_password" :id="'confirm_password'" :label="'Confirm Password'" />

            <input :disabled="loading" type="submit" value="Create User" class="mt-4 px-6 py-2 rounded-md bg-custom-red text-white hover:cursor-pointer">
        </form>

        <form @submit.prevent="updateAdminUser" v-if="update" class="w-[400px] h-fit grid gap-4 px-10 py-4 ml-[25%] rounded-md bg-white shadow-newdrop border-[1px] border-custom-gray border-opacity-20 z-10 absolute">
            <div class="w-full flow-root">
                <p @click="update = false" class="flex gap-2 float-right text-md my-auto text-custom-gray hover:text-custom-red hover:cursor-pointer">Close<XCircleIcon class="h-6 my-auto" /></p>
            </div>

            <div class="grid gap-2">
                <p class="text-md text-custom-dark-blue font-medium">Role <span class="text-custom-red">*</span></p>
                <v-select style="min-width: 200px;"
                    code="code"
                    v-model="updateUser.role"
                    :options="options"
                    label="name"
                    class="text-sm font-medium rounded-xl w-fit bg-custom-red disabled:text-custom-gray"
                ></v-select>  
            </div>

            <textInput @inputUpdate="updateInputChange" :inputValue="updateUser.name" :id="'name'" :label="'Name'" />
            <textInput @inputUpdate="updateInputChange" :inputValue="updateUser.email" :id="'email'" :label="'Email'" :email=true />
            <textInput @inputUpdate="updateInputChange" :inputValue="updateUser.password" :id="'password'" :label="'Password'" :required=false />
            <textInput @inputUpdate="updateInputChange" :inputValue="updateUser.confirm_password" :id="'confirm_password'" :label="'Confirm Password'" :required=false />

            <input :disabled="loading" type="submit" value="Update User" class="mt-4 px-6 py-2 rounded-md bg-custom-red text-white hover:cursor-pointer">
        </form>
        
        <div class="mt-8 grid grid-cols-5 gap-6 text-custom-gray font-medium pb-2 border-b-2 border-custom-gray border-opacity-20">
            <p>Rocket ID</p>
            <p>Role</p>
            <p>Name</p>
            <p>Email</p>
        </div>

        <div class="h-[600px] mt-8 overflow-y-scroll scrollbar">
            <div class="h-fit grid gap-4">
                <div v-for="(user, index) in users" :key="index" class="grid grid-cols-5 gap-6 text-sm text-custom-gray bg-white border-custom-gray border-opacity-20 border-[1px] p-2 rounded-md hover:inner-border-2">
                    <p>{{ user.rocket_id }}</p>
                    <p class="w-fit px-4 bg-custom-gray text-white rounded-md">{{ user.role }}</p>
                    <p>{{ user.name }}</p>
                    <p>{{ user.email }}</p>
                    <button :disabled="update" @click="showUpdate(user)" class="w-fit px-6 bg-custom-red text-white rounded-md disabled:opacity-40">Update User</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import NavBar from '../../components/admin/navbar.vue'
import loading from '../../components/loading-red.vue'
import textInput from '../../components/textInput.vue'
import roles from '../../../assets/roles.json'

import { PlusCircleIcon } from '@heroicons/vue/24/solid'
import { XCircleIcon } from '@heroicons/vue/24/outline'

export default {
    name: "AdminUsers",
    data(){
        return {
            createUser: false,
            update: false,
            options: roles,
            newUser: {
                role: '',
                name: '',
                email: '',
                password: '',
                confirm_password: ''
            },
            newErrors: [
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
            ],
            updateUser: {
                id: '',
                role: '',
                name: '',
                email: '',
                password: '',
                confirm_password: ''
            },
            updateErrors: [
                {
                    name: 'name',
                    errors: []
                },
                {
                    name: 'email',
                    errors: []
                }
            ],
            loading: true,
            role: '',
            users: []
        }
    },
    async created(){
        this.role = this.$route.meta.role

        if(this.role != 'super-admin'){
            this.$router.push({name: 'AdminDashboard'})
        }

        await axios.get('/api/users')
        .then(response => {
            const keys = Object.keys(response.data.users)

            keys.forEach(key => {
                this.users.push(response.data.users[key])
            })
        })

        this.loading = false
    },
    methods: {
        newInputChange(id, value, errors){
            this.newUser[id] = value
            
            if(errors){
                this.newErrors.forEach(error => {
                    if(error.name == id){
                        error.errors = errors
                    }
                })                    
            } else {
                this.newErrors.forEach(error => {
                    if(error.name == id){
                        error.errors = []
                    }
                })
            }
        },
        updateInputChange(id, value, errors){
            this.updateUser[id] = value
            
            if(errors){
                this.updateErrors.forEach(error => {
                    if(error.name == id){
                        error.errors = errors
                    }
                })                    
            } else {
                this.updateErrors.forEach(error => {
                    if(error.name == id){
                        error.errors = []
                    }
                })
            }
        },
        async showUpdate(user){
            const keys = Object.keys(this.updateUser)

            keys.forEach(key => {
                if(key != 'role'){
                    this.updateUser[key] = user[key]
                }
            })

            this.options.forEach(role => {
                if(role.code == user.role){
                    this.updateUser.role = role
                }
            })

            this.update = true
        },
        async updateAdminUser(){
            this.loading = true
            let valid = true

            this.updateErrors.forEach(item => {
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

            const keys = Object.keys(this.updateUser)

            keys.forEach(key => {
                if(this.updateUser[key] == '' && key != 'password' && key != 'confirm_password'){
                    valid = false
                    this.$alert({
                        title: 'Validation Error',
                        text: `${key.replace("_", " ")} is a required field`,
                        type: 'warn'
                    })
                }
            })

            let userUpdated = false

            if(valid){
                this.updateUser.role = this.updateUser.role.code

                let data = {}

                if(this.updateUser.password == ''){
                    data.id = this.updateUser.id
                    data.role = this.updateUser.role
                    data.name = this.updateUser.name
                    data.email = this.updateUser.email
                } else {
                    data = this.updateUser
                }

                await axios.post('/api/update/admin', data)
                .then(response => {
                    if(response.data.success){
                        this.$alert({
                            title: 'User Update',
                            text: response.data.message,
                            type: 'success'
                        })

                        this.update = false

                        userUpdated = true
                    }
                })
                .catch(error => {
                    if(error.response.status == 400){
                        const keys = Object.keys(error.response.data.message)

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

            if(userUpdated){
                keys.forEach(key => {
                    this.updateUser[key] = ''
                })

                this.users = []

                await axios.get('/api/users')
                .then(response => {
                    const keys = Object.keys(response.data.users)

                    keys.forEach(key => {
                        this.users.push(response.data.users[key])
                    })
                })
            }

            this.loading = false
        },
        async createAdminUser(){
            this.loading = true
            let valid = true

            this.newErrors.forEach(item => {
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

            const keys = Object.keys(this.newUser)

            keys.forEach(key => {
                if(this.newUser[key] == ''){
                    valid = false
                    this.$alert({
                        title: 'Validation Error',
                        text: `${key.replace("_", " ")} is a required field`,
                        type: 'warn'
                    })
                }
            })

            let userCreated = false

            if(valid){
                this.newUser.role = this.newUser.role.code

                await axios.post('/api/register/admin', this.newUser)
                .then(response => {
                    if(response.data.success){
                        this.$alert({
                            title: 'User Registration',
                            text: response.data.message,
                            type: 'success'
                        })

                        this.createUser = false

                        userCreated = true
                    }
                })
                .catch(error => {
                    if(error.response.status == 400){
                        const keys = Object.keys(error.response.data.message)

                        keys.forEach(key => {
                            error.response.data.message[key].forEach(error => {
                                this.$alert({
                                    title: 'Registration Error',
                                    text: error,
                                    type: 'error'
                                })
                            })
                        })

                    } else if(error.response.status == 401){
                        this.$alert({
                            title: 'Registration Error',
                            text: error.response.data.message,
                            type: 'error'
                        })
                    }
                })

                keys.forEach(key => {
                    this.newUser[key] = ''
                })
            }

            if(userCreated){
                this.users = []

                await axios.get('/api/users')
                .then(response => {
                    const keys = Object.keys(response.data.users)

                    keys.forEach(key => {
                        this.users.push(response.data.users[key])
                    })
                })
            }
            
            this.loading = false
        }
    },
    components: {
        NavBar,
        loading,
        textInput,
        PlusCircleIcon,
        XCircleIcon
    }
}
</script>