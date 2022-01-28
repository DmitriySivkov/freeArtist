<template>
  <q-markup-table :="{...theme}" :class="this.class">
    <tbody>
      <tr v-for="(value, key) in data" :key="key">
        <td class="text-left">{{ key }}</td>
        <td class="text-left">{{ value }}</td>
      </tr>
    </tbody>
  </q-markup-table>
</template>

<script>
  export default {
    props: {
      data: {
        type: Object,
        default: () => {}
      },
      theme: {
        type: Object,
        default: () => { return { dark: true } }
      },
      class: {
        type: String,
        default: ""
      }
    },
    setup() {}
  }
</script>
