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
					<div class="col-xs-12 col-sm-7 self-center">
						<span class="text-body1">{{ product.data.title }}</span>
					</div>
					<div class="col-xs-12 col-sm">
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
								isCartChecked && cart[cartItemIndex].products[productIndex].cart_amount > checkedProducts[product.data.id].amount ? 'red-8': ''
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

const cartStore = useCartStore()
const cart = computed(() => cartStore.data)

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

const isCartChecked = ref(false)
const checkedProducers = ref({})
const checkedProducts = ref({})

onMounted(() => {
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

		isCartChecked.value = true
	})
})

</script>
