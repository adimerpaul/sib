<template>
<q-page>
  <div class="row q-pt-xs">
    <div class="col-12">
      <div class="text-center text-h4 text-bold">
        Certificados
      </div>
    </div>
    <!--
    <div class="col-6 flex flex-center">
      <q-btn
        type="a"
        color="primary"
        label="Certificado de afiliación"
        icon="mdi-file-document"
        :href="`${$url}certificado/${$store.user.id}}`"
        target="_blank"
        no-caps
      />
    </div>
    <div class="col-6 flex flex-center">
      <q-btn
        type="a"
        color="primary"
        label="Certificado de pago"
        icon="mdi-file-document"
        :href="`${$url}certificado/${$store.user.id}}`"
        target="_blank"
        no-caps
      />
    </div>-->
    <div class="col-12">
      <q-table title="Treats" :rows="letters" :columns="colLetter" row-key="name" :search="letterSearch">
        <template v-slot:top-right>
          <q-btn color="green" label="Crear Solicitud" @click="verLetter"/>
          <q-input outlined dense v-model="letterSearch" label="Buscar" class="q-ml-md" clearable>
           <template v-slot:append>
             <q-icon name="search" />
           </template>
         </q-input>
       </template>
       <template v-slot:body-cell-opciones="props">
        <q-td :props="props">
           <q-btn color="green" icon="check" dense v-if="props.row.status==='pendiente' && store.roles.includes( 'administrador' )" @click="letter2=props.row; dialogVerificar=true; "/>
           <q-btn color="info" icon="picture_as_pdf" v-if="props.row.status==='APROBADO'"  @click=" kardex (props.row)"/>
      </q-td>
     </template>
       <template v-slot:body-cell-status="props">
          <q-td :props="props">
          <q-badge :color="props.row.status==='pendiente' || props.row.status==='RECHAZADO'?'red':'green'"  :label="props.row.status" />
        </q-td>
       </template>
    </q-table>
    </div>

    <q-dialog v-model="dialogLetter">
    <q-card>
    <q-card-section>
    <div class="text-h6">REGISTRAR SOLICITUD</div>
    </q-card-section>
    <q-card-section>
      <q-form @submit="onsubmit" class="q-gutter-md" >
        <div class="row">

          <div class="col-6"><q-select outlined v-model="letter.name" label="Titulo" :options="['ESTUDIO','TRABAJO','CURSOS','OTROS']" :rules="[val => val.length > 0 || 'Password requerido']" /></div>
          <div class="col-6"><q-input outlined v-model="letter.description" label="Descripcion" :rules="[val => val.length > 0 || 'Password requerido']"/></div>
        </div>
          <q-card-actions align="right">
            <q-btn  label="Cancelar" color="red" v-close-popup />
            <q-btn  label="Registrar" color="green" type="submit" :loading="loading"/>
          </q-card-actions>
       </q-form>
    </q-card-section>
    </q-card>
    </q-dialog>
  <q-dialog v-model="dialogVerificar">
    <q-card>
    <q-card-section>
    <div class="text-h6">APROBOBAR SOLICITUD</div>
    </q-card-section>
    <q-card-section>
        <div class="row">
          <div class="col-4"> <q-btn color="green"  label="APROBAR" @click="validar('APROBADO')"/></div>
          <div class="col-4"> <q-btn color="red"  label="RECHAZAR" @click="validar('RECHAZADO')"/></div>
          <div class="col-4"> <q-btn color="teal"  label="CANCELAR" v-close-popup /></div>
        </div>
    </q-card-section>
    </q-card>
  </q-dialog>
  <div id="myelement" class="hidden"></div>

  </div>
</q-page>
</template>

<script lang="ts">
import { Printd } from 'printd'

import { defineComponent } from 'vue'
import { useCounterStore } from 'stores/example-store'

export default defineComponent({
  name: 'CertificadosPage',
  data () {
    return {
      loading: false,
      dialogVerificar: false,
      letters: [],
      letter: {},
      letter2: {},
      store: useCounterStore(),
      colLetter: [
        { name: 'opciones', label: 'Opciones', field: 'opciones', align: 'left', sortable: true, style: 'width: 100px' },
        { name: 'date', label: 'Fecha', field: 'date', align: 'left', sortable: true },
        { name: 'name', label: 'Nombre', field: 'name', align: 'left', sortable: true },
        { name: 'status', label: 'Estado', field: 'status', align: 'left', sortable: true },
        { name: 'description', label: 'Descripcion', field: 'description', align: 'left', sortable: true },
        { name: 'ci', label: 'ci', field: row => row.user.ci, align: 'left', sortable: true },
        { name: 'nombre', label: 'nombre', field: row => row.user.nombres, align: 'left', sortable: true },
        { name: 'paterno', label: 'paterno', field: row => row.user.paterno, align: 'left', sortable: true },
        { name: 'materno', label: 'materno', field: row => row.user.materno, align: 'left', sortable: true }
      ],
      letterSearch: '',
      dialogLetter: false
    }
  },
  created () {
    this.getCerti()
    console.log(this.store.roles)
  },
  methods: {
    kardex (dato) {
      const contenido = 'Al ingenierio(a) ' + dato.user.nombres + ' ' + dato.user.paterno + ' ' + dato.user.materno + ' con numero de cedula ' + dato.user.ci +
       ' con numero de registro  RNI: ' + dato.user.rni + 'se acepta su solicitud por los motivos de ' + dato.description + ' en fecha de la solicitud ' + dato.date
      let cadena = ''
      cadena = `
      <html>
    <head>
      <style>
    .bold{font-weight: bold;}
    .textPrint-h1{font-size: 20px;}
    .textPrint-h5{font-size: 8px;}
    .textPrint-h6{font-size: 7px;}
    .p2{padding: 2px}
    .underline{text-decoration: underline;}
    .center{text-align: center;}
    .right{text-align: right;}
    .border{border: 1px solid black}
    .collapse{border-collapse: collapse;}
    .background{background: #edf2f7}
    .overflow-visible {white-space: initial;}
    .subrrayado{text-decoration: underline;}
</style>  

<meta http-equiv='content-type' content='text/html; utf-8'>
</head>
<body>
<div style="font-size: 11px;font-family: sans-serif;">
<table width="100%"  class="collapse" >
<tr>
    <td>
    <img src="logoverde.png" alt="">
    </td>
    <td colspan="2">
        <div class="p2 bold center textPrint-h1 ">
            SOCIEDAD DE INGENIEROS DE BOLIVIA <br>
            DEPARTAMENTAL ORURO
        </div>
    </td>
</tr>
<tr>

    <td>

    <td>
    </td>
    </td>
</tr>
</table>
<br>`
      cadena += '<div style="font-size: 18px"> ' + contenido + '</div> </div></body></html>'

      document.getElementById('myelement').innerHTML = cadena
      const d = new Printd()
      d.print(document.getElementById('myelement'))
    },
    validar (ped) {
      this.letter2.status = ped
      this.loading = true
      this.$api.put('letters/' + this.letter2.id, this.letter2).then(res => {
        this.dialogVerificar = false
        this.getCerti()
        this.loading = false
      })
    },
    verLetter () {
      this.letter = {}
      this.dialogLetter = true
    },
    getCerti () {
      const ver = this.store.roles.includes('administrador')
      this.$api.post('listLetter', { permiso: ver })
        .then(response => {
          console.log(response.data)
          this.letters = response.data
        })
    },
    onsubmit () {
      this.$api.post('letters', this.letter).then(res => {
        console.log(res)
        this.dialogLetter = false
        this.getCerti()
      })
    }
  }
})
</script>

<style scoped>

</style>
