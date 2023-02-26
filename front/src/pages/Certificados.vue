<template>
<q-page>
  <div class="row q-pt-xs">
    <div class="col-12">
      <div class="text-center text-h4 text-bold">
        Certificados
      </div>
    </div>
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
    </div>
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
           <q-btn color="green" icon="check" dense v-if="props.row.status==='pendiente' && $store.roles.includes('administrador')" @click="validar(props.row)"/>
      </q-td>
     </template>
       <template v-slot:body-cell-status="props">
          <q-td :props="props">
          <q-badge :color="props.row.status==='pendiente'?'red':'green'"  :label="props.row.status" />
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
            <q-btn  label="Registrar" color="green" type="submit" />
          </q-card-actions>
       </q-form>
    </q-card-section>
    </q-card>
    </q-dialog>

  </div>
</q-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue'

export default defineComponent({
  name: 'CertificadosPage',
  data () {
    return {
      loading: false,
      letters: [],
      letter: {},
      store: '',
      colLetter: [
        { name: 'opciones', label: 'Opciones', field: 'opciones', align: 'left', sortable: true, style: 'width: 100px' },
        { name: 'date', label: 'Fecha', field: 'date', align: 'left', sortable: true },
        { name: 'name', label: 'Nombre', field: 'name', align: 'left', sortable: true },
        { name: 'status', label: 'Estado', field: 'status', align: 'left', sortable: true },
        { name: 'description', label: 'Descripcion', field: 'description', align: 'left', sortable: true }
      ],
      letterSearch: '',
      dialogLetter: false
    }
  },
  created () {
    this.getCerti()
  },
  methods: {
    validar (ped) {
      this.$q.dialog({
        title: 'APROBOBAR SOLICITUD',
        message: 'Esta seguro de aprobar?',
        cancel: true,
        persistent: false
      }).onOk(() => {
        this.$api.put('letters/' + ped.id, ped).then(res => {
          this.getCerti()
        })
      }).onOk(() => {
        // console.log('>>>> second OK catcher')
      }).onCancel(() => {
        // console.log('>>>> Cancel')
      }).onDismiss(() => {
        // console.log('I am triggered on both OK and Cancel')
      })
    },
    verLetter () {
      this.letter = {}
      this.dialogLetter = true
    },
    getCerti () {
      const ver = this.$store.roles.includes('administrador')
      this.$api.post('listLetter', { permiso: ver })
        .then(response => {
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
