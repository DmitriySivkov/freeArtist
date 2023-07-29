<script setup>
import CategoryTag from "src/components/products/CategoryTag.vue"
import { PRODUCT_CATEGORY_LIST } from "src/const/productCategories.js"
</script>

<template>
	<div class="row q-col-gutter-xs full-height">
		<div
			v-for="(category, id) in PRODUCT_CATEGORY_LIST"
			:key="id"
			class="col-xs-4 col-sm-3 col-md-2"
		>
			<CategoryTag :category="category" />
		</div>
	</div>

</template>
