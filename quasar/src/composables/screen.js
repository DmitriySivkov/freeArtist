import { useQuasar } from "quasar"
import { computed } from "vue"

export const useScreen = () => {
	const $q = useQuasar()

	const isSmallScreen = computed(() => $q.screen.width < $q.screen.sizes.md)

	return {
		isSmallScreen
	}
}
