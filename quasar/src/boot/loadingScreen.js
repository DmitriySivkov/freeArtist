document.getElementById("blind").checked = true

setTimeout(
	() => {
		document.getElementById("loading-screen").remove()
		document.body.style.overflow = "hidden scroll"
	},
	2000
)
