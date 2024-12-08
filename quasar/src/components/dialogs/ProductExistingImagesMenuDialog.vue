<script setup>
import { ref } from "vue"
import { useDialogPluginComponent } from "quasar"

const props = defineProps({
	image: {
		type: Object,
		default: () => {}
	},
	isThumbnail: Boolean,
	toDelete: Boolean,
	togglePropFn: Function
})

const emit = defineEmits([
	...useDialogPluginComponent.emits,
])

const { dialogRef, onDialogHide, onDialogOK } = useDialogPluginComponent()

const backendServer = process.env.BACKEND_SERVER

const isThumbnailRef = ref(props.isThumbnail)
const isToDelete = ref(props.toDelete)

const toggleThumbnail = () => {
	isThumbnailRef.value = !isThumbnailRef.value

	props.togglePropFn("thumbnail_id")
}

const toggleToDelete = () => {
	isToDelete.value = !isToDelete.value

	props.togglePropFn("to_delete")
}
</script>

<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
	>
		<q-card
			class="q-dialog-plugin q-pa-sm"
			style="min-height:40vh; width:100%"
		>
			<div class="text-right q-mb-md">
				<q-icon
					v-close-popup
					name="close"
					size="md"
					class="cursor-pointer"
				/>
			</div>

			<q-card-section class="q-px-none">
				<q-img
					no-spinner
					class="rounded-borders"
					:src="`${backendServer}/storage/${image.path}`"
				>
					<div
						v-if="isThumbnailRef || isToDelete"
						class="row absolute-bottom"
						style="padding:0 !important;"
					>
						<div
							v-if="isThumbnailRef"
							class="col text-center text-body1 content-center q-py-sm"
						>
							Заставка
						</div>
						<q-separator
							v-if="isThumbnailRef && isToDelete"
							vertical
							color="white"
							class="q-my-xs"
						/>
						<div
							v-if="isToDelete"
							class="col text-center text-body1 content-center q-py-sm"
						>
							К удалению
						</div>
					</div>
				</q-img>
			</q-card-section>
			<q-card-section class="q-px-sm">
				<q-list
					separator
					class="text-body1"
				>
					<q-item
						tag="label"
						class="q-px-none"
					>
						<q-item-section>
							<q-item-label>Сделать заставкой</q-item-label>
						</q-item-section>
						<q-item-section side>
							<q-checkbox
								:model-value="isThumbnailRef"
								left-label
								indeterminate-value="false"
								@update:model-value="toggleThumbnail"
							/>
						</q-item-section>
					</q-item>
					<q-item
						tag="label"
						class="q-px-none"
					>
						<q-item-section>
							<q-item-label>Подготовить к удалению</q-item-label>
						</q-item-section>
						<q-item-section side>
							<q-checkbox
								:model-value="isToDelete"
								left-label
								indeterminate-value="false"
								@update:model-value="toggleToDelete"
							/>
						</q-item-section>
					</q-item>
				</q-list>
			</q-card-section>
		</q-card>
	</q-dialog>
</template>
