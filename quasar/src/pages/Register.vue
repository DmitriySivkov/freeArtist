<template>
	<q-page class="flex flex-center">
		<div class="q-pa-md q-col-gutter-sm">
			<div
				v-for="(item, index) in menuItems"
				:key="index"
				class="col-xs-12 col-md-6 col-lg-3 q-mb-sm"
			>
				<q-card class="bg-primary text-white">
					<q-item
						class="q-pa-xl"
						:to="item.link"
					>
						<q-item-section class="text-center text-h5">{{ item.title }}</q-item-section>
					</q-item>
				</q-card>
			</div>
		</div>
		<router-view />
	</q-page>
</template>

<script>
export default {
	setup() {
		const menuItems = [
			{ link: "/register/user", title: "Зарегистрироваться как пользователь" },
			{ link: "/register/producer", title: "Зарегистрироваться как мастер" }
		]

		return {
			menuItems
		}
	}
}
</script>
