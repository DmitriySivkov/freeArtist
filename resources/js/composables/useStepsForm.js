import { computed, reactive } from "vue"

export function useStepsForm(data) {
    const form = reactive(data);

    const currentStepIndex = computed(() =>
        Object.keys(form).find(stepIndex => form[stepIndex].isCurrent === true));

    const step = {
        isInvalid: computed(() =>
            Object.keys(form[currentStepIndex.value].fields)
                .find(key => form[currentStepIndex.value].fields[key].isValid !== true)),
        next: function () {
            let i = parseInt(currentStepIndex.value);
            form[i+1].isCurrent = true;
            form[i].isCurrent = false;
        },
        previous: function () {
            let i = parseInt(currentStepIndex.value);
            form[i-1].isCurrent = true;
            form[i].isCurrent = false;
        }
    };

    const validators = (input) =>
        Object.values(input.validation);

    return { form, step, validators }
}
