<template>
	<q-footer class="footer">
		<q-toolbar class="q-pa-sm">
			<div class="col-xs-12 col-lg-8">
				<div class="row q-gutter-sm">
					<div
						v-if="!isUserLogged"
						class="col-shrink"
					>
						<q-btn
							class="q-pa-md"
							icon="login"
							:color="['login', 'register'].includes(route.name) ? 'secondary': 'primary'"
							to="/auth"
						/>
					</div>
					<div class="col-shrink">
						<q-btn
							class="q-pa-md"
							icon="home"
							:color="route.name === 'home' ? 'secondary': 'primary'"
							to="/"
						/>
					</div>
					<div
						v-if="isUserLogged"
						class="col-shrink"
					>
						<q-btn
							class="q-pa-md"
							icon="account_circle"
							:color="route.name.includes('personal') ? 'secondary': 'primary'"
							to="/personal"
						/>
					</div>
					<div class="col-shrink">
						<q-btn
							class="q-pa-md"
							:label="cartCounter"
							icon-right="shopping_cart"
							:color="route.name === 'cart' ? 'secondary': 'primary'"
							to="/cart"
						/>
					</div>
					<div
						v-if="isUserLogged"
						class="col text-right"
					>
						<q-btn
							class="q-pa-md"
							icon="logout"
							@click="logout"
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
import { api } from "src/boot/axios"
import { useTeamStore } from "src/stores/team"
import { useRelationRequestStore } from "src/stores/relation-request"

const $router = useRouter()
const cartStore = useCartStore()
const userStore = useUserStore()
const teamStore = useTeamStore()
const relationRequestStore = useRelationRequestStore()

const route = $router.currentRoute

const isUserLogged = computed(() => userStore.is_logged)

const cartCounter = computed(() =>
	cartStore.data.reduce((carry, item) =>
		carry + item.products.length, 0)
)


// todo - move logout action to composable
const logout = () => {
	api.post("personal/logout")

	userStore.switchPersonal("user")

	userStore.setData({})
	teamStore.emptyUserTeams()
	relationRequestStore.emptyUserRequests()

	userStore.setIsLogged(false)

	$router.push({name: "home"})
}
</script>
