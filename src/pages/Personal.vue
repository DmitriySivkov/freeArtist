<template>
  <q-page class="column">
    <div class="q-pa-md row">
      <div class="col-xs-12 col-md-6 col-lg-3">
        <q-badge color="purple" text-color="white">
          Личная информация
        </q-badge>
        <TableData
          :data="{
            name:user.name,
            email:user.email,
            status:user.role.title
          }"
          class="bg-indigo-8"
          :theme="{ dark: true }"
        />
      </div>
    </div>
    <div class="q-pa-md row">
      <div class="col-xs-12 col-md-6 col-lg-3">
        <q-badge color="purple" text-color="white">
          Календарь заказов
        </q-badge>
        <div class="q-gutter-md row items-start">
          <OrderCalendar />
        </div>
      </div>
      <div class="col-xs-12 col-md-6 col-lg-3">
        <q-scroll-area style="height: 100%;">
          <OrderCalendarList />
        </q-scroll-area>
      </div>
    </div>
  </q-page>
</template>

<script>
  import { computed } from 'vue'
  import { useStore } from 'vuex'
  import TableData from 'src/components/TableData'
  import OrderCalendar from "src/components/OrderCalendar";
  import OrderCalendarList from "src/components/OrderCalendarList"

  export default {
    components: { TableData, OrderCalendar, OrderCalendarList },
    setup() {
      const $store = useStore()
      const user = computed(() => $store.state.user.data)
      return {
        user
      }
    },
  }
</script>
