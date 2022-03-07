import { computed, ref } from "vue"

export function useStep(form) {
    const current = ref(0);

    const isInvalid = computed(() =>
        Object.keys(form[current.value].fields)
            .find(key => !form[current.value].fields[key].isValid));

    const isLast = computed(() => current.value === Object.keys(form).length-1);
    const isFirst = computed(() => current.value === 0);

    const next = function () {
        form[current.value].isCurrent = false
        current.value++
        form[current.value].isCurrent = true
    };
    const previous = function () {
        form[current.value].isCurrent = false
        current.value--
        form[current.value].isCurrent = true
    };

    return { isInvalid, isFirst, isLast, next, previous }
}
