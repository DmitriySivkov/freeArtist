<script setup>
import { computed, ref, onMounted } from "vue"
import { useCartStore } from "src/stores/cart"
import { api } from "src/boot/axios"
import { useNotification } from "src/composables/notification"
import { useUserStore } from "src/stores/user"
import { Dialog, LocalStorage } from "quasar"
import EmptyCart from "src/components/cart/EmptyCart.vue"
import OrderInvalidProductDialog from "src/components/dialogs/OrderInvalidProductDialog.vue"
import OrderCheckoutDialog from "src/components/dialogs/OrderCheckoutDialog.vue"
import OrderCompletedDialog from "src/components/dialogs/OrderCompletedDialog.vue"
import CommonConfirmationDialog from "src/components/dialogs/CommonConfirmationDialog.vue"

const { notifySuccess, notifyError } = useNotification()

const cartStore = useCartStore()
const cart = computed(() => cartStore.data)

const hasOnlyAbsentProducts = computed(() =>
	!isCartLoaded.value ? {} :
		cart.value.reduce((carry, producerSet) =>
			({
				...carry,
				[producerSet.producer_id]: !producerSet.products.some((productSet) =>
					productSet.cart_amount > 0
				)
			}), {})
)

const addAmount = ({ producerId, product }) => {
	if (product.cart_amount >= products.value[product.data.id].amount) return

	cartStore.increaseProductAmount({
		producerId,
		product: product.data
	})
}

const decreaseAmount = ({ producerId, product }) => {
	if (product.cart_amount === 0) return

	cartStore.decreaseProductAmount({
		producerId,
		productId: product.data.id
	})
}

const removeProduct = ({ producer, product }) => {
	Dialog.create({
		component: CommonConfirmationDialog,
		componentProps: {
			text: `Убрать из корзины: &laquo;${product.data.title}&raquo; ?`,
			headline: producer.display_name
		}
	}).onOk(() => {
		cartStore.removeProduct({
			producerId: producer.id,
			productId: product.data.id
		})
	})
}

const setProductAmount = ({producerId, product, amount}) => {
	if (parseInt(amount) > products.value[product.data.id].amount) {
		amount = products.value[product.data.id].amount
	}

	cartStore.setProductAmount({
		producerId,
		product: product.data,
		specificAmount: amount
	})
}

const paymentMethods = ref({})

const paymentProviders = ref({})

const isCartLoaded = ref(false)
const producers = ref({})
const products = ref({})

const totalPrice = computed(() =>
	cart.value.reduce((carry, producerSet) =>
		({...carry, [producerSet.producer_id]: producerSet.products.reduce(
			(c, productSet) => c + productSet.data.price * productSet.cart_amount, 0
		).toFixed(2)
		}), {})
)

const userStore = useUserStore()

const showOrderCheckoutDialog = (producerId) => {
	const producerOrderObject = cart.value.find((i) => i.producer_id === producerId)

	Dialog.create({
		component: OrderCheckoutDialog,
		componentProps: {
			totalPrice: totalPrice.value[producerId],
			orderData: producerOrderObject,
			paymentMethods: paymentMethods.value[producerId],
			paymentProviderId: paymentProviders.value[producerId]
		}
	}).onOk((response) => {
		// при ошибке на orderCheckoutDialog всегда возвращать обратно объект с ключом 'error'
		if (typeof response === "object" && response.error) {
			if (response.error.exception === "App\\Exceptions\\OrderInvalidItemsException") {
				Dialog.create({
					component: OrderInvalidProductDialog,
					componentProps: {
						message: response.error.message,
						invalidProducts: response.error.invalid_items
					}
				}).onDismiss(() => {
					invalidProductsAction(response.error.invalid_items)
				})

				return
			}

			notifyError(response.error.message)

			return
		}

		cartStore.clearCartProducer(producerId)

		Dialog.create({
			component: OrderCompletedDialog,
		})
	})
}

onMounted(() => {
	init()
})

const init = () => {
	if (!cart.value.length) return

	let cartProducers = cart.value.map((producerSet) =>
		producerSet.producer_id
	)

	let cartProducts = cart.value.reduce((carry, producerSet) =>
		[
			...carry,
			...producerSet.products.map((p) => ({
				id: p.data.id,
				price: p.data.price,
				amount: p.cart_amount
			}))
		], [])

	const promise = api.post("cart/load", {
		producers: cartProducers,
		products: cartProducts
	})

	promise.catch((error) => {
		if (
			typeof error.response.data === "object" &&
			error.response.data.exception === "App\\Exceptions\\OrderInvalidItemsException"
		) {
			Dialog.create({
				component: OrderInvalidProductDialog,
				componentProps: {
					message: error.response.data.message,
					invalidProducts: error.response.data.invalid_items
				}
			}).onDismiss(() => {
				invalidProductsAction(error.response.data.invalid_items)

				init()
			})

			return
		}

		notifyError(error.response.data.message)
	})

	promise.then((response) => {
		producers.value = response.data.producers.reduce((carry, p) =>
			({...carry, [p.id]: p}), {}
		)

		products.value = response.data.products.reduce((carry, p) =>
			({...carry, [p.id]: p}), {}
		)

		paymentMethods.value = response.data.producers.reduce((carry, p) =>
			({
				...carry,
				[p.id]: p.payment_methods
			}), {}
		)

		paymentProviders.value = response.data.producers.reduce((carry, p) =>
			({
				...carry,
				[p.id]: p.payment_provider_id
			}), {}
		)

		isCartLoaded.value = true
	})
}

