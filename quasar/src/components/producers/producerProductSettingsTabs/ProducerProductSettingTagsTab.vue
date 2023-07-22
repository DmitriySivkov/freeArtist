<script setup>
import { defineComponent, onMounted, ref } from "vue"
import { useNotification } from "src/composables/notification"
import { api } from "src/boot/axios"
import draggable from "vuedraggable"

defineComponent({
	draggable
})

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

<template>
	<div class="q-pb-md">
		<span class="text-h5">Выберите теги для улучшенного поиска</span>
	</div>
	<div class="row q-gutter-sm">
		<div
			class="col-xs-12 col-lg-4"
			:class="$style.tag__container"
		>
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
						<q-item-section>
							{{ element.name }}
						</q-item-section>
					</q-item>
				</template>
			</draggable>
		</div>

		<div class="col-xs-12 col-lg-6">
			<draggable
				v-if="tagCloud.length"
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
					name: 'fade'
				}"
				v-bind="dragOptions"
			>
				<template #item="{ element }">
					<q-item
						clickable
						class="col-xs-3 col-lg-2 flex-center text-center"
						:class="$style.tag"
					>
						{{ element.name }}
					</q-item>
				</template>
			</draggable>
		</div>
	</div>

	<div class="q-py-md">
		<span class="text-h5">Добавьте слова наиболее точно описывающие продукт</span>
	</div>
	<div class="row">
		<div class="col-xs-12 col-lg-6">
			<q-select
				ref="select"
				label="Введите название"
				hint="Нажмите 'enter' чтобы добавить (максимум 8)"
				filled
				class="q-mb-sm"
				:model-value="keywords"
				use-input
				hide-dropdown-icon
				input-debounce="300"
				options-selected-class="primary"
				hide-selected
				@add="addKeyword"
				@new-value="addKeyword"
				@remove="removeKeyword($event)"
			/>
		</div>
	</div>
	<div
		class="row"
		style="min-height:160px"
	>
		<div
			class="col-xs-12 col-lg-6"
			style="border:1px solid rgba(0, 0, 0, 0.12)"
		>
			<draggable
				:list="keywords"
				group="keywords"
				@start="drag=true"
				@end="drag=false"
				@sort="sortKeyword"
				:item-key="(keyword) => keyword"
				class="q-list--separator"
				:component-data="{
					tag: 'div',
					type: 'transition-group',
					name: 'fade'
				}"
				v-bind="dragOptions"
			>
				<template #item="{ element }">
					<q-item
						clickable
						class="bg-primary text-white"
					>
						<q-item-section
							side
							@click="removeKeyword(element)"
						>
							<q-icon
								name="clear"
								color="white"
							/>
						</q-item-section>
						<q-item-section class="word-break_all">
							{{ element }}
						</q-item-section>
					</q-item>
				</template>
			</draggable>
		</div>
	</div>
</template>

<style lang="scss" module>
.tag {
	position:relative;
	height: 70px !important;
	overflow:hidden;
	color: white;
	background: $primary;
	border-bottom: 1px dotted rgba(0, 0, 0, 0.12);
	border-right: 1px dotted rgba(0, 0, 0, 0.12);

	&__container_draggable {
		background: #F1F1F1;
		display: flex;
		flex-flow: row wrap;
		align-content: flex-start;
		height: 100%;
	}

	&__container {
		border:1px dotted rgba(0, 0, 0, 0.4);
		min-height:140px
	}
}
</style>
