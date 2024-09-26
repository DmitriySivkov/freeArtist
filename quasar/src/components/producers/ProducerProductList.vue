<script setup>
import { ref, onMounted } from "vue"
import { Dialog } from "quasar"
import { useRouter } from "vue-router"
import { useNotification } from "src/composables/notification"
import { api } from "src/boot/axios"
import CommonConfirmationDialog from "src/components/dialogs/CommonConfirmationDialog.vue"

const props = defineProps({
	isAbleToManageProduct: Boolean
})

const $router = useRouter()
const { notifySuccess, notifyError } = useNotification()

const products = ref([])
const isMounting = ref(true)

const show = (product) => {
	$router.push({
		name:"personal_producer_products_show",
		params: {
			producer_id: $router.currentRoute.value.params.producer_id,
			product_id: product.id
		}
	})
}

const showDeleteDialog = (product) => {
	Dialog.create({
		component: CommonConfirmationDialog,
		componentProps: {
			text: `Удалить: &laquo;${product.title}&raquo; ?`,
			headline: "Подтвердите действие"
		}
	}).onOk(() => {
		product.is_loading = true

		const promise = deleteProduct(product.id)

		promise.then(() => {
			const index = products.value.findIndex((p) => p.id === product.id)

			products.value.splice(index, 1)

			notifySuccess(`Продукт «${product.title}» успешно удалён`)
		})

		promise.catch((error) => {
			delete (product.is_loading)

			notifyError(error.response.data)
		})
	})
}

const deleteProduct = (product_id) => {
	return api.delete(`personal/products/${product_id}`,{ data: { product_id } })
}

onMounted(() => {
	const promise = api.get(`personal/producers/${$router.currentRoute.value.params.producer_id}/products`)

	promise.then((response) => {
		products.value = response.data
	})

	promise.catch((error) => {
		notifyError(error.response.data)
	})

	promise.finally(() => isMounting.value = false)
})

</script>

<template>
	<q-linear-progress
		v-if="isMounting"
		indeterminate
		color="primary"
	/>

	<q-list class="q-pa-xs">
		<q-item
			v-for="(product, index) in products"
			:key="index"
			class="bg-primary text-white q-mb-xs rounded-borders"
			clickable
		>
			<q-item-section
				avatar
				@click="show(product)"
			>
				<q-icon
					size="sm"
					color="white"
					name="edit"
				/>
			</q-item-section>
			<q-item-section
				class="text-body1"
				@click="show(product)"
			>
				{{ product.title }}
			</q-item-section>
			<q-item-section side>
				<q-btn
					v-if="isAbleToManageProduct"
					flat
					icon="cancel"
					text-color="white"
					class="full-width q-py-lg"
					@click="showDeleteDialog(product)"
				/>
			</q-item-section>
			<q-inner-loading :showing="product.is_loading">
				<q-spinner-gears
					size="42px"
					color="primary"
				/>
			</q-inner-loading>
		</q-item>
	</q-list>
</template>
