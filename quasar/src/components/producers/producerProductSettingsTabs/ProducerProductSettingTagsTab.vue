<template>
	<div
		class="column"
		style="min-height:100vh"
	>
		<div class="col-auto">
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
								class="col-xs-6 col-md-4 flex-center text-center"
								:class="$style.tag"
							>
								<q-item-section
									side
									@click="removeTag(element)"
								>
									<q-icon
										name="clear"
										color="white"
									/>
								</q-item-section>
								<q-item-section class="text-body1">
									{{ element.name }}
								</q-item-section>
							</q-item>
						</template>
					</draggable>
				</div>
			</div>
		</div>

		<div class="row justify-center">
			<q-icon
				name="swap_vert"
				class="q-my-sm"
				size="md"
			/>
		</div>

		<div class="col">
			<div class="row justify-center">
				<div class="col-xs-12 col-sm-9 col-md-8 col-lg-6">
					<q-scroll-area
						visible
						style="height:25vh"
						:thumb-style="{ width: '15px' }"
					>
						<draggable
							:list="tagCloud"
							:sort="false"
							group="tags"
							@start="drag=true"
							@end="drag=false"
							item-key="id"
							class="row"
							:component-data="{
								tag: 'div',
								type: 'transition-group',
								name: 'fade',
							}"
							v-bind="dragOptions"
						>
							<template #item="{ element }">
								<q-item
									clickable
									class="col-xs-4 col-md-3 flex-center text-center text-body1"
									:class="$style.tag"
								>
									{{ element.name }}
								</q-item>
							</template>
						</draggable>
						<q-inner-loading :showing="isLoadingTagCloud">
							<q-spinner-gears
								size="md"
								color="primary"
							/>
						</q-inner-loading>
					</q-scroll-area>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { defineComponent, onMounted, ref } from "vue"
import { useNotification } from "src/composables/notification"
import { api } from "src/boot/axios"
import draggable from "vuedraggable"

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

const removeTag = (tag) => {
	const index = tags.value.findIndex((t) => t.id === tag.id)

	const removedTag = tags.value.splice(index, 1)[0]

	tagCloud.value.push(removedTag)

	emit(
		"update:modelValue",
		Object.assign(
			props.modelValue,
			{tags: tags.value}
		)
	)
}

const keywords = ref(props.modelValue.keywords)

const addKeyword = (keyword, doneFn) => {
	if (keywords.value.length === 8) {
		notifyError("Нельзя добавить больше ключевых слов")
		return
	}

	if (keyword.length > 20) {
		notifyError("Максимум 20 символов")
		return
	}

	doneFn(keyword, "add-unique")

	if (keywords.value.find((k) => k === keyword))
		return

	keywords.value.push(keyword)

	emit(
		"update:modelValue",
		Object.assign(
			props.modelValue,
			{keywords: keywords.value}
		)
	)
}

const removeKeyword = (keyword) => {
	const index = keywords.value.findIndex((k) => k === keyword)

	keywords.value.splice(index, 1)

	emit(
		"update:modelValue",
		Object.assign(
			props.modelValue,
			{keywords: keywords.value}
		)
	)
}

const sortKeyword = () => {
	emit(
		"update:modelValue",
		Object.assign(
			props.modelValue,
			{keywords: keywords.value}
		)
	)
}

const dragOptions = {
	animation: 200,
	group: "description",
	disabled: false,
	ghostClass: "bg-green"
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

.keyword {
	&__container_draggable {
		background: #F1F1F1;
		height: 100%
	}
}
</style>
