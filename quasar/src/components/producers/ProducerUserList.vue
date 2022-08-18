<template>
	<q-list>
		<q-item-label header>
			Выберите пользователя
		</q-item-label>
		<q-item
			tag="label"
			v-ripple
			v-for="(user, index) in users"
			:key="index"
		>
			<q-item-section
				side
				top
			>
				<q-radio
					v-model="selected_user"
					:val="user.id"
				/>
			</q-item-section>

			<q-item-section>
				<q-item-label>{{ user.name ?? user.phone }}</q-item-label>
			</q-item-section>
		</q-item>
	</q-list>
</template>

<script>
import { useStore } from "vuex"
import { computed, ref } from "vue"
export default {
	props: {
		users: {
			type: Array,
			default: () => []
		}
	},
	setup(props) {
		const $store = useStore()
		const user = computed(() => $store.state.user)
		const selected_user = ref(
			props.users.find((u) => u.id === user.value.data.id).id
		)
		return {
			selected_user
		}
	}
}
</script>
