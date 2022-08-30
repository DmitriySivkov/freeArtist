<template>
	<q-item-label header>
		Выберите настройки
	</q-item-label>
	<q-form
		@submit="onSubmit"
		@reset="onReset"
		class="q-gutter-md q-pa-md"
	>
		<q-input
			filled
			label="Название *"
			v-model="product.title"
			lazy-rules
			:rules="[ val => val && val.length > 3 || 'Название должно быть длиннее 3 символов']"
		/>

		<q-input
			filled
			type="number"
			label="Стоимость *"
			v-model="product.price"
			lazy-rules
			:rules="[
				val => val !== null && val !== '' || 'Укажите стоимость',
			]"
		/>

		<q-input
			filled
			type="number"
			label="Доступное количество"
			v-model="product.amount"
		/>
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
				/>
			</div>
		</q-page-sticky>
	</q-form>
</template>

<script>
import { useStore } from "vuex"
import { computed, toRef, watch } from "vue"
import { useUserProducer } from "src/composables/userProducer"
import { useNotification } from "src/composables/notification"
export default {
	props: {
		selectedProduct: {
			type: Object,
			default: () => {}
		}
	},
	setup(props) {
		const product = toRef(props, "selectedProduct")

		const onSubmit = () => 123
		const onReset = () => 123

		return {
			product,
			onSubmit,
			onReset
		}
	}
}
</script>
