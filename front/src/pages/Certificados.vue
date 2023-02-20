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
  </div>
</q-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue'

export default defineComponent({
  name: 'CertificadosPage'
})
</script>

<style scoped>

</style>
