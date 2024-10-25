<script setup>
import { ref, onMounted } from "vue"
import { api } from "src/boot/axios"
import { PAYMENT_METHOD_NAMES, PAYMENT_METHODS } from "src/const/paymentMethods.js"
import { debounce } from "lodash"
import { Dialog } from "quasar"
import AcquiringSettingsDialog from "src/components/dialogs/AcquiringSettingsDialog.vue"
import { useNotification } from "src/composables/notification"

const props = defineProps({
	producerId: Number,
	isAbleToManagePaymentMethods: Boolean
})

const { notifyError } = useNotification()

const selectedPaymentMethods = ref(null)

const togglePaymentMethod = ({ pmid, val }) => {
	if (!props.isAbleToManagePaymentMethods) return

	if (pmid === PAYMENT_METHODS.CARD) {
		showAcquiringSettingsDialog()

		return
	}

	selectedPaymentMethods.value[pmid] = val

	setPaymentMethodsAction()
}

const setPaymentMethodsAction = debounce(() => {
	const promise = api.post(`/personal/producers/${props.producerId}/payment-methods`, {
		payment_methods: Object.keys(selectedPaymentMethods.value).filter((pm) => !!selectedPaymentMethods.value[pm])
	})

	promise.catch(() => {
		notifyError("Не удалось")
	})
}, 2000)

const showAcquiringSettingsDialog = () => {
	Dialog.create({
		component: AcquiringSettingsDialog,
		componentProps: {
			producerId: props.producerId
		}
	}).onOk((hasAnyActivePaymentProviders) => {
		selectedPaymentMethods.value[PAYMENT_METHODS.CARD] = hasAnyActivePaymentProviders

		setPaymentMethodsAction()
	})
}

const isMounting = ref(true)

onMounted(() => {
	const promise = api.get(`/personal/producers/${props.producerId}/payment-methods`)

	promise.then((response) => {
		selectedPaymentMethods.value = Object.keys(PAYMENT_METHOD_NAMES)
			.reduce((carry, pmid) =>
				({...carry, [pmid]: response.data.includes(Number(pmid))}), {})
	})

	promise.finally(() => isMounting.value = false)
})
</script>

<template>
	<div class="column q-gutter-xs q-pa-sm">
		<q-linear-progress
			v-if="isMounting"
			indeterminate
			color="primary"
		/>
		<q-card
			v-else
			v-for="(method, pmid) in PAYMENT_METHOD_NAMES"
			:key="pmid"
			class="col flex flex-center q-hoverable"
			:class="[
				{'payment-method-active': selectedPaymentMethods[pmid]},
				{'cursor-pointer': isAbleToManagePaymentMethods}
			]"
			@click="togglePaymentMethod({ pmid: Number(pmid), val:!selectedPaymentMethods[pmid] })"
		>
			<span class="q-focus-helper"></span>
			<div class="row full-width">
				<q-card-section class="col-3">
					<q-checkbox
						:model-value="selectedPaymentMethods[pmid]"
						color="primary"
						:toggle-indeterminate="false"
						:name="pmid"
					/>
				</q-card-section>
				<q-card-section class="col-6 self-center">
					<span class="text-body1">{{ method }}</span>
				</q-card-section>
			</div>
		</q-card>
	</div>
</template>

<!-- todo - try to rewrite styles as a module -->
<style lang="scss">
.payment-method-active {
	background-color: $primary;
	color: white;
	transition: 0.3s ease;

	& .q-checkbox__bg {
			border: none !important;
		}

	&:hover {
		& .q-checkbox__bg {
			background-color: rgba($primary, 0)
		}
	}
}
</style>
