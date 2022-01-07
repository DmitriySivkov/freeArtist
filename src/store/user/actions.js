import { api } from 'boot/axios'

export const login = async ({commit}, payload) => {
  return new Promise((resolve, reject) => {
    api.post('auth', payload)
      .then((response) => {
        commit("SET_USER", response.data)
        resolve()
      })
      .catch((error) => {
        reject(error.response.data.errors)
      })
  })
}

export const signUp = async ({commit}, payload) => {
  return new Promise((resolve, reject) => {
    api.post('register', payload)
      .then(() => {
        resolve()
      })
      .catch((error) => {
        reject(error.response.data.errors)
      })
  })
}
