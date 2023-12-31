import './bootstrap'
import 'bootstrap'
import 'bootstrap/dist/css/bootstrap.min.css'
import './fontawesome6.js'

import { createApp } from 'vue'
import Main from "@/Vue/Main.vue"
import App from './Vue/App.vue'

createApp(App)
.component("Main", Main)
.mount("#app")
