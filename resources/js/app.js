import './bootstrap';
import './axiosConfig'
import {createApp} from 'vue'
import App from '../App.vue'
import '../css/app.css'
import '../css/tailwind.css'
import 'vue-select/dist/vue-select.css';
import Notifications from '@kyvg/vue3-notification'
import vSelect from "vue-select";
import {createRouter, createWebHistory} from 'vue-router';
import NotFound from './views/NotFound.vue'
import Home from './views/Home.vue'
import Register from './views/Register.vue'
import ResetPassword from './views/ResetPassword.vue'
import OnboardingForm from './views/OnboardingForm.vue'
import AdminLogin from './views/Admin/Login.vue'
import AdminDashboard from './views/Admin/Dashboard.vue'
import AdminProfile from './views/Admin/Profile.vue'

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: "/:catchAll(.*)",
            name: "NotFound",
            component: NotFound,
        },
        {
            path: "/",
            name: "Home",
            component: Home
        },
        {
            path: "/register",
            name: "Register",
            component: Register
        },
        {
            path: "/reset-password",
            name: "ResetPassword",
            component: ResetPassword
        },
        {
            path: "/onboarding-form",
            name: "OnboardingForm",
            component: OnboardingForm,
            beforeEnter: validateAccessToken
        },
        {
            path: "/admin",
            children: [
                {
                    path: "",
                    name: "AdminLogin",
                    component: AdminLogin
                },
                {
                    path: "dashboard",
                    name: "AdminDashboard",
                    component: AdminDashboard,
                    meta: {
                        admin: true,
                        role: ''
                    },
                    beforeEnter: validateAccessToken
                },
                {
                    path: "profile",
                    name: "AdminProfile",
                    component: AdminProfile,
                    meta: {
                        admin: true,
                        role: ''
                    },
                    beforeEnter: validateAccessToken
                }
            ]
        }
    ],
})

async function validateAccessToken(to, from, next) {
    const accessToken = localStorage.getItem('token');

    if (!accessToken) {
        next({ name: "Home" });
        return;
    }

    if(to.meta.admin) {
        await axios.post('/api/token/validate', {"admin": true})
        .then(response => {
            if(response.data.valid){
                to.meta.role = response.data.role;
                next();
            } else {
                router.replace({ name: "AdminLogin"});
            }
        })
        .catch(error => {
            console.error(error);
            next({ name: "AdminLogin" });
        });
    } else {
        await axios.post('/api/token/validate')
        .then(response => {
            if (response.data.valid) {
                next();
            } else {
                next({ name: "Home" });
            }
        })
        .catch(error => {
            console.error(error);
            next({ name: "Home" });
        });
    }

    
}

createApp(App).use(Notifications, { name: "alert" }).use(router).component("v-select", vSelect).mount("#app")
