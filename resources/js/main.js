import { createApp } from 'vue'
import { createHead } from '@unhead/vue'
import App from '@/App.vue'
import { registerPlugins } from '@core/utils/plugins'

// Styles
import '@core-scss/template/index.scss'
import '@styles/styles.scss'

// Create vue app
const app = createApp(App)

const head = createHead()

app.use(head)

// Register plugins
registerPlugins(app)

// Mount vue app
app.mount('#app')