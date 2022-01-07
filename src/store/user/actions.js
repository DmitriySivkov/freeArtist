import { api } from 'boot/axios'

export const login = async ({commit}, payload) => {
  return new Promise((resolve, reject) => {
    api.post('auth', payload)
      .then((response) => {
        commit("SET_USER", response.data)
      })
      .catch((error) => {
        reject(error.response.data.errors)
      })
  })
}
