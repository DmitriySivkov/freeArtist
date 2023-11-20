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
		class="col q-pa-xs cursor-pointer q-hoverable"
		:class="[
			$style.category__tag,
			{[$style.category__tag_active]: isSelected},
		]"
		@click="toggleCategory"
	>
		<span class="q-focus-helper"></span>
		<div class="column items-center full-height">

			<div
				class="col-auto bg-white"
				:class="$style.category__icon"
			>
				<img
					:src="category.picture"
					style="height:50px;"
				/>
			</div>

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
		padding: 7px;
	}
	&__tag {
		background-color: rgba(black, 0.4);
		color:white;

		&_active {
			background-color: rgba(black, 0.6);
		}
	}
}
</style>
