<template>
	<div class="column full-width no-wrap">
		<q-card
			v-for="n in 2"
			:key="n"
			class="col-6 q-mb-md"
			style="height:400px"
		>
			<div class="column fit">
				<div class="col-9 row">
					<q-skeleton class="col-xs-12 col-sm-8" />
					<q-separator
						vertical
						color="black"
					/>
					<q-skeleton class="col-xs-12 col-sm-grow" />
				</div>
				<q-separator color="black" />
				<div class="col-auto row">
				</div>
			</div>
		</q-card>
	</div>
</template>
