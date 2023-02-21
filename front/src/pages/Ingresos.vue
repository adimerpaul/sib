<template>
<q-page>
  <div class="row">
    <div class="col-12">
      <div class="row">
        <div class="col-6 col-md-4">
          <q-input outlined dense v-model="dateIni" type="date" label="Fecha Inicial"  />
        </div>
        <div class="col-6 col-md-4">
          <q-input outlined dense v-model="dateEnd" type="date" label="Fecha Final"  />
        </div>
        <div class="col-12 col-md-4 flex flex-center">
          <q-btn color="primary" label="Buscar" no-caps icon="search" @click="getSales" dense :loading="loading" />
        </div>
      </div>
    </div>
    <div class="col-12">
      <q-table :loading="loading" :rows-per-page-options="[0]" :rows="sales" flat bordered dense :search="salesSearch" :columns="saleColumns">
        <template v-slot:top-right>
          <q-btn color="green" label="Crear Ingreso" no-caps icon="add_circle_outline" @click="addIngreso" dense />
          <q-btn color="red" label="Crear Egreso" no-caps icon="add_circle_outline" @click="addEgreso" dense />
          <q-btn flat round icon="refresh" @click="getSales" dense />
          <q-input outlined dense v-model="salesSearch" label="Buscar" class="q-ml-md" clearable>
            <template v-slot:append>
              <q-icon name="search" />
            </template>
          </q-input>
        </template>
        <template v-slot:body-cell-actions="props">
          <q-td :props="props" auto-width>
<!--            <q-btn flat round icon="o_print" dense color="primary" title="Imprimir"/>-->
            <q-btn flat round @click="anularSale(props.row)" icon="highlight_off" dense color="red" title="Anular"/>
          </q-td>
        </template>
        <template v-slot:body-cell-amount="props">
          <q-td :props="props" auto-width>
            <q-icon :name="`${props.row.type==='Ingreso'?'o_attach_money':'o_money_off'}`" :color="`${props.row.type==='Ingreso'?'green':'red'}`" />
            <q-badge :label="props.row.type" :color="`${props.row.type==='Ingreso'?'green':'red'}`" /> {{props.row.amount}} Bs
          </q-td>
        </template>
        <template v-slot:body-cell-status="props">
          <q-td :props="props" auto-width>
            <q-badge :color="`${props.row.status==='Cancelado'?'green':'red'}`" :label="`${props.row.status}`" />
          </q-td>
        </template>
      </q-table>
    </div>
  </div>
  <q-dialog v-model="saleDialog">
    <q-card style="width: 400px;min-max: 80vh;">
      <q-card-section class="row items-center q-pb-none">
        <q-btn flat dense :icon="`${sale.type==='Ingreso'?'o_attach_money':'o_money_off'}`" :color="`${sale.type==='Ingreso'?'green':'red'}`" :class="`bg-${sale.type==='Ingreso'?'green':'red'}-3`" :style="`border: 1px solid ${sale.type==='Ingreso'?'#4caf50':'#f44336'}`" />
        <div class="text-h6">{{sale.type==='Ingreso'?'Crear Ingreso':'Crear Egreso'}}</div>
        <q-space />
        <q-btn flat icon="close" v-close-popup />
      </q-card-section>
      <q-card-section class="q-pt-none">
        <q-form @submit="onSubmit">
          <q-input outlined dense v-model="sale.name" label="Nombre" hint="" :rules="[val => val.length > 0 || 'El nombre es requerido']" />
          <q-input outlined dense v-model="sale.amount" label="Monto (Bs)" mask="#" reverse-fill-mask  :rules="[val => val.length > 0 || 'El monto es requerido']" />
