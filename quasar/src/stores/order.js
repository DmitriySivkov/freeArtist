import { defineStore } from "pinia"
import { api } from "src/boot/axios"

export const useOrderStore = defineStore("order", {
	state: () => ({
		data: []
	}),

	actions: {
		async getOrders(filter) {
			const response = await api.get("personal/orders", {
				params: filter
			})

			this.data = response.data
		},
	}
})
