<template>
    <main class="grow">
        Main content
        <w-card>
            <router-link to="/auth">
                <w-button color="white">
                    Авторизоваться
                </w-button>
            </router-link>
        </w-card>
    </main>
</template>

<script>
    export default {
        setup() {}
    }
</script>
