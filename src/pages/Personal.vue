<template>
  <q-page class="column">
    <div class="q-pa-md row q-col-gutter-sm">
      <div class="col-xs-12 col-md-6 col-lg-3 q-mb-sm">
        <q-card class="bg-indigo-10 text-white">
          <q-item class="q-pa-xl" to="/personal/user">
            <q-item-section class="text-center text-h5">Персональные данные</q-item-section>
          </q-item>
        </q-card>
      </div>
      <div class="col-xs-12 col-md-6 col-lg-3 q-mb-sm">
        <q-card class="bg-indigo-10 text-white">
          <q-item class="q-pa-xl" to="/personal/orders">
            <q-item-section class="text-center text-h5">Заказы</q-item-section>
          </q-item>
        </q-card>
      </div>
    </div>
    <router-view/>
  </q-page>
</template>

<script>
  export default {
    setup() {},
  }
</script>
