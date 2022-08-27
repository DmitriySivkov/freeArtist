<template>
	<ProducerStorefrontSettingList :settings="settings"/>
</template>
<script>
import ProducerStorefrontSettingList from "src/components/producers/ProducerStorefrontSettingList"
export default {
	components: { ProducerStorefrontSettingList },
	setup() {
		const settings = [
			{title: "Управлять продуктами", route_name: "personal_producer_storefront_settings_products"}
		]
		return {
			settings
		}
	}
}
</script>
