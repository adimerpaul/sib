<template>
    <q-page>
    <div class="text-h5 flex flex-center">REPORTES</div>
      <div class="row">
        <div class="col-md-3 col-xs-6"><q-input outlined type="date" dense v-model="ini" label="Fecha Inicial" /></div>
        <div class="col-md-3 col-xs-6"><q-input outlined type="date" dense v-model="fin" label="Fecha Final" /></div>
        <div class="col-md-3 col-xs-6"> <q-btn color="green"  label="Reporte Ingresos" @click="reporteIngreso"/></div>
        <div class="col-md-3 col-xs-6"> <q-btn color="yellow"  label="Reporte Egresos" @click="reporteEgreso"/></div>
      </div>
      <br>
      <div class="row q-pa-xs">

        <div class="col-md-3 col-xs-6"> <q-btn color="info"  label="Reporte Planillas " /></div>
        <div class="col-md-3 col-xs-6"> <q-btn color="accent"  label="Reporte Inventarios" /></div>
        <div class="col-md-3 col-xs-6"> <q-btn color="teal"  label="Reporte Activos Fijos" /></div>
      </div>
      <template v-if="tipo=='INGRESOS'">
        <div class="q-pa-xs">
            <q-table :title="`REPORTE DE ${tipo}`" :rows="datos" :columns="colIngreso" row-key="name" :search="ingSearch">
                <template v-slot:top-right>
                     <q-btn color="green" label="Excel" @click="exportSales"/>
                     <q-btn color="orange" label="Pdf" @click="generarPdf" />
                    <q-input outlined dense v-model="ingSearch" label="Buscar" class="q-ml-md" clearable>
                      <template v-slot:append>
                        <q-icon name="search" />
                      </template>
                    </q-input>
                  </template>
            </q-table>
        </div>
      </template>
      <template v-if="tipo=='EGRESOS'">
        <div class="q-pa-xs">
            <q-table :title="`REPORTE DE ${tipo}`" :rows="datos" :columns="colIngreso" row-key="name" :search="egrSearch">
                <template v-slot:top-right>
                     <q-btn color="green" label="Excel" @click="exportSales"/>
                     <q-btn color="orange" label="Pdf" @click="generarPdf" />
                     <q-input outlined dense v-model="egrSearch" label="Buscar" class="q-ml-md" clearable>
                      <template v-slot:append>
                        <q-icon name="search" />
                      </template>
                    </q-input>
                  </template>
            </q-table>
        </div>
      </template>
      <div id="myelement" class="hidden"></div>

    </q-page>
    </template>

<script>
import { date } from 'quasar'
import xlsx from 'json-as-xlsx'
import { Printd } from 'printd'

export default {
  name: 'ReportePage',
  data () {
    return {
      loading: false,
      cargos: [],
      tipo: 'INGRESOS',
      datos: [],
      ingSearch: '',
      egrSearch: '',
      informacion: [],
      cargo: {
      },
      fileColumns: [
        { name: 'opciones', label: 'Opciones', field: 'opciones', align: 'left', sortable: true, style: 'width: 100px' },
        { name: 'cargo', label: 'cargo', field: 'cargo', align: 'left', sortable: true },
        { name: 'salario', label: 'salario', field: 'salario', align: 'left', sortable: true },
        { name: 'turno', label: 'turno', field: 'turno', align: 'left', sortable: true }
      ],
      colIngreso: [
        { name: 'name', label: 'name', field: 'name', sortable: true },
        { name: 'amount', label: 'amount', field: 'amount', sortable: true },
        { name: 'date', label: 'date', field: 'date', sortable: true },
        { name: 'description', label: 'description', field: 'description', sortable: true },
        { name: 'user', label: 'user', field: row => row.user.name, sortable: true }
      ],
      filesSearch: '',
      dialogFile: false,
      ini: date.formatDate(new Date(), 'YYYY-MM-DD'),
      fin: date.formatDate(new Date(), 'YYYY-MM-DD')
    }
  },
  // created () {},
  methods: {
    generarPdf () {
      let tabla = ''
      let total = 0
      this.datos.forEach(r => {
        total += parseFloat(r.amount)
        tabla = '<tr><td>' + r.date + '</td><td>' + r.name + '</td><td>' + r.amount + '</td><td>' + r.description + '</td><td>' + r.user.name + '</td></tr>'
      })
      let cadena = ''
      // eslint-disable-next-line no-multi-str
      cadena = ' <style>\
      .cabeza{ \
        color: green; font-size:10px; margin-left:50px; font-weight: bold;\
      }\
      .titulo{font-weight: bold; text-align:center;}\
      table, th, td {  border: 1px solid;}\
      table {  width: 100%;  border-collapse: collapse; }\
      cuerpo{padding: 50px; margin-left: 50px;}\
      </style><div class="cuerpo">\
      <div class="cabeza"> SOCIEDAD DE INGENIEROS DE BOLIVIA REGIONAL ORURO <br> C. Belzu No. 650 Entre Vázquez y La Paz <br> Casilla Postal: 572 <br>ORURO BOLIVIA</div><hr>\
      <div class="titulo">REPORTE DE ' + this.tipo + ' </div>'
      // eslint-disable-next-line no-multi-str
      cadena += '<table>\
        <thead><tr><th>FECHA</th><th>NOMBRE</th><th>MONTO</th><th>DESCRIPCION</th><th>USUARIO</th></tr></thead>\
        <tbody>' + tabla + '</tbody></table><div><b>TOTAL: </b>' + total.toFixed(2) + '</div></div>'
      document.getElementById('myelement').innerHTML = cadena
      const d = new Printd()
      d.print(document.getElementById('myelement'))
    },
    reporteIngreso () {
      this.tipo = 'INGRESOS'
      this.$api.post('reporteIG', { ini: this.ini, fin: this.fin, tipo: 'Ingreso' }).then(res => {
        this.datos = res.data
      })
    },
    reporteEgreso () {
      this.tipo = 'EGRESOS'
      this.$api.post('reporteIG', { ini: this.ini, fin: this.fin, tipo: 'Egreso' }).then(res => {
        this.datos = res.data
      })
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
    exportSales () {
      const data = [
        {
          sheet: this.tipo,
          columns: [
            { label: 'Fecha', value: 'date' },
            { label: 'Nombre', value: 'name' },
            { label: 'Monto', value: 'amount' },
            { label: 'Descripcion', value: 'description' },
            { label: 'Usuario', value: (row) => row.user.name }
          ],
          content: this.datos
        }
      ]

      const settings = {
        fileName: 'Reporte-' + this.tipo, // Name of the resulting spreadsheet
        extraLength: 3, // A bigger number means that columns will be wider
        writeMode: 'writeFile', // The available parameters are 'WriteFile' and 'write'. This setting is optional. Useful in such cases https://docs.sheetjs.com/docs/solutions/output#example-remote-file
        writeOptions: {} // Style options from https://docs.sheetjs.com/docs/api/write-options
        // RTL: true // Display the columns from right-to-left (the default value is false)
      }

      xlsx(data, settings) // Will download the excel file
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
