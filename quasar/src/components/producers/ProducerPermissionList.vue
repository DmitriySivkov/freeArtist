<template>
	<q-list>
		<q-item-label header>
			Задайте права
		</q-item-label>
		<q-item
			tag="label"
			v-ripple
			v-for="(permission, index) in all_producer_permissions"
			:key="index"
		>
			<q-item-section
				side
				top
			>
				<q-checkbox
					v-model="permission_model"
					:val="permission.id"
					disable
				/>
			</q-item-section>

			<q-item-section>
				<q-item-label>{{ permission.display_name }}</q-item-label>
			</q-item-section>
		</q-item>
	</q-list>
</template>

<script>
import { useStore } from "vuex"
import { computed, ref } from "vue"
import { useUserProducer } from "src/composables/userProducer"
export default {
	props: {
		producer: {
			type: Object,
			default: () => {}
		}
	},
	setup(props) {
		const $store = useStore()
		const { userOwnProducer } = useUserProducer()
		const all_producer_permissions = computed(() => $store.state.permission.producer)
		const permission_model = ref(
			userOwnProducer.value.id === props.producer.id ?
				all_producer_permissions.value.map((p) => p.id) :
				[]
		)
		return {
			all_producer_permissions,
			permission_model
		}
	}
}
</script>
