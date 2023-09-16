<template>
	<q-footer class="footer">
		<q-toolbar class="q-pa-sm">
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
							icon="home"
							:color="route.name === 'home' ? 'secondary': 'primary'"
							to="/"
						/>
					</div>
					<div
						v-if="isUserLogged"
						class="col-xs-shrink"
					>
						<q-btn
							class="q-pa-md"
							icon="account_circle"
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
	cartStore.data.reduce((carry, item) =>
		carry + item.products.length, 0)
)
</script>
