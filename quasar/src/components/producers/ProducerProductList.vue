<template>
	<div class="absolute column fit">
		<div
			v-if="!isLoading"
			class="col"
		>
			<q-card
				square
				class="bg-primary"
			>
				<template
					v-for="(product, index) in products"
					:key="index"
				>
					<q-card-section
						horizontal
						class="justify-between"
						:class="{'no-pointer-events': product.is_loading}"
						style="border-bottom:1px solid rgba(255,255,255,0.7)"
					>
						<div
							class="col-xs-9 col-md-11 cursor-pointer q-hoverable"
							@click="show(product)"
						>
							<span class="q-focus-helper"></span>
							<div class="row full-height items-center">
								<div class="col-xs-3 col-md-1 text-center">
									<q-icon
										size="1.7em"
										color="white"
										name="edit"
									/>
								</div>
								<div class="col text-white">
									{{ product.title }}
								</div>
							</div>
						</div>
						<div class="col-grow">
							<q-btn
								flat
								square
								icon="more_vert"
								text-color="white"
								class="full-width q-py-lg"
							>
								<q-menu fit>
									<q-list>
										<q-item
											clickable
											v-close-popup
											:disable="!isAbleToManageProduct"
											@click="showDeleteDialog(product)"
										>
											<q-item-section class="text-center">
												Удалить
											</q-item-section>
										</q-item>
									</q-list>
								</q-menu>
							</q-btn>
						</div>
						<q-inner-loading :showing="product.is_loading">
							<q-spinner-gears
								size="42px"
								color="primary"
							/>
						</q-inner-loading>
					</q-card-section>
				</template>
			</q-card>
			<q-page-sticky
				v-if="isAbleToManageProduct"
				position="bottom-right"
				class="transform-none"
				:offset="[18,18]"
			>
				<q-btn
					:to="{name:'personal_producer_products_detail_create'}"
					round
					size="1.5em"
					icon="add"
					color="primary"
				/>
			</q-page-sticky>
		</div>
		<div
			v-else
			class="column fit"
		>
			<q-skeleton
				v-for="i in 5"
				:key="i"
				type="QInput"
				class="col-1 bg-primary"
				bordered
				square
				style="border-bottom:1px solid rgba(255,255,255,0.7)"
			/>
		</div>
	</div>
</template>

<script setup>
import { ref, onMounted } from "vue"
import { Dialog } from "quasar"
import { useRouter } from "vue-router"
import { useProducerStore } from "src/stores/producer"
import { useNotification } from "src/composables/notification"
import { api } from "src/boot/axios"

const props = defineProps({
	isAbleToManageProduct: Boolean
})

const $router = useRouter()
const producer_store = useProducerStore()
const { notifySuccess, notifyError } = useNotification()

const products = ref([])
const isLoading = ref(true)

const show = (product) => {
	$router.push({
		name:"personal_producer_products_detail_show",
		params: {
			product_id: product.id
		}
	})
}

const showDeleteDialog = (product) => {
	Dialog.create({
		title: "Подтверждение",
		message: `Удалить: ${product.title} ?`,
		cancel: true,
	}).onOk(() => {
		product.is_loading = true

		const promise = deleteProduct(product.id)

		promise.then(() => {
			const index = products.value.findIndex((p) => p.id === product.id)

			products.value.splice(index, 1)

			notifySuccess(`Продукт «${product.title}» успешно удалён`)
		})

		promise.catch((error) => {
			delete (product.is_loading)

			notifyError(error.response.data)
		})
	})
}

const deleteProduct = (product_id) => {
	return api.delete(`personal/products/${product_id}`,{ data: { product_id } })
}

onMounted(() => {
	const promise = api.get("personal/producers/" + $router.currentRoute.value.params.producer_id + "/products")

	promise.then((response) => {
		products.value = response.data
	})

	promise.catch((error) => {
		notifyError(error.response.data)
	})

	promise.finally(() => isLoading.value = false)
})

</script>
