<template>
<q-page>
  <div class="row">
    <div class="col-12">
      <q-table :loading="loading" title="Usuarios" dense bordered flat :filter="colegiadoSearch" :rows="users" :columns="userColumns" :rows-per-page-options="[0]">
        <template v-slot:top-right>
          <q-btn @click="userAdd" color="primary" icon="add_circle_outline" dense label="Agregar" no-caps />
          <q-btn @click="getUsers" icon="refresh" dense flat round />
          <q-input dense outlined v-model="colegiadoSearch" label="Buscar" clearable>
            <template v-slot:append>
              <q-icon name="search" />
            </template>
          </q-input>
        </template>
        <template v-slot:body-cell-options="props" >
          <q-td :props="props">
            <q-btn-dropdown color="primary" label="Opciones" no-caps dense>
              <q-list>
                <q-item clickable v-close-popup @click="userEdit(props.row)">
                  <q-item-section avatar>
                    <q-avatar icon="o_edit" />
                  </q-item-section>
                  <q-item-section>Editar</q-item-section>
                </q-item>
                <q-item clickable v-close-popup @click="userDelete(props.row)">
                  <q-item-section avatar>
                    <q-avatar icon="o_delete" />
                  </q-item-section>
                  <q-item-section>Eliminar</q-item-section>
                </q-item>
                <q-item clickable v-close-popup @click="userResetPassword(props.row)">
                  <q-item-section avatar>
                    <q-avatar icon="o_lock" />
                  </q-item-section>
                  <q-item-section>Resetear contraseña</q-item-section>
                </q-item>
              </q-list>
            </q-btn-dropdown>
          </q-td>
        </template>
        <template v-slot:body-cell-avatar="props" >
          <q-td :props="props" @click="showPhoto(`${$url}../images/${props.row.avatar}`)">
            <q-img :src="`${$url}../images/${props.row.avatar}`" style="width: 40px; height: 40px;" />
          </q-td>
        </template>
<!--        https://www.google.com/maps/dir/-17.9852434,-67.1328482/@-17.9834219,-67.1314857,17z-->
        <template v-slot:body-cell-location="props" >
          <q-td :props="props">
            <q-btn type="a" :href="`https://www.google.com/maps/dir/${props.row.lat},${props.row.lng}/@${props.row.lat},${props.row.lng},17z`" target="_blank" color="primary" icon="o_place" dense label="Ubicación" no-caps />
          </q-td>
        </template>
      </q-table>
    </div>
  </div>
  <q-dialog v-model="userDialog">
    <q-card>
      <q-card-section class="row items-center q-pb-none">
        <div class="text-subtitle2"> {{userCreate=='create' ? 'Agregar' : 'Editar'}} Usuario </div>
        <q-space />
        <q-btn icon="close" flat round dense v-close-popup />
      </q-card-section>
      <q-card-section class="q-pt-none">
        <q-form @submit="userSave">
        <div class="row">
        <div class="col-12">
          <div class="row">
            <div class="col-6">
              <q-input outlined dense v-model="user.name" label="Nombre" :rules="[val => val && Object.keys(val).length > 0 || 'Nombre requerido']" />
            </div>
            <div class="col-6">
              <q-input outlined dense v-model="user.email" label="Email" :rules="[val => val && Object.keys(val).length > 0 || 'Email requerido']" />
            </div>
            <div class="col-6" v-if="userCreate=='create'">
              <q-input :type="showPassword ? 'text' : 'password'" outlined dense v-model="user.password" label="Password" :rules="[val => val && Object.keys(val).length > 0 || 'Password requerido']" >
                <template v-slot:append>
                  <q-icon :name="showPassword ? 'visibility' : 'visibility_off'" class="cursor-pointer" @click="showPassword = !showPassword" />
                </template>
              </q-input>
            </div>
            <div class="col-6" v-if="userCreate=='create'">
              <q-input :type="showPassword ? 'text' : 'password'" outlined dense v-model="user.confirm_password" label="Confirmar Password" :rules="[val => val && Object.keys(val).length > 0 || 'Confirmar Password requerido', val => val === user.password || 'Las contraseñas no coinciden']">
                <template v-slot:append>
                  <q-icon :name="showPassword ? 'visibility' : 'visibility_off'" class="cursor-pointer" @click="showPassword = !showPassword" />
                </template>
              </q-input>
            </div>
            <div class="col-12 flex flex-center" v-if="userCreate=='create'">
              <q-uploader
                accept=".jpg, .png"
                multiple
                auto-upload
                label="Arrastra una imagen o haz click para seleccionar"
                @uploading="uploadingFn"
                @failed="errorFn"
                ref="uploader"
                max-files="1"
                auto-expand
                :url="`${$url}upload/${userCreate=='create' ? '1' : '1'}/${userCreate ? 'shopUser' : 'shop'}`"
                stack-label="upload image"/>
            </div>
            <div class="col-2">
              <div class="text-subtitle2 text-grey">Roles</div>
              <q-option-group
                dense
                v-model="user.roles"
                :options="roles"
                color="green"
                type="checkbox"
              />
            </div>
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
  </q-dialog>
</q-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import 'leaflet/dist/leaflet.css'
import { date } from 'quasar'
export default defineComponent({
  name: 'UsersPage',
  data () {
    return {
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
        { name: 'rolesLabel', label: 'Roles', field: 'rolesLabel', align: 'left', sortable: true },
        { name: 'avatar', label: 'Avatar', field: 'avatar', align: 'left', sortable: true }
      ]
    }
  },
  created () {
    this.getUsers()
    this.$api.get('roles').then((res) => {
      this.roles = []
      res.data.forEach((item) => {
        this.roles.push({ label: item.name, value: item.id })
      })
    })
  },
  methods: {
    errorFn () {
      // console.log(err)
      this.$q.notify({
        color: 'red-4',
        textColor: 'white',
        icon: 'cloud_done',
        position: 'top',
        message: 'Error al subir la imagen, intente nuevamente el nombre no debe contener espacios o ñ'
      })
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
        title: 'Confirmación',
        message: '¿Está seguro de resetear la contraseña?',
        cancel: true,
        prompt: {
          model: '',
          type: 'password',
          label: '',
          hint: 'Ingrese la nueva contraseña'
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
            message: 'Contraseña reseteada'
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
        title: 'Confirmación',
        message: '¿Está seguro de eliminar este usuario?',
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
          this.getUsers()
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
    getUsers () {
      this.loading = true
      this.$api.get('users/usuario').then((response) => {
        this.loading = false
        this.users = []
        response.data.forEach((item) => {
          const roles = item.roles.map((role) => role.id)
          const rolesLabel = item.roles.map((role) => role.name)
          item.roles = []
          roles.forEach((role) => {
            item.roles.push(role)
            item.rolesLabel = rolesLabel.join(', ')
          })
          this.users.push(item)
        })
      })
    },
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
      if (this.userCreate === 'create') {
        this.$api.post('users', this.user).then(() => {
          this.loading = false
          this.userDialog = false
          this.getUsers()
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
          this.getUsers()
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
