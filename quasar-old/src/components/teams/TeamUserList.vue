<template>
	<q-list>
		<q-item-label header>
			Выберите пользователя
		</q-item-label>
		<q-item
			tag="label"
			v-for="(user, index) in users"
			:key="index"
		>
			<q-item-section
				side
				top
			>
				<q-radio
					:model-value="modelValue"
					@update:model-value="userChanged"
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
export default {
	props: {
		modelValue: Number,
		users: {
			type: Array,
			default: () => []
		}
	},
	setup(props, context) {
		const userChanged = (user_id) => {
			context.emit("update:modelValue", user_id)
		}
		return {
			userChanged
		}
	}
}
</script>
