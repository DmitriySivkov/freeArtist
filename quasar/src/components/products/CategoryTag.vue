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
		class="cursor-pointer q-hoverable full-height"
		:class="[
			$style.category__tag,
			{[$style.category__tag_active]: isSelected},
		]"
		@click="toggleCategory"
	>
		<span class="q-focus-helper"></span>
		<div class="column items-center full-height">
			<q-img
				:src="category.picture"
				class="col-auto"
				:class="$style.category__icon"
				height="35px"
				fit="contain"
			/>
			<div class="col flex items-center text-center">
				<span class="text-body1">{{ category.name }}</span>
			</div>
		</div>
	</q-card>
</template>

<style lang="scss" module>
.category {
	&__icon {
		border-radius: 50%;
	}
	&__tag {
		background-color: rgba(black, 0.4);
		color:white;
		min-height: 85px;

		&_active {
			background-color: rgba(black, 0.6);
		}
	}
}
</style>
