<template>
	<div class="column no-wrap absolute full-width">
		<div class="row justify-center">
			<div class="col-xs-12 col-sm-9 col-md-8 col-lg-7 col-xl-6 q-px-md">
				<ProducerPublicProductListFilter
					:tags-static="tagsStatic"
					:selected-tags="selectedTags"
					@changeTag="changeTag"
				/>
				<ProducerPublicProductList
					:selected-tags="selectedTags"
				/>
			</div>
		</div>
	</div>
</template>

<script setup>
import ProducerPublicProductListFilter from "src/components/producers/ProducerPublicProductListFilter.vue"
import ProducerPublicProductList from "src/components/producers/ProducerPublicProductList.vue"
import { ref, onMounted } from "vue"
import { api } from "src/boot/axios"
import { useRouter } from "vue-router"

const $router = useRouter()

let tagsStatic = []
const selectedTags = ref([])

onMounted(() => {
	if ($router.currentRoute.value.query.categories) {
		const promise = api.get("tags", {
			params: {
				categories: $router.currentRoute.value.query.categories ?
					$router.currentRoute.value.query.categories.split(",") : null,
				withAllTags: 1
			}
		})

		promise.then((response) => {
			tagsStatic = response.data.all_tags
			selectedTags.value = response.data.items
		})
	} else {
		const promise = api.get("tags")

		promise.then((response) => tagsStatic = response.data.items)
	}
})

const changeTag = (tags) => {
	selectedTags.value = tags
}
</script>
