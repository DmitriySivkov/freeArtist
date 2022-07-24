<template>
	<ProducerList />
	<router-view />
</template>

<script>
import ProducerList from "src/components/producers/ProducerList"
export default {
	components: { ProducerList },
}
</script>
