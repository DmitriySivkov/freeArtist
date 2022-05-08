<template>
	<q-item
		v-for="(producer, index) in producers"
		:key="index"
		clickable
		@click="show(producer.id)"
		:class="{ 'bg-light-green-2': cart.hasOwnProperty(producer.id) }"
	>
		<q-item-section avatar>
			<q-icon
				name="shopping_cart"
				size="md"
			/>
		</q-item-section>
		<q-item-section>
			{{ producer.title }}
		</q-item-section>
	</q-item>
</template>

<script>
import { useStore } from "vuex"
import { computed } from "vue"
import { useRouter } from "vue-router"
export default ({
	setup() {
		const $store = useStore()
		const $router = useRouter()
		const producers = computed(() => $store.state.producer.data)
		const cart = computed(() => $store.state.cart.data)

		return {
			producers,
			cart,
			show(id) {
				$router.push("/producers/" + id)
			}
		}
	}
})
</script>
