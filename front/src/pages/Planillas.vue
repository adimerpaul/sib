<template>
  <q-page>
  <div class="text-h5 flex flex-center">RRHH Y GENERACION DE PLANILLAS</div>
  <div class="row ">
      <div class="col-md-3 q-pa-xs" ><q-btn  class="full-width" color="teal"  label="Cargos" @click="verCargo"/></div>
      <div class="col-md-3 q-pa-xs" ><q-btn  class="full-width" color="accent"  label="Empleados" @click="verEmpleado"/></div>
      <div class="col-md-3 q-pa-xs" ><q-btn  class="full-width" color="info"  label="Asistencia" @click="dialogExcel=true"/></div>
      <div class="col-md-3 q-pa-xs" ><q-btn  class="full-width" color="green"  label="Generar Planilla" @click="cargarPlantilla"/></div>
  </div>
    <div class="row">
      <div class="col-12">
        <q-table :loading="loading" :rows-per-page-options="[0]" :rows="asistencias" title="Asistencias" flat bordered dense :search="asistenciaSearch" :columns="asistenciaCol">
          <template v-slot:top-right>
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
                  <div class="col-2"> <q-btn color="info" icon="manage_search" @click="getAsistencia"/>
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
    <q-dialog v-model="dialogExcel" >
      <q-card style="min-width: 350px">
        <q-card-section>
          <div class="text-h6">Listado de Asistencia</div>
          <input type="file" @change="getArch" >
        </q-card-section>
        <q-card-section>
          <q-table :rows="datasist" :columns="asistenciaColumns" row-key="name" />
        </q-card-section>
        <q-card-actions align="right" class="text-primary">
          <q-btn flat label="Cancel" v-close-popup />
          <q-btn flat label="Registrar"  @click="registrarAsistencia"/>
        </q-card-actions>
      </q-card>
    </q-dialog>
    <q-dialog v-model="dialogPlanilla" >
      <q-card style="min-width: 350px">
        <q-card-section>
          <div class="text-h6">Generar Planilla</div>
        </q-card-section>
        <q-card-section>
          <div class="row">
            <div class="col-4"><q-select dense  outlined v-model="mes" :options="meses" label="Mes" /></div>
            <div class="col-4"><q-input dense outlined v-model="anio" label="Anio" /></div>
            <div class="col-4"><q-input dense type='number' outlined v-model="dias" label="Dias Trabajo" /></div>
          </div>
        </q-card-section>
        <q-card-actions align="right" class="text-primary">
          <q-btn flat label="Cancel" v-close-popup />
          <q-btn flat label="Registrar"  @click="registrarPlanilla"/>
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
  </template>

