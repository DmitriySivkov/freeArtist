import { defineStore } from "pinia"
import { LocalStorage } from "quasar"

export const useCartStore = defineStore("cart", {
	state: () => ({
		data: [] // array of objects: producer_id + producer_name + product list (cart_amount, data)
	}),

	actions: {
		increaseProductAmount({ producerId, product }) {
			producerId = parseInt(producerId)

			let producerSet = this.data.find((item) => item.producer_id === producerId)

			if (producerSet) {
				if (!producerSet.products || !producerSet.products.length) {
					producerSet.products = [{ cart_amount: 1, data: product }]
				} else {
					let productSet = producerSet.products.find((p) => p.data.id === product.id)

					if (productSet) {
						productSet.cart_amount++
					} else {
						producerSet.products.unshift({ cart_amount: 1, data: product })
					}

				}
			} else {
				this.data.unshift({
					producer_id: producerId,
					products: [{ cart_amount: 1, data: product }]
				})
			}

			LocalStorage.set("cart", this.data)
		},

		decreaseProductAmount({ producerId, productId }) {
			producerId = parseInt(producerId)
			productId = parseInt(productId)

			let producerSet = this.data.find((item) => item.producer_id === producerId)

			let productSet = producerSet.products.find((p) => p.data.id === productId)

			productSet.cart_amount--

			LocalStorage.set("cart", this.data)
		},

		removeProduct({ producerId, productId }) {
			producerId = parseInt(producerId)
			productId = parseInt(productId)

			let producerSet = this.data.find((item) => item.producer_id === producerId)

			producerSet.products = producerSet.products.filter((p) => p.data.id !== productId)

			if (!producerSet.products.length) {
				this.data = this.data.filter((item) => item.producer_id !== producerId)
			}

			LocalStorage.set("cart", this.data)
		},

		setProductAmount({ producerId, product, specificAmount }) {
			producerId = parseInt(producerId)
			specificAmount = parseInt(specificAmount)

			let producerSet = this.data.find((item) => item.producer_id === producerId)

			if (producerSet) {
				if (!producerSet.products || !producerSet.products.length) {
					if (specificAmount > 0) {
						producerSet.products = [{ cart_amount: specificAmount, data: product }]
					}
				} else {
					let productSet = producerSet.products.find((p) => p.data.id === product.id)

					if (productSet) {
						if (specificAmount > 0) {
							productSet.cart_amount = specificAmount
						} else {
							producerSet.products = producerSet.products.filter((p) => p.data.id !== product.id)

							if (!producerSet.products.length) {
								this.data = this.data.filter((item) => item.producer_id !== producerId)
							}
						}
					} else {
						if (specificAmount > 0) {
							producerSet.products.unshift({cart_amount: specificAmount, data: product})
						} else {
							producerSet.products = producerSet.products.filter((p) => p.data.id !== product.id)
						}
					}

				}
			} else {
				if (specificAmount > 0) {
					this.data.unshift({
						producer_id: producerId,
						products: [{ cart_amount: specificAmount, data: product }]
					})
				} else {
					this.data = this.data.filter((item) => item.producer_id !== producerId)
				}

			}

			LocalStorage.set("cart", this.data)
		},

		setCart(data) {
			this.data = data
		},

		setCartProducerProductData({producerId, productId, data, cartAmount}) {
			let producerSet = this.data.find((producerSet) => producerSet.producer_id === producerId)

			let product = producerSet.products.find((p) => p.data.id === productId)

			// здесь мержим два объекта data (важно: не перезаписываем)
			Object.assign(product.data, data)

			if (typeof cartAmount === "number") {
				product.cart_amount = cartAmount
			}
		},

		clearCartProducer(producerId) {
			this.data = this.data.filter((producerSet) => producerSet.producer_id !== producerId)
			LocalStorage.set("cart", this.data)
		},

		clearCart() {
			this.data = []
			LocalStorage.remove("cart")
		}
	}
})
