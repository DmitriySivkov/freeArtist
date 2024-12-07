import "dotenv/config.js"
import { configure } from "quasar/wrappers"
// const { join } = require("path")
// const { mergeConfig } = require("vite")

export default configure(function (ctx) {
	return {
		eslint: {
			fix: true,
			warnings: true,
			errors: true
		},

		bin: {
			windowsAndroidStudio: process.env.IDE_PATH // only for windows capacitor builds
		},

		preFetch: true,

		boot: [
			"stores",
			"globals",
			"axios",
			"authViaToken",
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
					process.env.BACKEND_SERVER_PRODUCTION,
				BACKEND_HOST: ctx.dev ? process.env.BACKEND_HOST : process.env.BACKEND_HOST_PRODUCTION,
				SESSION_DOMAIN: ctx.dev ?
					(ctx.mode.capacitor ? process.env.SESSION_DOMAIN_TEST : process.env.SESSION_DOMAIN) :
					process.env.SESSION_DOMAIN_PRODUCTION
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
			open: false, // open browser on load

			/** best to leave capacitor host as localhost: https://capacitorjs.com/docs/config#schema (section 'server') **/
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
			// (Optional!)
			hideSplashscreen: false, // disables auto-hiding the Splashscreen by Quasar CLI

			// (Optional!)
			capacitorCliPreparationParams: [ "sync", ctx.targetName ],

			// (Optional) If not present, will look for package.json > name
			appName: "Хлебная лавка", // string
			// (Optional) If not present, will look for package.json > version
			// version: '...', // string
			// (Optional) If not present, will look for package.json > description
			description: "Печём булки", // string

			plugins: {
				SplashScreen: {
					// launchShowDuration: 3000,
					// launchAutoHide: true,
					// launchFadeOutDuration: 3000,
					// backgroundColor: "#ffffffff",
					// androidSplashResourceName: "splash",
					// androidScaleType: "CENTER_CROP",
					// showSpinner: true,
					// androidSpinnerStyle: "large",
					// iosSpinnerStyle: "small",
					// spinnerColor: "#999999",
					// splashFullScreen: true,
					// splashImmersive: true,
					// layoutName: "launch_screen",
					// useDialog: true,
				}
			}
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
