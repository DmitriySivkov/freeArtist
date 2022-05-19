// Be careful when using SSR for cross-request state pollution
// due to creating a Singleton instance here;
// If any client changes this (global) instance, it might be a
// good idea to move this instance creation inside of the
// "export default () => {}" function below (which runs individually
// for each client)

/** seems like theres no need for asynchronous requests
 * as it loads before entire app anyways
 * ... but that is not sure
 * */
export default ({ store }) => {
	store.dispatch("user/checkTokenCookie")
}
