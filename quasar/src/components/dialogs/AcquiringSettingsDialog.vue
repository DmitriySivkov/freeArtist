<script setup>
// todo - fix jumping on transition
import { useDialogPluginComponent } from "quasar"
import { ref, onMounted } from "vue"
import { api } from "src/boot/axios"
import { useNotification } from "src/composables/notification"
import { useScreen } from "src/composables/screen"
import ProducerAcquiringSettingsProviderList from "src/components/producers/producerAcquiringSettings/ProducerAcquiringSettingsProviderList.vue"
import ProducerAcquiringSettingsProviderSetup from "src/components/producers/producerAcquiringSettings/ProducerAcquiringSettingsProviderSetup.vue"


defineEmits([
	...useDialogPluginComponent.emits,
])

const { dialogRef, onDialogHide, onDialogOK } = useDialogPluginComponent()

const { notifyError } = useNotification()

const props = defineProps({
	producerId: Number
})

const { isSmallScreen } = useScreen()

const selectedPaymentProvider = ref(null)

const producerPaymentProviders = ref([])

const isMounting = ref(true)

const onSetupSuccess = (paymentProvider) => {
	// if theres any active providers
	onDialogOK(
		paymentProvider.is_active ?? producerPaymentProviders.value.find((pp) => pp.is_active)
	)
}

onMounted(() => {
	const promise = api.get(
		`personal/producers/${props.producerId}/payment-providers`,
	)

	promise.catch((error) => {
		notifyError(error.response.data)
	})

	promise.then((response) => {
		producerPaymentProviders.value = response.data
	})

	promise.finally(() => isMounting.value = false)
})
</script>

<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
		:maximized="isSmallScreen"
		transition-show="slide-up"
		transition-hide="slide-down"
	>
		<q-card
			class="q-dialog-plugin column q-pa-md bg-grey-4"
			:class="{[$style['half-height']]: !isSmallScreen }"
			style="overflow-x:hidden"
		>
			<div
				v-if="isSmallScreen && !selectedPaymentProvider"
				class="col-auto q-pa-none q-mb-lg text-right"
			>
				<q-icon
					v-close-popup
					name="close"
					size="md"
					class="cursor-pointer"
				/>
			</div>
			<transition
				mode="out-in"
				enter-active-class="animated fadeInLeft"
			>
				<div
					v-if="!selectedPaymentProvider"
					class="col"
				>
					<ProducerAcquiringSettingsProviderList
						v-model="selectedPaymentProvider"
						:producer-payment-providers="producerPaymentProviders"
					/>
				</div>
			</transition>
			<transition
				mode="out-in"
				enter-active-class="animated fadeInRight"
			>
				<div
					v-if="selectedPaymentProvider"
					class="col column no-wrap"
				>
					<ProducerAcquiringSettingsProviderSetup
						v-model="selectedPaymentProvider"
						:producer-id="props.producerId"
						:producer-payment-providers="producerPaymentProviders"
						@success="onSetupSuccess"
					/>
				</div>
			</transition>
			<q-inner-loading :showing="isMounting">
				<q-spinner-gears
					size="lg"
					color="primary"
				/>
			</q-inner-loading>
		</q-card>
	</q-dialog>
</template>

<style lang="scss" module>
.half-height {
	height: 50vh;
}
</style>
