<template>
  <q-layout>
    <q-page-container>
      <q-page>
        <div class="row">
          <div class="col-12 col-md-3"></div>
          <div class="col-12 col-md-6 q-pa-md">
            <div class="text-center text-subtitle2 text-grey q-pa-xs">CUENTA</div>
            <div class="text-center text-h2 text-bold q-pa-md">Tu Cuenta</div>
            <q-card>
              <q-card-section>
                <q-form @submit="login">
                  <div class="row">
                    <div class="col-12">
                      <q-input outlined v-model="user.email" label="Email" type="email" :rules="[val => val && val.length > 0 || 'Email requerido']" />
                    </div>
                    <div class="col-12">
                      <q-input outlined v-model="user.password" label="Contraseña" :type="showPassword ? 'text' : 'password'" :rules="[val => val && val.length > 0 || 'Contraseña requerida']">
                        <template v-slot:append>
                          <q-icon :name="showPassword ? 'visibility' : 'visibility_off'" class="cursor-pointer" @click="showPassword = !showPassword" />
                        </template>
                      </q-input>
                    </div>
                    <div class="col-12">
                      <q-checkbox v-model="remember" label="Recordar" class="text-grey" />
                    </div>
                    <div class="col-12">
                      <q-btn :loading="loading" size="18px" no-caps label="Iniciar Sesión" color="primary" class="q-mt-md full-width" type="submit" />
                    </div>
                    <div class="col-12">
                      <div class="text-center text-subtitle2 text-grey q-pa-xs">
                        <a class="text-primary" href="">¿Olvidaste tu contraseña?</a>
                      </div>
                    </div>
                  </div>
                </q-form>
              </q-card-section>
            </q-card>
          </div>
          <div class="col-12 col-md-3"></div>
        </div>
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script>
import { defineComponent } from 'vue'

export default defineComponent({
  name: 'LoginPage',
  data () {
    return {
      user: {},
      remember: false,
      showPassword: false,
      loading: false
    }
  },
  methods: {
    login () {
      this.loading = true
      this.$api.post('login', this.user).then(res => {
        this.$store.user = res.data.user
        const roles = []
        res.data.user.roles.forEach(role => {
          roles.push(role.name)
        })
        this.$store.roles = roles
        this.$api.defaults.headers.common.Authorization = 'Bearer ' + res.data.token
        localStorage.setItem('tSib', res.data.token)
        this.$store.isLogged = true
        this.loading = false
        this.$q.notify({
          message: 'Bienvenido',
          color: 'positive',
          icon: 'check_circle',
          position: 'top'
        })
        this.$router.push('/')
      }).catch(err => {
        this.loading = false
        this.$q.notify({
          message: err.response.data.message,
          color: 'negative',
          position: 'top',
          timeout: 2500
        })
      })
    }
  }
})
</script>

<style scoped>

</style>
