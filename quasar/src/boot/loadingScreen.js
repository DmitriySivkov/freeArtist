document.getElementById("blind").checked = true

setTimeout(
	() => {
		document.getElementById("loading-screen").remove()

		Object.assign(
			document.getElementsByTagName("body")[0].style,{
				"overflow-x": "hidden",
				"overflow-y": "scroll"
			})
	},
	2000
)
