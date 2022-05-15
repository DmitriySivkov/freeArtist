import { reactive } from "vue"
import { useStore } from "vuex"

export const useCartManager = (productsInitObject) => {
	const $store = useStore()
	const products = reactive(productsInitObject)

	const increase = (producer, product_id) => {
		if (products[product_id] === 999) return
		products[product_id]++

		$store.commit("cart/ADD_TO_CART", { producer, products })
	}

	const decrease = (producer, product_id) => {
		if (products[product_id] === 0) return
		products[product_id]--

		$store.commit("cart/ADD_TO_CART", { producer, products })
	}

	const orderAmountChanged = (producer, product_id, product_amount) => {
		if (product_amount === "") products[product_id] = 0
		if (product_amount > 999) products[product_id] = 999

		$store.commit("cart/ADD_TO_CART", { producer, products })
	}

	return { products, increase, decrease, orderAmountChanged }

}
