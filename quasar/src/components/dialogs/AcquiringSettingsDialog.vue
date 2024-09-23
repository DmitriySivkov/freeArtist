<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
		:maximized="isMobileWidthThreshold"
		transition-show="slide-up"
		transition-hide="slide-down"
	>
		<q-card
			class="q-dialog-plugin column q-pa-md bg-grey-4"
			:class="{[$style['half-height']]: !isMobileWidthThreshold }"
			style="overflow-x:hidden"
		>
			<div
				v-if="isMobileWidthThreshold && !selectedPaymentProvider"
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
					class="col column"
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

<script setup>
// todo - fix jumping on transition
// todo - добавить в ProducerAcquiringSettingsProviderSetup чекбокс "активный провайдер"
import { useDialogPluginComponent } from "quasar"
import { ref, computed, onMounted } from "vue"
import { useQuasar } from "quasar"
import { api } from "src/boot/axios"
import { useNotification } from "src/composables/notification"
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

const $q = useQuasar()
const isMobileWidthThreshold = computed(() => $q.screen.width < $q.screen.sizes.md)

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

<style lang="scss" module>
.half-height {
	height: 50vh;
}
</style>
