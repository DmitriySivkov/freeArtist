<template>
	<q-list bordered>
		<q-item
			v-if="!isUserLogged"
			clickable
			to="/auth"
			:class="{'bg-primary text-white': ['login', 'register'].includes(route.name)}"
		>
			<q-item-section avatar>
				<q-icon name="login" />
			</q-item-section>

			<q-item-section>Войти</q-item-section>
		</q-item>
		<q-item
			v-if="isUserLogged"
			clickable
			@click="logout"
		>
			<q-item-section avatar>
				<q-icon name="login" />
			</q-item-section>

			<q-item-section>Выйти</q-item-section>
		</q-item>
		<q-item
			clickable
			to="/"
			:class="route.name === 'home' ? 'bg-primary text-white' : 'text-dark'"
		>
			<q-item-section avatar>
				<q-icon name="home" />
			</q-item-section>

			<q-item-section>Главная</q-item-section>
		</q-item>

		<q-item
			v-if="isUserLogged"
			clickable
			to="/personal"
			:class="{'bg-primary text-white': route.name.includes('personal')}"
		>
			<q-item-section avatar>
				<q-icon name="account_circle" />
			</q-item-section>

			<q-item-section>Личный кабинет</q-item-section>
		</q-item>

		<q-item
			clickable
			to="/cart"
			:class="{'bg-primary text-white': route.name === 'cart'}"
		>
			<q-item-section avatar>
				<q-icon name="shopping_cart" />
			</q-item-section>

			<q-item-section>
				<div>Корзина
					<span v-if="cartCounter">
						(<small>{{ cartCounter }}</small>)
					</span>
				</div>
			</q-item-section>
		</q-item>

		<q-item
			clickable
			to="/orders"
			:class="{'bg-primary text-white': route.name === 'user_orders'}"
		>
			<q-item-section avatar>
				<q-icon name="list" />
			</q-item-section>

			<!-- todo - order count -->
			<q-item-section>Заказы</q-item-section>
		</q-item>
	</q-list>
</template>

<script setup>
import { computed } from "vue"
import { useRouter } from "vue-router"
import { useCartStore } from "src/stores/cart"
import { useUserStore } from "src/stores/user"
import { useNotification } from "src/composables/notification"
import { useTeamStore } from "stores/team"
import { useRelationRequestStore } from "stores/relation-request"
import { api } from "src/boot/axios"

const $router = useRouter()
const cartStore = useCartStore()
const userStore = useUserStore()
const teamStore = useTeamStore()
const relationRequestStore = useRelationRequestStore()

const { notifySuccess } = useNotification()

const route = $router.currentRoute

const isUserLogged = computed(() => userStore.is_logged)

const logout = () => {
	api.post("personal/logout")

	userStore.switchPersonal("user")

	userStore.setData({})
	teamStore.emptyUserTeams()
	relationRequestStore.emptyUserRequests()

	userStore.setIsLogged(false)

	$router.push({name: "home"})
}

const cartCounter = computed(() =>
	cartStore.data.reduce((carry, item) =>
		carry + item.products.length, 0)
)
</script>
