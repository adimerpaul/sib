<template>
    <q-page>
    <div class="text-h5 flex flex-center">RRHH Y GENERACION DE PLANILLAS</div>
    <div class="row ">
        <div class="col-md-3 q-pa-xs" ><q-btn  class="full-width" color="teal"  label="Cargos" @click="verCargo"/></div>
        <div class="col-md-3 q-pa-xs" ><q-btn  class="full-width" color="accent"  label="Empleados" @click="verEmpleado"/></div>
        <div class="col-md-3 q-pa-xs" ><q-btn  class="full-width" color="info"  label="Asistencia" /></div>
        <div class="col-md-3 q-pa-xs" ><q-btn  class="full-width" color="green"  label="Generar Planilla" /></div>
    </div>
      <div class="row">
        <div class="col-12">
          <q-table :loading="loading" :rows-per-page-options="[0]" :rows="cargos" title="Cargos" flat bordered dense :search="asistenciaSearch" :columns="asistenciaColumns">
            <template v-slot:top-right>
              <q-btn flat round icon="refresh" @click="getAsistencia" dense />
              <q-input outlined dense v-model="asistenciaSearch" label="Buscar" class="q-ml-md" clearable >
                <template v-slot:append>
                  <q-icon name="search" />
                </template>
              </q-input>
            </template>
            <template v-slot:top-left>
                <div class="row">
                    <div class="col-5"><q-input dense outlined v-model="ini" type="date" label="Fecha Ini" /></div>
                    <div class="col-5"><q-input dense outlined v-model="fin" type="date" label="Fecha Fin" /></div>
                    <div class="col-2"> <q-btn color="info" icon="manage_search" />
                    </div>
                </div>
              </template>
          </q-table>
        </div>
      </div>

      <q-dialog v-model="dialogCargo" class="full-width">
        <q-card>
          <q-card-section class="row items-center q-pb-none">
            <div class="text-h6">Crear Cargo</div>
            <q-space />
            <q-btn flat icon="close" v-close-popup />
          </q-card-section>
          <q-card-section class="q-pt-none">
            <q-form @submit="onSubmit">
                <div class="row">
                    <div class="col-3"><q-input dense outlined v-model="cargo.cargo" label="Nombre" :rules="[val => val.length > 0 || 'El nombre es requerido']" /></div>
                    <div class="col-3"><q-input dense outlined v-model="cargo.salario" type="number" step="0.1" label="Salario" :rules="[val => val > 0 || 'El valor requerida']" /></div>
                    <div class="col-3"><q-input dense outlined v-model="cargo.turno"  label="Turno" :rules="[val => val.length > 0 || 'El valor requerida']" /></div>
                    <div class="col-3"><q-btn :loading="loading" label="Guardar" color="primary" type="submit" /></div>
                </div>
            </q-form>
          </q-card-section>
          <q-card-section>
            <div class="col-12">
                <q-table :loading="loading" :rows-per-page-options="[0]" :rows="cargos" title="Cargos" flat bordered dense :search="cargoSearch" :columns="fileColumns">
                  <template v-slot:top-right>
                    <q-btn flat round icon="refresh" @click="getFiles" dense />
                    <q-input outlined dense v-model="cargoSearch" label="Buscar" class="q-ml-md" clearable>
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
          </q-card-section>
        </q-card>
      </q-dialog>

      <q-dialog v-model="dialogEmpleado" full-width>
        <q-card>
          <q-card-section class="row items-center q-pb-none">
            <div class="text-h6">Crear Empleado</div>
            <q-space />
            <q-btn flat icon="close" v-close-popup />
          </q-card-section>
          <q-card-section class="q-pt-none">
            <q-form @submit="registro">
                <div class="row">
                    <div class="col-3"><q-input dense outlined v-model="empleado.ci" label="CI" :rules="[val => val.length > 0 || 'El cedula es requerido']" /></div>
                    <div class="col-3"><q-select dense outlined v-model="empleado.expedido" label="Expedido" :options="['CH','LP','CB','OR','PT','TJ','SC','BE','PD']"/></div>
                    <div class="col-3"><q-input dense outlined v-model="empleado.nombre" label="Nombres" :rules="[val => val.length > 0 || 'El nombre es requerido']" /></div>
                    <div class="col-3"><q-input dense outlined v-model="empleado.apellido" label="Apellidos" :rules="[val => val.length > 0 || 'El apellido es requerido']" /></div>
                    <div class="col-3"> <q-radio dense v-model="empleado.sexo" val="Masculino" label="Masculino" />
                    <q-radio dense v-model="empleado.sexo" val="Femenino" label="Femenino" /></div>
                    <div class="col-3"><q-input dense outlined v-model="empleado.fechanac" type="date" label="Fecha Nac" :rules="[val => val.length > 0 || 'El valor requerida']" /></div>
                    <div class="col-3"><q-input dense outlined v-model="empleado.fechaing" type="date" label="Fecha Ing" :rules="[val => val.length > 0 || 'El valor requerida']" /></div>
                    <div class="col-3"><q-select dense outlined v-model="cargo"  label="Cargo" :options="cargos" /></div>
                    <div class="col-3"><q-btn :loading="loading" label="Guardar" color="primary" type="submit" /></div>
                </div>
            </q-form>
          </q-card-section>
          <q-card-section>
            <div class="col-12">
                <q-table :loading="loading" :rows-per-page-options="[0]" :rows="empleados" title="Empleados" flat bordered dense :search="empleadoSearch" :columns="empleadoColumns">
                  <template v-slot:top-right>
                    <q-btn flat round icon="refresh" @click="getFiles" dense />
                    <q-input outlined dense v-model="empleadoSearch" label="Buscar" class="q-ml-md" clearable>
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
          </q-card-section>
        </q-card>
      </q-dialog>
    </q-page>
    </template>

