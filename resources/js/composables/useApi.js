import { ref } from "vue"

export function useApi(subject) {
    const result = ref("")
    const error = ref(false)
    const loading = ref(false)

    const call = async () => {
        error.value = false
        loading.value = true
        try {
            const res = await subject
        } catch {
            error.value = true
        } finally {
            loading.value = false
        }
    }

    return { result, call, loading, error }
}
