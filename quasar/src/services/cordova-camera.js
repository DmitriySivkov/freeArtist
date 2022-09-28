async function getBase64FromFileObject(fileObject) {
	return new Promise((resolve, reject) => {
		var reader = new FileReader()
		reader.onloadend = function (evt) {
			var image = new Image()
			image.onload = function (e) {
				resolve(evt.target.result)
			}
			image.src = evt.target.result
		}
		reader.readAsDataURL(fileObject)
	})
}

export default {
	getBase64FromFileObject
}
