<template>
<q-page>
  <div class="row">
    <div class="col-12">
      <q-card>
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle2"> {{userCreate=='create' ? 'Agregar' : 'Editar'}} Colegiado </div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section class="q-pt-none">
          <q-form @submit="userSave">
            <div class="row">
              <div class="col-12 col-md-7">
                <div class="row">
                  <div class="col-6 col-md-3">
                    <q-input outlined readonly :loading="loading" dense v-model="user.paterno" label="Apellido Paterno" :rules="[val => val && Object.keys(val).length > 0 || 'Apellido Paterno requerido']" />
                  </div>
                  <div class="col-6 col-md-3">
                    <q-input outlined readonly :loading="loading" dense v-model="user.materno" label="Apellido Materno" />
                  </div>
                  <div class="col-6 col-md-3">
                    <q-input outlined readonly :loading="loading" dense v-model="user.rni" label="R.N.I" />
                  </div>
                  <div class="col-6 col-md-3">
                    <q-input outlined readonly :loading="loading" dense v-model="user.ci" label="C.I" />
                  </div>
                  <div class="col-6 col-md-3">
                    <q-input outlined readonly :loading="loading" dense v-model="user.nombres" label="Nombres" :rules="[val => val && Object.keys(val).length > 0 || 'Nombres requerido']" />
                  </div>
                  <div class="col-6 col-md-3">
                    <q-input outlined readonly :loading="loading" dense v-model="user.email" label="Email" :rules="[val => val && Object.keys(val).length > 0 || 'Email requerido']" />
                  </div>
                  <div class="col-6 col-md-3">
                    <q-input outlined readonly :loading="loading" dense type="date" v-model="user.fechaNac" label="Fecha Nacimiento" :rules="[val => val && Object.keys(val).length > 0 || 'Fecha Nacimiento requerido']" />
                  </div>
                  <div class="col-6 col-md-3">
                    <q-select outlined readonly :loading="loading" dense v-model="user.departamento" :options="departamentos" label="Departamento" />
                  </div>
                  <div class="col-6 col-md-3">
                    <q-input outlined :loading="loading" dense v-model="user.nacimiento" label="Lugar Nacimiento" />
                  </div>
                  <div class="col-6 col-md-3">
                    <q-select outlined :loading="loading" dense v-model="user.sexo" :options="['M','F']" label="Sexo" />
                  </div>
                  <div class="col-6 col-md-3">
                    <q-input outlined :loading="loading" dense v-model="user.zona" label="Zona" hint=""/>
                  </div>
                  <div class="col-6 col-md-6">
                    <q-input outlined :loading="loading" dense v-model="user.direccion" label="Direcci??n" hint=""/>
                  </div>
                  <div class="col-6 col-md-3">
                    <q-input outlined :loading="loading" dense v-model="user.telefono" label="Telefono" hint=""/>
                  </div>
                  <div class="col-6 col-md-3">
                    <q-input outlined :loading="loading" dense v-model="user.oficina" label="Oficina" hint=""/>
                  </div>
                  <div class="col-6 col-md-3">
                    <q-input outlined :loading="loading" dense v-model="user.celular" label="Celular" hint=""/>
                  </div>
                  <div class="col-6 col-md-3">
                    <q-input outlined :loading="loading" dense v-model="user.especialidad" label="Especialidad" hint=""/>
                  </div>
                  <div class="col-6 col-md-3">
                    <q-input outlined :loading="loading" dense v-model="user.fechaDiploma" label="Fecha Diploma" hint=""/>
                  </div>
                  <div class="col-6 col-md-6">
                    <q-input outlined :loading="loading" dense v-model="user.universidad" label="Universidad" hint=""/>
                  </div>
                  <div class="col-12">
                    <div class="col-12 flex flex-center" v-if="userCreate=='create'">
                      <q-uploader
                        accept=".jpg, .png"
                        multiple
                        auto-upload
                        label="Arrastra una imagen o haz click para seleccionar"
                        @uploading="uploadingFn"
                        ref="uploader"
                        max-files="1"
                        auto-expand
                        :url="`${$url}upload/${userCreate=='create' ? '1' : '1'}/${userCreate ? 'shopUser' : 'shop'}`"
                        stack-label="upload image"/>
                    </div>
                  </div>
                  <div class="col-6" v-if="userCreate=='create'">
                    <q-input :type="showPassword ? 'text' : 'password'" outlined :loading="loading" dense v-model="user.password" label="Password" :rules="[val => val && Object.keys(val).length > 0 || 'Password requerido']" >
                      <template v-slot:append>
                        <q-icon :name="showPassword ? 'visibility' : 'visibility_off'" class="cursor-pointer" @click="showPassword = !showPassword" />
                      </template>
                    </q-input>
                  </div>
                  <div class="col-6" v-if="userCreate=='create'">
                    <q-input :type="showPassword ? 'text' : 'password'" outlined :loading="loading" dense v-model="user.confirm_password" label="Confirmar Password" :rules="[val => val && Object.keys(val).length > 0 || 'Confirmar Password requerido', val => val === user.password || 'Las contrase??as no coinciden']">
                      <template v-slot:append>
                        <q-icon :name="showPassword ? 'visibility' : 'visibility_off'" class="cursor-pointer" @click="showPassword = !showPassword" />
                      </template>
                    </q-input>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-5">
                <div class="text-grey text-bold text-subtitle2"> Mover el marker para seleccionar la ubicaci??n </div>
                <div style="height:500px" class="items-center">
                  <l-map ref="map" @click="clickMaps" :zoom="13" :maxZoom="17" :center="[-17.970, -67.1111 ]" >
                    <l-tile-layer
                      :url="styleMap?`https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png`:`https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}`"
                      layer-type="base"
                      name="OpenStreetMap"
                    ></l-tile-layer>
                    <l-marker :lat-lng="markerLatLng" @moveend="ondragend" draggable  ></l-marker>
                    <l-control position="topright" >
                      <q-btn @click="styleMap=!styleMap" icon="map" class="bg-primary text-white" dense round></q-btn>
                    </l-control>
                  </l-map>
                </div>
              </div>
            </div>
            <q-card-actions align="right">
              <q-btn flat label="Cancelar" color="primary" @click="userDialog = false" :loading="loading" />
              <q-btn :color="userCreate=='create'?'green':'orange'" type="submit" :loading="loading"  :label="userCreate=='create'?'Crear':'Actualizar'" />
            </q-card-actions>
          </q-form>
        </q-card-section>
      </q-card>
    </div>
  </div>
