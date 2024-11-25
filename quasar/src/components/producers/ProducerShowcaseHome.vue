<script setup>
import { ref, computed, watch } from "vue"
import { api } from "src/boot/axios"
import { useCartStore } from "src/stores/cart"
import { useUserStore } from "src/stores/user"
import { useMiscStore } from "src/stores/misc"
import { debounce } from "lodash"
import { useRouter } from "vue-router"
import { HOME_VIEW_TYPES } from "src/const/homeViewTypes"
import ProducerPublicList from "src/components/producers/ProducerPublicList.vue"
import ProducerPublicProductList from "src/components/producers/ProducerPublicProductList.vue"

const $router = useRouter()

const cartStore = useCartStore()
const userStore = useUserStore()
const miscStore = useMiscStore()

const userLocation = computed(() => userStore.location)
const userRange = computed(() => userStore.location_range)

const producers = computed(() => miscStore.homePageProducers)
const selectedProducer = computed(() => miscStore.homePageSelectedProducer)
const categories = computed(() => miscStore.homePageSelectedCategories)

const isProductsView = computed(() =>
	$router.currentRoute.value.query?.view === String(HOME_VIEW_TYPES.PRODUCTS)
)

const cart = computed(() => cartStore.data)

const scrollComponent = ref(null)

const fetchProducers = async() => {
	const limit = 5

	if (!producers.value.length) {
		isInitializing.value = true
	}

	const response = await api.get("producers", {
		params: {
			offset: !isInitializing.value ?
				producers.value.length : 0,
			location: userLocation.value,
			range: userRange.value,
			categories: categories.value
		}
	})

	miscStore.setHomePageProducers(response.data)

	if (response.data.length < limit && scrollComponent.value) {
		scrollComponent.value.stop()
	}
}

const fetchProducts = async() => {
	const limit = 5

	let params = {
		offset: producerProducts.value.length,
		categories: categories.value
	}

	const response = await api.get(
		`producers/${selectedProducer.value}/products`,
		{ params }
	)

	producerProducts.value = [...producerProducts.value, ...response.data]

	if (response.data.length < limit && scrollComponent.value) {
		scrollComponent.value.stop()
	}
}

const producerProducts = ref([])

const showProducerProductsIndex = (producerId) => {
	scrollComponent.value.stop() // in case infinite scroll is still uploading stuff

	$router.push({
		query: { view: HOME_VIEW_TYPES.PRODUCTS }
	})

	miscStore.setHomePageSelectedProducer(producerId)

	producerProducts.value = []

	reinit()
}

const showProducerProductsDetail = ({ producerId, productId }) => {
	scrollComponent.value.stop() // in case infinite scroll is still uploading stuff

	$router.push({
		name: "producer_public_products_detail",
		params: {
			producer_id: producerId,
			product_id: productId
		}
	})
}

const checkProducerList = () => {
	if (!producers.value.length && scrollComponent.value) {
		reinit()
	}
}

const load = async (index, done) => {
	if (!isProductsView.value) {
		await fetchProducers()
	} else {
		await fetchProducts()
	}

	isInitializing.value = false

	done()
}

const isInitializing = ref(false)

const reinit = debounce(() => {
	scrollComponent.value.stop()
	scrollComponent.value.resume()
	scrollComponent.value.poll()
}, 300)

watch([
	() => userRange.value,
	() => userLocation.value,
	() => categories.value
],() => {
	miscStore.setHomePageVerticalScroll(0)
	miscStore.emptyHomePageProducers()
	producerProducts.value = []

	isInitializing.value = true
	reinit()
},{
	deep: true
})
</script>

<template>
	<q-infinite-scroll
		ref="scrollComponent"
		:offset="100"
		@load="load"
	>
		<template v-if="!isProductsView && !isInitializing">
			<ProducerPublicList
				:producers="producers"
				@show-index="showProducerProductsIndex"
				@show-detail="showProducerProductsDetail"
			/>
		</template>
		<template v-if="isProductsView && !isInitializing">
			<ProducerPublicProductList
				:products="producerProducts"
				:selected-producer="selectedProducer"
				@unmount="checkProducerList"
			/>
		</template>
		<template v-slot:loading>
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
