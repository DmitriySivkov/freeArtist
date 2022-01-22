<template>
  <q-page class="column justify-center">
    {{ user.name }}
  </q-page>
</template>

<script>
  import { useQuasar } from 'quasar'
  import {computed, ref} from 'vue'
  import { useStore } from 'vuex'
  import { useRoute } from 'vue-router'
  export default {
    setup() {
      const $store = useStore()
      const user = computed(() => $store.state.user.data)

      return {
        user
      }
    },
  }
</script>
