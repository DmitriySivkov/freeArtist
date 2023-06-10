<template>
	<ProducerFilterHome />
	<ProducerListHome />
</template>

<script>
import ProducerListHome from "src/components/producers/ProducerListHome.vue"
import ProducerFilterHome from "src/components/producers/ProducerFilterHome.vue"
export default {
	components: {
		ProducerListHome,
		ProducerFilterHome
	},
}
</script>
