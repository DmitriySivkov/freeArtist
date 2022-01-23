<template>
  <q-page class="column">
    <div class="q-pa-md row">
      <div class="col-xs-12 col-md-6 col-lg-3">
        <q-badge color="purple" text-color="white">
          Личная информация
        </q-badge>
        <q-markup-table dark class="bg-indigo-8">
          <tbody>
            <tr>
              <td class="text-left">Имя</td>
              <td class="text-left">{{ user.name }}</td>
            </tr>
            <tr>
              <td class="text-left">Email</td>
              <td class="text-left">{{ user.email }}</td>
            </tr>
            <tr>
              <td class="text-left">Статус</td>
              <td class="text-left">{{ user.role.title }}</td>
            </tr>
          </tbody>
        </q-markup-table>
      </div>
    </div>
    <div class="q-pa-md row">
      <div class="col-xs-12 col-md-6 col-lg-3">
        <q-badge color="purple" text-color="white">
          Календарь заказов
        </q-badge>
        <div class="q-gutter-md row items-start">
          <q-date
            @update:model-value="this.showOrders($event)"
            v-model="this.qdate"
            mask="DD.MM.YYYY"
            :locale="this.ruLocale"
          />
        </div>
      </div>
    </div>
  </q-page>
</template>

<script>
  import { computed, ref } from 'vue'
  import { useStore } from 'vuex'
  import { date } from 'quasar'
  export default {
    setup() {
      const $store = useStore()
      const user = computed(() => $store.state.user.data)
      const qdate = ref(date.formatDate(Date.now(), 'DD.MM.YYYY'))
      const ruLocale = {
        days: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'],
        daysShort: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'],
        months: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
        monthsShort: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
        firstDayOfWeek: 1
      }
      return {
        user,
        qdate,
        ruLocale,

        showOrders(value) {
          console.log(value)
        }
      }
    },
  }
</script>
