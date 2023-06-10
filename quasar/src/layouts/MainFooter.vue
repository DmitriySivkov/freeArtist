<template>
	<q-footer class="footer">
		<q-toolbar class="q-pa-sm">
			<div class="col-xs-12 col-lg-8">
				<div class="row q-gutter-sm">
					<div class="col-xs-shrink">
						<q-btn
							class="q-pa-md"
							icon="login"
							:text-color="['login', 'register'].includes(route.name) ? 'indigo-10': 'white'"
							:color="['login', 'register'].includes(route.name) ? 'secondary': 'primary'"
							to="/auth"
						/>
					</div>
					<div class="col-xs-shrink">
						<q-btn
							class="q-pa-md"
							icon="home"
							:text-color="route.name === 'home' ? 'indigo-10': 'white'"
							:color="route.name === 'home' ? 'secondary': 'primary'"
							to="/"
						/>
					</div>
					<div
						v-if="is_user_logged"
						class="col-xs-shrink"
					>
						<q-btn
							class="q-pa-md"
							icon="account_circle"
							:text-color="route.name.includes('personal') ? 'indigo-10': 'white'"
							:color="route.name.includes('personal') ? 'secondary': 'primary'"
							to="/personal"
						/>
					</div>
					<div class="col-xs-shrink">
						<q-btn
							class="q-pa-md"
							:label="cartCounter"
							icon-right="shopping_cart"
							:text-color="cartCounter > 0 || route.name === 'cart' ? 'indigo-10': 'white'"
							:color="cartCounter > 0 || route.name === 'cart' ? 'secondary': 'primary'"
							to="/cart"
						/>
					</div>
				</div>
			</div>
		</q-toolbar>
	</q-footer>
</template>

<script>
import { computed } from "vue"
import { useRouter } from "vue-router"
import { useCartStore } from "src/stores/cart"
import { useUserStore } from "src/stores/user"
export default {
	setup() {
		const $router = useRouter()
		const cart_store = useCartStore()
		const user_store = useUserStore()

		const route = $router.currentRoute

		const is_user_logged = computed(() => user_store.is_logged)

		const cartCounter = computed(
			() => Object.values(cart_store.data)
				.reduce((accum, cart_item) => accum + cart_item.product_list.length, 0)
		)

		return {
			cartCounter,
			route,
			is_user_logged
		}
	}
}
</script>
