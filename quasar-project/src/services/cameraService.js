export const cameraService = () => {
	const base64ToBlob = async (imageDataURL) => await (await fetch(imageDataURL)).blob()

	return { base64ToBlob }
}
