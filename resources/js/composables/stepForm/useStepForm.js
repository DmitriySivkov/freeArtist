import { computed, reactive } from "vue"
import { useStep } from "Root/composables/stepForm/useStep"

export function useStepForm(data) {
    const form = reactive(data);

    const currentStepIndex = computed(() =>
        Object.keys(form).find(stepIndex => form[stepIndex].isCurrent === true));

    const step = useStep(form, currentStepIndex);

    const validators = (input) =>
        Object.values(input.validation);

    return { form, step, validators }
}
