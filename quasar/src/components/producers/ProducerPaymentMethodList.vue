<template>
	<div class="column absolute fit">
		<div class="row full-height justify-center">
			<div
				v-if="!isMounting"
				class="col-xs-12 col-sm-9 col-md-6 col-lg-5 full-height q-pt-xs"
			>
				<div class="column full-height q-gutter-xs">
					<q-card
						v-for="(method, pmid) in PAYMENT_METHOD_NAMES"
						:key="pmid"
						class="col flex flex-center q-hoverable cursor-pointer"
						:class="{'payment-method-active': selectedPaymentMethods[pmid]}"
						@click="selectPaymentMethod({ pmid, val:!selectedPaymentMethods[pmid] })"
					>
						<span class="q-focus-helper"></span>
						<div class="row full-width">
							<q-card-section class="col-4">
								<q-checkbox
									:model-value="selectedPaymentMethods[pmid]"
									color="primary"
									:toggle-indeterminate="false"
									:name="pmid"
								/>
							</q-card-section>
							<q-card-section class="col-8 self-center">
								<span class="text-body1">{{ method }}</span>
							</q-card-section>
						</div>
					</q-card>
					<q-btn
						v-if="isAbleToManagePaymentMethods"
						label="Сохранить"
						color="primary"
						class="col-shrink q-py-lg"
						:loading="isSettingMethods"
						:disable="isSettingMethods"
						@click="setPaymentMethods"
					/>
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
import { PAYMENT_METHOD_NAMES } from "src/const/paymentMethods.js"
import ProducerPersonalPaymentMethodsSkeleton from "src/components/skeletons/ProducerPersonalPaymentMethodsSkeleton.vue"

const props = defineProps({
	isAbleToManagePaymentMethods: Boolean
})

const $router = useRouter()

const selectedPaymentMethods = ref(null)

const isSettingMethods = ref(false)
const isMounting = ref(true)

const selectPaymentMethod = ({ pmid, val }) => {
	selectedPaymentMethods.value[pmid] = val
}

const setPaymentMethods = () => {
	isSettingMethods.value = true

	const promise = api.post(`/personal/paymentMethods/${$router.currentRoute.value.params.producer_id}`, {
		payment_methods: Object.keys(selectedPaymentMethods.value).filter((pm) => !!selectedPaymentMethods.value[pm])
	})

	promise.finally(() => isSettingMethods.value = false)
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
