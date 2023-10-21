<template>
	<q-card
		class="bg-primary text-white cursor-pointer q-hoverable text-body1"
		@click="showCalendarDialog"
	>
		<span class="q-focus-helper"></span>
		<q-item>
			<q-item-section avatar>
				<q-avatar
					text-color="white"
					icon="date_range"
					size="48px"
				/>
			</q-item-section>
			<q-item-section>
				{{ dateRange }}
			</q-item-section>
		</q-item>
	</q-card>
</template>

<script setup>
import { computed } from "vue"
import { Dialog } from "quasar"
import ProducerOrderListCalendarDialog from "src/components/dialogs/ProducerOrderListCalendarDialog.vue"

const props = defineProps({
	modelValue: [String, Object]
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
