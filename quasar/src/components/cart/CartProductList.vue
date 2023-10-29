<template>
	<div
		v-if="cart.length"
		class="fit"
	>
		<div
			v-for="(cartItem, cartItemIndex) in cart"
			:key="cartItem.producer_id"
			class="row justify-center q-mb-xs"
		>
			<q-card class="col">
				<q-card-section>
					<span
						v-if="isCartChecked"
						class="text-h6"
					>
						{{ checkedProducers[cartItem.producer_id].display_name }}
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
						<span class="text-body1">{{ product.data.title }}</span>
					</div>
					<div class="col-xs-12 col-sm offset-sm-1">
						<q-input
							dense
							filled
							:disable="!isCartChecked"
							:model-value="cart[cartItemIndex].products[productIndex].cart_amount"
							@update:model-value="setProductAmount({
								producerId: cart[cartItemIndex].producer_id,
								product: cart[cartItemIndex].products[productIndex],
								amount: $event
							})"
							type="number"
							:bg-color="
								isCartChecked && cart[cartItemIndex].products[productIndex].cart_amount > checkedProducts[product.data.id].amount ? 'negative': ''
							"
							:input-class="[
								{'text-center': true},
								{'text-white': isCartChecked && cart[cartItemIndex].products[productIndex].cart_amount > checkedProducts[product.data.id].amount}
							]"
						>
							<template v-slot:before>
								<q-btn
									icon="remove"
									size="md"
									color="primary"
									:disable="!isCartChecked"
									@click="removeFromCart({
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
									:disable="!isCartChecked"
									@click="addToCart({
										producerId: cart[cartItemIndex].producer_id,
										product: cart[cartItemIndex].products[productIndex]
									})"
									class="full-height"
								/>
							</template>
						</q-input>
					</div>
				</q-card-section>
				<q-separator />
				<q-card-section class="q-pa-lg">
					<div
						v-if="$q.screen.width >= $q.screen.sizes.sm"
						class="row q-col-gutter-sm"
					>
						<div class="col-xs-12 col-sm-8">
							<div
								v-if="isCartChecked"
								class="row q-col-gutter-xs full-height"
							>
								<div
									v-for="method in paymentMethods[cartItem.producer_id].methods"
									:key="method.id"
									class="col-6 flex"
								>
									<q-card
										class="q-py-md full-width text-body1 text-center q-hoverable cursor-pointer"
										:class="{'bg-primary text-white': method.id === paymentMethods[cartItem.producer_id].selectedPaymentMethodId}"
										@click="selectPaymentMethod({
											producerId: cartItem.producer_id,
											methodId: method.id
										})"
									>
										<span class="q-focus-helper"></span>
										<div class="row">
											<q-card-section class="col-3">
												<q-radio
													:model-value="paymentMethods[cartItem.producer_id].selectedPaymentMethodId"
													checked-icon="radio_button_checked"
													unchecked-icon="radio_button_unchecked"
													:color="paymentMethods[cartItem.producer_id].selectedPaymentMethodId === method.id ? 'white' : 'black'"
													:name="`payment_method_${method.id}_producer_${cartItem.producer_id}`"
													:val="method.id"
													@update:model-value="selectPaymentMethod({
														producerId: cartItem.producer_id,
														methodId: $event
													})"
												/>
											</q-card-section>
											<q-card-section class="col-9 self-center">
												<span class="text-body1">{{ method.name }}</span>
											</q-card-section>
										</div>
									</q-card>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm">
							<div class="column full-height">
								<div class="col text-right text-h6">
									{{ totalPrice[cartItem.producer_id] }} ₽
								</div>
								<div class="col-shrink">
									<q-btn
										class="q-py-md full-width"
										label="Оформить заказ"
										color="primary"
										@click="makeNewOrder(cartItem.producer_id)"
										:disable="!isCartChecked || !!hasInvalidAmount[cartItem.producer_id]"
										:loading="isLoading"
									/>
								</div>
							</div>
						</div>
					</div>
					<div
						v-else
						class="row"
					>
						<div class="col-12 text-right q-pb-sm text-h6">
							{{ totalPrice[cartItem.producer_id] }} ₽
						</div>
						<div class="col-12 q-py-md">
							<div
								v-if="isCartChecked"
								class="row q-col-gutter-xs"
							>
								<div
									v-for="method in paymentMethods[cartItem.producer_id].methods"
									:key="method.id"
									class="col-12"
								>
									<q-card
										class="q-py-md text-body1 text-center q-hoverable cursor-pointer"
										:class="{'bg-primary text-white': method.id === paymentMethods[cartItem.producer_id].selectedPaymentMethodId}"
										@click="selectPaymentMethod({
											producerId: cartItem.producer_id,
											methodId: method.id
										})"
									>
										<span class="q-focus-helper"></span>
										<div class="row">
											<q-card-section class="col-3">
												<q-radio
													:model-value="paymentMethods[cartItem.producer_id].selectedPaymentMethodId"
													checked-icon="radio_button_checked"
													unchecked-icon="radio_button_unchecked"
													:color="paymentMethods[cartItem.producer_id].selectedPaymentMethodId === method.id ? 'white' : 'black'"
													:name="`payment_method_${method.id}_producer_${cartItem.producer_id}`"
													:val="method.id"
													@update:model-value="selectPaymentMethod({
														producerId: cartItem.producer_id,
														methodId: $event
													})"
												/>
											</q-card-section>
											<q-card-section class="col-9 self-center">
												<span class="text-body1">{{ method.name }}</span>
											</q-card-section>
										</div>
									</q-card>
								</div>
							</div>
						</div>
						<div class="col-12">
							<q-btn
								class="q-py-lg full-width"
								label="Оформить заказ"
								color="primary"
								@click="makeNewOrder(cartItem.producer_id)"
								:disable="!isCartChecked || !!hasInvalidAmount[cartItem.producer_id]"
								:loading="isLoading"
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

