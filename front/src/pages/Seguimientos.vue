<template>
<q-page>
  <div class="row">
    <div class="col-12">
      <q-table :loading="loading" :rows-per-page-options="[0]" :rows="files" title="Mis documentos" flat bordered dense :search="filesSearch" :columns="fileColumns">
        <template v-slot:top-right>
          <q-btn color="primary" label="Crear Documento" no-caps icon="add_circle_outline" @click="addFile" dense />
          <q-btn flat round icon="refresh" @click="getFiles" dense />
          <q-input outlined dense v-model="filesSearch" label="Buscar" class="q-ml-md" clearable>
            <template v-slot:append>
              <q-icon name="search" />
            </template>
          </q-input>
        </template>
        <template v-slot:body-cell-opciones="props">
          <q-td :props="props">
            <q-btn flat round icon="o_delete" @click="deleteFile(props.row)" dense />
          </q-td>
        </template>
        <template v-slot:body-cell-path="props">
          <q-td :props="props">
            <q-btn type="a" flat round icon="attach_file" :href="`${$url}../files/${props.row.path}`" target="_blank" dense />
          </q-td>
        </template>
      </q-table>
    </div>
  </div>
  <q-dialog v-model="dialogFile" persistent>
    <q-card>
      <q-card-section class="row items-center q-pb-none">
        <div class="text-h6">Crear Documento</div>
        <q-space />
        <q-btn flat icon="close" v-close-popup />
      </q-card-section>
      <q-card-section class="q-pt-none">
        <q-form @submit="onSubmit">
          <q-input dense outlined v-model="file.name" label="Nombre" :rules="[val => val.length > 0 || 'El nombre es requerido']" />
          <q-uploader
            class="q-mb-md"
            multiple
            auto-upload
            label="Arrastra un archivo o haz click para seleccionar"
            @uploading="uploadingFn"
            ref="uploader"
            max-files="1"
            auto-expand
            :url="`${$url}upload/1/fileCreate`"
            stack-label="upload image"/>
          <q-input dense outlined v-model="file.date" type="date" label="Fecha" :rules="[val => val.length > 0 || 'La fecha es requerida']" />
          <q-card-actions align="right">
            <q-btn :loading="loading" flat label="Cancelar" color="primary" v-close-popup />
            <q-btn :loading="loading" label="Guardar" color="primary" type="submit" />
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
  name: 'SeguimientosPage',
  data () {
    return {
      loading: false,
      files: [],
      file: {
        name: '',
        date: date.formatDate(new Date(), 'YYYY-MM-DD'),
        user_id: this.$store.user.id
      },
      fileColumns: [
        { name: 'opciones', label: 'Opciones', field: 'opciones', align: 'left', sortable: true, style: 'width: 100px' },
        { name: 'name', label: 'Nombre', field: 'name', align: 'left', sortable: true },
        { name: 'date', label: 'Fecha', field: 'date', align: 'left', sortable: true },
        { name: 'path', label: 'Archivo', field: 'path', align: 'left', sortable: true }
      ],
      filesSearch: '',
      dialogFile: false
    }
  },
  created () {
    this.getFiles()
  },
  methods: {
    addFile () {
      this.dialogFile = true
      this.file = {
        name: '',
        date: date.formatDate(new Date(), 'YYYY-MM-DD'),
        user_id: this.$store.user.id
      }
    },
    onSubmit () {
      if (this.file.path === '' || this.file.path === undefined) {
        this.$q.notify({
          message: 'Debe seleccionar un archivo',
          color: 'negative',
          icon: 'warning',
          position: 'top'
        })
        return
      }
      this.loading = true
      this.$api.post('files', this.file)
        .then(response => {
          console.log(response)
          this.dialogFile = false
          this.getFiles()
        })
        .catch(error => {
          console.log(error)
        }).finally(() => {
          this.loading = false
        })
    },
    uploadingFn (e) {
      e.xhr.onload = () => {
        if (e.xhr.readyState === e.xhr.DONE) {
          this.file.path = e.xhr.response
        }
      }
    },
    deleteFile (file) {
      this.$q.dialog({
        title: 'Eliminar',
        message: '¿Está seguro de eliminar el documento?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        this.loading = true
        this.$api.delete(`files/${file.id}`)
          .then(response => {
            console.log(response)
            this.getFiles()
          })
          .catch(error => {
            console.log(error)
          })
      })
    },
    getFiles () {
      this.loading = true
      this.$api.get('files')
        .then(response => {
          this.files = response.data
        })
        .catch(error => {
          console.log(error)
        }).finally(() => {
          this.loading = false
        })
    }
  }
}
</script>

<style scoped>

</style>
