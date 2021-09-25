<template>
    <main class="grow">
        <w-flex justify-center>
            <w-card title="Регистрация" title-class="blue-light5--bg" class="xs12 lg6">
                <w-form>
                    <w-card v-show="this.form[0].isCurrent" class="xs12 lg6" title="Заполните поля" title-class="blue-light5--bg">
                        <w-input
                            ref="name"
                            label="Имя"
                            class="mb3"
                            :validators="this.validators(this.form[0].fields.name)"
                            @input="this.form[0].fields.name.isValid = $refs.name.valid"
                        />
                        <w-input
                            ref="email"
                            label="Адрес электронной почты"
                            type="email"
                            class="mb3"
                            :validators="this.validators(this.form[0].fields.email)"
                            @input="this.form[0].fields.email.isValid = $refs.email.valid"
                        />
                        <w-input
                            ref="password"
                            label="Пароль"
                            type="password"
                            class="mb3"
                            :validators="this.validators(this.form[0].fields.password)"
                            @input="this.form[0].fields.password.isValid = $refs.password.valid"
                        />
                    </w-card>
                    <w-card v-show="this.form[1].isCurrent" class="xs12 lg6" title="Выберите роль" title-class="blue-light5--bg">
                        <w-radios
                            ref="role"
                            :items="this.form[1].fields.role.items"
                            :validators="this.validators(this.form[1].fields.role)"
                            @input="this.form[1].fields.role.isValid = $refs.role.inputValue"
                            inline
                        />
                    </w-card>
                    <w-button
                        v-if="!this.form[0].isCurrent"
                        @click="this.step.previous()"
                        class="mt3"
                    >
                        Назад
                    </w-button>
                    <w-button
                        @click="this.step.next()"
                        class="mt3"
                        :disabled="!!this.step.isInvalid.value"
                    >
                        Продолжить
                    </w-button>
                </w-form>
            </w-card>
        </w-flex>
    </main>
</template>

<script>
    import { useStepsForm } from "Root/composables/useStepsForm";
    export default {
        setup() {
            const { form, step, validators } = useStepsForm({
                0: {
                    fields: {
                        name: {
                            validation: {
                                required: value => !!value || "Необходимо заполнить имя"
                            },
                            isValid: false,
                        },
                        email: {
                            validation: {
                                required: value => !!value || "Необходимо заполнить электронную почту"
                            },
                            isValid: false,
                        },
                        password: {
                            validation: {
                                required: value => !!value || "Необходимо заполнить пароль",
                                minLength: value => value.length >= 8 || "Введите еще " + (8 - value.length) + " знаков"
                            },
                            isValid: false,
                        },
                    },
                    isCurrent: true
                },
                1: {
                    fields: {
                        role: {
                            items: [{label: "Мастер", value: 1}, {label: "Организация", value: 2}, {label: "Рекламодатель", value: 3}],
                            validation: {
                                required: value => !!value || "Необходимо выбрать роль"
                            },
                            isValid: false,
                        },
                    },
                    isCurrent: false
                },
            });

            return { form, step, validators }
        }
    }
</script>
