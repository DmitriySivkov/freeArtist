<template>
	<div class="absolute column full-width no-wrap">
		<div class="col-auto sticky__common_top">
			<div class="row full-height">
				<q-card
					square
					class="col-12 flex flex-center q-px-md q-py-lg bg-green-4 cursor-pointer q-hoverable"
					@click="$router.push({name:'personal_producer_products_detail_create'})"
				>
					<span class="q-focus-helper"></span>
					<span class="text-body1 text-white">Создать продукт</span>
				</q-card>
			</div>
		</div>
		<q-linear-progress
			v-if="isMounting"
			indeterminate
			color="primary"
		/>
		<div
			v-for="(product, index) in products"
			:key="index"
			class="col-xs-2 col-sm-1"
		>
			<q-card class="bg-primary full-height">
				<div class="row items-center full-height text-body1">
					<div
						class="col-xs-9 col-md-11 full-height cursor-pointer q-hoverable"
						@click="show(product)"
					>
						<span class="q-focus-helper"></span>
						<div class="row full-height items-center">
							<div class="col-xs-3 col-sm-2 col-md-1 text-center">
								<q-icon
									size="sm"
									color="white"
									name="edit"
								/>
							</div>
							<div class="col text-white">
								{{ product.title }}
							</div>
						</div>
					</div>
					<div class="col">
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
				</div>
			</q-card>
		</div>
	</div>
</template>

<script setup>
import { ref, onMounted } from "vue"
import { Dialog } from "quasar"
import { useRouter } from "vue-router"
import { useNotification } from "src/composables/notification"
import { api } from "src/boot/axios"

const props = defineProps({
	isAbleToManageProduct: Boolean
})

const $router = useRouter()
const { notifySuccess, notifyError } = useNotification()

const products = ref([])
const isMounting = ref(true)

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

	promise.finally(() => isMounting.value = false)
})

</script>
