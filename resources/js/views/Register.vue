<template>
    <main class="grow">
        <w-flex justify-center>
            <w-card title="Регистрация" title-class="blue-light5--bg" class="xs12 lg6">
                <w-form @input="this.form.onValidate">
                    <w-card v-show="this.step.common.isCurrent" class="xs12 lg6" title="Заполните поля" title-class="blue-light5--bg">
                        <w-input
                            label="Имя"
                            class="mb3"
                            :validators="this.wInput.getValidators(this.form.common.name)"
                        />
                        <w-input
                            label="Адрес электронной почты"
                            type="email"
                            class="mb3"
                            :validators="this.wInput.getValidators(this.form.common.email)"
                        />
                        <w-input
                            label="Пароль"
                            type="password"
                            class="mb3"
                            :validators="this.wInput.getValidators(this.form.common.password)"
                        />
                    </w-card>
                    <w-card v-show="this.step.role.isCurrent" class="xs12 lg6" title="Выберите роль" title-class="blue-light5--bg">
                        <w-radios
                            :items="this.form.role.items"
                            :validators="this.wInput.getValidators(this.form.role)"
                            inline
                        />
                    </w-card>
                    <w-button
                        type="submit"
                        class="mt3"
                        :disabled="!this.step[this.currentStep()].isValid"
                    >
                        Продолжить
                    </w-button>
                </w-form>
            </w-card>
        </w-flex>
    </main>
</template>

<script>
    import WaveInput from "Root/utils/WaveInput";
    export default {
        setup() {
            const step = {
                common: {
                    isValid: false,
                    isCurrent: true,
                },
                role: {
                    isValid: false,
                    isCurrent: false,
                },
            };
            const currentStep = () =>
                Object.keys(step).find(key => step[key].isCurrent === true);
            const form = {
                common: {
                    name: {
                        validation: {
                            required: value => {
                                if (!!value) {
                                    step[currentStep()].isValid = true;
                                    return true;
                                } else {
                                    step[currentStep()].isValid = false;
                                    return "Необходимо заполнить имя"
                                }
                            }
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
                onValidate: status => console.log(step)
            };
            const wInput = new WaveInput();
            return { form, currentStep, wInput, step }
        },
    }
</script>
