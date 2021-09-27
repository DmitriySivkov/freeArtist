import { computed } from "vue"

export function useStep(form, currentStepIndex) {
    const isInvalid = computed(() =>
        Object.keys(form[currentStepIndex.value].fields)
            .find(key => form[currentStepIndex.value].fields[key].isValid !== true));
    const isLast = computed(() => parseInt(currentStepIndex.value) === Object.keys(form).length-1);
    const isFirst = computed(() => parseInt(currentStepIndex.value) === 0);
    const next = function () {
        let i = parseInt(currentStepIndex.value);
        form[i+1].isCurrent = true;
        form[i].isCurrent = false;
    };
    const previous = function () {
        let i = parseInt(currentStepIndex.value);
        form[i-1].isCurrent = true;
        form[i].isCurrent = false;
    };

    return { isInvalid, isFirst, isLast, next, previous }
}
