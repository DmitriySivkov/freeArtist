<template>
	<q-footer
		elevated
		class="footer"
	>
		<q-toolbar class="q-pa-md justify-center">
			<div class="col-xs-12 col-lg-8">
				<div class="row q-gutter-sm">
					<div class="col-xs-shrink">
						<q-btn
							class="q-pa-md"
							icon="login"
							:color="['login', 'register'].includes(route.name) ? 'secondary': 'primary'"
							to="/auth"
						/>
					</div>
					<div class="col-xs-shrink">
						<q-btn
							class="q-pa-md"
							icon-right="home"
							:color="route.name === 'home' ? 'secondary': 'primary'"
							to="/"
						/>
					</div>
					<div
						v-if="user.is_logged"
						class="col-xs-shrink"
					>
						<q-btn
							class="q-pa-md"
							icon-right="account_circle"
							:color="route.name.includes('personal') ? 'secondary': 'primary'"
							to="/personal"
						/>
					</div>
					<div class="col-xs-shrink">
						<q-btn
							class="q-pa-md"
							:label="cartCounter"
							icon-right="shopping_cart"
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
export default {
	setup() {
		const $router = useRouter()
    const cart_store = useCartStore()
		const route = $router.currentRoute
		const user = computed(() => $store.state.user)
		const cartCounter = computed(
			() => Object.values(cart_store.data)
				.reduce((accum, cart_item) => accum + cart_item.products.length, 0)
		)

		return {
			cartCounter,
			route,
			user
		}
	}
}
</script>
