export const cameraService = () => {
	const base64ToBlob = async (imageDataURL) => await (await fetch(imageDataURL)).blob()

	const toBase64 = (file) => new Promise((resolve, reject) => {
		const reader = new FileReader()
		reader.readAsDataURL(file)
		reader.onload = () => resolve(reader.result)
		reader.onerror = error => reject(error)
	})

	return {
		base64ToBlob,
		toBase64
	}
}


