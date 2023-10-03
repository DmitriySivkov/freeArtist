<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
	>
		<q-card class="q-dialog-plugin q-pa-md">
			<q-card-section class="row">
				<div class="col-12 text-center text-h5">
					{{ message }}
				</div>
				<div class="col-12 q-py-lg">
					<q-list separator>
						<q-item
							v-for="invalidProduct in invalidProducts"
							:key="invalidProduct.id"
						>
							<div class="col-12">
								<div class="row">
									<div class="col-12 text-h5">
										{{ invalidProduct.title }}
									</div>
									<div class="col-12 text-h6 text-grey-7">
										<div class="row justify-between">
											<div class="col">
												осталось:
											</div>
											<div class="col text-right">
												{{ invalidProduct.amount }}
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
						label="Продолжить"
						color="primary"
						class="q-pa-lg full-width"
						@click="onDialogOK"
					/>
				</div>
			</q-card-section>
		</q-card>
	</q-dialog>
</template>

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
