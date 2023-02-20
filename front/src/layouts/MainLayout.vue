<template>
  <q-layout view="lHh Lpr lFf">
    <q-header>
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="leftDrawerOpen = !leftDrawerOpen"
        />

        <q-toolbar-title>
          <q-img
            src="logo.png" width="50px"
          />
          {{ $store.nombre }}
        </q-toolbar-title>

        <div>
          <q-btn dense flat aria-label="Menu" no-caps icon-right="arrow_drop_down">
          <q-avatar>
            <q-img v-if="$store.user.avatar!=undefined"  :src="`${$url}../images/${$store.user.avatar}`" width="50px"/>
          </q-avatar>
            <q-menu>
              <q-list>
                <q-item clickable v-ripple>
                  <q-item-section avatar>
                    <q-icon name="account_circle" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>
                      {{ $store.user.name }}
                    </q-item-label>
                    <q-item-label caption>
                      {{ $store.user.email }}
                    </q-item-label>
                  </q-item-section>
                </q-item>
                <q-item clickable v-ripple v-if="$store.roles.includes('socio')" to="/datos">
                  <q-item-section avatar>
                    <q-icon name="switch_account" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>
                      Mis datos
                    </q-item-label>
                  </q-item-section>
                </q-item>
                <q-item clickable v-ripple @click="logout">
                  <q-item-section avatar>
                    <q-icon name="exit_to_app" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>
                      Cerrar Sesión
                    </q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
            </q-menu>
          </q-btn>
        </div>
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
    >
      <q-list>
        <q-item-label class="text-center text-bold" header>
          Usuario: {{$store.user.name}}
        </q-item-label>
        <EssentialLink
          v-for="link in essentialLinks"
          :key="link.title"
          v-bind="link"
        />
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
import { defineComponent } from 'vue'
import EssentialLink from 'components/EssentialLink.vue'

export default defineComponent({
  name: 'MainLayout',

  components: {
    EssentialLink
  },
  data () {
    return {
      essentialLinks: [
        {
          title: 'Principal',
          caption: 'Inicio',
          icon: 'o_home',
          link: '/',
          visible: 'true'
        },
        {
          title: 'Usuarios',
          caption: 'Usuarios',
          icon: 'o_supervisor_account',
          link: '/users',
          visible: 'administrador'
        },
        {
          title: 'Colegiados',
          caption: 'Colegiados',
          icon: 'o_supervisor_account',
          link: '/colegiados',
          visible: 'administrador'
        },
        {
          title: 'Mis datos',
          caption: 'Mis datos',
          icon: 'o_switch_account',
          link: '/datos',
          visible: 'socio'
        },
        {
          title: 'Mis Documentos',
          caption: 'Mis Documentos',
          icon: 'o_inventory_2',
          link: '/seguimientos',
          visible: 'socio'
        },
        {
          title: 'Certificados',
          caption: 'Certificados',
          icon: 'o_assignment',
          link: '/certificados',
          visible: 'socio'
        }
      ],
      leftDrawerOpen: false
    }
  },
  methods: {
    logout () {
      this.$q.dialog({
        title: 'Cerrar Sesión',
        message: '¿Está seguro que desea cerrar sesión?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        this.$q.loading.show()
        this.$api.post('logout').then(() => {
          localStorage.removeItem('tSib')
          this.$store.user = {}
          this.$store.isLogged = false
          this.$router.push('/login')
        }).finally(() => {
          this.$q.loading.hide()
        })
      })
    }
  }
})
</script>
