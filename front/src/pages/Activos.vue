<template>
  <q-page>
    <div class="row">
      <div class="col-12">
        <q-table :loading="loading" :rows-per-page-options="[0]" :rows="inventaries" flat bordered dense :search="inventarySearch">
          <template v-slot:top-right>
            <q-btn label="Crear Categoria" color="primary" no-caps icon="add_circle_outline" @click="categoryCreate" dense />
            <q-btn color="green" label="Crear Activo" no-caps icon="add_circle_outline" @click="InvetaryAdd" dense />
            <q-btn flat round icon="refresh" @click="inventaryGet" dense />
            <q-btn flat round icon="o_download" @click="exportSales" dense />
            <q-input outlined dense v-model="inventarySearch" label="Buscar" class="q-ml-md" clearable>
              <template v-slot:append>
                <q-icon name="search" />
              </template>
            </q-input>
          </template>
          <template v-slot:body-cell-actions="props">
            <q-td :props="props" auto-width>
              <q-btn flat size="11px" class="q-pa-none q-ma-none" round @click="anularSale(props.row)" icon="highlight_off" dense color="red" title="Anular" v-if="props.row.status==='Cancelado'"/>
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
    <q-dialog v-model="inventarieDialog">
      <q-card style="width: 400px;min-max: 80vh;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">Crear Inventarios</div>
          <q-space />
          <q-btn flat icon="close" v-close-popup />
        </q-card-section>
        <q-card-section class="q-pt-none">
          <q-form @submit="onSubmit">
            <div class="row">
              <div class="col-12">
                <q-select @update:model-value="codeGenerate" outlined dense v-model="inventary.category_id" :options="categories" label="Categoria" option-label="name" option-value="id" emit-value map-options hint="" />
              </div>
              <div class="col-12">
                <q-input outlined dense v-model="inventary.code" label="Codigo" hint="" :rules="[val => Object.keys(val).length > 0 || 'El codigo es requerido']" />
              </div>
              <div class="col-12">
                <q-input outlined dense v-model="inventary.name" label="Nombre" hint="" :rules="[val => Object.keys(val).length > 0 || 'El nombre es requerido']" />
              </div>
              <div class="col-12">
                <q-input outlined dense v-model="inventary.description" label="Descripcion" hint="" :rules="[val => Object.keys(val).length > 0 || 'La descripcion es requerida']" />
              </div>
              <div class="col-12">
                <q-input outlined dense v-model="inventary.price" label="Precio" hint="" :rules="[val => Object.keys(val).length > 0 || 'El precio es requerido']" />
              </div>
              <div class="col-12 flex flex-center">
                <q-uploader
                  accept=".jpg, .png"
                  multiple
                  auto-upload
                  label="Arrastra una imagen o haz click para seleccionar"
                  @uploading="uploadingFn"
                  ref="uploader"
                  max-files="1"
                  auto-expand
                  :url="`${$url}upload/1/shopUser`"
                  stack-label="upload image"/>
              </div>
              <div class="col-12">
                <q-input outlined dense v-model="inventary.quantity" label="Cantidad" hint="" :rules="[val => Object.keys(val).length > 0 || 'La cantidad es requerida']" />
              </div>
              <div class="col-12">
                <q-select outlined dense v-model="inventary.user_id" :options="users" label="Usuario" option-label="name" option-value="id" emit-value map-options hint="" />
              </div>
            </div>
            <div class="row">
              <div class="col-6 flex flex-center">
                <q-btn :loading="loading" color="primary" label="Guardar" type="submit" no-caps icon="o_save" dense />
              </div>
              <div class="col-6 flex flex-center">
                <q-btn :loading="loading" color="negative" label="Cancelar" type="reset" no-caps icon="cancel" dense v-close-popup />
              </div>
            </div>
            <pre>{{inventary}}</pre>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
    <q-dialog v-model="categoryShow">
      <q-card>
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">Control Categorias</div>
          <q-space />
          <q-btn flat icon="close" v-close-popup />
        </q-card-section>
        <q-card-section>
          <q-form @submit.prevent="categorySubmit" ref="myForm">
          <div class="row">
            <div class="col-12 col-md-8">
              <q-input outlined dense v-model="category.name" label="Nombre" hint="" :rules="[val => val.length > 0 || 'El nombre es requerido']" />
            </div>
            <div class="col-12 col-md-4 text-center q-pt-xs">
              <q-btn :loading="loading" color="primary" label="Guardar" type="submit" no-caps icon="o_save" dense  />
            </div>
          </div>
          </q-form>
          <q-table :loading="loading" :rows-per-page-options="[0]" :rows="categories" flat bordered dense :search="inventarySearch" :columns="categoryColumns">
            <template v-slot:top-right>
              <q-btn flat round icon="refresh" @click="categoriesGet" dense />
              <q-input outlined dense v-model="inventarySearch" label="Buscar" class="q-ml-md" clearable>
                <template v-slot:append>
                  <q-icon name="search" />
                </template>
              </q-input>
            </template>
            <template v-slot:body-cell-actions="props">
              <q-td :props="props" auto-width>
                <q-btn flat size="11px" class="q-pa-none q-ma-none" round @click="deleteCategory(props.row)" icon="delete" dense color="red" title="Anular"/>
              </q-td>
            </template>
          </q-table>
        </q-card-section>
      </q-card>
    </q-dialog>
    <div id="myElement" class="hidden"></div>
    <pre>{{ users }}</pre>
  </q-page>
