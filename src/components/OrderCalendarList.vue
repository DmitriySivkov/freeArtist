<template>
  <div v-if="orderList.length > 0">
    <q-card v-for="(orderItem, orderItemIndex) in orderList" :key="orderItemIndex" dark bordered class="bg-grey-9">
      <div class="text-subtitle1 text-center">
        Заказал: {{ orderItem.customer.name }}
      </div>
      <q-separator dark />
      <q-card-section v-for="product in orderItem.products" :key="product.product_id">
        Продукт: {{ product.title }}
        <q-separator dark inline />
      </q-card-section>
    </q-card>
  </div>
  <div v-else>
    <q-card dark bordered class="bg-grey-9">
      <div class="text-subtitle1 text-center">
        Заказов нет
      </div>
      <q-separator dark />
    </q-card>
  </div>
</template>

<script>
  import { computed } from "vue"
  import { useStore } from "vuex"

  export default {
    setup() {
      const $store = useStore()
      const orderList = computed(() => $store.state.order.data)
      return {
        orderList
      }
    }
  }
</script>
