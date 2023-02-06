import { LocalStorage } from "quasar"
import { useNotification } from "src/composables/notification"

export const ADD_TO_CART = (state, payload) => {

	let products = payload.producer.products
		.filter((product) => Object.keys(payload.products).includes(product.id.toString()) && payload.products[product.id] !== 0)
		.map((product) =>
			({
				amount: payload.products[product.id],
				data: product
			})
		)

	try {
		if (products.length === 0 && state.data.hasOwnProperty(payload.producer.id))
			delete state.data[payload.producer.id]
		else
			state.data[payload.producer.id] = { producer: payload.producer, products }

		LocalStorage.set("cart", state.data)
	} catch (error) {
		useNotification()
			.notifyError("Ошибка локального хранилища")
	}
}

export const SET_CART = (state, payload) => {
	state.data = payload
}
