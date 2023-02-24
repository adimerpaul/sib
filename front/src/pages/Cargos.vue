<template>
    <q-page>
      <div class="row">
        <div class="col-12">
          <q-table :loading="loading" :rows-per-page-options="[0]" :rows="cargos" title="Cargos" flat bordered dense :search="filesSearch" :columns="fileColumns">
            <template v-slot:top-right>
              <q-btn color="primary" label="Crear Nuevo" no-caps icon="add_circle_outline" @click="addFile" dense />
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
          </q-table>
        </div>
      </div>

      <q-dialog v-model="dialogFile" persistent>
        <q-card>
          <q-card-section class="row items-center q-pb-none">
            <div class="text-h6">Crear Cargo</div>
            <q-space />
            <q-btn flat icon="close" v-close-popup />
          </q-card-section>
          <q-card-section class="q-pt-none">
            <q-form @submit="onSubmit">
              <q-input dense outlined v-model="cargo.cargo" label="Nombre" :rules="[val => val.length > 0 || 'El nombre es requerido']" />
              <q-input dense outlined v-model="cargo.salario" type="number" step="0.1" label="Salario" :rules="[val => val > 0 || 'El valor requerida']" />
              <q-input dense outlined v-model="cargo.turno"  label="Turno" :rules="[val => val.length > 0 || 'El valor requerida']" />
              <q-card-actions align="right">
                <q-btn :loading="loading" flat label="Cancelar" color="primary" v-close-popup />
                <q-btn :loading="loading" label="Guardar" color="primary" type="submit" />
              </q-card-actions>
            </q-form>
          </q-card-section>
        </q-card>
      </q-dialog>
      <q-dialog v-model="dialogExcel" >
        <q-card style="min-width: 350px">
          <q-card-section>
            <div class="text-h6">Listado de Asistencia</div>
            <input type="file" @change="getArch" >
          </q-card-section>

          <q-card-actions align="right" class="text-primary">
            <q-btn flat label="Cancel" v-close-popup />
            <q-btn flat label="Registrar"  />
          </q-card-actions>
        </q-card>
      </q-dialog>
    </q-page>
    </template>

<script>
import * as XLSX from 'xlsx/xlsx.mjs'

export default {
  name: 'CargosPage',
  data () {
    return {
      loading: false,
      cargos: [],
      cargo: {
      },
      fileColumns: [
        { name: 'opciones', label: 'Opciones', field: 'opciones', align: 'left', sortable: true, style: 'width: 100px' },
        { name: 'cargo', label: 'cargo', field: 'cargo', align: 'left', sortable: true },
        { name: 'salario', label: 'salario', field: 'salario', align: 'left', sortable: true },
        { name: 'turno', label: 'turno', field: 'turno', align: 'left', sortable: true }
      ],
      filesSearch: '',
      dialogFile: false,
      XLSX: require('xlsx')
    }
  },
  created () {
    this.getFiles()
  },
  methods: {
    getArch (evt) {
      const index = 0

      const files = evt.target.files[0] // FileList object
      const reader = new FileReader()
      reader.readAsBinaryString(files)
      reader.onload = function (e) {
        try {
          const data = e.target.result
          const workbook = XLSX.read(data, { type: 'binary' })
          const wsname = workbook.SheetNames[index] // Tome la primera hoja 0 primera
          const ws = XLSX.utils.sheet_to_json(workbook.Sheets[wsname]) // Generar contenido de tabla json
          this.calificacion = []
          ws.forEach((e, i) => {
            if (i >= 10 && e.__EMPTY !== undefined && e.__EMPTY.trim() !== '' && e.__EMPTY.trim() !== ' ' && e.__EMPTY != null) {
              const nombre = e.__EMPTY.replace('  ', ' ')
              const nota = e.__EMPTY_26
              this.calificacion.push(e)
              const indexFind = (this.materia.findIndex(e => e.nombreCompleto === nombre))
              if (indexFind >= 0) {
                this.materia[indexFind].promedio = nota
              }
            }
          })
        //      this.calificacion=ws
        // console.log(ws)
        } catch (e) {
          return false
        }
      }

      // Read in the image file as a data URL.
      // reader.readAsDataURL(files);

      // console.log(reader.ws)

      // var xl2json = XLSX.utils.sheet_to_json(files.Sheets[0])
      // xl2json.parseExcel(files[0]);
      // console.log(XLSX.utils.sheet_to_json(reader.Sheets[reader.SheetNames[0]]))
    },
    addFile () {
      this.dialogFile = true
      this.cargo = { }
    },
    onSubmit () {
      this.loading = true
      this.$api.post('charges', this.cargo)
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

    deleteFile (cargo) {
      this.$q.dialog({
        title: 'Eliminar',
        message: '¿Está seguro de eliminar?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        this.loading = true
        this.$api.delete(`charges/${cargo.id}`)
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
      this.$api.get('charges')
        .then(response => {
          this.cargos = response.data
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
