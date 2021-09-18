<template>
    <main class="grow">
        <w-flex justify-center>
            <w-card title="Войти в личный кабинет" title-class="blue-light5--bg" class="xs12 lg6">
                <w-form>
                    <w-input label="Имя" class="mb3"></w-input>
                    <w-input label="Фамилия" class="mb3"></w-input>
                    <w-input label="Адрес электронной почты" type="email" class="mb3"></w-input>
                    <w-input label="Пароль" type="password" class="mb3"></w-input>
                </w-form>
                <w-button type="submit" class="mb3">Принять</w-button>
                <router-link to="/register">
                    <w-button type="submit" class="mb3 ml5">Зарегистрироваться</w-button>
                </router-link>
            </w-card>
        </w-flex>
    </main>
</template>

<script>
    export default {
        setup() {}
    }
</script>
