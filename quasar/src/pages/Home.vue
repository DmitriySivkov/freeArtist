<template>
	<ProducerList />
	<router-view />
</template>

<script>
import ProducerList from "src/components/home/ProducerList"
export default {
	components: { ProducerList },
}
</script>
