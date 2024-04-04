import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { createPinia } from 'pinia'
import { registerPlugins } from '@core/utils/plugins'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'

// Styles
import '@core-scss/template/index.scss'
import '@styles/styles.scss'

const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)

// Create vue app
const app = createApp(App)

app.use(router)
app.use(pinia)
// Register plugins
registerPlugins(app)

// Mount vue app
app.mount('#app')