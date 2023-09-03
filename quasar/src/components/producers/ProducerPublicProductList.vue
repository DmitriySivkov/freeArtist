<template>
	<q-infinite-scroll
		ref="scrollComponent"
		@load="load"
	>
		<q-page>
			<template v-if="products.length && !isInitializing">
				<q-card
					v-for="product in products"
					:key="product.id"
					class="row product__card product__card_list"
				>
					<div class="column no-wrap full-width">
						<div class="col-grow row">
							<div class="col cursor-pointer">
								<q-img
									no-spinner
									class="product__card-image fit"
									:src="product.thumbnail ? `${backendServer}/storage/${product.thumbnail.path}` : '/no-image.png'"
									fit="cover"
									:ratio="16/9"
								/>
							</div>
						</div>
						<q-separator />
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
			</template>
			<template v-else-if="!products.length && !isInitializing">
				<!-- todo - nothing found -->
			</template>
			<template v-else>
				<ProducerPublicProductListSkeleton />
			</template>
		</q-page>
	</q-infinite-scroll>
</template>

<script setup>
import { api } from "src/boot/axios"
import { useRouter } from "vue-router"
import { computed, ref, watch } from "vue"
import { useCartStore } from "src/stores/cart"
import { useProducerStore } from "src/stores/producer"
import ProducerPublicProductListSkeleton from "src/components/skeletons/ProducerPublicProductListSkeleton.vue"
import { debounce } from "lodash"

const props = defineProps({
	selectedTags: {
		type: Array,
		default: () => ([])
	}
})

const $router = useRouter()
const cart_store = useCartStore()
const producerStore = useProducerStore()
const cart = computed(() => cart_store.data)
const backendServer = process.env.BACKEND_SERVER

const products = ref([])
const scrollComponent = ref(null)
const isInitializing = ref(true)

const load = async (index, done) => {
	await fetchProducts()

	isInitializing.value = false

	done()
}

const fetchProducts = async() => {
	const limit = 5

	const response = await api.get(`producers/${$router.currentRoute.value.params.producer_id}/products`, {
		params: {
			offset: products.value.length,
			tags: props.selectedTags.map((tag) => tag.id) // todo - pass tags as string - not as array
		}
	})

	products.value = [...products.value, ...response.data]

	if (response.data.length < limit && scrollComponent.value) {
		scrollComponent.value.stop()
	}
}

const reinit = debounce(() => {
	scrollComponent.value.stop()
	scrollComponent.value.resume()
	scrollComponent.value.poll()
}, 700)

watch(
	() => props.selectedTags,
	() => {
		products.value = []
		isInitializing.value = true
		reinit()
	}
)

</script>
