<script setup>
import { ref } from "vue"
import { useRouter } from "vue-router"
import { api } from "src/boot/axios.js"

const $router = useRouter()

const backendServer = process.env.BACKEND_SERVER

const products = ref([])

const scrollComponent = ref(null)

const load = async (index, done) => {
	const limit = 5

	let params = {
		offset: products.value.length,
		categories: props.categories
	}

	const response = await api.get(
		`producers/${selectedProducer.value.id}/products`,
		{ params }
	)

	products.value = [...products.value, ...response.data]

	if (response.data.length < limit && scrollComponent.value) {
		scrollComponent.value.stop()
	}

	if (response.data.length < limit && scrollComponent.value) {
		scrollComponent.value.stop()
	}

	isInitializing.value = false

	done()
}
</script>

<template>
	<q-infinite-scroll
		ref="scrollComponent"
		@load="load"
	>
		<template #default>
			<transition-group
				enter-active-class="animated fadeInDown"
				appear
			>
				<q-card
					v-for="product in products"
					:key="product.id"
					class="row product__card product__card_list cursor-pointer"
					@click="$router.push({
						name: 'producer_products_detail',
						params: {
							producer_id: $router.currentRoute.value.query.pid,
							product_id: product.id
						}
					})"
				>
					<div class="column no-wrap full-width">
						<div class="col-grow row">
							<div class="col">
								<q-img
									no-spinner
									class="product__card-image fit"
									:src="product.thumbnail ? `${backendServer}/storage/${product.thumbnail.path}` : '/no-image.png'"
									fit="cover"
									:ratio="16/9"
								/>
							</div>
						</div>
						<q-separator v-if="!product.tags.length" />
						<div
							v-if="product.tags.length"
							class="col-shrink row q-pa-xs"
						>
							<q-chip
								v-for="tag in product.tags"
								:key="tag.id"
								class="col-shrink"
							>
								{{ tag.name }}
							</q-chip>
						</div>
						<q-separator v-if="product.tags.length" />
						<div class="col row">
							<span class="text-h6 q-pa-md">{{ product.title }}</span>
						</div>
					</div>
				</q-card>
			</transition-group>
		</template>
		<template #loading>
			<div class="row justify-center q-my-md">
				<q-linear-progress
					indeterminate
					rounded
					color="grey-4"
				/>
			</div>
		</template>
	</q-infinite-scroll>
</template>
