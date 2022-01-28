import { api } from "src/boot/axios"

export const getList = async ({commit}, payload) => {
  return new Promise((resolve, reject) => {
    api.get("orders", {
      params: payload
    })
      .then((response) => {
        commit("SET_ORDER_LIST", response.data)
      })
      .catch((error) => {
        reject(error.response.data.errors)
      })
  })
}