</q-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import { LMap, LTileLayer, LMarker, LControl } from '@vue-leaflet/vue-leaflet'
import 'leaflet/dist/leaflet.css'
import { date } from 'quasar'
export default defineComponent({
  name: 'ColegiadosPage',
  components: {
    LMap,
    LTileLayer,
    LMarker,
    LControl
  },
  data () {
    return {
      departamentos: [
        'La Paz',
        'Cochabamba',
        'Santa Cruz',
        'Oruro',
        'Potos??',
        'Tarija',
        'Chuquisaca',
        'Pando',
        'Beni'
      ],
      styleMap: true,
      users: [],
      colegiadoSearch: '',
      showPassword: false,
      userDialog: false,
      user: {
        roles: [2],
        name: '',
        email: '',
        password: '',
        confirm_password: '',
        fechaNac: date.formatDate(new Date(), 'YYYY-MM-DD'),
        fechaInscripcion: date.formatDate(new Date(), 'YYYY-MM-DD'),
        fechaValido: date.formatDate(new Date(), 'YYYY-MM-DD')
      },
      roles: [],
      userCreate: 'create',
      loading: false,
      markerLatLng: [-17.970, -67.1111],
      userColumns: [
        { name: 'options', field: 'options', label: 'Opciones', align: 'center', sortable: false, style: 'width: 100px' },
        { name: 'name', label: 'Nombre', field: 'name', align: 'left', sortable: true },
        { name: 'email', label: 'Email', field: 'email', align: 'left', sortable: true },
        { name: 'location', label: 'Ubicaci??n', field: 'location', align: 'left', sortable: true },
        { name: 'avatar', label: 'Avatar', field: 'avatar', align: 'left', sortable: true }
      ]
    }
  },
  mounted () {
    // this.getUsers()
    this.loading = true
    setTimeout(() => {
      this.$api.get('users/' + this.$store.user.id).then((res) => {
        if (res.data.lat == null) {
          res.data.lat = -17.970
          res.data.lng = -67.1111
        }
        const roles = res.data.roles.map((role) => role.id)
        res.data.roles = []
        roles.forEach((role) => {
          res.data.roles.push(role)
        })
        this.userEdit(res.data)
      }).finally(() => {
        this.loading = false
      })
    }, 1500)
    this.userCreate = 'edit'

    // this.$api.get('roles').then((res) => {
    //   this.roles = []
    //   res.data.forEach((item) => {
    //     this.roles.push({ label: item.name, value: item.id })
    //   })
    // })
  },
  methods: {
    makeAlert () {
      alert('Hello')
    },
    showPhoto (photo) {
      this.$q.dialog({
        html: true,
        title: 'Foto',
        message: `<img src="${photo}" style="width:100%">`
      })
    },
    userResetPassword (user) {
      this.$q.dialog({
        title: 'Confirmaci??n',
        message: '??Est?? seguro de resetear la contrase??a?',
        cancel: true,
        prompt: {
          model: '',
          type: 'password',
          label: '',
          hint: 'Ingrese la nueva contrase??a'
        },
        persistent: true
      }).onOk((data) => {
        this.loading = true
        this.$api.put(`userResetPassword/${user.id}`, {
          password: data
        }).then(() => {
          this.loading = false
          this.$q.notify({
            color: 'green-4',
            textColor: 'white',
            icon: 'cloud_done',
            position: 'top',
            message: 'Contrase??a reseteada'
          })
        }).catch((err) => {
          this.loading = false
          this.$q.notify({
            color: 'red-4',
            textColor: 'white',
            icon: 'cloud_done',
            position: 'top',
            message: err.response.data.message
          })
        })
      })
    },
    userDelete (user) {
      this.$q.dialog({
        title: 'Confirmaci??n',
        message: '??Est?? seguro de eliminar este usuario?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        this.loading = true
        this.$api.delete(`users/${user.id}`).then(() => {
          this.$q.notify({
            color: 'green-4',
            textColor: 'white',
            icon: 'cloud_done',
            message: 'Usuario eliminado'
          })
          // this.getUsers()
          this.loading = false
        }).catch(() => {
          this.$q.notify({
            color: 'red-4',
            textColor: 'white',
            icon: 'cloud_done',
            message: 'Error al eliminar usuario'
          })
          this.loading = false
        })
      })
    },
    userEdit (user) {
      console.log(user)
      this.userCreate = 'edit'
      this.userDialog = true
      this.user = user
      this.markerLatLng = [user.lat, user.lng]
    },
    ondragend (e) {
      this.markerLatLng = [e.target._latlng.lat, e.target._latlng.lng]
    },
    clickMaps (e) {
      if (e.latlng) {
        this.markerLatLng = e.latlng
      }
    },
    // getUsers () {
    //   this.loading = true
    //   this.$api.get('users/colegiado').then((response) => {
    //     this.loading = false
    //     this.users = []
    //     response.data.forEach((item) => {
    //       const roles = item.roles.map((role) => role.id)
    //       item.roles = []
    //       roles.forEach((role) => {
    //         item.roles.push(role)
    //       })
    //       this.users.push(item)
    //     })
    //   })
    // },
    userAdd () {
      this.userCreate = 'create'
      this.userDialog = true
      this.markerLatLng = [-17.970, -67.1111]
      this.user = {
        roles: [2],
        name: '',
        email: '',
        password: '',
        confirm_password: '',
        fechaNac: date.formatDate(new Date(), 'YYYY-MM-DD'),
        fechaInscripcion: date.formatDate(new Date(), 'YYYY-MM-DD'),
        fechaValido: date.formatDate(new Date(), 'YYYY-MM-DD')
      }
    },
    userSave () {
      this.loading = true
      this.user.lat = this.markerLatLng[0] === undefined ? this.markerLatLng.lat : this.markerLatLng[0]
      this.user.lng = this.markerLatLng[1] === undefined ? this.markerLatLng.lng : this.markerLatLng[1]
      this.user.type = 'colegiado'
      if (this.userCreate === 'create') {
        this.$api.post('users', this.user).then(() => {
          this.loading = false
          this.userDialog = false
          // this.getUsers()
        }).catch((error) => {
          this.$q.notify({
            message: error.response.data.message,
            color: 'negative',
            position: 'top',
            timeout: 2500
          })
          this.loading = false
        })
      } else {
        this.$api.put('users/' + this.user.id, this.user).then((response) => {
          console.log(response.data)
          this.loading = false
          this.userDialog = false
          // this.getUsers()
        }).catch((error) => {
          this.$q.notify({
            message: error.response.data.message,
            color: 'negative',
            position: 'top',
            timeout: 2500
          })
          this.loading = false
        })
      }
    },
    uploadingFn (e) {
      e.xhr.onload = () => {
        if (e.xhr.readyState === e.xhr.DONE) {
          if (e.xhr.status === 200) {
            this.user.avatar = e.xhr.response
          }
        }
      }
    }
  }
})
</script>

<style scoped>

</style>