<script setup>
import { computed, ref, onMounted } from "vue"
import { useCartStore } from "src/stores/cart"
import { api } from "src/boot/axios"
import EmptyCart from "src/components/cart/EmptyCart.vue"
import { PAYMENT_METHODS } from "src/const/paymentMethods"
import { useNotification } from "src/composables/notification"
import AuthDialog from "src/components/dialogs/AuthDialog.vue"
import OrderInvalidProductDialog from "src/components/dialogs/OrderInvalidProductDialog.vue"
import { useUserStore } from "src/stores/user"
import { Dialog } from "quasar"

const { notifySuccess, notifyError } = useNotification()

const cartStore = useCartStore()
const cart = computed(() => cartStore.data)

const hasInvalidAmount = computed(() =>
	!isCartChecked.value ? {} :
		cart.value.reduce((carry, producerSet) =>
			({
				...carry,
				[producerSet.producer_id]:
				producerSet.products.filter((productSet) => productSet.cart_amount > checkedProducts.value[productSet.data.id].amount).length
			}), {})
)

function addToCart({producerId, product}) {
	if (product.cart_amount >= checkedProducts.value[product.data.id].amount) return

	cartStore.increaseProductAmount({
		producerId,
		product: product.data
	})
}

function removeFromCart({producerId, product}) {
	if (product.cart_amount === 0) return

	cartStore.decreaseProductAmount({
		producerId,
		productId: product.data.id
	})
}

function setProductAmount({producerId, product, amount}) {
	if (parseInt(amount) > checkedProducts.value[product.data.id].amount) {
		amount = checkedProducts.value[product.data.id].amount
	}

	cartStore.setProductAmount({
		producerId,
		product: product.data,
		specificAmount: amount
	})
}

const paymentMethods = ref({})

const selectPaymentMethod = ({ producerId, methodId }) => {
	paymentMethods.value[producerId].selectedPaymentMethodId = methodId
}

const isCartChecked = ref(false)
const checkedProducers = ref({})
const checkedProducts = ref({})

const totalPrice = computed(() =>
	cart.value.reduce((carry, producerSet) =>
		({...carry, [producerSet.producer_id]: producerSet.products.reduce(
			(c, productSet) => c + productSet.data.price * productSet.cart_amount, 0
		).toFixed(2)
		}), {})
)

const userStore = useUserStore()

const makeNewOrder = (producerId) => {
	if (userStore.is_logged) {
		orderAction(producerId)
	} else {
		Dialog.create({
			component: AuthDialog,
		}).onOk(() => {
			orderAction(producerId)
		})
	}
}

const isLoading = ref(false)

function orderAction(producerId) {
	let order = Object.assign({}, cart.value.find((item) => item.producer_id === producerId))

	order.payment_method = paymentMethods.value[producerId].selectedPaymentMethodId

	order.order_products = order.products.map((p) => ({
		product_id: p.data.id,
		amount: p.cart_amount
	}))

	isLoading.value = true

	const promise = api.post("personal/orders", {
		...order
	})

	promise.then((response) => {
		cartStore.clearCartProducer(producerId)

		notifySuccess(response.data.message)
	})

	promise.catch((error) => {
		if (
			typeof error.response.data === "object" &&
			error.response.data.hasOwnProperty("invalid_items")
		) {
			Dialog.create({
				component: OrderInvalidProductDialog,
				componentProps: {
					message: error.response.data.message,
					invalidProducts: error.response.data.invalid_items
				}
			}).onDismiss(() => {
				isCartChecked.value = false
				init()
			})

			return
		}

		notifyError(error.response.data.message)
	})

	promise.finally(() => isLoading.value = false)
}

onMounted(() => {
	init()
})

function init() {
	if (!cart.value.length) return

	let producers = cart.value.map((producerSet) => producerSet.producer_id)

	let products = cart.value.reduce((carry, producerSet) =>
		[
			...carry,
			...producerSet.products.map((productSet) => productSet.data.id)
		], [])

	const promise = api.post("cart/checkProducts", {
		producers,
		products
	})

	promise.then((response) => {
		checkedProducers.value = response.data.producers.reduce((carry, p) =>
			({...carry, [p.id]: p}), {}
		)
		checkedProducts.value = response.data.products.reduce((carry, p) =>
			({...carry, [p.id]: p}), {}
		)

		paymentMethods.value = response.data.producers.reduce((carry, p) =>
			({
				...carry,
				[p.id]: {
					selectedPaymentMethodId: p.payment_methods.find((pm) => pm.id === PAYMENT_METHODS.CASH).id,
					methods: p.payment_methods
				}
			}), {}
		)

		isCartChecked.value = true
	})
}
</script>
