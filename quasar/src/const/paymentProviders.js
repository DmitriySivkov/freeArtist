export const PAYMENT_PROVIDERS = {
	YOOKASSA: 1,
	ROBOKASSA: 2
}

export const PAYMENT_PROVIDER_NAMES = {
	[PAYMENT_PROVIDERS.YOOKASSA]: "ЮКаssа",
	[PAYMENT_PROVIDERS.ROBOKASSA]: "Robokassa"
}

export const PAYMENT_PROVIDER_INPUTS = {
	[PAYMENT_PROVIDERS.YOOKASSA]: [
		{
			name: "shop_id",
			type: "number",
			label: "ID магазина"
		},
		{
			name: "secret_key",
			type: "text",
			label: "Секретный ключ"
		}
	],
	[PAYMENT_PROVIDERS.ROBOKASSA]: [
		{
			name: "shop_id",
			type: "number",
			label: "ID магазина"
		},
		{
			name: "secret_key",
			type: "text",
			label: "Секретный ключ"
		}
	]
}
