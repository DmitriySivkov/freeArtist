<script setup>
import { computed } from "vue"
import { useRouter } from "vue-router"
import { useCartStore } from "src/stores/cart"
import { useUserStore } from "src/stores/user"
import { useNotification } from "src/composables/notification"

const $router = useRouter()
const cart_store = useCartStore()
const user_store = useUserStore()

const route = $router.currentRoute

const is_user_logged = computed(() => user_store.is_logged)

const cartCounter = computed(
	() => Object.values(cart_store.data)
		.reduce((accum, cart_item) => accum + cart_item.product_list.length, 0)
)

const { notifySuccess } = useNotification()

const logout = () => {
	user_store.logout().then(() =>
	{
		notifySuccess("До свидания!")
		$router.push({"name": "home"})
	}
	)
}
</script>
<template>
	<q-list bordered>

		<q-item
			v-if="!is_user_logged"
			clickable
			v-ripple
			to="/auth"
			:class="{'bg-primary text-white': ['login', 'register'].includes(route.name)}"
		>
			<q-item-section avatar>
				<q-icon name="login" />
			</q-item-section>

			<q-item-section>{{ !is_user_logged ? 'Войти' : 'Выйти' }}</q-item-section>
		</q-item>
		<q-item
			v-if="is_user_logged"
			clickable
			v-ripple
			@click="logout"
			:class="{'bg-primary text-white': ['login', 'register'].includes(route.name)}"
		>
			<q-item-section avatar>
				<q-icon name="login" />
			</q-item-section>

			<q-item-section>{{ !is_user_logged ? 'Войти' : 'Выйти' }}</q-item-section>
		</q-item>
		<q-item
			clickable
			v-ripple
			to="/"
			:class="route.name === 'home' ? 'bg-primary text-white' : 'text-dark'"
		>
			<q-item-section avatar>
				<q-icon name="home" />
			</q-item-section>

			<q-item-section>Главная</q-item-section>
		</q-item>

		<q-item
			v-if="is_user_logged"
			clickable
			v-ripple
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
			v-ripple
			to="/cart"
			:class="{'bg-primary text-white': route.name === 'cart'}"
		>
			<q-item-section avatar>
				<q-icon name="shopping_cart" />
			</q-item-section>

			<q-item-section>Корзина{{ cartCounter ? ` (${cartCounter})` : '' }}</q-item-section>
		</q-item>
	</q-list>
</template>
