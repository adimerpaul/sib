<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated>
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
          Sociedad de Ingenieros de Bolivia
        </q-toolbar-title>

        <div>
          <q-btn flat dense round icon="account_circle" aria-label="Menu">
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
                <q-item clickable v-ripple>
                  <q-item-section avatar>
                    <q-icon name="settings" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>
                      Configuración
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
        <q-item-label
          header
        >
          Essential Links
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

const linksList = [
  {
    title: 'Docs',
    caption: 'quasar.dev',
    icon: 'school',
    link: 'https://quasar.dev'
  },
  {
    title: 'Github',
    caption: 'github.com/quasarframework',
    icon: 'code',
    link: 'https://github.com/quasarframework'
  },
  {
    title: 'Discord Chat Channel',
    caption: 'chat.quasar.dev',
    icon: 'chat',
    link: 'https://chat.quasar.dev'
  },
  {
    title: 'Forum',
    caption: 'forum.quasar.dev',
    icon: 'record_voice_over',
    link: 'https://forum.quasar.dev'
  },
  {
    title: 'Twitter',
    caption: '@quasarframework',
    icon: 'rss_feed',
    link: 'https://twitter.quasar.dev'
  },
  {
    title: 'Facebook',
    caption: '@QuasarFramework',
    icon: 'public',
    link: 'https://facebook.quasar.dev'
  },
  {
    title: 'Quasar Awesome',
    caption: 'Community Quasar projects',
    icon: 'favorite',
    link: 'https://awesome.quasar.dev'
  }
]

export default defineComponent({
  name: 'MainLayout',

  components: {
    EssentialLink
  },
  data () {
    return {
      essentialLinks: linksList,
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
