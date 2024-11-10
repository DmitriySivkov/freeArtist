export const ORDER_STATUSES = {
	NEW: 1,
	PROCESS: 2,
	CANCEL: 3,
	DONE: 4
}

export const ORDER_STATUS_NAMES = {
	[ORDER_STATUSES.NEW]: "Новый",
	[ORDER_STATUSES.PROCESS]: "Готовим",
	[ORDER_STATUSES.CANCEL]: "Отмена",
	[ORDER_STATUSES.DONE]: "Готово",
}

export const ORDER_CARD_STATUS_TO_CLASS = {
	[ORDER_STATUSES.NEW]: "text-black bg-grey-2",
	[ORDER_STATUSES.PROCESS]: "text-white bg-green-8",
	[ORDER_STATUSES.CANCEL]: "text-white bg-grey-8",
	[ORDER_STATUSES.DONE]: "text-white bg-blue-8",
}

export const ORDER_BADGE_STATUS_TO_CLASS = {
	[ORDER_STATUSES.PROCESS]: { color: "green-8", textColor: "white"},
	[ORDER_STATUSES.CANCEL]: { color: "grey-8", textColor: "white"},
	[ORDER_STATUSES.DONE]: { color: "blue-8", textColor: "white"},
}
