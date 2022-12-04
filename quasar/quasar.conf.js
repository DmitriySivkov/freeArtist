const ESLintPlugin = require("eslint-webpack-plugin")
module.exports = function (ctx) {
	return {
		preFetch: true,
		boot: [
			"authViaSession", // show loader
			"axios",
			"setCartFromLocalStorage",
			"browseProducers",
			"browseRoles",
			"browsePermissions",
			"money",
			"ws",
			"routes" // hide loader
		],

		css: [
			"app.scss"
		],

		extras: [
			"fontawesome-v5",
			"roboto-font",
			"material-icons",
		],

		framework: {
			iconSet: "material-icons",
			all: "auto",

			components: [],
			directives: [],

			plugins: [
				"Notify",
				"Cookies",
				"Meta",
				"LocalStorage",
				"SessionStorage",
				"Loading",
				"Dialog"
			]
		},

		supportIE: true,

		build: {
			vueRouterMode: "history",
			showProgress: true,
			gzip: false,
			analyze: false,
			publicPath: "/",
			ignorePublicFolder: true,

			env: {
				// on mobile dev - change address to available inside network
				BACKEND_SERVER: ctx.dev ? (ctx.mode.spa ? "https://api.freeartist.loc" : (ctx.mode.capacitor ? "https://192.168.1.2" : null)) :
					"https://46ca-46-237-11-106.ngrok.io",
				BACKEND_HOST: "api.freeartist.loc"
			},

			chainWebpack (chain) {
				chain
					.plugin("eslint-webpack-plugin")
					.use(ESLintPlugin, [{ extensions: ["js", "vue"] }])
			},
		},

		devServer: {
			https: true,
			port: ctx.mode.spa ? 8081
				: (ctx.mode.pwa ? 9090 : 9000),
			open: true // open browser on load
		},

		animations: "all",

		ssr: {
			pwa: false
		},

		pwa: {
			workboxPluginMode: "GenerateSW",
			workboxOptions: {},
			manifest: {
				name: "Quasar App",
				short_name: "Quasar App",
				description: "A Quasar Framework app",
				display: "standalone",
				orientation: "portrait",
				background_color: "#ffffff",
				theme_color: "#027be3",
				icons: [
					{
						"src": "statics/icons/icon-128x128.png",
						"sizes": "128x128",
						"type": "image/png"
					},
					{
						"src": "statics/icons/icon-192x192.png",
						"sizes": "192x192",
						"type": "image/png"
					},
					{
						"src": "statics/icons/icon-256x256.png",
						"sizes": "256x256",
						"type": "image/png"
					},
					{
						"src": "statics/icons/icon-384x384.png",
						"sizes": "384x384",
						"type": "image/png"
					},
					{
						"src": "statics/icons/icon-512x512.png",
						"sizes": "512x512",
						"type": "image/png"
					}
				]
			}
		},

		cordova: {
			id: "org.cordova.quasar.app"
		},

		capacitor: {
			hideSplashscreen: true
		},

		electron: {
			bundler: "packager",

			packager: {},

			builder: {
				appId: "quapp"
			},

			nodeIntegration: true,

			extendWebpack (cfg) {}
		}
	}
}
