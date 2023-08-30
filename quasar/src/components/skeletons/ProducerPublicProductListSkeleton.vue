<template>
	<div class="column no-wrap fit">
		<q-card
			v-for="n in 2"
			:key="n"
			class="col-6 column no-wrap q-mb-md"
		>
			<q-skeleton
				class="col-9 row"
			/>
			<q-separator color="black" />
		</q-card>
	</div>
</template>
