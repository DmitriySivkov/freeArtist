<template>
	<div class="q-ma-sm">
		<div class="row q-col-gutter-sm">
			<div
				v-for="(item, index) in menu"
				:key="index"
				class="col-xs-12 col-md-6 col-lg-3 flex"
			>
				<q-card
					class="bg-indigo-10 text-white fit"
				>
					<q-item
						class="q-pa-xl fit"
						:to="item.link"
					>
						<q-item-section class="text-center text-h5">
							{{ item.title }}
						</q-item-section>
					</q-item>
				</q-card>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	setup() {
		const menu = [
			{ title: "Персональные данные", link: "/personal/user" },
			{ title: "Ваши заказы", link: "/personal/orders" },
			{ title: "Зарегистрировать изготовителя", link: "/personal/register-producer" },
			{ title: "Присоединиться к изготовителю", link: "/personal/join-producer" }
		]

		return {
			menu
		}
	}
}
</script>
