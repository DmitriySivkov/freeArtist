<template>
	<ProductList />
	<router-view />
</template>

<script>
import ProductList from "src/components/cart/ProductList"
export default {
	components: { ProductList },
}
</script>
