import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'

export default function (app) {
  app.component('Multiselect', Multiselect) // Register as a global component
}
