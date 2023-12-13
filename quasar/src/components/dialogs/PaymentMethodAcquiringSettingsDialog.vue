<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
		:maximized="isMobileWidthThreshold"
		transition-show="slide-up"
		transition-hide="slide-down"
	>
		<q-card
			class="q-dialog-plugin q-pa-md"
			style="min-height:400px;overflow-x: hidden"
		>
			<div
				v-if="isMobileWidthThreshold"
				class="row q-mb-sm"
			>
				<div class="col">
					<q-icon
						v-if="!selectedPaymentProvider"
						v-close-popup
						name="keyboard_backspace"
						size="md"
						class="cursor-pointer"
					/>
					<q-icon
						v-else
						name="keyboard_backspace"
						size="md"
						class="cursor-pointer"
						@click="selectedPaymentProvider = null"
					/>
				</div>
			</div>

			<transition enter-active-class="animated fadeInLeft">
				<q-list
					v-if="!selectedPaymentProvider"
					separator
				>
					<q-item
						v-for="(name, providerId) in PAYMENT_PROVIDER_NAMES"
						:key="providerId"
						clickable
						class="bg-primary text-white rounded-borders q-mb-xs"
						@click="selectedPaymentProvider = providerId"
					>
						<q-item-section class="text-body1">
							{{ name }}
						</q-item-section>
					</q-item>
				</q-list>
			</transition>
			<transition enter-active-class="animated fadeInRight">
				<div v-if="!!selectedPaymentProvider">
					<div
						v-if="!isMobileWidthThreshold"
						class="row q-mb-sm"
					>
						<div class="col">
							<q-icon
								name="keyboard_backspace"
								size="md"
								class="cursor-pointer"
								@click="selectedPaymentProvider = null"
							/>
						</div>
					</div>

					<div class="rounded-borders bg-primary text-white q-pa-md q-mb-md text-body1">
						{{ PAYMENT_PROVIDER_NAMES[selectedPaymentProvider] }}
					</div>

					<q-form
						@submit="setPaymentProvider"
					>

						<q-input
							filled
							label="ID магазина"
							class="q-mb-xs"
						/>

						<q-input
							filled
							label="Секретный ключ"
						/>

						<q-btn
							label="Продолжить"
							type="submit"
							color="primary"
							class="q-pa-lg q-mt-md full-width text-body1"
							:loading="isLoading"
						/>
					</q-form>
				</div>
			</transition>
		</q-card>
	</q-dialog>
</template>

<script setup>
import { useDialogPluginComponent } from "quasar"
import { ref, computed } from "vue"
import { useQuasar } from "quasar"
import { api } from "src/boot/axios"
import { PAYMENT_PROVIDER_NAMES } from "src/const/paymentProviders.js"

defineEmits([
	...useDialogPluginComponent.emits,
])

const { dialogRef, onDialogHide, onDialogOK } = useDialogPluginComponent()

const $q = useQuasar()
const isMobileWidthThreshold = computed(() => $q.screen.width < $q.screen.sizes.md)

const selectedPaymentProvider = ref(null)

const isLoading = ref(false)

const setPaymentProvider = () => {
	isLoading.value = true
}
</script>
