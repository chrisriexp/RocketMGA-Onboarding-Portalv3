import './bootstrap';
import './axiosConfig'
import {createApp} from 'vue'
import App from '../App.vue'
import '../css/app.css'
import '../css/tailwind.css'
import Notifications from '@kyvg/vue3-notification'
import {createRouter, createWebHistory} from 'vue-router';
import NotFound from './views/NotFound.vue'
import Home from './views/Home.vue'
import Register from './views/Register.vue'
import ResetPassword from './views/ResetPassword.vue'
import OnboardingForm from './views/OnboardingForm.vue'

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
        }
    ],
})

function validateAccessToken(to, from, next) {
    const accessToken = localStorage.getItem('token');
    if (!accessToken) {
        next({ name: "Home" });
        return;
    }

    axios.get('/api/token/validate')
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

createApp(App).use(Notifications, { name: "alert" }).use(router).mount("#app")
