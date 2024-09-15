<script setup>
import { useRouter } from "vue-router"
import { ref, watch, onMounted } from "vue"
import { scroll } from "quasar"

const props = defineProps({
	producers: {
		type: Array,
		default: () => []
	},
	navigationProducer: {
		type: Object,
		required: false,
		default: () => ({})
	}
})

const emit = defineEmits(["show"])

const $router = useRouter()

const backendServer = process.env.BACKEND_SERVER

const carousel = ref([])
const slide = ref(
	props.producers.reduce(
		(carry, p) => ({...carry, [p.id]: 1}), {}
	)
)

const { getScrollTarget, getVerticalScrollPosition, setVerticalScrollPosition } = scroll

const showProducts = (producerId) => {
	let target = getScrollTarget(
		document.getElementById(`pid-${producerId}`)
	)

	emit("show", {
		producerId,
		scrollPosition: getVerticalScrollPosition(target)
	})
}

onMounted(() => {
	if (!props.navigationProducer) return

	setVerticalScrollPosition(
		getScrollTarget(
			document.getElementById(`pid-${props.navigationProducer.producerId}`)
		),
		props.navigationProducer.scrollPosition,
		0
	)
})

watch(
	() => props.producers,
	(val, oldVal) => {
		slide.value = {
			...slide.value,
			...val.map((i) => i.id)
				.filter((pid) =>
					!oldVal.map(i => i.id).includes(pid)
				)
				.reduce((carry, pid) =>
					({...carry, [pid]: 1}), {}
				)
		}
	}
)

</script>

<template>
	<transition-group
		enter-active-class="animated fadeInDown"
		appear
	>
		<q-card
			v-for="(producer, i) in producers"
			:key="producer.id"
			class="row product__card_shrink q-mb-sm"
			:id="`pid-${producer.id}`"
		>
			<div class="column no-wrap full-width">
				<div class="col row">
					<div
						class="col-12 cursor-pointer"
						@click="showProducts(producer.id)"
					>
						<q-img
							no-spinner
							class="product__card-image fit"
							:src="producer.logo ? `${backendServer}/storage/${producer.logo.path}` : '/no-image.png'"
							:ratio="16/9"
						/>
					</div>
					<q-carousel
						:ref="el => carousel.push(el)"
						v-model="slide[producer.id]"
						class="col-12 bg-secondary product__card-carousel q-px-xs"
						transition-prev="slide-right"
						transition-next="slide-left"
						swipeable
						animated
						:draggable="false"
						keep-alive
					>
						<q-carousel-slide
							v-for="i in Math.ceil(producer.products.length/2)"
							:key="i"
							:name="i"
							class="q-pa-none overflow-hidden"
						>
							<div
								class="col row"
								:class="
									producer.products.length > 2 ?
										(
											i === 1 ? 'justify-start'
											: (i === Math.ceil(producer.products.length/2) ? 'justify-end' : 'justify-center')
										)
										: 'justify-center'
								"
							>
								<div class="col-10">
									<div
										class="row q-gutter-xs q-py-xs no-wrap"
										:class="
											producer.products.length > 2 ?
												(
													i === 1 ? 'justify-start'
													: (i === Math.ceil(producer.products.length/2) ? 'justify-end' : 'justify-center')
												)
												: 'justify-center'
										"
									>
										<q-card
											v-for="product in producer.products.slice((i-1)*3 - (i === 1 ? 0 : i), i*3 - (i === 1 ? 0 : i-1))"
											:key="product.thumbnail.id"
											class="col-6 full-height"
										>
											<q-img
												no-spinner
												:src="backendServer + '/storage/' + product.thumbnail.path"
											/>
										</q-card>
										<div
											v-if="
												producer.products.length > 2 &&
													producer.products.length % 2 !== 0 &&
													i === Math.ceil(producer.products.length/2)
											"
											class="col-6"
										></div>
									</div>
								</div>
							</div>
						</q-carousel-slide>

						<template #control>
							<q-carousel-control
								v-if="slide[producer.id] !== 1"
								position="left"
								:offset="[0, 0]"
								class="flex flex-center"
							>
								<q-btn
									push
									round
									dense
									color="orange"
									text-color="black"
									icon="arrow_left"
									@click="carousel[i].previous()"
								/>
							</q-carousel-control>

							<q-carousel-control
								v-if="producer.products.length > 2 && slide[producer.id] !== Math.ceil(producer.products.length/2)"
								position="right"
								:offset="[0, 0]"
								class="flex flex-center"
							>
								<q-btn
									push
									round
									dense
									color="orange"
									text-color="black"
									icon="arrow_right"
									@click="carousel[i].next()"
								/>
							</q-carousel-control>
						</template>
					</q-carousel>
				</div>
				<q-separator />
				<div class="col-grow row">
					<span class="text-h6 q-pa-md">{{ producer.display_name }}</span>
				</div>
			</div>
		</q-card>
	</transition-group>
</template>
