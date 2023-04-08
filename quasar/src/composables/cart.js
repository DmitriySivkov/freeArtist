import { reactive } from "vue"
import { useCartStore } from "src/stores/cart"
import {LocalStorage} from "quasar"

/**
 * чтобы использовать этот composable необходимо инициировать объект products
 * локальный products должен выглядеть как объект типа: {'id продукта': 'кол-во продукта'}
 * либо пустой объект - {}
 * в products необязательно должны лежать продукты только одного продюсера
 * */
export const useCart = (productsInitObject) => {
	const cart_store = useCartStore()
	const products = reactive(productsInitObject)

	const increase = (producer, product_id) => {
		let producerProduct = producer.products.find((product) => product.id === product_id)

		if (products[product_id] === producerProduct.amount) return
		products[product_id]++

		cart_store.addToCart({ producer, products })
	}

	const decrease = (producer, product_id) => {
		if (products[product_id] === 0) return
		products[product_id]--

		cart_store.addToCart({ producer, products })
	}

	const orderAmountChanged = (producer, product_id, product_amount) => {
		let producerProduct = producer.products.find((product) => product.id === product_id)
		if (product_amount === "")
			products[product_id] = 0
		if (product_amount > producerProduct.amount)
			products[product_id] = producerProduct.amount

		cart_store.addToCart({ producer, products })
	}

	const clearCart = () => {
		cart_store.setCart({})
		LocalStorage.set("cart", {})
	}

	return {
		products,
		increase,
		decrease,
		orderAmountChanged,
		clearCart
	}

}
