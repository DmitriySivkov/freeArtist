<template>
	<div class="q-pa-md">
		<q-list
			bordered
			separator
		>
			<q-item
				v-for="(setting, index) in settings"
				:key="index"
				clickable
				v-ripple
				@click="goToSettingDetail(setting.route_name)"
			>
				<q-item-section>
					{{ setting.title }}
				</q-item-section>
			</q-item>
		</q-list>
	</div>
</template>

<script>
import { useRouter } from "vue-router"
export default ({
	props: {
		settings: {
			type: Array,
			default: () => []
		}
	},
	setup() {
		const $router = useRouter()

		const goToSettingDetail = (route_name) => $router.push({
			name: route_name,
		})

		return {
			goToSettingDetail
		}
	}
})
</script>
