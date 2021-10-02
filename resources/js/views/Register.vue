<template>
    <main class="grow">
        <w-flex justify-center>
            <w-card title="Регистрация" title-class="blue-light5--bg" class="xs12 md6">
                <w-form @success="this.submit($refs).call()">
                    <div v-show="this.form[0].isCurrent">
                        <w-card title="Заполните поля" title-class="blue-light5--bg">
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
                    </div>
                    <div v-show="this.form[1].isCurrent">
                        <w-card title="Выберите роль" title-class="blue-light5--bg">
                            <w-radios
                                class="xs12"
                                ref="role"
                                :items="this.form[1].fields.role.items"
                                :validators="this.validators(this.form[1].fields.role)"
                                v-model="this.form[1].fields.role.isValid"
                            >
                                <template #item="{ item }">
                                    <w-card :title="item.label" title-class="blue-light5--bg">
                                        {{ item.description }}
                                    </w-card>
                                </template>
                            </w-radios>
                        </w-card>
                    </div>
                    <w-button
                        :disabled="this.step.isFirst.value"
                        @click="this.step.previous()"
                        class="mt3 mr2"
                    >
                        Назад
                    </w-button>
                    <w-button v-if="this.step.isLast.value === false"
                        @click="this.step.next()"
                        class="mt3"
                        :disabled="!!this.step.isInvalid.value"
                    >
                        Продолжить
                    </w-button>
                    <w-button v-else
                        type="submit"
                        class="mt3"
                        :disabled="!!this.step.isInvalid.value"
                    >
                        Завершить регистрацию
                    </w-button>
                </w-form>
            </w-card>
        </w-flex>
    </main>
</template>

<script>
    import { useStepForm } from "Root/composables/stepForm/useStepForm";
    export default {
        setup() {
            const { form, step, validators, submit } = useStepForm({
                0: {
                    fields: {
                        name: {
                            isValid: false,
                            validation: {
                                required: value => !!value || "Необходимо заполнить имя"
                            },
                        },
                        email: {
                            isValid: false,
                            validation: {
                                required: value => !!value || "Необходимо заполнить электронную почту"
                            },
                        },
                        password: {
                            isValid: false,
                            validation: {
                                required: value => !!value || "Необходимо заполнить пароль",
                                minLength: value => value.length >= 8 || "Введите еще " + (8 - value.length) + " знаков"
                            },
                        },
                    },
                    isCurrent: true
                },
                1: {
                    fields: {
                        role: {
                            isValid: false,
                            items: [
                                {
                                    value: 1,
                                    label: "Мастер",
                                    description: "Бонусы и ачивки мастера"
                                },
                                {
                                    value: 2,
                                    label: "Организация",
                                    description: "Различные перки организации"
                                },
                                {
                                    value: 3,
                                    label: "Рекламодатель",
                                    description: "Доступность и прозрачность рекламы"
                                },
                            ],
                            validation: {
                                required: value => !!value || "Необходимо выбрать роль"
                            },
                        },
                    },
                    isCurrent: false
                },
            });
            const zxc = (data) => console.log(data)
            return { form, step, validators, submit, zxc }
        }
    }
</script>
