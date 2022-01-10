<template>
  <q-item v-if="!this.isApiCall" :to="this.link">
    <q-item-section
      v-if="this.icon"
      avatar
    >
      <q-icon :name="this.icon" />
    </q-item-section>
    <q-item-section>
      <q-item-label>{{ title }}</q-item-label>
      <q-item-label caption>
        {{ caption }}
      </q-item-label>
    </q-item-section>
  </q-item>

  <q-item v-else clickable @click="makeApiCall">
    <q-item-section
      v-if="this.icon"
      avatar
    >
      <q-icon :name="this.icon" />
    </q-item-section>
    <q-item-section>
      <q-item-label>{{ title }}</q-item-label>
      <q-item-label caption>
        {{ caption }}
      </q-item-label>
    </q-item-section>
  </q-item>
</template>

<script>
import { defineComponent } from 'vue'
import { useStore } from "vuex"

export default defineComponent({
  name: 'EssentialLink',
  props: {
    title: {
      type: String,
      required: true
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
    },
    isApiCall: {
      type: Boolean,
      default: false
    },
    apiCall: {
      type: Function
    }
  },
  setup(props) {
    const $store = useStore()
    return {
      makeApiCall() {
        props.apiCall($store)
      }
    }
  },
})
</script>
