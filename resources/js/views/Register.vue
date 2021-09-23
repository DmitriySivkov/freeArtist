<template>
    <main class="grow">
        <w-flex justify-center>
            <w-card title="Регистрация" title-class="blue-light5--bg" class="xs12 lg6">
                <w-form
                    @update:model-value="this.wForm.step[this.currentStep].isValid = this.findInvalidFields($refs) === undefined"
                >
                    <w-card v-show="this.wForm.step.common.isCurrent" class="xs12 lg6" title="Заполните поля" title-class="blue-light5--bg">
                        <w-input
                            ref="name"
                            label="Имя"
                            class="mb3"
                            :validators="this.wForm.getValidators(this.wForm.common.name)"
                        />
                        <w-input
                            ref="email"
                            label="Адрес электронной почты"
                            type="email"
                            class="mb3"
                            :validators="this.wForm.getValidators(this.wForm.common.email)"
                        />
                        <w-input
                            ref="password"
                            label="Пароль"
                            type="password"
                            class="mb3"
                            :validators="this.wForm.getValidators(this.wForm.common.password)"
                        />
                    </w-card>
                    <w-card v-show="this.wForm.step.role.isCurrent" class="xs12 lg6" title="Выберите роль" title-class="blue-light5--bg">
                        <w-radios
                            ref="role"
                            :items="this.wForm.role.items"
                            :validators="this.wForm.getValidators(this.wForm.role)"
                            inline
                        />
                    </w-card>
                    <w-button
                        type="submit"
                        class="mt3"
                        :disabled="!this.wForm.step[this.currentStep].isValid"
                    >
                        Продолжить
                    </w-button>
                </w-form>
            </w-card>
        </w-flex>
    </main>
</template>

<script>
    import { computed, reactive } from "vue"
    import WaveForm from "Root/utils/WaveForm";
    export default {
        setup() {
            const wForm = reactive(new WaveForm({
                common: {
                    name: {
                        validation: {
                            required: value => !!value || "Необходимо заполнить имя"
                        }
                    },
                    email: {
                        validation: {
                            required: value => !!value || "Необходимо заполнить электронную почту"
                        }
                    },
                    password: {
                        validation: {
                            required: value => !!value || "Необходимо заполнить пароль",
                            minLength: value => value.length >= 8 || "Введите еще " + (8 - value.length) + " знаков"
                        }
                    },
                },
                role: {
                    items: [
                        { label: "Мастер", value: 1 },
                        { label: "Организация", value: 2 },
                        { label: "Рекламодатель", value: 3 }
                    ],
                    validation: {
                        required: value => !!value || "Необходимо выбрать роль"
                    }
                },
                step: {
                    common: { isValid: false, isCurrent: true },
                    role: { isValid: false, isCurrent: false },
                }
            }));
            const currentStep = computed(() =>
                Object.keys(wForm.step).find(key => wForm.step[key].isCurrent === true)
            );
            const findInvalidFields = (fields) => wForm.validateStep(fields, currentStep.value);
            return { wForm, currentStep, findInvalidFields }
        }
    }
</script>
