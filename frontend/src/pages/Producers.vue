<template>
	<ProducerList />
	<router-view />
</template>

<script>
import { useStore } from "vuex"
import ProducerList from "src/components/producers/ProducerList"
export default {
	components: { ProducerList },
	setup() {
		const $store = useStore()
		$store.dispatch("producer/getList")
	}
}
</script>