<script>
import { date } from 'quasar'

export default {
  name: 'CargosPage',
  data () {
    return {
      loading: false,
      cargos: [],
      cargo: {},
      empleado: {},
      empleados: [],
      asistencias: [],
      planillas: [],
      ini: date.formatDate(new Date(), 'YYYY-MM-DD'),
      fin: date.formatDate(new Date(), 'YYYY-MM-DD'),
      fileColumns: [
        { name: 'opciones', label: 'Opciones', field: 'opciones', align: 'left', sortable: true, style: 'width: 100px' },
        { name: 'cargo', label: 'cargo', field: 'cargo', align: 'left', sortable: true },
        { name: 'salario', label: 'salario', field: 'salario', align: 'left', sortable: true },
        { name: 'turno', label: 'turno', field: 'turno', align: 'left', sortable: true }
      ],
      asistenciaColumns: [
        { name: 'empleado', label: 'empleado', field: 'empleado', align: 'left', sortable: true },
        { name: 'fecha', label: 'fecha', field: 'fecha', align: 'left', sortable: true },
        { name: 'ingreso', label: 'ingreso', field: 'ingreso', align: 'left', sortable: true },
        { name: 'salida', label: 'salida', field: 'salida', align: 'left', sortable: true },
        { name: 'horas', label: 'horas', field: 'horas', align: 'left', sortable: true }
      ],
      empleadoColumns: [
        { name: 'opciones', label: 'Opciones', field: 'opciones', align: 'left', sortable: true, style: 'width: 100px' },
        { name: 'ci', label: 'ci', field: 'ci', align: 'left', sortable: true },
        { name: 'expedido', label: 'expedido', field: 'expedido', align: 'left', sortable: true },
        { name: 'nombre', label: 'nombre', field: 'nombre', align: 'left', sortable: true },
        { name: 'apellido', label: 'apellido', field: 'apellido', align: 'left', sortable: true },
        { name: 'fechanac', label: 'fechanac', field: 'fechanac', align: 'left', sortable: true },
        { name: 'sexo', label: 'sexo', field: 'sexo', align: 'left', sortable: true },
        { name: 'fechaing', label: 'fechaing', field: 'fechaing', align: 'left', sortable: true },
        { name: 'cargo', label: 'cargo', field: row => row.charge.cargo, align: 'left', sortable: true }
      ],
      filesSearch: '',
      cargoSearch: '',
      empleadoSearch: '',
      asistenciaSearch: '',
      dialogFile: false,
      dialogCargo: false,
      dialogEmpleado: false
    }
  },
  created () {
    this.getFiles()
    this.getEmpleados()
  },
  methods: {
    verCargo () {
      this.dialogCargo = true
      this.cargo = { }
    },
    verEmpleado () {
      this.dialogEmpleado = true
      this.empleado = { expedido: 'OR', fechanac: date.formatDate(new Date(), 'YYYY-MM-DD'), fechaing: date.formatDate(new Date(), 'YYYY-MM-DD'), sexo: 'Masculino' }
      this.cargo = this.cargos[0]
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

    registro () {
      this.loading = true
      this.empleado.charge_id = this.cargo.id
      this.$api.post('employees', this.empleado)
        .then(response => {
          console.log(response)
          this.dialogEmpleado = false
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
          response.data.forEach(r => { r.label = r.cargo })
          this.cargos = response.data
        })
        .catch(error => {
          console.log(error)
        }).finally(() => {
          this.loading = false
        })
    },
    getEmpleados () {
      this.loading = true
      this.$api.get('employees')
        .then(response => {
          console.log(response.data)
          this.empleados = response.data
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
