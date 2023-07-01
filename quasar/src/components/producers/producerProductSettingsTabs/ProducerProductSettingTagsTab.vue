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
	if (tags.value.length > 7) {
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
	if (keywords.value.length === 7) {
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
	<div class="q-py-md">
		<span class="text-h5">Выберите теги для улучшенного поиска</span>
	</div>
	<div class="row">
		<div class="col-xs-12 col-lg-8">
			<draggable
				v-if="tagCloud.length"
				:list="tagCloud"
				:sort="false"
				group="tags"
				@start="drag=true"
				@end="drag=false"
				item-key="id"
			>
				<template #item="{ element }">
					<q-chip
						clickable
						color="primary"
						text-color="white"
					>
						{{ element.name }}
					</q-chip>
				</template>
			</draggable>
		</div>
	</div>

	<div
		class="row q-mb-lg q-mt-sm"
		style="min-height:160px"
	>
		<div
			class="col-xs-12 col-lg-4"
			style="border:1px solid rgba(0, 0, 0, 0.12)"
		>
			<draggable
				:list="tags"
				group="tags"
				@start="drag=true"
				@end="drag=false"
				@add="addTag"
				@sort="sortTag"
				item-key="id"
				class="q-list--separator"
			>
				<template #item="{ element }">
					<q-item
						clickable
						class="bg-primary text-white"
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
						<q-item-section class="word-break_all">
							{{ element.name }}
						</q-item-section>
					</q-item>
				</template>
			</draggable>
		</div>
	</div>

	<div class="q-py-md">
		<span class="text-h5">Добавьте слова наиболее точно описывающие продукт</span>
	</div>
	<div class="row">
		<div class="col-xs-12 col-lg-8">
			<q-select
				ref="select"
				label="Введите название"
				hint="Нажмите 'enter' чтобы добавить (максимум 7)"
				filled
				class="q-mb-sm"
				:model-value="keywords"
				use-input
				hide-dropdown-icon
				input-debounce="1000"
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
			class="col-xs-12 col-lg-4"
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
