<script setup>
import { computed, onMounted, ref } from "vue"
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

const tagCloud = ref([])
const isLoadingTagCloud = ref(true)

const defaultTags = computed(() => props.modelValue.tags.filter((t) => !t.is_new))
const newTags = computed(() => props.modelValue.tags.filter((t) => t.is_new))

const createTag = (tag, doneFn) => {
	if (props.modelValue.tags.length === 7) {
		notifyError("Нельзя добавить больше тегов")
		return
	}

	if (props.modelValue.tags.find((t) => t.name === tag))
		return

	// 'add-unique' does not work for current case
	doneFn(tag, "add-unique")

	tag = {
		id: crypto.randomUUID(),
		name: tag,
		is_new: 1
	}

	emit(
		"update:modelValue",
		Object.assign(
			props.modelValue,
			{tags: [...props.modelValue.tags, tag]}
		)
	)
}

const removeOptionById = (tagId) => {
	emit(
		"update:modelValue",
		Object.assign(
			props.modelValue,
			{tags: props.modelValue.tags.filter((t) => t.id !== tagId)}
		)
	)
}

onMounted(() => {
	const promise = api.get("personal/tags")

	promise.then((response) => {
		tagCloud.value = response.data
	})

	promise.finally(() => isLoadingTagCloud.value = false)
})
</script>

<template>
	<div
		v-if="tagCloud.length"
		class="row"
	>
		<div class="col-xs-12 col-lg-8">
			<q-chip
				v-for="tag in tagCloud"
				:key="tag.id"
				clickable
				color="primary"
				text-color="white"
			>
				{{ tag.name }}
			</q-chip>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-lg-8">
			<q-select
				ref="select"
				label="Введите название"
				hint="Нажмите 'enter' чтобы добавить (максимум 7)"
				filled
				class="q-mb-sm"
				:model-value="modelValue.tags"
				use-input
				hide-dropdown-icon
				input-debounce="1000"
				option-value="id"
				option-label="name"
				options-selected-class="primary"
				hide-selected
				@add="createTag"
				@new-value="createTag"
				@remove="removeOptionById($event.value.id)"
			/>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-6 col-lg-4">
			<q-item-label header>
				Имеющиеся
			</q-item-label>
			<q-list separator>
				<q-item
					v-for="tag in defaultTags"
					:key="tag.id"
					clickable
					class="bg-primary text-white"
				>
					<q-item-section
						side
						@click="removeOptionById(tag.id)"
					>
						<q-icon
							name="clear"
							color="white"
						/>
					</q-item-section>
					<q-item-section class="word-break_all">
						{{ tag.name }}
					</q-item-section>
				</q-item>
			</q-list>
		</div>
		<q-separator
			vertical
			class="q-mt-sm"
		/>
		<div class="col-xs-grow col-lg-4">
			<q-item-label header>
				Добавить
			</q-item-label>
			<q-list separator>
				<q-item
					v-for="tag in newTags"
					:key="tag.id"
					clickable
					class="bg-teal-4 text-white"
				>
					<q-item-section
						side
						@click="removeOptionById(tag.id)"
					>
						<q-icon
							name="clear"
							color="white"
						/>
					</q-item-section>
					<q-item-section class="word-break_all">
						{{ tag.name }}
					</q-item-section>
				</q-item>
			</q-list>
		</div>
	</div>
</template>
