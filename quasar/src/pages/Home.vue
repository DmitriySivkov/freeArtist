<template>
	<ProducerListHome />
	<router-view />
</template>

<script>
import ProducerListHome from "src/components/producers/ProducerListHome"
export default {
	components: { ProducerListHome },
}
</script>
