<template>
  <q-page class="column justify-center">
    <div class="q-pa-md row justify-center">
      <div class="col-xs-12 col-md-6 col-lg-3">
        <q-form
          @submit="onSubmit"
          @reset="onReset"
          class="q-gutter-md"
        >
          <q-input
            filled
            v-model="email"
            label="Адрес электронной почты *"
            lazy-rules
            :rules="[
            val => !!val || 'Введите адрес электронной почты',
            isValidEmail
          ]"
          />

          <q-input
            v-model="password"
            filled
            :type="isPwd ? 'password' : 'text'"
            label="Пароль *"
            lazy-rules
            :rules="[
            val => !!val || 'Введите пароль',
            val => val.length > 6 || 'не менее 6 символов',
          ]"
          >
            <template v-slot:append>
              <q-icon
                :name="isPwd ? 'visibility_off' : 'visibility'"
                class="cursor-pointer"
                @click="isPwd = !isPwd"
              />
            </template>
          </q-input>

          <div class="row justify-evenly">
            <q-btn label="Подтвердить" type="submit" color="primary" class="col-xs-12 col-md-5"/>
            <q-btn label="Сбросить" type="reset" color="primary" flat class="q-ml-sm col-xs-12 col-md-5" />
          </div>
        </q-form>
        <div class="q-mt-lg">Ещё нет аккаунта? - <router-link to="/register">Зарегистрироваться</router-link></div>
      </div>
    </div>
  </q-page>
</template>

<script>
  import { useQuasar } from 'quasar'
  import { ref } from 'vue'
  import { api } from 'boot/axios'
  export default {
    setup() {
      const $q = useQuasar()

      const email = ref(null)
      const password = ref('')
      const isPwd = ref(true)

      return {
        email,
        password,
        isPwd,

        onSubmit () {
          api.post("auth", {
            email: email.value,
            password: password.value,
          }).then(() => {
            $q.notify({
              color: 'green-4',
              textColor: 'white',
              icon: 'cloud_done',
              message: 'Будь как дома, путник!'
            })
          }).catch((error) => {
            const errors = Object.values(error.response.data.errors).reduce((accum, val) => accum.concat(...val), []);
            for (var val of errors) {
              $q.notify({
                color: 'red-5',
                textColor: 'white',
                icon: 'warning',
                message: val
              })
            }
          })
        },

        onReset () {
          email.value = null
          password.value = null
        },

        isValidEmail (val) {
          return /^(?=[a-zA-Z0-9@._%+-]{6,254}$)[a-zA-Z0-9._%+-]{1,64}@(?:[a-zA-Z0-9-]{1,63}\.){1,8}[a-zA-Z]{2,63}$/.test(val)
            || 'Неверный формат';
        }

      }
    },
  }
</script>
