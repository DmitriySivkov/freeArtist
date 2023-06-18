<script setup>
import { ref, computed } from "vue"
import { api } from "src/boot/axios"
import { useNotification } from "src/composables/notification"

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

const options = ref([])

const defaultTags = computed(() => props.modelValue.tags.filter((t) => !t.is_new))
const newTags = computed(() => props.modelValue.tags.filter((t) => t.is_new))

const addTag = ({ value }) => {
	if (props.modelValue.tags.length === 5) {
		notifyError("Нельзя добавить больше тегов")
		return
	}

	if (typeof value === "string") {

		if (props.modelValue.tags.find((t) => t.name === value))
			return

		value = {
			id: crypto.randomUUID(),
			name: value,
			is_new: 1,
			is_created: 1
		}
	} else {
		value = {...value, is_new: 1}
	}

	emit(
		"update:modelValue",
		Object.assign(
			props.modelValue,
			{
				tags: [...props.modelValue.tags, value]
			}
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

const loadTags = async (query, update) => {
	if (query.length < 1) return

	const response = await api.get("personal/tags", {
		params: { query }
	})

	update(() => {
		options.value = response.data.map((tag) => {
			return {
				id: tag.id,
				name: tag.name
			}
		})
	})
}
</script>

<template>
	<div class="row">
		<div class="col-xs-12 col-lg-8">
			<q-select
				label="Выберите тег или создайте собственный"
				filled
				class="q-mb-sm"
				:model-value="modelValue.tags"
				use-input
				multiple
				hide-dropdown-icon
				input-debounce="1000"
				:options="options"
				option-value="id"
				option-label="name"
				options-selected-class="primary"
				new-value-mode="add-unique"
				hide-selected
				@filter="loadTags"
				@add="addTag"
				@remove="removeOptionById($event.value.id)"
			>
				<template #option="scope">
					<q-item v-bind="scope.itemProps">
						<q-item-section>
							<q-item-label>{{ scope.opt.name }}</q-item-label>
						</q-item-section>
					</q-item>
				</template>
				<template #no-option>
					<q-item>
						<q-item-section class="text-grey">
							Теги не найдены
						</q-item-section>
					</q-item>
				</template>
			</q-select>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-6 col-lg-4">
			<q-list>
				<q-item-label header>
					Имеющиеся
				</q-item-label>
				<q-item
					v-for="tag in defaultTags"
					:key="tag.id"
					clickable
				>
					<q-item-section
						side
						@click="removeOptionById(tag.id)"
					>
						<q-icon name="clear" />
					</q-item-section>
					<q-item-section>
						<q-item-label> {{ tag.name }} </q-item-label>
					</q-item-section>
				</q-item>
			</q-list>
		</div>
		<q-separator
			vertical
			class="q-mt-sm"
		/>
		<div class="col-xs-grow col-lg-4">
			<q-list>
				<q-item-label header>
					Добавить
				</q-item-label>
				<q-item
					v-for="tag in newTags"
					:key="tag.id"
					clickable
				>
					<q-item-section
						side
						@click="removeOptionById(tag.id)"
					>
						<q-icon name="clear" />
					</q-item-section>
					<q-item-section>
						<q-item-label>
							{{ tag.name }}
						</q-item-label>
					</q-item-section>
				</q-item>
			</q-list>
		</div>
	</div>
</template>
