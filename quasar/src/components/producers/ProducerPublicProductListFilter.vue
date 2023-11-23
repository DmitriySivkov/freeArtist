<script setup>
import { ref } from "vue"

const props = defineProps({
	tagsStatic: {
		type: Array,
		default: () => ([])
	},
	selectedTags: {
		type: Array,
		required: false,
		default: () => ([])
	}
})

const emit = defineEmits([
	"changeTag"
])

const tags = ref(props.tagsStatic)

const filterTags = (val, update) => {
	if (val === "") {
		update(() => {
			tags.value = props.tagsStatic
		})
		return
	}

	update(() => {
		const needle = val.toLowerCase()
		tags.value = props.tagsStatic.filter(v => v.name.toLowerCase().indexOf(needle) > -1)
	})
}
</script>

<template>
	<div class="sticky__common_top q-my-sm">
		<q-select
			square
			filled
			multiple
			clearable
			use-input
			input-debounce="0"
			:model-value="selectedTags"
			@update:model-value="emit('changeTag', $event)"
			@filter="filterTags"
			@clear="emit('changeTag',[])"
			:options="tags"
			option-label="name"
			option-value="id"
			behavior="dialog"
			bg-color="bright"
			input-class="truncate-chip-labels"
			:placeholder="!selectedTags.length ? 'Выбрать категорию' : ''"
		>
			<!-- todo - control max height of selection -->
			<template #selected-item="{ opt, index, removeAtIndex }">
				<q-chip
					square
					removable
					color="primary"
					text-color="white"
					:label="opt.name"
					@remove="removeAtIndex(index)"
				/>
			</template>
			<template v-slot:option="{ itemProps, opt, selected, toggleOption }">
				<q-item
					v-bind="itemProps"
					clickable
					@click="toggleOption(opt)"
					class="items-center"
					:class="{'bg-primary text-white': selected }"
				>
					{{ opt.name }}
				</q-item>
			</template>
		</q-select>
	</div>
</template>
