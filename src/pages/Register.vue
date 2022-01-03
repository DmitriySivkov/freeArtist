<template>
  <q-page class="flex flex-center">
    <div class="q-pa-md">
      <q-form
        @submit="onSubmit"
        @reset="onReset"
        class="q-gutter-md"
      >
        <q-input
          filled
          v-model="name"
          label="Ваше имя *"
          hint="ФИО"
          lazy-rules
          :rules="[
            val => val && val.length > 6 || 'Введите имя, не менее 6 символов'
          ]"
        />

        <q-input
          filled
          v-model="email"
          label="Адрес электронной почты *"
          lazy-rules
          :rules="[ val => val && val.length > 0 || 'Введите адрес электронной почты']"
        />

        <q-field
          filled
          v-model="roles.selected"
          :rules="[val => !!val || 'Выберите роль']"
          borderless
        >
          <template v-slot:control>
              <q-option-group
                filled
                :options="roles.all"
                type="radio"
                v-model="roles.selected"
                inline
              />
          </template>
        </q-field>
        <div class="q-px-sm">
          Внимание! Вы будете зарегистрированы как:
        </div>
        <div class="q-px-sm">
          <strong>{{ role }}</strong>
        </div>

        <q-toggle v-model="accept" label="Я принимаю условия пользования сервисом" />

        <div>
          <q-btn label="Подтвердить" type="submit" color="primary"/>
          <q-btn label="Сбросить" type="reset" color="primary" flat class="q-ml-sm" />
        </div>
      </q-form>

    </div>
  </q-page>
</template>

<script>
  import { useQuasar } from 'quasar'
  import { ref, reactive, computed } from 'vue'
  import { api } from 'boot/axios'
  export default {
    setup() {
      const $q = useQuasar()

      const name = ref(null)
      const email = ref(null)
      const accept = ref(false)

      const roles = reactive({
        all: [],
        selected: null
      })
      api.get("roles").then((response) => {
        roles.all = response.data.roles.map((item) => {
          return {
            label: item.title,
            value: item.id
          }
        })
      })

      const role = computed(() =>
        roles.selected ?
          roles.all.find((item) => item.value === roles.selected).label :
          ''
      )

      return {
        name,
        email,
        roles,
        accept,
        role,

        onSubmit () {
          if (accept.value !== true) {
            $q.notify({
              color: 'red-5',
              textColor: 'white',
              icon: 'warning',
              message: 'You need to accept the license and terms first'
            })
          }
          else {
            $q.notify({
              color: 'green-4',
              textColor: 'white',
              icon: 'cloud_done',
              message: 'Submitted'
            })
          }
        },

        onReset () {
          name.value = null
          email.value = null
          accept.value = false
        },

      }
    },
  }
</script>
