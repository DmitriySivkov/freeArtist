<template>
	<div class="row">
		<div class="col-xs-12 col-md-3">
			<q-card
				bordered
				class="q-ma-md border-dashed border-black bg-green-3 shadow-0"
				:class="{'text-white bg-green-6': isDragging}"
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
					@dragenter.prevent="isDragging = true"
					@dragleave.prevent="isDragging = false"
					@dragover.prevent
					@drop.prevent="drop"
				>
					Добавить фото
				</q-card-section>
			</q-card>
			<q-file
				v-model="image"
				ref="file_picker"
				accept=".jpg, image/*"
				style="display:none"
			/>
		</div>
	</div>
</template>

<script>
import { ref } from "vue"
export default {
	setup() {
		const image = ref(null)
		const isDragging = ref(false)
		const backend_server = process.env.BACKEND_SERVER

		const drop = (e) => {
			console.log(e.dataTransfer.files[0]) //file here
		}

		return {
			image,
			backend_server,
			isDragging,
			drop
		}
	}
}
</script>
