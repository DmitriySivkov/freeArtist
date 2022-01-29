<template>
  <div class="q-pa-md row q-col-gutter-sm">
    <div class="col-xs-12 col-md-6 col-lg-3">
      <OrderCalendar />
    </div>
    <div class="col-xs-12 col-md-6 col-lg-3">
      <OrderCalendarList />
    </div>
  </div>
</template>

<script>
  import OrderCalendar from "src/components/orders/OrderCalendar"
  import OrderCalendarList from "src/components/orders/OrderCalendarList"

  export default {
    components: { OrderCalendar, OrderCalendarList },
    setup() {

    }
  }
</script>
