<script setup>
import ProducerPublicProductDetailSkeleton from "src/components/skeletons/ProducerPublicProductDetailSkeleton.vue"
import { ref, computed, onMounted } from "vue"
import { api } from "src/boot/axios"
import { useRouter } from "vue-router"
import { useCartStore } from "src/stores/cart"

const $router = useRouter()
const backendServer = process.env.BACKEND_SERVER

const cart = useCartStore()
const cartProducer = computed(() =>
	cart.data.find((item) =>
		item.producer_id === Number($router.currentRoute.value.params.producer_id)
	)
)
const cartProducerProduct = computed(() =>
	cartProducer.value ?
		cartProducer.value.products.find(
			(p) => p.data.id === Number($router.currentRoute.value.params.product_id)
		) : null
)

const addToCart = () => {
	if (cartProductAmount.value >= product.value.amount) return

	cart.increaseProductAmount({
		producerId: $router.currentRoute.value.params.producer_id,
		product: product.value
	})
}

const removeFromCart = () => {
	if (cartProductAmount.value === 0) return

	cart.decreaseProductAmount({
		producerId: $router.currentRoute.value.params.producer_id,
		productId: $router.currentRoute.value.params.product_id
	})
}

const setProductAmount = (amount) => {
	if (parseInt(amount) > product.value.amount) {
		amount = product.value.amount
	}

	cart.setProductAmount({
		producerId: $router.currentRoute.value.params.producer_id,
		product: product.value,
		specificAmount: amount
	})
}

const product = ref(null)

const cartProductAmount = computed(() =>
	cartProducerProduct.value ? cartProducerProduct.value.cart_amount : 0
)

const slide = ref(null)

onMounted(() => {
	const promise = api.get(
		`producers/${$router.currentRoute.value.params.producer_id}/products/${$router.currentRoute.value.params.product_id}`
	)

	promise.then((response) => {
		product.value = response.data

		slide.value = product.value.thumbnail ?
			product.value.thumbnail.id :
			product.value.images[0].id
	})
})
</script>

<template>
	<q-page class="row justify-center bg-primary">
		<div
			v-if="product"
			class="col-xs-12 col-sm-9 col-md-8 col-lg-7 col-xl-6 relative-position"
		>
			<q-card class="absolute fit">
				<q-responsive :ratio="16/9">
					<q-carousel
						swipeable
						animated
						transition-prev="slide-right"
						transition-next="slide-left"
						:navigation="product.images.length > 1"
						navigation-icon="radio_button_unchecked"
						navigation-active-icon="radio_button_checked"
						:arrows="product.images.length > 1"
						v-model="slide"
						infinite
					>
						<q-carousel-slide
							v-for="image in product.images"
							:key="image.id"
							:name="image.id"
							:img-src="`${backendServer}/storage/${image.path}`"
						/>
					</q-carousel>
				</q-responsive>
				<div class="row">
					<q-card
						bordered
						class="col-12"
					>
						<q-card-section>
							<span class="text-h5">{{ product.title }}</span>
						</q-card-section>

						<q-card-section class="row justify-between q-py-xs q-mb-md">
							<div class="col-4">
								<span class="text-h6 text-primary">{{ product.price }} ₽</span>
							</div>
							<div class="col-xs col-sm-5 col-lg-4">
								<q-input
									dense
									filled
									:model-value="cartProductAmount"
									@update:model-value="setProductAmount"
									type="number"
									input-class="text-center"
								>
									<template v-slot:before>
										<q-btn
											icon="remove"
											size="md"
											color="primary"
											@click="removeFromCart"
											class="full-height"
										/>
									</template>
									<template v-slot:after>
										<q-btn
											icon="add"
											size="md"
											color="primary"
											@click="addToCart"
											class="full-height"
										/>
									</template>
								</q-input>
							</div>
						</q-card-section>

						<q-card-section
							v-for="(ingr, index) in product.composition"
							:key="index"
							class="q-px-sm q-py-sm q-hoverable"
						>
							<span class="q-focus-helper"></span>
							<q-card-section class="q-py-none">
								<span class="text-h6">{{ ingr.name }}</span>
							</q-card-section>

							<q-separator
								v-if="ingr.description"
								inset
							/>

							<q-card-section class="q-py-xs">
								{{ ingr.description }}
							</q-card-section>
						</q-card-section>
					</q-card>
				</div>
			</q-card>
		</div>
		<div
			v-else
			class="col-xs-12 col-sm-9 col-md-8 col-lg-7 col-xl-6"
		>
			<ProducerPublicProductDetailSkeleton />
		</div>
	</q-page>
</template>
