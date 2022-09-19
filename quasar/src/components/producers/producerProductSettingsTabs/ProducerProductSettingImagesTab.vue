<template>
	<q-btn
		flat
		label="Добавить изображение"
		class="bg-primary text-white full-width q-pa-lg"
		@click="showFilePrompt"
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
		const showFilePrompt = () => {
			$q.dialog({
				component: AddImageDialog
			}).onOk((option) => {
				alert(option)
			})
		}

		const img = ref(null)
		const takePicture = async() => {
			const base64 = await cordovaCamera.getBase64FromCamera()
		}

		return {
			takePicture,
			showFilePrompt
		}
	}
}
</script>
