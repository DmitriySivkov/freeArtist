<script setup>
defineProps({
	modelValue: Number,
	users: {
		type: Array,
		default: () => []
	}
})

defineEmits([
	"update:modelValue"
])
</script>

<template>
	<q-list>
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
					@update:model-value="$emit('update:modelValue', user.id)"
					:val="user.id"
				/>
			</q-item-section>
			<q-item-section>
				<q-item-label>{{ user.name ?? user.phone }}</q-item-label>
			</q-item-section>
		</q-item>
	</q-list>
</template>
