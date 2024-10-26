<script setup>
import { useDialogPluginComponent } from "quasar"

const props = defineProps({
	message: String,
	invalidProducts: Array,
	producerId: Number
})

defineEmits([
	...useDialogPluginComponent.emits,
])

const { dialogRef, onDialogHide, onDialogOK } = useDialogPluginComponent()
</script>

<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
	>
		<q-card class="q-dialog-plugin q-px-sm">
			<q-card-section class="row">
				<div class="col-12 text-center text-h5">
					{{ message }}
				</div>
				<div class="col-12 q-py-lg">
					<q-list separator>
						<q-item
							v-for="invalidProduct in invalidProducts"
							:key="invalidProduct.id"
							class="q-px-xs"
						>
							<div class="col-12">
								<div class="row">
									<div class="col-12 text-h5">
										<q-badge :label="invalidProduct.producer" />
									</div>
									<div class="col-12 text-h5">
										{{ invalidProduct.title }}
									</div>
									<div
										v-if="invalidProduct.cart_amount > invalidProduct.amount"
										class="col-12 text-h6 text-grey-7"
									>
										<div class="row justify-between">
											<div class="col">
												осталось:
											</div>
											<div class="col text-right">
												{{ invalidProduct.amount }}
											</div>
										</div>
									</div>
									<div
										v-if="invalidProduct.cart_price !== invalidProduct.price"
										class="col-12 text-h6 text-grey-7"
									>
										<div class="row justify-between">
											<div class="col">
												стоимость:
											</div>
											<div class="col text-right">
												{{ invalidProduct.cart_price }}<small>₽</small>
												<q-icon name="arrow_right_alt" />
												{{ invalidProduct.price }}<small>₽</small>
											</div>
										</div>
									</div>
								</div>
							</div>
						</q-item>
					</q-list>
				</div>
				<div class="col-12">
					<q-btn
						label="Пересчитать"
						color="primary"
						class="q-pa-md full-width"
						@click="onDialogOK"
					/>
				</div>
			</q-card-section>
		</q-card>
	</q-dialog>
</template>
