<script setup>
import { computed } from "vue"
import { useRouter } from "vue-router"
import { useCartStore } from "src/stores/cart"
import { useUserStore } from "src/stores/user"

const $router = useRouter()
const cartStore = useCartStore()
const userStore = useUserStore()

const route = $router.currentRoute

const isUserLogged = computed(() => userStore.is_logged)

const cartCounter = computed(() =>
	cartStore.data?.reduce((carry, item) =>
		carry + item.products.length, 0)
)
</script>

<template>
	<q-footer class="footer bg-indigo-8 main-layout-footer q-pa-sm">
		<div class="col-xs-12 col-lg-8">
			<div class="row q-col-gutter-sm">
				<div
					v-if="!isUserLogged"
					class="col"
				>
					<q-btn
						class="fit q-pa-md"
						icon="login"
						:color="['login', 'register'].includes(route.name) ? 'primary': 'secondary'"
						to="/auth"
					/>
				</div>
				<div class="col">
					<q-btn
						class="fit q-pa-md"
						icon="home"
						:color="route.name === 'home' ? 'primary' : 'secondary'"
						to="/"
					/>
				</div>
				<div
					v-if="isUserLogged"
					class="col"
				>
					<q-btn
						class="fit q-pa-md"
						icon="account_circle"
						:color="route.name.includes('personal') ? 'primary': 'secondary'"
						to="/personal"
					/>
				</div>
				<div class="col">
					<q-btn
						class="fit q-pa-md"
						:label="cartCounter"
						icon-right="shopping_cart"
						:color="route.name === 'cart' ? 'primary': 'secondary'"
						to="/cart"
					/>
				</div>
				<div class="col">
					<q-btn
						class="fit q-pa-md"
						icon="shopping_cart_checkout"
						:color="route.name === 'user_orders' ? 'primary': 'secondary'"
						to="/orders"
					/>
				</div>
			</div>
		</div>
	</q-footer>
</template>
