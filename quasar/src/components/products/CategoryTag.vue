<script setup>
import { ref } from "vue"

const props = defineProps({
	category: Object
})

const emit = defineEmits([
	"select",
	"unselect"
])

const isSelected = ref(false)

const toggleCategory = () => {
	if (!isSelected.value) {
		isSelected.value = true
		emit("select", props.category.id)
	} else {
		isSelected.value = false
		emit("unselect", props.category.id)
	}
}
</script>

<template>
	<q-card
		bordered
		clickable
		class="full-height cursor-pointer q-hoverable"
		:class="[
			$style.category__tag,
			{[$style.category__tag_active]: isSelected},
		]"
		@click="toggleCategory"
	>
		<span class="q-focus-helper"></span>
		<div class="column fit flex-center">
			<q-icon
				class="col-6 text-center bg-white"
				:class="$style.category__icon"
			>
				<img
					:src="category.picture"
					style="width:auto"
				/>
			</q-icon>

			<div class="col flex flex-center text-center">
				<span class="text-body1">{{ category.name }}</span>
			</div>
		</div>
	</q-card>
</template>

<style lang="scss" module>
.category {
	&__icon {
		border-radius: 50%;
		padding: 7%;
		margin-top: 8px;
	}
	&__tag {
		background-color: rgba(black, 0.4);
		color:white;

		&_active {
			background-color: rgba(black, 0.7);
		}
	}
}
</style>
