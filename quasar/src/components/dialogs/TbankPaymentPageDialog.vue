<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
		:maximized="isMobileWidthThreshold"
	>
		<q-card class="q-dialog-plugin q-pa-md">
			<form
				class="payform-tbank"
				name="payform-tbank"
				onsubmit="pay(this); return false;"
			>
				<input
					class="payform-tbank-row"
					type="hidden"
					name="terminalkey"
					value="TBankTest"
				>
				<input
					class="payform-tbank-row"
					type="hidden"
					name="frame"
					value="false"
				>
				<input
					class="payform-tbank-row"
					type="hidden"
					name="language"
					value="ru"
				>
				<input
					class="payform-tbank-row"
					type="text"
					placeholder="Сумма заказа"
					name="amount"
					required
				>
				<input
					class="payform-tbank-row"
					type="hidden"
					placeholder="Номер заказа"
					name="order"
				>
				<input
					class="payform-tbank-row"
					type="text"
					placeholder="Описание заказа"
					name="description"
				>
				<input
					class="payform-tbank-row"
					type="text"
					placeholder="ФИО плательщика"
					name="name"
				>
				<input
					class="payform-tbank-row"
					type="email"
					placeholder="E-mail"
					name="email"
				>
				<input
					class="payform-tbank-row"
					type="tel"
					placeholder="Контактный телефон"
					name="phone"
				>
				<input
					class="payform-tbank-row payform-tbank-btn"
					type="submit"
					value="Оплатить"
				>
			</form>
		</q-card>
	</q-dialog>
</template>

<script setup>
import { useDialogPluginComponent, useQuasar } from "quasar"
import { computed, onMounted } from "vue"

onMounted(() => {
	let script = document.createElement("script")

	script.src = "https://securepay.tinkoff.ru/html/payForm/js/tinkoff_v2.js"
	document.head.append(script)
})

defineEmits([
	...useDialogPluginComponent.emits,
])

const props = defineProps({})

const { dialogRef, onDialogHide, onDialogOK, onDialogCancel } = useDialogPluginComponent()

const $q = useQuasar()
const isMobileWidthThreshold = computed(() => $q.screen.width < $q.screen.sizes.md)
</script>

<style>
.payform-tbank {
	display: -webkit-box;
	display: -ms-flexbox;
	display: flex;
	margin: 2px auto;
	-webkit-box-orient: vertical;
	-webkit-box-direction: normal;
	-ms-flex-direction: column;
	flex-direction: column;
	max-width: 250px;
}
.payform-tbank-row {
	margin: 2px;
	border-radius: 4px;
	-webkit-box-flex: 1;
	-ms-flex: 1;
	flex: 1;
	-webkit-transition: 0.3s;
	-o-transition: 0.3s;
	transition: 0.3s;
	border: 1px solid #DFE3F3;
	padding: 15px;
	outline: none;
	background-color: #DFE3F3;
	font-size: 15px;
}
.payform-tbank-row:focus {
	background-color: #FFFFFF;
	border: 1px solid #616871;
	border-radius: 4px;
}
.payform-tbank-btn {
	background-color: #FBC520;
	border: 1px solid #FBC520;
	color: #3C2C0B;
}
.payform-tbank-btn:hover {
	background-color: #FAB619;
	border: 1px solid #FAB619;
}
</style>