const invalidProductsAction = (invalidProducts) => {
	invalidProducts.forEach((ip) =>
		cartStore.setCartProducerProductData({
			producerId: ip.producer_id,
			productId: ip.id,
			data: {
				price: ip.price,
				amount: ip.amount
			},
			cartAmount: ip.amount < ip.cart_amount ? ip.amount : ip.cart_amount
		})
	)

	LocalStorage.set("cart", cart.value)
}
</script>

<template>
	<div v-if="cart.length">
		<div
			v-for="(cartItem, cartItemIndex) in cart"
			:key="cartItem.producer_id"
			class="row justify-center q-mb-xs"
		>
			<q-card class="col">
				<q-card-section>
					<span
						v-if="isCartLoaded"
						class="text-h6"
					>
						{{ producers[cartItem.producer_id].display_name }}
					</span>
					<q-spinner
						v-else
						color="primary"
						size="md"
					/>
				</q-card-section>
				<q-separator />
				<q-card-section
					v-for="(product, productIndex) in cartItem.products"
					:key="productIndex"
					class="row q-pa-lg q-hoverable"
				>
					<span class="q-focus-helper"></span>
					<div class="col-xs-12 col-sm-6 self-center">
						<q-item>
							<q-item-section>
								<q-item-label class="text-body1">
									{{ product.data.title }}
								</q-item-label>
								<q-item-label
									v-if="isCartLoaded && !products[product.data.id].amount"
									class="text-subtitle2 text-grey"
								>
									Закончился
								</q-item-label>
							</q-item-section>
						</q-item>
					</div>
					<div class="col-xs-10 col-sm self-center">
						<q-input
							dense
							filled
							:disable="!isCartLoaded || (isCartLoaded && !products[product.data.id].amount)"
							:model-value="cart[cartItemIndex].products[productIndex].cart_amount"
							@update:model-value="setProductAmount({
								producerId: cart[cartItemIndex].producer_id,
								product: cart[cartItemIndex].products[productIndex],
								amount: $event
							})"
							type="number"
							:bg-color="
								isCartLoaded && cart[cartItemIndex].products[productIndex].cart_amount > products[product.data.id].amount ? 'negative': ''
							"
							:input-class="[
								{'text-center': true},
								{'text-white': isCartLoaded && cart[cartItemIndex].products[productIndex].cart_amount > products[product.data.id].amount}
							]"
						>
							<template v-slot:before>
								<q-btn
									icon="remove"
									size="md"
									color="primary"
									:disable="!isCartLoaded || (isCartLoaded && !products[product.data.id].amount)"
									@click="decreaseAmount({
										producerId: cart[cartItemIndex].producer_id,
										product: cart[cartItemIndex].products[productIndex]
									})"
									class="full-height"
								/>
							</template>
							<template v-slot:after>
								<q-btn
									icon="add"
									size="md"
									color="primary"
									:disable="!isCartLoaded || (isCartLoaded && !products[product.data.id].amount)"
									@click="addAmount({
										producerId: cart[cartItemIndex].producer_id,
										product: cart[cartItemIndex].products[productIndex]
									})"
									class="full-height"
								/>
							</template>
						</q-input>
					</div>
					<div class="col-xs-2 col-sm-auto self-center text-right q-pl-lg">
						<q-icon
							name="close"
							size="md"
							class="cursor-pointer"
							color="grey-8"
							@click="removeProduct({
								producer: producers[cartItem.producer_id],
								product: cart[cartItemIndex].products[productIndex]
							})"
						/>
					</div>
				</q-card-section>
				<q-separator />
				<q-card-section class="q-pa-lg">
					<div class="row">
						<div class="col-12 text-right q-pb-sm text-h6">
							{{ totalPrice[cartItem.producer_id] }} ₽
						</div>
						<div class="col-12">
							<q-btn
								class="q-py-lg full-width"
								label="Оформить заказ"
								color="primary"
								@click="showOrderCheckoutDialog(cartItem.producer_id)"
								:disable="!isCartLoaded || hasOnlyAbsentProducts[cartItem.producer_id]"
							/>
						</div>
					</div>
				</q-card-section>
			</q-card>
		</div>
	</div>
	<div
		v-else
		class="fit"
	>
		<EmptyCart />
	</div>
</template>
