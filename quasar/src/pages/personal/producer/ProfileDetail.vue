<template>
	<ProducerProfile :team="team"/>
</template>

<script setup>
import { useRoute } from "vue-router"
import ProducerProfile from "src/components/producers/ProducerProfile.vue"
import { useTeamStore } from "src/stores/team"

const $route = useRoute()
const team_store = useTeamStore()
const team = team_store.user_teams.find(
	(t) => t.detailed.id === parseInt($route.params.producer_id)
)
</script>
