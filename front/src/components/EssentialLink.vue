<template>
  <div>
  <q-item v-if="(visible=='true'||this.$store.roles.includes(visible)) && subLinks.length==0" clickable :to="link" exact active-class="bg-primary text-white">
    <q-item-section
      v-if="icon"
      avatar
    >
      <q-icon :name="icon" />
    </q-item-section>
    <q-item-section>
      <q-item-label>{{ title }}</q-item-label>
      <q-item-label caption class=" text-grey" >{{ caption }}</q-item-label>
    </q-item-section>
  </q-item>
<!--  <q-expansion-item v-else :content-inset-level="0.5" expand-separator :icon="icon" :label="title" :caption="caption" exact >-->
<!--    <q-expansion-item hide-expand-icon v-for="(s,i) in subLinks" :key="i" expand-separator :content-inset-level="0.5" :icon="s.icon" :label="s.title" :to="s.link" exact>-->
<!--    </q-expansion-item>-->
<!--  </q-expansion-item>-->
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'

export default defineComponent({
  name: 'EssentialLink',
  props: {
    title: {
      type: String,
      required: true
    },
    visible: {
      type: String,
      required: false
    },
    subLinks: {
      default: []
    },
    caption: {
      type: String,
      default: ''
    },

    link: {
      type: String,
      default: '#'
    },

    icon: {
      type: String,
      default: ''
    }
  }
})
</script>
