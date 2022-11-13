<template>
	<div class="row">
		<div class="col-xs-12 col-md-3">
			<q-card
				bordered
				class="q-ma-md border-dashed border-black bg-pink-1 shadow-0"
				style="height:300px"
			>
				<q-card-section v-if="image">
					<!--					<q-img-->
					<!--						:src="backend_server + '/storage/' + image.path"-->
					<!--						fit="contain"-->
					<!--					/>-->
					<q-img
						fit="contain"
					/>
				</q-card-section>
				<q-card-section
					v-else
					class="row flex-center full-height cursor-pointer"
				>
					Добавьте или перетащите фото
				</q-card-section>
			</q-card>
		</div>
	</div>
</template>

<script>
import { ref } from "vue"
export default {
	setup() {
		const image = ref(null)
		const backend_server = process.env.BACKEND_SERVER

		return {
			image,
			backend_server
		}
	}
}
</script>
