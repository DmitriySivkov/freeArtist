export const ORDER_STATUSES = {
	NEW: 1,
	PROCESS: 2,
	CANCEL: 3,
	DONE: 4
}

export const ORDER_STATUS_NAMES = {
	[ORDER_STATUSES.NEW]: "В обработке",
	[ORDER_STATUSES.PROCESS]: "Готовим",
	[ORDER_STATUSES.CANCEL]: "Не можем выполнить :(",
	[ORDER_STATUSES.DONE]: "Выполнено",
}
