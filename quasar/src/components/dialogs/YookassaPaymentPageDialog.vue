<script setup>
import { useDialogPluginComponent } from "quasar"
import { onMounted } from "vue"
import { useScreen } from "src/composables/screen"

onMounted(() => {
	let script = document.createElement("script")

	script.src = "https://yookassa.ru/checkout-widget/v1/checkout-widget.js"
	document.head.append(script)

	script.onload = function() {
		renderForm()
	}
})

defineEmits([
	...useDialogPluginComponent.emits,
])

const props = defineProps({
	confirmationToken: String
})

const { dialogRef, onDialogHide, onDialogOK, onDialogCancel } = useDialogPluginComponent()

const { isSmallScreen } = useScreen()

function renderForm() {
	const checkout = new window.YooMoneyCheckoutWidget({
		confirmation_token: props.confirmationToken, //Token that must be obtained from YooMoney before the payment process
		// return_url: "https://example.com/", //Link to the payment completion page, it could be any page on your website

		//If necessary, you can customize the widget's colors, see detailed instructions in the documentation
		//customization: {
		//Color scheme customization, minimum one parameter, color values in HEX
		//colors: {
		//Accent color: Pay button, selected radio buttons, checkboxes, and text boxes
		//control_primary: '#00BF96', //Color value in HEX

		//Color of the payment form and its elements
		//background: '#F2F3F5' //Color value in HEX
		//}
		//},
		error_callback: function(error) {
			console.log(error)
		}
	})

	checkout.on("complete", () => {
		checkout.destroy()

		onDialogOK()
	})

	checkout.on("fail", () => {
		checkout.destroy()

		onDialogCancel()
	})

	//Display of payment form in the container
	checkout.render("payment-form")
}
</script>

<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
		:maximized="isSmallScreen"
	>
		<q-card class="q-dialog-plugin q-pa-md">
			<div id="payment-form"></div>
		</q-card>
	</q-dialog>
</template>
