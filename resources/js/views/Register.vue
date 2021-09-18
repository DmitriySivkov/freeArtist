<template>
    <main class="grow">
        <w-flex justify-center>
            <w-card title="Регистрация" title-class="blue-light5--bg" class="xs12 lg6">
                <w-form :no-keyup-validation="false">
                    <w-input
                        label="Имя"
                        class="mb3"
                        :validators="this.wInput.getValidators(this.form.name)"
                    />
                    <w-input
                        label="Адрес электронной почты"
                        type="email"
                        class="mb3"
                        :validators="this.wInput.getValidators(this.form.email)"
                    />
                    <w-input
                        label="Пароль"
                        type="password"
                        class="mb3"
                        :validators="this.wInput.getValidators(this.form.password)"
                    />
                </w-form>
                <w-button type="submit" class="mb3">Продолжить</w-button>
            </w-card>
        </w-flex>
    </main>
</template>

<script>
    import WaveInput from "Root/utils/WaveInput";
    export default {
        setup() {
            const form = {
                name: {
                    validation: {
                        required: value => !!value || "Необходимо заполнить имя"
                    },
                },
                email: {
                    validation: {
                        required: value => !!value || "Необходимо заполнить электронную почту"
                    },
                },
                password: {
                    validation: {
                        required: value => !!value || "Необходимо заполнить пароль",
                        minLength: value => value.length >= 8 || "Введите еще " + (8 - value.length) + " знаков"
                    },
                },
            };
            const wInput = new WaveInput();
            return { form, wInput }
        },
    }
</script>
