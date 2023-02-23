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
import Login from './views/Login.vue'
import ResetPassword from './views/ResetPassword.vue'
import OnboardingForm from './views/OnboardingForm.vue'
import AdminLogin from './views/Admin/Login.vue'
import AdminDashboard from './views/Admin/Dashboard.vue'
import AdminProfile from './views/Admin/Profile.vue'
import AgencyView from './views/Admin/Agency.vue'
import AdminAgents from './views/Admin/Agents.vue'
import AdminReview from './views/Admin/UnderReview.vue'
import AdminApproved from './views/Admin/Approved.vue'
import AdminDocuments from './views/Admin/Documents.vue'
import AdminUsers from './views/Admin/Users.vue'

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
            path: "/login",
            name: "Login",
            component: Login
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
                    path: "agency/:rocket_id",
                    name: "AgencyView",
                    component: AgencyView,
                    props: true,
                    meta: {
                        admin: true,
                        role: ''
                    },
                    beforeEnter: validateAccessToken
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
                    path: "agents",
                    name: "AdminAgents",
                    component: AdminAgents,
                    meta: {
                        admin: true,
                        role: ''
                    },
                    beforeEnter: validateAccessToken
                },
                {
                    path: "under-review",
                    name: "AdminReview",
                    component: AdminReview,
                    meta: {
                        admin: true,
                        role: ''
                    },
                    beforeEnter: validateAccessToken
                },
                {
                    path: "approved",
                    name: "AdminApproved",
                    component: AdminApproved,
                    meta: {
                        admin: true,
                        role: ''
                    },
                    beforeEnter: validateAccessToken
                },
                {
                    path: "documents",
                    name: "AdminDocuments",
                    component: AdminDocuments,
                    meta: {
                        admin: true,
                        role: ''
                    },
                    beforeEnter: validateAccessToken
                },
                {
                    path: "users",
                    name: "AdminUsers",
                    component: AdminUsers,
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
