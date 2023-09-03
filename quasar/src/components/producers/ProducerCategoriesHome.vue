<script setup>
import { ref } from "vue"
import CategoryTag from "src/components/products/CategoryTag.vue"
import { PRODUCT_CATEGORY_LIST } from "src/const/productCategories.js"

const emit = defineEmits([
	"change"
])

const selectedCategoryIds = ref([])

const addSelectedCategory = (categoryId) => {
	selectedCategoryIds.value.push(categoryId)

	emit("change", selectedCategoryIds.value)
}

const removeSelectedCategory = (categoryId) => {
	selectedCategoryIds.value = selectedCategoryIds.value.filter((c) => c !== categoryId)

	emit("change", selectedCategoryIds.value)
}
</script>

<template>
	<div class="row q-col-gutter-xs full-height">
		<div
			v-for="category in PRODUCT_CATEGORY_LIST"
			:key="category.id"
			class="col-xs-4 col-sm-3 col-md-2"
		>
			<CategoryTag
				:category="category"
				@select="addSelectedCategory"
				@unselect="removeSelectedCategory"
			/>
		</div>
	</div>

</template>
