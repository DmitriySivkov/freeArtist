<template>
	<div class="column no-wrap absolute full-height full-width">
		<div class="col-3">
			<ProducerFilterHome />
		</div>
		<div class="col-grow">
			<ProducerListHome />
		</div>
	</div>
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
