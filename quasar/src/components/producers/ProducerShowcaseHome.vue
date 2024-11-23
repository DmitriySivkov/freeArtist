<script setup>
import { ref, computed, watch } from "vue"
import { api } from "src/boot/axios"
import { useCartStore } from "src/stores/cart"
import { useUserStore } from "src/stores/user"
import { useMiscStore } from "src/stores/misc"
import { debounce } from "lodash"
import { useRouter } from "vue-router"
import ProducerPublicListHome from "src/components/producers/ProducerPublicListHome.vue"
import ProducerPublicProductListHome from "src/components/producers/ProducerPublicProductListHome.vue"

const props = defineProps({
	categories: Array
})

const $router = useRouter()

const cartStore = useCartStore()
const userStore = useUserStore()
const miscStore = useMiscStore()

const userLocation = computed(() => userStore.location)

const userRange = computed(() => userStore.location_range)

const producers = computed(() => miscStore.homePageProducers)
const selectedProducer = computed(() => miscStore.homePageSelectedProducer)

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
			categories: props.categories
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
		categories: props.categories
	}

	const response = await api.get(
		`producers/${selectedProducer.value.id}/products`,
		{ params }
	)

	producerProducts.value = [...producerProducts.value, ...response.data]

	if (response.data.length < limit && scrollComponent.value) {
		scrollComponent.value.stop()
	}
}

const producerProducts = ref([])

const showProducerProductsIndex = ({ producerId, scrollPosition }) => {
	scrollComponent.value.stop() // in case infinite scroll is still uploading stuff

	miscStore.setHomePageSelectedProducer({ id: producerId, scrollPosition })

	producerProducts.value = []

	reinit()
}

const showProducerProductsDetail = ({ producerId, productId, scrollPosition }) => {
	scrollComponent.value.stop() // in case infinite scroll is still uploading stuff

	$router.push({
		name: "producer_public_products_detail",
		params: {
			producer_id: producerId,
			product_id: productId
		},
		query: {
			pid: producerId,
			sp: scrollPosition
		}
	})
}

const checkProducerList = () => {
	miscStore.setHomePageSelectedProducer({ id: null, scrollPosition: selectedProducer.value.scrollPosition })

	if (!producers.value.length && scrollComponent.value) {
		reinit()
	}
}

const load = async (index, done) => {
	if (!selectedProducer.value.id) {
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
	() => props.categories
],() => {
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
		<template v-if="!selectedProducer.id && !isInitializing">
			<ProducerPublicListHome
				:producers="producers"
				:selected-producer="selectedProducer"
				@show-index="showProducerProductsIndex"
				@show-detail="showProducerProductsDetail"
			/>
		</template>
		<template v-if="selectedProducer.id && !isInitializing">
			<ProducerPublicProductListHome
				:products="producerProducts"
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
