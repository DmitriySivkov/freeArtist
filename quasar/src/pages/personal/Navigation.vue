<template>
	<q-page class="column">
		<div class="q-pa-md row q-col-gutter-sm">
			<div
				v-for="(item, index) in menu"
				:key="index"
				class="col-xs-12 col-md-6 col-lg-3 q-mb-sm"
			>
				<q-card class="bg-indigo-10 text-white">
					<q-item
						class="q-pa-xl"
						:to="item.link"
					>
						<q-item-section class="text-center text-h5">
							{{ item.title }}
						</q-item-section>
					</q-item>
				</q-card>
			</div>
		</div>
	</q-page>
</template>

<script>
export default {
	setup() {
		const menu = [
			{ title: "Персональные данные", link: "/personal/user" },
			{ title: "Ваши заказы", link: "/personal/orders" },
			{ title: "Стать изготовителем", link: "/personal/become-producer" }
		]

		return {
			menu
		}
	}
}
</script>
