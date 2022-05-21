import { reactive } from "vue"
import { useStore } from "vuex"

/**
 * чтобы использовать этот composable необходимо инициировать объект products
 * локальный products должен выглядеть как объект типа: {'id продукта': 'кол-во продукта'}
 * либо пустой объект - {}
 * в products необязательно должны лежать продукты только одного продюсера
 * */
export const useCartManager = (productsInitObject) => {
	const $store = useStore()
	const products = reactive(productsInitObject)

	const increase = (producer, product_id) => {
		let producerProduct = producer.products.find((product) => product.id === product_id)
		if (products[product_id] === producerProduct.amount) return
		products[product_id]++

		$store.commit("cart/ADD_TO_CART", { producer, products })
	}

	const decrease = (producer, product_id) => {
		if (products[product_id] === 0) return
		products[product_id]--

		$store.commit("cart/ADD_TO_CART", { producer, products })
	}

	const orderAmountChanged = (producer, product_id, product_amount) => {
		let producerProduct = producer.products.find((product) => product.id === product_id)
		if (product_amount === "")
			products[product_id] = 0
		if (product_amount > producerProduct.amount)
			products[product_id] = producerProduct.amount

		$store.commit("cart/ADD_TO_CART", { producer, products })
	}

	return { products, increase, decrease, orderAmountChanged }

}
