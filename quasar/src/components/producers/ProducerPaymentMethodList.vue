<template>
	<div class="column absolute fit">
		<div class="row full-height justify-center">
			<div
				v-if="!isMounting"
				class="col-xs-12 col-sm-9 col-md-6 col-lg-5 full-height"
			>
				<div class="column full-height q-gutter-xs">
					<q-card
						v-for="(method, pmid) in PAYMENT_METHOD_NAMES"
						:key="pmid"
						class="col flex flex-center q-hoverable"
						:class="[
							{'payment-method-active': selectedPaymentMethods[pmid]},
							{'cursor-pointer': isAbleToManagePaymentMethods}
						]"
						@click="selectPaymentMethod({ pmid, val:!selectedPaymentMethods[pmid] })"
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
							<q-card-section
								v-if="[PAYMENT_METHODS.CARD, PAYMENT_METHODS.SBP].includes(Number(pmid))"
								class="col-3 self-center text-center"
							>
								<q-icon
									name="settings"
									size="sm"
									:color="!!selectedPaymentMethods[pmid] ? 'white' : 'black'"
									@click.stop="showAcquiringSettingsDialog"
								/>
							</q-card-section>
						</div>
					</q-card>
				</div>
			</div>
			<div
				v-else
				class="col-xs-12 col-sm-9 col-md-6 col-lg-5 full-height q-pt-xs"
			>
				<ProducerPersonalPaymentMethodsSkeleton />
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, onMounted } from "vue"
import { api } from "src/boot/axios"
import { useRouter } from "vue-router"
import { PAYMENT_METHOD_NAMES, PAYMENT_METHODS } from "src/const/paymentMethods.js"
import { debounce } from "lodash"
import { Dialog } from "quasar"
import ProducerPersonalPaymentMethodsSkeleton from "src/components/skeletons/ProducerPersonalPaymentMethodsSkeleton.vue"
import AcquiringSettingsDialog from "src/components/dialogs/AcquiringSettingsDialog.vue"

const props = defineProps({
	isAbleToManagePaymentMethods: Boolean
})

const $router = useRouter()

const selectedPaymentMethods = ref(null)

const isMounting = ref(true)

const selectPaymentMethod = ({ pmid, val }) => {
	if (!props.isAbleToManagePaymentMethods) return

	selectedPaymentMethods.value[pmid] = val

	setPaymentMethodsAction()
}

const setPaymentMethodsAction = debounce(() => {
	const promise = api.post(`/personal/paymentMethods/${$router.currentRoute.value.params.producer_id}`, {
		payment_methods: Object.keys(selectedPaymentMethods.value).filter((pm) => !!selectedPaymentMethods.value[pm])
	})
}, 3000)

const showAcquiringSettingsDialog = () => {
	Dialog.create({
		component: AcquiringSettingsDialog,
	}).onOk(() => {
		// todo
	})
}

onMounted(() => {
	const promise = api.get(`/personal/paymentMethods/${$router.currentRoute.value.params.producer_id}`)

	promise.then((response) => {
		selectedPaymentMethods.value = Object.keys(PAYMENT_METHOD_NAMES)
			.reduce((carry, pmid) =>
				({...carry, [pmid]: response.data.includes(Number(pmid))}), {})
	})

	promise.finally(() => isMounting.value = false)
})
</script>

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
