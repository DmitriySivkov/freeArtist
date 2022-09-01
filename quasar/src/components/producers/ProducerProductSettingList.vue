<template>
	<q-form
		@submit="onSubmit"
		@reset="onReset"
		class="q-gutter-md"
	>
		<q-card>
			<q-tabs
				v-model="tab"
				dense
				inline-label
				active-color="white"
				active-bg-color="secondary"
				indicator-color="transparent"
				align="justify"
				class="text-white bg-indigo-10"
			>
				<q-tab
					name="common"
					label="Общие"
					class="q-pa-sm"
				/>
				<q-tab
					name="composition"
					label="Состав"
					class="q-pa-sm"
				/>
				<q-tab
					name="pictures"
					label="Изображения"
					class="q-pa-sm"
				/>
			</q-tabs>

			<q-separator />

			<q-tab-panels
				v-model="tab"
				animated
			>
				<q-tab-panel name="common">
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
				</q-tab-panel>

				<q-tab-panel name="composition">
					<q-btn
						label="Добавить ингридиент"
						color="primary"
						class="q-mb-md"
						@click="addIngridient"
					/>
					<q-card
						v-for="(ingredient, index) in product.composition"
						:key="index"
						flat
					>
						<q-input
							filled
							label="Название ингридиента"
							v-model="ingredient.name"
							lazy-rules
							:rules="[ val => val && val.length > 2 || 'Название ингридиента должно быть длиннее 2 символов']"
							class="q-pb-xs"
						/>
						<q-input
							filled
							type="textarea"
							label="Описание ингридиента (необязательно)"
							v-model="ingredient.description"
							lazy-rules
						/>
						<q-separator
							v-if="index !== product.composition.length-1"
							class="q-mt-sm q-mb-sm bg-primary"
						/>
					</q-card>
				</q-tab-panel>

				<q-tab-panel name="pictures">
					3
				</q-tab-panel>
			</q-tab-panels>
		</q-card>

	</q-form>
	<q-page-sticky
		v-if="Object.keys(selectedProduct).length > 0"
		expand
		position="bottom"
		class="row q-mb-xs"
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
</template>

<script>
import { useStore } from "vuex"
import { computed, ref } from "vue"
import { useUserProducer } from "src/composables/userProducer"
import { useNotification } from "src/composables/notification"
import _ from "lodash"
export default {
	props: {
		selectedProduct: {
			type: Object,
			default: () => {}
		}
	},
	setup(props) {
		const product = ref(_.cloneDeep(props.selectedProduct))
		const tab = ref("common")

		const onSubmit = () => 123
		const onReset = () => 123

		const addIngridient = () =>
			product.value.composition.unshift({name:"", description:""})


		return {
			product,
			tab,
			onSubmit,
			onReset,
			addIngridient
		}
	}
}
</script>
