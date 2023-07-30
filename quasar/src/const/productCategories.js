export const PRODUCT_CATEGORIES = {
	burgers: 1,
	desserts: 2,
	pastry: 3,
	shawarma: 4,
	cookies: 5,
	other: 6
}
export const PRODUCT_CATEGORY_LIST = {
	[PRODUCT_CATEGORIES.burgers]: {
		id: PRODUCT_CATEGORIES.burgers,
		name: "Бургеры и сэндвичи",
		picture: "src/assets/product-categories/burgers.png"
	},
	[PRODUCT_CATEGORIES.desserts]: {
		id: PRODUCT_CATEGORIES.desserts,
		name: "Десерты",
		picture: "src/assets/product-categories/desserts.png"
	},
	[PRODUCT_CATEGORIES.pastry]: {
		id: PRODUCT_CATEGORIES.pastry,
		name: "Выпечка",
		picture: "src/assets/product-categories/pastry.png"
	},
	[PRODUCT_CATEGORIES.shawarma]: {
		id: PRODUCT_CATEGORIES.shawarma,
		name: "Шаурма и гирос",
		picture: "src/assets/product-categories/shawarma.png"
	},
	[PRODUCT_CATEGORIES.cookies]: {
		id: PRODUCT_CATEGORIES.cookies,
		name: "Печенье",
		picture: "src/assets/product-categories/cookies.png"
	},
	[PRODUCT_CATEGORIES.other]: {
		id: PRODUCT_CATEGORIES.other,
		name: "Другое",
		picture: "src/assets/product-categories/other.png"
	},
}
