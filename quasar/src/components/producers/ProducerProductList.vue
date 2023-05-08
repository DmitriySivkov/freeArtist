<template>
	<div class="column full-height">
		<div class="col">
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
						:class="{'no-pointer-events': !!product.tmp_uuid}"
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
						<q-inner-loading :showing="!!product.tmp_uuid">
							<q-spinner-gears
								size="42px"
								color="primary"
							/>
						</q-inner-loading>
					</q-card-section>
					<q-separator dark />
				</template>
			</q-card>
		</div>
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
				color="secondary"
			/>
		</q-page-sticky>
	</div>
</template>

<script>
import { Dialog } from "quasar"
import { useRouter } from "vue-router"
import { useProducerStore } from "src/stores/producer"
import { useNotification } from "src/composables/notification"
export default {
	props: {
		products: {
			type: Array,
			default: () => []
		},
		isAbleToManageProduct: Boolean,
	},
	setup() {
		const $router = useRouter()
		const producer_store = useProducerStore()
		const { notifySuccess, notifyError } = useNotification()

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
				message: "Удалить: " + product.title + " ?",
				cancel: true,
			}).onOk(() => {
				let tmp_uuid = crypto.randomUUID()
				let producer_id = $router.currentRoute.value.params.producer_id

				producer_store.commitProducerProductFields({
					producer_id: parseInt(producer_id),
					product_id: product.id,
					fields: { tmp_uuid }
				})

				const promise = producer_store.deleteProducerProduct({
					product_id: product.id
				})

				promise.then(() => {
					producer_store.commitRemoveProducerProduct({
						producer_id: parseInt(producer_id),
						product_id: product.id
					})

					notifySuccess("Продукт «" + product.title + "» успешно удалён")
				})

				promise.catch((error) => {
					producer_store.commitProducerProductFields({
						producer_id: parseInt(producer_id),
						product_id: product.id,
						tmp_uuid,
						fields: {}
					})

					notifyError(error.response.data)
				})
			})
		}

		return {
			show,
			showDeleteDialog
		}
	}
}
</script>
