<template>
	123
</template>

<script>
import { useRouter } from "vue-router"
import { useNotification } from "src/composables/notification"
import { ref, computed } from "vue"
import { useStore } from "vuex"
import cordovaCamera from "src/services/cordova-camera"
export default {
	props: {
		selectedProduct: {
			type: Object,
			default: () => {}
		}
	},
	setup(props) {
		const img = ref()
		const uploadImageFromCamera = async() => {
			const base64 = await cordovaCamera.getBase64FromCamera()
		}

		return {

		}
	}
}
</script>
