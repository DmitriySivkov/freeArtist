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
				// when developing mobile - substitute address with api server address exposed via ngrok
				// white screen after ngrok usage ? - router / external IP issue. Replugging router helps
				BACKEND_SERVER: ctx.mode.spa
					? "https://api.freeartist.loc"
					: (ctx.mode.cordova ? "https://11de-109-161-120-97.ngrok.io" : null),
				BACKEND_HOST: "api.freeartist.loc"
			},

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
			https: true,
			port: ctx.mode.spa ? 8081
				: (ctx.mode.pwa ? 9090 : 9000),
			host: ctx.mode.spa ? "app.freeartist.loc" : null,
			open: true,
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
