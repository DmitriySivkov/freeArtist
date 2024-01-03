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
			:class="{[$style['half-height']]: !isMobileWidthThreshold }"
			style="overflow-x:hidden"
		>
			<transition
				mode="out-in"
				enter-active-class="animated fadeInLeft"
			>
				<div v-if="!selectedPaymentProvider">
					<div
						v-if="isMobileWidthThreshold"
						class="row q-mb-sm"
					>
						<div class="col text-right">
							<q-icon
								v-close-popup
								name="close"
								size="md"
								class="cursor-pointer"
							/>
						</div>
					</div>
					<ProducerAcquiringSettingsProviderList
						v-model="selectedPaymentProvider"
					/>
				</div>
			</transition>
			<transition
				mode="out-in"
				enter-active-class="animated fadeInRight"
			>
				<div v-if="!!selectedPaymentProvider">
					<ProducerAcquiringSettingsProviderSetup
						v-model="selectedPaymentProvider"
						@success="onDialogOK"
					/>
				</div>
			</transition>
		</q-card>
	</q-dialog>
</template>

<script setup>
// todo - fix jumping on transition
import { useDialogPluginComponent } from "quasar"
import { ref, computed } from "vue"
import { useQuasar } from "quasar"
import ProducerAcquiringSettingsProviderList from "src/components/producers/producerAcquiringSettings/ProducerAcquiringSettingsProviderList.vue"
import ProducerAcquiringSettingsProviderSetup from "src/components/producers/producerAcquiringSettings/ProducerAcquiringSettingsProviderSetup.vue"

defineEmits([
	...useDialogPluginComponent.emits,
])

const { dialogRef, onDialogHide, onDialogOK } = useDialogPluginComponent()

const $q = useQuasar()
const isMobileWidthThreshold = computed(() => $q.screen.width < $q.screen.sizes.md)

const selectedPaymentProvider = ref(null)
</script>

<style lang="scss" module>
.half-height {
	height: 50vh;
}
</style>