<script>
import { date } from 'quasar'
import * as XLSX from 'xlsx/xlsx.mjs'
// import moment from 'moment'

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
      datasist: [],
      planillas: [],
      dias: 24,
      mes: {},
      meses: [
        { label: 'ENERO', valor: '01' },
        { label: 'FEBRERO', valor: '02' },
        { label: 'MARZO', valor: '03' },
        { label: 'ABRIL', valor: '04' },
        { label: 'MAYO', valor: '05' },
        { label: 'JUNIO', valor: '06' },
        { label: 'JULIO', valor: '07' },
        { label: 'AGOSTO', valor: '08' },
        { label: 'SEPTIEMBRE', valor: '09' },
        { label: 'OCTUBRE', valor: '10' },
        { label: 'NOVIEMBRE', valor: '11' },
        { label: 'DICIEMBRE', valor: '12' }
      ],
      // moment: require('moment'),
      ini: date.formatDate(new Date(), 'YYYY-MM-DD'),
      fin: date.formatDate(new Date(), 'YYYY-MM-DD'),
      fileColumns: [
        { name: 'opciones', label: 'Opciones', field: 'opciones', align: 'left', sortable: true, style: 'width: 100px' },
        { name: 'cargo', label: 'cargo', field: 'cargo', align: 'left', sortable: true },
        { name: 'salario', label: 'salario', field: 'salario', align: 'left', sortable: true },
        { name: 'turno', label: 'turno', field: 'turno', align: 'left', sortable: true }
      ],
      asistenciaColumns: [
        { name: 'ci', label: 'ci', field: 'ci', align: 'left', sortable: true },
        { name: 'Nombre', label: 'Nombre', field: 'Nombre', align: 'left', sortable: true },
        { name: 'Fecha', label: 'Fecha', field: 'Fecha', align: 'left', sortable: true },
        { name: 'Total', label: 'Total', field: 'Total', align: 'left', sortable: true }
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
      asistenciaCol: [
        { name: 'ci', label: 'ci', field: row => row.employee.ci, sortable: true },
        { name: 'Nombre', label: 'Nombre', field: row => row.employee.nombre + ' ' + row.employee.apellido, sortable: true },
        { name: 'Fecha', label: 'Fecha', field: 'fecha', sortable: true },
        { name: 'Horarios', label: 'Horarios', field: 'Horarios', sortable: true },
        { name: 'Total', label: 'Total', field: 'total', sortable: true }
      ],
      filesSearch: '',
      cargoSearch: '',
      empleadoSearch: '',
      asistenciaSearch: '',
      dialogFile: false,
      dialogCargo: false,
      dialogExcel: false,
      dialogEmpleado: false,
      dialogPlanilla: false
    }
  },
  created () {
    this.getFiles()
    this.getEmpleados()
  },
  methods: {
    registrarPlanilla () {
      this.$api.post('payrolls', { mes: this.mes, anio: this.anio, dias: this.dias }).then(response => {
        console.log(response.data)
        window.open(this.$url + 'generarPdf/' + this.mes.valor + '/' + this.anio, '_blank')
      })
    },
    cargarPlantilla () {
      this.anio = date.formatDate(new Date(), 'YYYY')
      this.meses.forEach(r => {
        if (r.valor === date.formatDate(new Date(), 'MM')) {
          this.mes = r
        }
      })
      this.dialogPlanilla = true
    },
    registrarAsistencia () {
      if (this.datasist.length === 0) {
        return false
      }
      this.loading = true
      this.$api.post('attendance', { datos: this.datasist })
        .then(response => {
          // console.log(response.data)
          this.dialogExcel = false
          this.datasist = []
          this.getAsistencia()
        })
        .catch(error => {
          console.log(error)
        }).finally(() => {
          this.loading = false
        })
    },
    getArch (evt) {
      const index = 0
      // const mc = this
      // eslint-disable-next-line @typescript-eslint/no-this-alias
      const mc = this
      this.datasist = []
      const files = evt.target.files[0] // FileList object
      const reader = new FileReader()
      reader.readAsBinaryString(files)
      reader.onload = function (e) {
        try {
          const data = e.target.result
          const workbook = XLSX.read(data, { type: 'binary' })
          const wsname = workbook.SheetNames[index] // Tome la primera hoja 0 primera
          const ws = XLSX.utils.sheet_to_json(workbook.Sheets[wsname]) // Generar contenido de tabla json
          ws.forEach(r => {
            r.ci = r['ID de Usuario']
            r.tarde = r['Llegada Tarde']
            // r.fecha = moment(date(r.Fecha)).format('YYYY-MM-DD')
            // thi s.datasist.push(r)
          })
          console.log(ws)
          mc.datasist = ws
          console.log(mc.datasist)
          // this.calificacion = []
          /* ws.forEach((e, i) => {
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
      */
        } catch (e) {
          this.datasist = []
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
    getAsistencia () {
      this.$api.post('asistencia', { ini: this.ini, fin: this.fin })
        .then(response => {
          console.log(response.data)
          this.asistencias = response.data
        })
    },
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
          console.log(response.data)
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
          // console.log(response)
          // this.dialogEmpleado = false
          this.getEmpleados()
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
