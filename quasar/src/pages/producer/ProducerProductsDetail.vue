<template>
	<div class="column no-wrap absolute fit">
		<div class="row justify-center full-height">
			<div
				v-if="product"
				class="col-xs-12 col-sm-9 col-md-8 col-lg-7 col-xl-6"
			>
				<q-responsive
					:ratio="16/9"
				>
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
				<div class="column fit">
					<q-card class="col-grow">
						<div class="row">
							<q-card class="col-12 full-height">
								<q-card-section>
									<span class="text-h5">{{ product.title }}</span>
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
			</div>
			<div
				v-else
				class="col-xs-12 col-sm-9 col-md-8 col-lg-7 col-xl-6 q-px-md"
			>
				<ProducerPublicProductDetailSkeleton />
			</div>
		</div>
	</div>
</template>

<script setup>
import ProducerPublicProductListFilter from "src/components/producers/ProducerPublicProductListFilter.vue"
import ProducerPublicProductList from "src/components/producers/ProducerPublicProductList.vue"
import ProducerPublicProductDetailSkeleton from "src/components/skeletons/ProducerPublicProductDetailSkeleton.vue"
import { ref, onMounted } from "vue"
import { api } from "src/boot/axios"
import { useRouter } from "vue-router"

const $router = useRouter()
const backendServer = process.env.BACKEND_SERVER

const product = ref(null)

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
