const routes = [
    { path: "/", component: () => import("Root/views/Home") },
    { path: "/auth", component: () => import("Root/views/Auth") },
    { path: "/register", component: () => import("Root/views/Register") }
];

export default routes
