import { boot } from 'quasar/wrappers'
import axios, { AxiosInstance } from 'axios'
import { useCounterStore } from 'stores/example-store'

declare module '@vue/runtime-core' {
  interface ComponentCustomProperties {
    $api: AxiosInstance;
  }
}

// Be careful when using SSR for cross-request state pollution
// due to creating a Singleton instance here;
// If any client changes this (global) instance, it might be a
// good idea to move this instance creation inside of the
// "export default () => {}" function below (which runs individually
// for each client)
const api = axios.create({ baseURL: import.meta.env.VITE_API_BACK })

class Role {
  id: number
  name: string
  constructor (id: number, name: string) {
    this.id = id
    this.name = name
  }
}

export default boot(({ app, router }) => {
  // for use inside Vue files (Options API) through this.$axios and this.$api

  app.config.globalProperties.$axios = axios
  app.config.globalProperties.$store = useCounterStore()
  app.config.globalProperties.$url = import.meta.env.VITE_API_BACK
  // ^ ^ ^ this will allow you to use this.$axios (for Vue Options API form)
  //       so you won't necessarily have to import axios in each vue file

  app.config.globalProperties.$api = api

  app.config.globalProperties.$api.get('cogs').then((res) => {
    // app.config.globalProperties.$store.nombre = res.data[0].value
    // app.config.globalProperties.$store.direccion = res.data[1].value
    // app.config.globalProperties.$store.telefono = res.data[2].value
    // app.config.globalProperties.$store.email = res.data[3].value
    app.config.globalProperties.$store.cogs = res.data
  })
  const token = localStorage.getItem('tSib')
  if (token) {
    app.config.globalProperties.$api.defaults.headers.common.Authorization = `Bearer ${token}`
    app.config.globalProperties.$api.post('me').then((res) => {
      app.config.globalProperties.$store.user = res.data
      app.config.globalProperties.$store.isLogged = true
      app.config.globalProperties.$store.roles = res.data.roles.map((role: Role) => role.name)
    }).catch(() => {
      app.config.globalProperties.$store.user = {}
      app.config.globalProperties.$store.isLogged = false
      app.config.globalProperties.$store.roles = []
      router.push('/login')
    })
  }
  // ^ ^ ^ this will allow you to use this.$api (for Vue Options API form)
  //       so you can easily perform requests against your app's API
})

export { api }