</template>

<script>
import xlsx from 'json-as-xlsx'
import { date } from 'quasar'
import { Printd } from 'printd'
export default {
  name: 'ActivosPage',
  data () {
    return {
      d: new Printd(),
      inventarieDialog: false,
      categoryStatus: 'Crear',
      loading: false,
      inventaries: [],
      categoryShow: false,
      inventary: {
        name: '',
        code: '',
        description: '',
        price: '',
        image: '',
        quantity: '',
        category_id: 1,
        user_id: ''
      },
      categories: [],
      category: {
        name: '',
        code: ''
      },
      users: [],
      inventarySearch: '',
      inventaryColumns: [
        { name: 'actions', label: 'Acciones', field: 'actions', align: 'left', sortable: false },
        { name: 'amount', label: 'Monto', field: 'amount', align: 'left', sortable: true },
        { name: 'name', label: 'Nombre', field: 'name', align: 'left', sortable: true },
        { name: 'status', label: 'Estado', field: 'status', align: 'left', sortable: true },
        { name: 'date', label: 'Fecha', field: 'date', align: 'left', sortable: true },
        { name: 'time', label: 'Hora', field: 'time', align: 'left', sortable: true },
        { name: 'user', label: 'Usuario', field: (row) => row.user.name, align: 'left', sortable: true }
      ],
      categoryColumns: [
        { name: 'actions', label: 'Acciones', field: 'actions', align: 'left', sortable: false },
        { name: 'id', label: 'Id', field: 'id', align: 'left', sortable: true },
        { name: 'name', label: 'Nombre', field: 'name', align: 'left', sortable: true },
        { name: 'code', label: 'Codigo', field: 'code', align: 'left', sortable: true }
      ]
    }
  },
  created () {
    this.inventaryGet()
    this.categoriesGet()
    this.usersGet()
  },
  methods: {
    uploadingFn (e) {
      e.xhr.onload = () => {
        if (e.xhr.readyState === e.xhr.DONE) {
          if (e.xhr.status === 200) {
            this.inventary.image = e.xhr.response
          }
        }
      }
    },
    usersGet () {
      this.users = [{
        id: null,
        name: ''
      }]
      this.$api.get('users').then((response) => {
        response.data.forEach((user) => {
          this.users.push(user)
        })
      })
    },
    deleteCategory (category) {
      this.$q.dialog({
        title: 'Eliminar',
        message: '¿Estas seguro de eliminar esta categoria?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        this.$q.loading.show()
        this.$api.delete('categories/' + category.id).then(() => {
          this.categoriesGet()
        }).finally(() => {
          this.$q.loading.hide()
        })
      })
    },
    categoryCreate () {
      this.categoryStatus = 'Crear'
      this.category = {
        name: '',
        code: ''
      }
      this.categoryShow = true
    },
    categorySubmit () {
      this.$q.loading.show()
      if (this.categoryStatus === 'Crear') {
        this.$api.post('categories', this.category).then(() => {
          this.categoriesGet()
          this.$refs.myForm.reset()
          this.category = {
            name: '',
            code: ''
          }
        }).finally(() => {
          this.$q.loading.hide()
        })
      } else {
        this.$api.put('categories/' + this.category.id, this.category).then(() => {
          this.categoriesGet()
        }).finally(() => {
          this.$q.loading.hide()
        })
      }
    },
    categoriesGet () {
      this.$api.get('categories').then((response) => {
        this.categories = response.data
      })
    },
    exportSales () {
      const data = [
        {
          columns: [
            { label: 'N', value: 'id' },
            { label: 'Nombre', value: 'name' },
            { label: 'Monto', value: 'amount' },
            { label: 'Estado', value: 'status' },
            { label: 'Fecha', value: 'date' },
            { label: 'Hora', value: 'time' },
            { label: 'Usuario', value: (row) => row.user.name }
          ],
          content: this.inventaries
        }
      ]

      const settings = {
        fileName: 'MySpreadsheet', // Name of the resulting spreadsheet
        extraLength: 3, // A bigger number means that columns will be wider
        writeMode: 'writeFile', // The available parameters are 'WriteFile' and 'write'. This setting is optional. Useful in such cases https://docs.sheetjs.com/docs/solutions/output#example-remote-file
        writeOptions: {} // Style options from https://docs.sheetjs.com/docs/api/write-options
        // RTL: true // Display the columns from right-to-left (the default value is false)
      }

      xlsx(data, settings) // Will download the excel file
    },
    anularSale (inventarie) {
      this.$q.dialog({
        title: 'Anular',
        message: '¿Está seguro de anular esta venta?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        this.loading = true
        this.$api.post('anularSale', inventarie).then((response) => {
          console.log(response)
          this.inventaryGet()
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
      this.$api.post('inventories', this.inventary).then((response) => {
        console.log(response)
        this.inventarieDialog = false
        this.inventaryGet()
      }).catch((error) => {
        console.log(error)
      }).finally(() => {
        this.loading = false
      })
    },
    inventaryGet () {
      this.loading = true
      this.$api.get('inventories').then((response) => {
        this.inventaries = response.data
      }).catch((error) => {
        console.log(error)
      }).finally(() => {
        this.loading = false
      })
    },
    InvetaryAdd () {
      if (this.categories === []) {
        this.$q.notify({
          message: 'No hay categorias',
          color: 'negative',
          position: 'top',
          icon: 'warning'
        })
        return false
      }
      this.inventarieDialog = true
      this.inventary = {
        name: '',
        description: '',
        price: '',
        image: '',
        quantity: '',
        category_id: this.categories === [] ? null : this.categories[0].id,
        user_id: ''
      }
      this.codeGenerate(this.categories === [] ? null : this.categories[0].id)
    },
    codeGenerate (id) {
      if (id == null) {
        this.inventary.code = ''
      } else {
        this.$api.get('categories/' + id).then((response) => {
          this.inventary.code = response.data
        })
      }
    },
    addEgreso () {
      this.inventarieDialog = true
      this.inventarie = {
        name: 'EGRESO POR COMPRA',
        amount: '',
        date: date.formatDate(new Date(), 'YYYY-MM-DD'),
        status: 'Cancelado',
        type: 'Egreso',
        description: '',
        user_id: this.$store.user.id
      }
    }
  }
}
</script>

<style scoped>

</style>