<!--          <q-input outlined dense v-model="sale.date" type="date" label="Fecha" />-->
<!--          <q-input outlined dense v-model="sale.status" label="Estado" />-->
          <q-input outlined dense v-model="sale.description" label="Descripción" />
          <div class="row">
            <div class="col-6 flex flex-center">
              <q-btn :loading="loading" color="primary" label="Guardar" type="submit" no-caps icon="o_save" dense />
            </div>
            <div class="col-6 flex flex-center">
              <q-btn :loading="loading" color="negative" label="Cancelar" type="reset" no-caps icon="cancel" dense v-close-popup />
            </div>
          </div>
        </q-form>
      </q-card-section>
    </q-card>
  </q-dialog>
  <div id="myElement">
    hola
  </div>
</q-page>
</template>

<script>
import { date } from 'quasar'
import { Printd } from 'printd'
// import { Recibo } from 'src/addons/Recibo'
export default {
  name: 'IngresosPage',
  data () {
    return {
      d: new Printd(),
      saleDialog: false,
      loading: false,
      sales: [],
      sale: {
        name: '',
        amount: 0,
        status: '',
        type: '',
        description: ''
      },
      salesSearch: '',
      dateIni: date.formatDate(new Date(), 'YYYY-MM-DD'),
      dateEnd: date.formatDate(new Date(), 'YYYY-MM-DD'),
      saleColumns: [
        { name: 'actions', label: 'Acciones', field: 'actions', align: 'left', sortable: false },
        { name: 'amount', label: 'Monto', field: 'amount', align: 'left', sortable: true },
        { name: 'name', label: 'Nombre', field: 'name', align: 'left', sortable: true },
        { name: 'status', label: 'Estado', field: 'status', align: 'left', sortable: true },
        { name: 'date', label: 'Fecha', field: 'date', align: 'left', sortable: true },
        { name: 'time', label: 'Hora', field: 'time', align: 'left', sortable: true }
      ]
    }
  },
  created () {
    this.getSales()
  },
  mounted () {
    // const recido = new Recibo()
    // document.getElementById('myElement').innerHTML = recido.print()
    // this.d.print(document.getElementById('myElement'))
  },
  methods: {
    anularSale (sale) {
      this.$q.dialog({
        title: 'Anular',
        message: '¿Está seguro de anular esta venta?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        this.loading = true
        this.$api.post('anularSale', sale).then((response) => {
          console.log(response)
          this.getSales()
        }).catch((error) => {
          console.log(error)
        }).finally(() => {
          this.loading = false
        })
      }).onCancel(() => {
        console.log('Cancel')
      }).onDismiss(() => {
        console.log('Dismissed')
      })
    },
    onSubmit () {
      this.loading = true
      this.$api.post('sales', this.sale).then((response) => {
        console.log(response)
        this.saleDialog = false
        this.getSales()
      }).catch((error) => {
        console.log(error)
      }).finally(() => {
        this.loading = false
      })
    },
    getSales () {
      this.loading = true
      this.$api.post('getSales', {
        dateIni: this.dateIni,
        dateEnd: this.dateEnd
      }).then((response) => {
        this.sales = response.data
      }).catch((error) => {
        console.log(error)
      }).finally(() => {
        this.loading = false
      })
    },
    addIngreso () {
      this.saleDialog = true
      this.sale = {
        name: '',
        amount: '',
        status: 'Cancelado',
        type: 'Ingreso',
        description: ''
      }
    },
    addEgreso () {
      this.saleDialog = true
      this.sale = {
        name: '',
        amount: '',
        date: date.formatDate(new Date(), 'YYYY-MM-DD'),
        status: 'Cancelado',
        type: 'Egreso',
        description: '',
        user_id: this.$store.user.id
      }
    },
    editIngreso (row) {
      this.$router.push({ name: 'Ingreso', params: { id: row.id } })
    },
    deleteIngreso (row) {
      this.$q.dialog({
        title: 'Eliminar',
        message: '¿Está seguro de eliminar el registro?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        this.loading = true
        this.$api.delete('/sales/' + row.id).then((response) => {
          console.log(response.data)
          this.getSales()
        }).catch((error) => {
          console.log(error)
        }).finally(() => {
          this.loading = false
        })
      })
    }
  }
}
</script>

<style scoped>

</style>
