import { LocalStorage } from "quasar"
import { Notify } from "quasar"

export const ADD_TO_CART = (state, payload) => {

	Object.keys(payload.producerProductList).forEach((productId) => {
		if (payload.producerProductList[productId] === 0)
			delete payload.producerProductList[productId]
	})

	let producerProductListFiltered = {[payload.producerId]: payload.producerProductList}

	try {
		state.data = {...state.data, ...producerProductListFiltered}
		LocalStorage.set("cart", state.data)
	} catch (error) {
		Notify.create({
			color: "red-5",
			textColor: "white",
			icon: "warning",
			message: "Ошибка локального хранилища"
		})
	}
}

export const SET_CART = (state, payload) => {
	state.data = payload
}
