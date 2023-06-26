<script setup>
import { computed, defineComponent, onMounted, ref } from "vue"
import { useNotification } from "src/composables/notification"
import { api } from "src/boot/axios"
import { clone } from "lodash"
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

const tagsSorted = (e) => {
	console.log(
		!isEqual(tags.value, defaultTags)
	)
}

const defaultKeywords = clone(props.modelValue.keywords)

const newKeywords = computed(() =>
	props.modelValue.keywords.filter((k) => !defaultKeywords.includes(k))
)

const currentKeywords = computed(() =>
	props.modelValue.keywords.filter((k) => defaultKeywords.includes(k))
)

const addKeyword = (keyword, doneFn) => {
	if (props.modelValue.keywords.length === 7) {
		notifyError("Нельзя добавить больше ключевых слов")
		return
	}

	doneFn(keyword, "add-unique")

	if (props.modelValue.keywords.find((k) => k === keyword))
		return

	emit(
		"update:modelValue",
		Object.assign(
			props.modelValue,
			{keywords: [...props.modelValue.keywords, keyword]}
		)
	)
}

const removeKeyword = (keyword) => {
	emit(
		"update:modelValue",
		Object.assign(
			props.modelValue,
			{keywords: props.modelValue.keywords.filter((k) => k !== keyword)}
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
			class="col-xs-6 col-lg-4"
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
				:model-value="modelValue.keywords"
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
		style="min-height:120px"
	>
		<div
			class="col-xs-6 col-lg-4"
			style="border-right:1px solid rgba(0, 0, 0, 0.12)"
		>
			<q-item-label header>
				Имеющиеся
			</q-item-label>
			<q-list separator>
				<q-item
					v-for="(keyword, index) in currentKeywords"
					:key="index"
					clickable
					class="bg-primary text-white"
				>
					<q-item-section
						side
						@click="removeKeyword(keyword)"
					>
						<q-icon
							name="clear"
							color="white"
						/>
					</q-item-section>
					<q-item-section class="word-break_all">
						{{ keyword }}
					</q-item-section>
				</q-item>
			</q-list>
		</div>
		<div class="col-xs-6 col-lg-4">
			<q-item-label header>
				Добавить
			</q-item-label>
			<q-list separator>
				<q-item
					v-for="(keyword, index) in newKeywords"
					:key="index"
					clickable
					class="bg-primary text-white"
				>
					<q-item-section
						side
						@click="removeKeyword(keyword)"
					>
						<q-icon
							name="clear"
							color="white"
						/>
					</q-item-section>
					<q-item-section class="word-break_all">
						{{ keyword }}
					</q-item-section>
				</q-item>
			</q-list>
		</div>
	</div>
</template>
