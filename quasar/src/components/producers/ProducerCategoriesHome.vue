<script setup>
import { computed } from "vue"
import CategoryTag from "src/components/products/CategoryTag.vue"
import { PRODUCT_CATEGORY_LIST } from "src/const/productCategories.js"
import { useUserStore } from "src/stores/user"

const userStore = useUserStore()

const selectedCategoryIds = computed(() => userStore.selected_categories)

const addSelectedCategory = (categoryId) => {
	userStore.setSelectedCategories(
		[categoryId, ...selectedCategoryIds.value]
	)
}

const removeSelectedCategory = (categoryId) => {
	userStore.setSelectedCategories(
		selectedCategoryIds.value.filter((c) => c !== categoryId)
	)
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
