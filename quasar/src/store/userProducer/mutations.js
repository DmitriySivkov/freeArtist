export const SET_USER_PRODUCER = (state, producer) => {
	if (state.producers.length > 0)
		state.producers = [...state.producers, producer]
	else
		state.producers = producer
}

export const SET_PRODUCER_INCOMING_RELATION_REQUESTS = (state, payload) => {
	let producer = state.producers.find((team) => team.id === payload.producer_id)
	if (producer.hasOwnProperty("incoming_coworking_requests")) {
		producer.incoming_coworking_requests = [...payload.incoming_coworking_requests, producer.incoming_coworking_requests]
	} else {
		producer.incoming_coworking_requests = payload.incoming_coworking_requests
	}
}

export const EMPTY_USER_PRODUCER = (state) => {
	state.producers = []
}
