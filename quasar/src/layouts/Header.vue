<template>
	<q-header
		elevated
		class="header"
	>
		<q-toolbar class="q-pa-md justify-center">
			<div class="col-xs-12 col-lg-8">
				<q-toolbar-title class="text-h5">
					{{ route.meta.route_name || ''}}
				</q-toolbar-title>
			</div>
		</q-toolbar>
	</q-header>
</template>

<script>
import { useRoute } from "vue-router"
export default {
	setup () {
		const route = useRoute()
		return {
			route
		}
	}
}
</script>
