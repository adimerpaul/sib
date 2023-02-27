<template>
  <q-page class="row flex flex-center">
    <div class="col-12 text-center">
      <q-img  src="logoverde.png" width="200px" />
      <div class="text-grey text-h5 text-bold">
        {{$store.cogs.nombre}}
      </div>
    </div>
  </q-page>
</template>

<script>
export default {
  name: 'IndexPage',
  setup () {
    return {
    }
  }
}
</script>
