<template>
<q-page>
<div class="row">
  <div class="col-12">
    <q-table title="Correspondencia" :rows="mails" :columns="mailColumn" :rows-per-page-options="[0]" dense flat bordered :filter="mailSearch" :loading="loading" >
      <template v-slot:top-right>
        <q-btn dense icon="add_circle_outline" label="Agregar Entrada" color="green" no-caps @click="mailAdd('Entrada')"/>
        <q-btn dense icon="add_circle_outline" label="Agregar Salida" color="red" no-caps @click="mailAdd('Salida')"/>
        <q-input outlined v-model="mailSearch" label="Buscar" dense flat clearable>
          <template v-slot:append>
            <q-icon name="search" class="cursor-pointer">
              <q-tooltip>Buscar</q-tooltip>
            </q-icon>
          </template>
        </q-input>
      </template>
    </q-table>
  </div>
</div>
<q-dialog v-model="mailShow">
<q-card>
  <q-card-section class="row items-center">
    <div class="text-h6">{{ mailStatus === 'add' ? 'Agregar' : 'Editar' }} Correspondencia</div>
    <q-space/>
    <q-btn flat icon="close" @click="mailShow = false"/>
  </q-card-section>
  <q-card-section>
    <q-form @submit.prevent="mailSubmit">
      <q-input outlined dense v-model="mail.fecha" label="Fecha" type="date" :rules="[val => val && val.length > 0 || 'Campo requerido']"/>
      <q-input outlined dense v-model="mail.cite" label="Cite" :rules="[val => val && val.length > 0 || 'Campo requerido']"/>
      <q-input outlined dense v-model="mail.remite" label="Remite" :rules="[val => val && val.length > 0 || 'Campo requerido']"/>
      <q-input outlined dense v-model="mail.referencia" label="Referencia" :rules="[val => val && val.length > 0 || 'Campo requerido']"/>
      <q-input outlined dense v-model="mail.dirigido" label="Dirigido" :rules="[val => val && val.length > 0 || 'Campo requerido']"/>
      <q-input outlined dense v-model="mail.observaciones" label="Observaciones" :rules="[val => val && val.length > 0 || 'Campo requerido']"/>
      <q-uploader
        accept=".jpg, .png"
        multiple
        auto-upload
        label="Arrastra un archivo o haz click para seleccionar"
        @uploading="uploadingFn"
        @failed="errorFn"
        ref="uploader"
        max-files="1"
        auto-expand
        :url="`${$url}upload/1/'fileCreate'}`"
        stack-label="upload image"/>
      <q-input dense readonly v-model="mail.type" label="Tipo" :rules="[val => val && val.length > 0 || 'Campo requerido']"/>
      <q-card-actions align="right">
        <q-btn :loading="loading" flat label="Cancelar" color="primary" @click="mailShow = false"/>
        <q-btn :loading="loading" label="Guardar" color="primary" type="submit"/>
      </q-card-actions>
    </q-form>
  </q-card-section>
</q-card>
</q-dialog>
</q-page>
</template>

<script>
import { date } from 'quasar'

export default {
  name: 'CorrespondenciaPage',
  data () {
    return {
      mailShow: false,
      mailStatus: 'add',
      mailColumn: [
        { name: 'actions', label: 'Acciones', field: 'actions', align: 'left', sortable: true },
        { name: 'fecha', label: 'Fecha', field: 'fecha', align: 'left', sortable: true },
        { name: 'cite', label: 'Cite', field: 'cite', align: 'left', sortable: true },
        { name: 'remite', label: 'Remite', field: 'remite', align: 'left', sortable: true },
        { name: 'referencia', label: 'Referencia', field: 'referencia', align: 'left', sortable: true },
        { name: 'dirigido', label: 'Dirigido', field: 'dirigido', align: 'left', sortable: true },
        { name: 'observaciones', label: 'Observaciones', field: 'observaciones', align: 'left', sortable: true },
        { name: 'archivoDigital', label: 'Archivo Digital', field: 'archivoDigital', align: 'left', sortable: true },
        { name: 'type', label: 'Tipo', field: 'type', align: 'left', sortable: true }
      ],
      mails: [],
      loading: false,
      mailSearch: '',
      mail: {
        fecha: date.formatDate(new Date(), 'YYYY-MM-DD'),
        cite: '',
        remite: '',
        referencia: '',
        dirigido: '',
        observaciones: '',
        archivoDigital: '',
        type: ''
      }
    }
  },
  created () {
    this.mailGet()
  },
  methods: {
    mailGet () {
      this.loading = true
      this.$api.get('mails').then(res => {
        this.mails = res.data
      }).finally(() => {
        this.loading = false
      })
    },
    errorFn () {
      this.$q.notify({
        color: 'red-4',
        textColor: 'white',
        icon: 'cloud_done',
        position: 'top',
        message: 'Error al subir la imagen, intente nuevamente el nombre no debe contener espacios o ñ'
      })
    },
    uploadingFn (e) {
      e.xhr.onload = () => {
        if (e.xhr.readyState === e.xhr.DONE) {
          if (e.xhr.status === 200) {
            console.log(e.xhr.response)
            this.mail.archivoDigital = e.xhr.response
          }
        }
      }
    },
    mailSubmit () {
      console.log(this.mail)
      if (this.mailStatus === 'add') {
        if (this.mail.archivoDigital === '') {
          this.$q.notify({
            color: 'red-4',
            textColor: 'white',
            icon: 'cloud_done',
            position: 'top',
            message: 'Debe subir un archivo'
          })
          return
        }
        this.$api.post('mails', this.mail).then(res => {
          this.mails.push(res.data)
          this.mailShow = false
          this.mailGet()
        })
      } else {
        this.$api.put(`mails/${this.mail.id}`, this.mail).then(res => {
          this.mails[this.mails.findIndex(item => item.id === this.mail.id)] = res.data
          this.mailShow = false
          this.mailGet()
        })
      }
    },
    mailAdd (type) {
      this.mailShow = true
      this.mailStatus = 'add'
      this.mail = {
        fecha: date.formatDate(new Date(), 'YYYY-MM-DD'),
        cite: '',
        remite: '',
        referencia: '',
        dirigido: '',
        observaciones: '',
        archivoDigital: '',
        type: type
      }
    }
  }
}
</script>

<style scoped>

</style>
