<template>
	<q-btn
		flat
		label="Добавить изображение"
		class="bg-primary text-white full-width q-pa-lg"
		@click="showFilePrompt"
	/>
	<q-img
		v-if="img_path"
		:src="img_path"
		:ratio="1"
		width="150px"
	/>
	<q-file
		v-model="img"
		ref="file_picker"
		accept=".jpg, image/*"
		style="display:none"
		@update:model-value="getImagePath"
	/>
</template>

<script>
import { useQuasar } from "quasar"
import { useRouter } from "vue-router"
import { useNotification } from "src/composables/notification"
import { ref, computed } from "vue"
import { useStore } from "vuex"
import cordovaCamera from "src/services/cordova-camera"
import AddImageDialog from "src/components/dialogs/AddImageDialog"
export default {
	// eslint-disable-next-line vue/no-unused-components
	components: { AddImageDialog },
	props: {
		selectedProduct: {
			type: Object,
			default: () => {}
		}
	},
	setup(props) {
		const $q = useQuasar()
		const img = ref(null)
		const img_path = ref("")
		const file_picker = ref(null)

		const showFilePrompt = () => {
			$q.dialog({
				component: AddImageDialog
			}).onOk((option) => {
				if (option === 1)
					fromGallery()
			})
		}

		const fromGallery = () => {
			file_picker.value.pickFiles()
		}

		const takePicture = async() => {
			const base64 = await cordovaCamera.getBase64FromCamera()
		}

		const getImagePath = (img) => {
			img_path.value = URL.createObjectURL(img)
		}

		return {
			img,
			img_path,
			file_picker,
			takePicture,
			showFilePrompt,
			getImagePath
		}
	}
}
</script>
