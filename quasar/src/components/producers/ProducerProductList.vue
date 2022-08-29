<template>
	<div class="q-pa-md">
		<q-list
			bordered
			separator
		>
			<q-item
				v-for="(product, index) in products"
				:key="index"
				clickable
			>
				<q-item-section>
					{{ product.title }}
				</q-item-section>
			</q-item>
		</q-list>
	</div>
</template>

<script>
import { useRouter } from "vue-router"
export default ({
	props: {
		products: {
			type: Array,
			default: () => []
		}
	},
	setup() {
		const $router = useRouter()

		return {
		}
	}
})
</script>
