import { defineStore } from "pinia"

export const useProducerOrdersStore = defineStore("producer_orders", {
	state: () => ({
		data: []
	}),

	actions: {
		commitData(data) {
			this.data = [...data, ...this.data]
		},

		commitOrderFields({ orderId, fields }) {
			let order = this.data.find((o) => o.id === orderId)

			if (order) {
				Object.assign(order, fields)
			}
		},

		emptyData() {
			this.data = []
		}
	}
})
