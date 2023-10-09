import { defineStore } from "pinia"
import { api } from "src/boot/axios"
// todo - remove order store?
export const useOrderStore = defineStore("order", {
	state: () => ({
		data: []
	}),

	actions: {
		// todo - move request action to component
		async getOrders(filter) {


			this.data = response.data
		},
	}
})
