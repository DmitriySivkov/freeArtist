import { defineStore } from "pinia"
import { api } from "src/boot/axios"

export const useOrderStore = defineStore("order", {
	state: () => ({
		data: []
	}),

	actions: {
		async getList(payload) {
			const response = await api.get("personal/orders", {
				params: payload
			})
			this.data = payload
		},
		async create(payload) {
			const response = await api.post("personal/orders", {
				cart: payload
			})
		}
	}
})
