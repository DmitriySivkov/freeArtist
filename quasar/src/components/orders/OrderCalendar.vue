<template>
	<q-card
		class="bg-primary text-white cursor-pointer q-hoverable text-body1"
		@click="showCalendarDialog"
	>
		<span class="q-focus-helper"></span>
		<div class="row flex flex-center">
			<div class="col-shrink">
				<q-avatar
					text-color="white"
					icon="date_range"
					size="48px"
				/>
			</div>
			<div class="col">
				{{ dateRange }}
			</div>
			<div class="col-shrink q-mr-md">
				<q-spinner-oval
					v-show="isLoading"
					color="white"
					size="24px"
				/>
			</div>
		</div>
	</q-card>
</template>

<script setup>
// todo - grid breaks when date is range & when loader is visible - on lg+ screens
import { computed } from "vue"
import { Dialog } from "quasar"
import ProducerOrderListCalendarDialog from "src/components/dialogs/ProducerOrderListCalendarDialog.vue"

const props = defineProps({
	modelValue: [String, Object],
	isLoading: {
		type: Boolean,
		default: true
	}
})

const emit = defineEmits([
	"update:modelValue"
])

const dateRange = computed(() =>
	typeof props.modelValue === "string" ?
		props.modelValue :
		`${props.modelValue.from} - ${props.modelValue.to}`
)

const showCalendarDialog = () => {
	Dialog.create({
		component: ProducerOrderListCalendarDialog,
		componentProps: {
			date: props.modelValue
		}
	}).onOk((date) => {
		emit("update:modelValue", date)
	})
}
</script>
