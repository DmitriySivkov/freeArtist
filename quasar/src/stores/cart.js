import { defineStore } from "pinia"
import { LocalStorage } from "quasar"

export const useCartStore = defineStore("cart", {
	state: () => ({
		data: [] // array of objects: producerId + product list with amount
	}),

	actions: {
		add({ producerId, product }) {
			let producerSet = this.data.find((item) => item.producerId === producerId)

			if (producerSet) {
				if (!producerSet.products || !producerSet.products.length) {
					producerSet.products = [{...product, amount: 1}]
				} else {
					let product = producerSet.products.find((p) => p.id === product.id)

					if (product) {
						product.amount++
					} else {
						producerSet.products.unshift({...product, amount: 1})
					}

				}
			} else {
				this.data.unshift({
					producerId,
					products: [{...product, amount: 1}]
				})
			}

			LocalStorage.set("cart", this.data)
		},

		remove({ producerId, productId }) {
			let producerSet = this.data.find((item) => item.producerId === producerId)

			let product = producerSet.products.find((p) => p.id === productId)

			if (product.amount === 1) {
				producerSet.products = producerSet.products.filter((p) => p.id !== productId)

				if (!producerSet.products.length) {
					this.data = this.data.filter((item) => item.producerId !== producerId)
				}
			} else {
				product.amount--
			}

			LocalStorage.set("cart", this.data)
		},
	}
})
