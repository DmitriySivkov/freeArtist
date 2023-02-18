import { defineStore } from "pinia"
import { LocalStorage } from "quasar"
import { useNotification } from "src/composables/notification"

export const useCartStore = defineStore("cart", {
	state: () => ({
		data: {}
	}),

	actions: {
		addToCart({ producer, products }) {
			let product_list = producer.products
				.filter((product) => Object.keys(products).includes(product.id.toString()) && products[product.id] !== 0)
				.map((product) =>
					({
						amount: products[product.id],
						data: product
					})
				)

			try {
				if (product_list.length === 0 && this.data.hasOwnProperty(producer.id))
					delete this.data[producer.id]
				else
					this.data[producer.id] = { producer: producer, product_list }

				LocalStorage.set("cart", this.data)
			} catch (error) {
				useNotification()
					.notifyError("Ошибка локального хранилища")
			}
		},

		setCart(data) {
			this.data = data
		}
	}
})
