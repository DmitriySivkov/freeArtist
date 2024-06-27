import { useCartStore } from "src/stores/cart"
import {Dialog, LocalStorage} from "quasar"
import {api} from "boot/axios"
import OrderInvalidProductDialog from "components/dialogs/OrderInvalidProductDialog"
import {PAYMENT_METHODS} from "src/const/paymentMethods"

export const useCart = () => {
	const cartStore = useCartStore()

	const increase = (producer, product_id) => {
		let producerProduct = producer.products.find((product) => product.id === product_id)

		if (products[product_id] === producerProduct.amount) return
		products[product_id]++

		cartStore.addToCart({ producer, products })
	}

	const decrease = (producer, product_id) => {
		if (products[product_id] === 0) return
		products[product_id]--

		cartStore.addToCart({ producer, products })
	}

	const orderAmountChanged = (producer, product_id, product_amount) => {
		let producerProduct = producer.products.find((product) => product.id === product_id)
		if (product_amount === "")
			products[product_id] = 0
		if (product_amount > producerProduct.amount)
			products[product_id] = producerProduct.amount

		cartStore.addToCart({ producer, products })
	}

	const clearCart = () => {
		cartStore.setCart({})
		LocalStorage.set("cart", {})
	}

	// const initCart = () => {
	// 	if (!cartStore.data.length) return
	//
	// 	let cartProducers = cartStore.data.map((producerSet) =>
	// 		producerSet.producer_id
	// 	)
	//
	// 	let cartProducts = cartStore.data.reduce((carry, producerSet) =>
	// 		[
	// 			...carry,
	// 			...producerSet.products.map((p) => ({
	// 				id: p.data.id,
	// 				price: p.data.price,
	// 				amount: p.cart_amount
	// 			}))
	// 		], [])
	//
	// 	const promise = api.post("cart/load", {
	// 		producers: cartProducers,
	// 		products: cartProducts
	// 	})
	//
	// 	promise.catch((error) => {
	// 		if (
	// 			typeof error.response.data === "object" &&
	// 			error.response.data.exception === "App\\Exceptions\\OrderInvalidItemsException"
	// 		) {
	// 			let invalidProducts = error.response.data.invalid_items.map(function(p) {
	// 				const cartProduct = cartProducts.find((cp) => cp.id === p.id)
	// 				p.cartAmount = cartProduct.amount
	// 				p.cartPrice = cartProduct.price
	// 				return p
	// 			})
	//
	// 			Dialog.create({
	// 				component: OrderInvalidProductDialog,
	// 				componentProps: {
	// 					message: error.response.data.message,
	// 					invalidProducts
	// 				}
	// 			}).onDismiss(() => {
	// 				// setting real amount and price
	// 				invalidProducts.forEach((ip) =>
	// 					cartStore.setCartProducerProductData({
	// 						producerId: ip.producer_id,
	// 						productId: ip.id,
	// 						data: {
	// 							price: ip.price,
	// 							amount: ip.amount
	// 						},
	// 						cartAmount: ip.amount < ip.cartAmount ? ip.amount : ip.cartAmount
	// 					})
	// 				)
	//
	// 				LocalStorage.set("cart", cartStore.data)
	//
	// 				isCartLoaded.value = false
	// 				initCart()
	// 			})
	//
	// 			return
	// 		}
	//
	// 		notifyError(error.response.data.message)
	// 	})
	//
	// 	promise.then((response) => {
	// 		producers.value = response.data.producers.reduce((carry, p) =>
	// 			({...carry, [p.id]: p}), {}
	// 		)
	//
	// 		products.value = response.data.products.reduce((carry, p) =>
	// 			({...carry, [p.id]: p}), {}
	// 		)
	//
	// 		paymentMethods.value = response.data.producers.reduce((carry, p) =>
	// 			({
	// 				...carry,
	// 				[p.id]: {
	// 					selectedPaymentMethodId: p.payment_methods.find((pm) => pm.id === PAYMENT_METHODS.CASH).id,
	// 					methods: p.payment_methods
	// 				}
	// 			}), {}
	// 		)
	//
	// 		isCartLoaded.value = true
	// 	})
	// }

	return {
		products,
		increase,
		decrease,
		orderAmountChanged,
		clearCart,
	}

}
