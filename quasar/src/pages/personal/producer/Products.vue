<script setup>
import { useRouter } from "vue-router"
import { computed } from "vue"
import { useUserStore } from "src/stores/user"
import { useUser } from "src/composables/user"
import { PRODUCER_PERMISSION_NAMES } from "src/const/permissions"
import ProducerProductList from "src/components/producers/ProducerProductList.vue"

const userStore = useUserStore()
const $router = useRouter()

const { hasPermission } = useUser()

const team = computed(() =>
	userStore.teams.find((t) =>
		t.detailed_id === parseInt($router.currentRoute.value.params.producer_id)
	)
)

const isAbleToManageProduct = computed(() =>
	hasPermission(team.value.id, PRODUCER_PERMISSION_NAMES.PRODUCT) ||
	team.value.user_id === userStore.data.id
)

const create = () => {
	$router.push({
		name:"personal_producer_products_create",
		params: {
			producer_id: team.value.detailed_id,
		}
	})
}
</script>

<template>
	<q-page class="row justify-center">
		<div class="col-xs-12 col-md-8 bg-white">
			<q-btn
				no-caps
				class="full-width q-px-md q-py-lg bg-secondary"
				@click="create"
			>
				<span class="text-h6 text-white">Создать продукт</span>
			</q-btn>

			<ProducerProductList
				:is-able-to-manage-product="isAbleToManageProduct"
			/>
		</div>
	</q-page>
</template>
