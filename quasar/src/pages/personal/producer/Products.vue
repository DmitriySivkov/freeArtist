<template>
	<div class="column absolute full-width no-wrap bg-primary">
		<div class="row full-height justify-center">
			<div class="col-xs-12 col-md-8 bg-white">
				<ProducerProductList
					:producer-id="team.detailed_id"
					:is-able-to-manage-product="isAbleToManageProduct"
				/>
			</div>
		</div>
	</div>
</template>

<script setup>
import ProducerProductList from "src/components/producers/ProducerProductList.vue"
import { useRouter } from "vue-router"
import { computed } from "vue"
import { useUserPermission } from "src/composables/userPermission"
import { useUserStore } from "src/stores/user"
import { useTeamStore } from "src/stores/team"

const userStore = useUserStore()
const teamStore = useTeamStore()
const $router = useRouter()

const { hasPermission } = useUserPermission()

const team = computed(() =>
	teamStore.user_teams.find((t) =>
		t.id === parseInt($router.currentRoute.value.params.team_id)
	)
)

const isAbleToManageProduct = computed(() =>
	hasPermission(team.value.id,"producer_product") ||
			team.value.user_id === userStore.data.id
)

</script>
