<script setup>
import { computed, ref, watch } from "vue"
import { api } from "src/boot/axios"
import { useCartStore } from "src/stores/cart"
import { useUserStore } from "src/stores/user"
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

const userLocation = computed(() => userStore.location)

const userRange = computed(() => userStore.location_range)

const producers = ref([])

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

	producers.value = [...producers.value, ...response.data]

	if (response.data.length < limit && scrollComponent.value) {
		scrollComponent.value.stop()
	}
}

const fetchProducts = async() => {
	const limit = 5

	let params = {
		offset: producerProducts.value.length
	}

	const response = await api.get(
		`producers/${producerId.value}/products`,
		{ params }
	)

	producerProducts.value = [...producerProducts.value, ...response.data]

	if (response.data.length < limit && scrollComponent.value) {
		scrollComponent.value.stop()
	}
}

const producerId = computed(() =>
	$router.currentRoute.value.query.pid
)

const producerProducts = ref([])

const navigationProducer = ref(null)

const showProducerProducts = ({ producerId, scrollPosition }) => {
	$router.push({ query: { pid: producerId } })
	navigationProducer.value = { producerId, scrollPosition }

	producerProducts.value = []

	reinit()
}

const load = async (index, done) => {
	if (!producerId.value) {
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
	producers.value = []
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
		<template v-if="producers.length && !isInitializing && !producerId">
			<ProducerPublicListHome
				:producers="producers"
				:navigation-producer="navigationProducer"
				@show="showProducerProducts"
			/>
		</template>
		<template v-if="producerProducts.length && !isInitializing && producerId">
			<ProducerPublicProductListHome
				:products="producerProducts"
			/>
		</template>
		<template v-slot:loading>
			<div class="row justify-center q-my-md">
				<q-spinner-dots
					size="md"
					color="white"
				/>
			</div>
		</template>
	</q-infinite-scroll>
</template>
