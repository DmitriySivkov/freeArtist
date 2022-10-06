<template>
	<q-list>
		<q-item-label header>
			Задайте права
		</q-item-label>
		<q-item
			tag="label"
			v-for="(permission, index) in all_producer_permissions"
			:key="index"
		>
			<q-item-section
				side
				top
			>
				<q-checkbox
					v-model="selected_permissions"
					:val="permission.id"
					:disable="!isAbleToEditUserPermissions"
				/>
			</q-item-section>

			<q-item-section>
				<q-item-label>{{ permission.display_name }}</q-item-label>
			</q-item-section>
		</q-item>
	</q-list>
	<q-page-sticky
		expand
		position="bottom"
		class="row q-pa-xs"
	>
		<div class="col-xs-12 col-md-6">
			<q-btn
				label="Сохранить"
				type="submit"
				color="primary"
				class="q-pa-lg full-width"
				@click="setUserPermissions(producer.id, userId, selected_permissions)"
				:disable="!isAbleToEditUserPermissions"
			/>
		</div>
	</q-page-sticky>
</template>

<script>
import { useStore } from "vuex"
import { computed, ref, watch } from "vue"
import { useUserProducer } from "src/composables/userProducer"
import { useNotification } from "src/composables/notification"
export default {
	props: {
		userId: Number,
		producer: {
			type: Object,
			default: () => ({})
		}
	},
	setup(props) {
		const $store = useStore()
		const { getProducerUser, syncProducerUserPermissions } = useUserProducer()
		const { notifySuccess, notifyError } = useNotification()
		const all_producer_permissions = computed(() => $store.state.permission.producer)

		const selected_permissions = ref(
			props.userId === props.producer.user_id ?
				all_producer_permissions.value.map((p) => p.id) :
				getProducerUser(props.producer.id, props.userId).permissions.map((p) => p.id)
		)
		const auth_user = computed(() => $store.state.user)
		const selected_user = computed(() => props.producer.users.find((u) => u.id === props.userId))

		const isAbleToEditUserPermissions = computed(() =>
			props.userId !== props.producer.user_id &&
			(
				auth_user.value.data.id === props.producer.user_id ||
				props.producer.users.find((u) => u.id === auth_user.value.data.id)
					.permissions
					.map((p) => p.name)
					.includes("producer_manage_permissions")
			)
		)

		const setUserPermissions = (producer_id, user_id, permissions) => {
			syncProducerUserPermissions(producer_id, user_id, permissions)
				.then(() => {
					notifySuccess(
						"Права пользователя: '" +
						(selected_user.value.name ?? selected_user.value.phone) +
						"' успешно изменены"
					)
				}).catch((error) => {
					notifyError(error.response.data)
				})
		}

		watch(() => props.userId, (selected_user_id) => {
			selected_permissions.value = selected_user_id === props.producer.user_id ?
				all_producer_permissions.value.map((p) => p.id) :
				getProducerUser(props.producer.id, selected_user_id).permissions.map((p) => p.id)
		})

		return {
			all_producer_permissions,
			selected_permissions,
			isAbleToEditUserPermissions,
			setUserPermissions
		}
	}
}
</script>
