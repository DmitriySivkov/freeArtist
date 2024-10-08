require("dotenv").config()
const { configure } = require("quasar/wrappers")
const { join } = require("path")
// const { mergeConfig } = require("vite")

module.exports = configure(function (ctx) {
	return {
		eslint: {
			fix: true,
			warnings: true,
			errors: true
		},

		preFetch: true,

		boot: [
			"stores",
			"globals",
			"authViaToken",
			"axios",
			"setLocation",
			"setCartFromLocalStorage",
			"ws",
			"routes",
			"loadingScreen" // keep at the end
		],

		css: [
			"app.scss"
		],

		extras: [
			// "fontawesome-v6",
			// "roboto-font",
			"material-icons"
		],

		build: {
			target: {
				browser: [ "es2020" ], // todo - check if theres no problem with safari
				node: "node16"
			},

			vueRouterMode: "history", // available values: 'hash', 'history'
			// vueRouterBase,
			// vueDevtools,
			// vueOptionsAPI: false,

			// rebuildCache: true, // rebuilds Vite/linter/etc cache on startup

			// publicPath: '/',
			// analyze: true,
			env: {
				// on mobile dev - change address to available inside network
				BACKEND_SERVER: ctx.dev ? (ctx.mode.spa ? process.env.BACKEND_SERVER : (ctx.mode.capacitor ? "https://192.168.1.2" : null)) :
					"https://e217-95-106-191-13.ngrok.io",
				BACKEND_HOST: process.env.BACKEND_HOST,
				SESSION_DOMAIN: process.env.SESSION_DOMAIN
			},
			// rawDefine: {}
			// ignorePublicFolder: true,
			// minify: false,
			// polyfillModulePreload: true,
			// distDir

			// extendViteConf (viteConf, { isServer, isClient }) {}
			// viteVuePluginOptions: {},


			// vitePlugins: [
			//   [ 'package-name', { ..options.. } ]
			// ]
		},

		devServer: {
			https: true,
			port: 3000,
			fs: {
				cachedChecks: true
			},
			hmr: {
				clientPort: 443,
			},
			open: false // open browser on load
		},

		framework: {
			config: {},

			iconSet: "material-icons",

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

		animations: "all",

		// https://v2.quasar.dev/quasar-cli-vite/quasar-config-js#property-sourcefiles
		// sourceFiles: {
		//   rootComponent: 'src/App.vue',
		//   router: 'src/router/index',
		//   store: 'src/store/index',
		//   registerServiceWorker: 'src-pwa/register-service-worker',
		//   serviceWorker: 'src-pwa/custom-service-worker',
		//   pwaManifestFile: 'src-pwa/manifest.json',
		//   electronMain: 'src-electron/electron-main',
		//   electronPreload: 'src-electron/electron-preload'
		// },

		// https://v2.quasar.dev/quasar-cli/developing-ssr/configuring-ssr
		ssr: {
			// ssrPwaHtmlFilename: 'offline.html', // do NOT use index.html as name!
			// will mess up SSR

			// extendSSRWebserverConf (esbuildConf) {},
			// extendPackageJson (json) {},

			pwa: false,

			// manualStoreHydration: true,
			// manualPostHydrationTrigger: true,

			prodPort: 3000, // The default port that the production server should use
			// (gets superseded if process.env.PORT is specified at runtime)

			middlewares: [
				"render" // keep this as last one
			]
		},

		// https://v2.quasar.dev/quasar-cli/developing-pwa/configuring-pwa
		pwa: {
			workboxMode: "generateSW", // or 'injectManifest'
			injectPwaMetaTags: true,
			swFilename: "sw.js",
			manifestFilename: "manifest.json",
			useCredentialsForManifestTag: false,
			// useFilenameHashes: true,
			// extendGenerateSWOptions (cfg) {}
			// extendInjectManifestOptions (cfg) {},
			// extendManifestJson (json) {}
			// extendPWACustomSWConf (esbuildConf) {}
		},

		// Full list of options: https://v2.quasar.dev/quasar-cli/developing-cordova-apps/configuring-cordova
		cordova: {
			// noIosLegacyBuildFlag: true, // uncomment only if you know what you are doing
		},

		// Full list of options: https://v2.quasar.dev/quasar-cli/developing-capacitor-apps/configuring-capacitor
		capacitor: {
			hideSplashscreen: true
		},

		// Full list of options: https://v2.quasar.dev/quasar-cli/developing-electron-apps/configuring-electron
		electron: {
			// extendElectronMainConf (esbuildConf)
			// extendElectronPreloadConf (esbuildConf)

			inspectPort: 5858,

			bundler: "packager", // 'packager' or 'builder'

			packager: {
				// https://github.com/electron-userland/electron-packager/blob/master/docs/api.md#options

				// OS X / Mac App Store
				// appBundleId: '',
				// appCategoryType: '',
				// osxSign: '',
				// protocol: 'myapp://path',

				// Windows only
				// win32metadata: { ... }
			},

			builder: {
				// https://www.electron.build/configuration/configuration

				appId: "quasar-project"
			}
		},

		// Full list of options: https://v2.quasar.dev/quasar-cli-vite/developing-browser-extensions/configuring-bex
		bex: {
			contentScripts: [
				"my-content-script"
			],

			// extendBexScriptsConf (esbuildConf) {}
			// extendBexManifestJson (json) {}
		}
	}
})
