<script setup>
import { onMounted, ref, computed } from "vue"
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

const tags = ref([])
const isLoadingTags = ref(true)

const selectedTags = ref(props.modelValue.tags)
const selectedTagIds = computed(() =>
	selectedTags.value.map((t) => t.id)
)

const toggleTag = (tag) => {
	if (selectedTagIds.value.includes(tag.id)) {
		selectedTags.value = selectedTags.value.filter((t) => t.id !== tag.id)
	} else {
		if (selectedTags.value.length >= 5) {
			notifyError("Нельзя добавить больше тегов")
			return
		}

		selectedTags.value.push(tag)
	}

	emit(
		"update:modelValue",
		Object.assign(
			props.modelValue,
			{ tags: selectedTags.value }
		)
	)
}

onMounted(() => {
	const promise = api.get("personal/tags")

	promise.then((response) => {
		tags.value = response.data
	})

	promise.finally(() => isLoadingTags.value = false)
})
</script>

<template>
	<div class="row justify-center">
		<div class="col-xs-12 col-sm">
			<div class="text-body1 text-center bg-secondary rounded-borders text-white q-pa-md q-mb-sm">
				Выберите теги
			</div>
			<q-linear-progress
				v-if="isLoadingTags"
				indeterminate
				color="primary"
			/>
		</div>
	</div>

	<div class="row justify-center">
		<div class="col-xs-12 col-sm">
			<q-scroll-area
				style="height:45vh"
				class="rounded-borders"
			>
				<div class="row q-gutter-xs">
					<q-card
						v-for="tag in tags"
						:key="tag.id"
						class="col-grow text-white flex flex-center text-center q-pa-xs"
						:class="selectedTagIds.includes(tag.id) ? 'bg-primary' : 'bg-secondary'"
						style="min-height:70px; width:25%"
						@click="toggleTag(tag)"
					>
						{{ tag.name }}
					</q-card>
				</div>
			</q-scroll-area>
		</div>
	</div>
</template>
