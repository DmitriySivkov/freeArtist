<template>
	<q-header elevated>
		<q-toolbar class="q-pa-md q-pa-md q-pl-lg">
			<q-toolbar-title class="text-h5">
				{{ route.meta.route_name || ''}}
			</q-toolbar-title>
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
