import { api } from 'src/boot/axios'

export const login = async ({commit}, payload) => {
  return new Promise((resolve, reject) => {
    api.post('auth', payload, {headers: {"X-APP-TYPE":"web-app"}})
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
    api.post('register', payload, {headers: {"X-APP-TYPE":"web-app"}})
      .then(() => {
        resolve()
      })
      .catch((error) => {
        reject(error.response.data.errors)
      })
  })
}

export const logout = async ({commit}, payload) => {
  return new Promise((resolve, reject) => {
    api.post('logout', payload)
      .then(() => {
        commit("SET_USER", {})
        resolve()
      })
      .catch((error) => {
        reject(error.response.data.errors)
      })
  })
}

export const checkTokenCookie = async () => {
  await api.post('hasTokenCookie')
}

export const verifyEmail = async ({commit}, payload) => {
  await api.post('auth/verify-email', {
    hash: payload.hash,
    email: payload.email
  })
}
