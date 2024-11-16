<script setup>
import { useRouter } from "vue-router"
import { ref, watch, onMounted } from "vue"
import { scroll } from "quasar"
import { useScreen } from "src/composables/screen.js"

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

const { isSmallScreen } = useScreen()

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
				<div
					v-if="producer.logo"
					class="row items-center"
				>
					<div class="col text-center q-mx-xs q-px-xs full-height content-center product__card_title-container">
						<div
							class="text-white product__card_title text-h6"
							:class="{'text-body1': isSmallScreen}"
						>
							{{ producer.display_name }}
						</div>
					</div>
					<div class="col-5">
						<q-img
							no-spinner
							class="rounded-borders"
							:src="`${backendServer}/storage/${producer.logo.path}`"
							fit="contain"
						/>
					</div>
				</div>
				<div
					v-else
					class="row items-center"
				>
					<div class="col fit">
						<div class="row fit relative-position">
							<div class="text-center full-height content-center product__card_title-container absolute">
								<div
									class="text-white product__card_title text-h6"
									:class="{'text-body1': isSmallScreen}"
								>
									{{ producer.display_name }}
								</div>
							</div>
						</div>
					</div>
					<div class="col-5">
						<div class="holder">
							<div class="inner-holder"></div>
						</div>
					</div>
				</div>
				<div class="row">
					<!--					<div-->
					<!--						class="col-12 cursor-pointer"-->
					<!--						@click="showProducts(producer.id)"-->
					<!--					>-->
					<!--						<q-img-->
					<!--							no-spinner-->
					<!--							class="product__card-image fit"-->
					<!--							:src="producer.logo ? `${backendServer}/storage/${producer.logo.path}` : '/no-image.png'"-->
					<!--							:ratio="16/9"-->
					<!--						/>-->
					<!--					</div>-->
					<q-carousel
						:ref="el => carousel.push(el)"
						v-model="slide[producer.id]"
						class="col-12 bg-grey-4 product__card-carousel"
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
			</div>
		</q-card>
	</transition-group>
</template>

<style>
.holder {
	padding-bottom:56.25%;
	height:0;
	position:relative;
}
.inner-holder {
	width:100%;
	height:100%;
	display:block;
	position:absolute;
}
</style>
