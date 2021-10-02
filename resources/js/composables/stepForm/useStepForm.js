import { reactive, ref } from "vue"
import { useStep } from "Root/composables/stepForm/useStep"
import { useApi } from "Root/composables/useApi";
import axios from "axios";

export function useStepForm(data) {
    const form = reactive(data);

    const step = useStep(form);

    const validators = (input) =>
        Object.values(input.validation);

    const submit = (data) => useApi(axios.post("api/register", data));

    return { form, step, validators, submit }
}
