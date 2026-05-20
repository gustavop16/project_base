import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { createPinia } from 'pinia'
import './assets/css/tailwind.css'
import './assets/css/custom.css'
import '@vueform/multiselect/themes/default.css'
import i18n from './i18n'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import money from 'v-money3'

const pinia = createPinia()

createApp(App)
.use(pinia)
.use(router)
.use(i18n)
.use(money)
.component('font-awesome-icon', FontAwesomeIcon)
.mount('#app')
