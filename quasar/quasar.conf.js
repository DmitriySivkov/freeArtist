module.exports = function (ctx) {
	return {
		preFetch: true,
		boot: [
			"axios",
			"setCartFromLocalStorage",
			"browseProducers",
			"browseRoles"
		],

		css: [
			"app.scss"
		],

		extras: [
			"fontawesome-v5",
			"roboto-font",
			"material-icons"
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

			extendWebpack (cfg) {
				cfg.module.rules.push({
					enforce: "pre",
					test: /\.(js|vue)$/,
					loader: "eslint-loader",
					exclude: /node_modules/,
					options: {
						formatter: require("eslint").CLIEngine.getFormatter("stylish")
					}
				})
			},
		},

		devServer: {
			https: false,
			port: ctx.mode.spa ? 8081
				: (ctx.mode.pwa ? 9090 : 9000),
			open: true
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
