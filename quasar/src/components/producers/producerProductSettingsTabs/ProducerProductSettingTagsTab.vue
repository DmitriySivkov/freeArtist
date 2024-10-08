<template>
	<div class="row justify-center">
		<div
			class="relative-position col-xs-12 col-sm-9 col-md-8 col-lg-6"
			:class="$style.tag__container"
		>
			<div
				v-if="!tags.length"
				class="absolute flex flex-center fit text-body1"
			>
				Переместите теги из окна ниже
			</div>
			<draggable
				:list="tags"
				group="tags"
				@start="drag=true"
				@end="drag=false"
				@add="addTag"
				@sort="sortTag"
				item-key="id"
				class="row"
				:class="$style.tag__container_draggable"
				:component-data="{
					type: 'transition-group',
					name: 'fade'
				}"
				v-bind="dragOptions"
			>
				<template #item="{ element }">
					<q-item
						clickable
						class="col-xs-4 col-md-3 flex-center text-center bg-primary"
						:class="$style.tag"
					>
						<q-item-section class="text-body1">
							{{ element.name }}
						</q-item-section>
					</q-item>
				</template>
			</draggable>
			<q-linear-progress
				v-if="isLoadingTagCloud"
				indeterminate
				color="primary"
			/>
		</div>
	</div>

	<div class="row justify-center q-my-md">
		<q-icon
			name="swap_vert"
			size="md"
		/>
	</div>

	<div class="row justify-center">
		<div class="col-xs-12 col-sm-9 col-md-8 col-lg-6">
			<q-scroll-area
				visible
				style="height:25vh"
				:thumb-style="{ width: '15px' }"
			>
				<draggable
					:list="tagCloud"
					group="tags"
					:sort="false"
					@start="drag=true"
					@end="drag=false"
					item-key="id"
					class="row relative-position"
					:component-data="{
						type: 'transition-group',
						name: 'fade',
					}"
					v-bind="dragOptions"
				>
					<template #item="{ element }">
						<q-item
							clickable
							class="col-xs-4 col-md-3 flex-center text-center text-body1 bg-secondary"
							:class="$style.tag"
						>
							{{ element.name }}
						</q-item>
					</template>
				</draggable>
			</q-scroll-area>
		</div>
	</div>
</template>

<script setup>
import { onMounted, ref } from "vue"
import { useNotification } from "src/composables/notification"
import { api } from "src/boot/axios"

const props = defineProps({
	modelValue: {
		type: Object,
		default: () => ({})
	}
})

const emit = defineEmits([
	"update:modelValue"
])

const { notifyError } = useNotification()

const drag = ref(false)

// todo - ghost block does not get valid position when dragging from bottom to top
const tagCloud = ref([])
const isLoadingTagCloud = ref(true)

const tags = ref(props.modelValue.tags)

const addTag = (e) => {
	if (tags.value.length > 8) {
		const index = tags.value.findIndex(
			(t) => t.id === e.item.__draggable_context.element.id
		)

		const extraTag = tags.value.splice(index, 1)[0]

		tagCloud.value.push(extraTag)

		notifyError("Нельзя добавить больше тегов")
	}

	emit(
		"update:modelValue",
		Object.assign(
			props.modelValue,
			{tags: tags.value}
		)
	)
}

const sortTag = () => {
	emit(
		"update:modelValue",
		Object.assign(
			props.modelValue,
			{tags: tags.value}
		)
	)
}

const dragOptions = {
	animation: 200,
	disabled: false,
	ghostClass: "low-opacity"
}

onMounted(() => {
	const promise = api.get("personal/tags")

	promise.then((response) => {
		tagCloud.value = response.data.filter(
			(t) => !props.modelValue.tags.map((t) => t.id).includes(t.id)
		)
	})

	promise.finally(() => isLoadingTagCloud.value = false)
})
</script>

<style lang="scss" module>
.tag {
	position:relative;
	height: 70px !important;
	overflow:hidden;
	color: white;
	background: $primary;
	border-bottom: 1px dotted rgba(0, 0, 0, 0.12);
	border-right: 1px dotted rgba(0, 0, 0, 0.12);

	&__container {
		border:1px dotted rgba(0, 0, 0, 0.4);
		min-height:140px
	}

	&__container_draggable {
		background: #F1F1F1;
		display: flex;
		flex-flow: row wrap;
		align-content: flex-start;
		height: 100%;
	}
}
</style>
