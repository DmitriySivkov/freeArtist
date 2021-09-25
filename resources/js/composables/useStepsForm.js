import { computed, reactive } from "vue"

export function useStepsForm(data) {
    const form = reactive(data);

    const currentStepIndex = computed(() =>
        Object.keys(form).find(key => form[key].isCurrent === true));

    const step = {
        index: currentStepIndex.value,
        isInvalid: computed(() =>
            Object.keys(form[currentStepIndex.value].fields)
                .find(key => form[currentStepIndex.value].fields[key].isValid !== true)),
        next: function () {
            form[parseInt(currentStepIndex.value)+1].isCurrent = true
            form[currentStepIndex.value].isCurrent = false
        },
        previous: function () {
            form[parseInt(currentStepIndex.value)-1].isCurrent = true
            form[parseInt(currentStepIndex.value)+1].isCurrent = false
        }
    };

    const validators = (input) =>
        Object.values(input.validation);

    return { form, step, validators }
}
