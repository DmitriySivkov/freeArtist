<script setup>
import { computed } from "vue"
import { useUserStore } from "src/stores/user"
import { PRODUCER_PERMISSIONS } from "src/const/permissions"

const props = defineProps({
	ownerId: Number,
	user: {
		type: Object,
		default: () => {}
	},
	isAbleToEditUserPermissions: {
		type: Boolean,
		default: true
	}
})

const emit = defineEmits([
	"change"
])

const userStore = useUserStore()

const selectedPermissions = computed({
	get: () => props.user.id === props.ownerId ?
		PRODUCER_PERMISSIONS.map((p) => p.name) :
		props.user.permissions,
	set: (permissions) => emit("change", { userId: props.user.id, permissions })
})
</script>

<template>
	<q-list>
		<q-item
			tag="label"
			v-for="(permission, index) in PRODUCER_PERMISSIONS"
			:key="index"
		>
			<q-item-section
				side
				top
			>
				<q-checkbox
					v-model="selectedPermissions"
					:val="permission.name"
					:disable="!isAbleToEditUserPermissions"
					indeterminate-value="false"
				/>
			</q-item-section>

			<q-item-section>
				<q-item-label>{{ permission.display_name }}</q-item-label>
			</q-item-section>
		</q-item>
	</q-list>
</template>
