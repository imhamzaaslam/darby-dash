import { createRouter, createWebHistory } from 'vue-router'
// import { useAuthStore } from './store/auth'

// const requireVisitor = (to, from, next) => {
//     const authStore = useAuthStore()

//     if (to.path === '/forgot-password' && !['/login', '/reset-password'].includes(from.path)) {
//         // can only access from login or reset password page...
//         return next({ name: 'login' })
//     }

//     if (authStore.isAuthenticated) {
//         // authenticated, so no access...
//         return next({ name: 'dashboard' })
//     }

//     // unauthenticated, so accessing...

//     return next()
// }

// const requireAuth = (to, from, next) => {
//     const authStore = useAuthStore()

//     if (!authStore.isAuthenticated) {
//         return next({ name: 'login' })
//     }

//     return next()
// }

// const isAuthorized = (to, from , next) => {
//     const authStore = useAuthStore()
//     const customerAuthorizedPages = [
//         'sales',
//         'vat',
//         'settings',
//         'productsIndex',
//         'productDetails',
//     ]
//     const adminAuthorizedPages = [
//         'users',
//         'user',
//         'userEdit',
//         'platforms',
//         'adminProducts',
//         'adminProduct',
//         'workflow',
//         'supportEdit',
//         'supportCreate',
//         'activityLog',
//     ]

//     if (authStore.isCustomer && customerAuthorizedPages.indexOf(to.name) > -1) {
//         return next();
//     }

//     if (authStore.isAdmin && adminAuthorizedPages.indexOf(to.name) > -1) {
//         return next();
//     }

//     return next({ name: 'dashboard'});
// }

const routes = [
    // {
    //     path: '/',
    //     redirect: {
    //         name: 'dashboard'
    //     },
    //     beforeEnter: requireAuth,
    //     component: () => import('./layouts/Layout.vue'),
    //     children: [
    //         {
    //             path: '/',
    //             name: 'dashboard',
    //             component: () => import('./views/Dashboard.vue')
    //         },
    //         {
    //             path: '/admin/products',
    //             name: 'adminProducts',
    //             beforeEnter: isAuthorized,
    //             component: () => import('./views/admin/products/Overview.vue')
    //         },
    //         {
    //             path: '/admin/products/:productUuid',
    //             name: 'adminProduct',
    //             beforeEnter: isAuthorized,
    //             component: () => import('./views/admin/products/Detail.vue')
    //         },
    //         {
    //             // for admins only
    //             path: '/users',
    //             component: () => import('./views/admin/users/Users.vue'),
    //             beforeEnter: isAuthorized,
    //             children: [
    //                 {
    //                     // for admins only
    //                     path: '',
    //                     name: 'users',
    //                     component: () => import('./views/admin/users/Overview.vue'),
    //                     props: true,
    //                 },
    //                 {
    //                     // for admins only
    //                     path: ':uuid',
    //                     name: 'user',
    //                     component: () => import('./views/admin/users/Detail.vue'),
    //                     props: true,
    //                 },
    //                 {
    //                     // for admins only
    //                     path: ':uuid/edit',
    //                     name: 'userEdit',
    //                     component: () => import('./views/admin/users/Edit.vue'),
    //                     props: true,
    //                 },
    //             ]
    //         },
    //         {
    //             // for admins only
    //             path: '/platforms',
    //             name: 'platforms',
    //             beforeEnter: isAuthorized,
    //             component: () => import('./views/admin/Platforms.vue'),
    //         },
    //         {
    //             // for admins only
    //             path: '/workflow',
    //             name: 'workflow',
    //             beforeEnter: isAuthorized,
    //             component: () => import('./views/admin/Workflow.vue'),
    //         },
    //         {
    //             // for admins only
    //             path: '/activity-log',
    //             name: 'activityLog',
    //             beforeEnter: isAuthorized,
    //             component: () => import('./views/admin/ActivityLog.vue'),
    //         },
    //         {
    //             // for customers only
    //             path: '/sales',
    //             name: 'sales',
    //             beforeEnter: isAuthorized,
    //             component: () => import('./views/Sales.vue'),
    //         },
    //         {
    //             // for customers only
    //             path: '/vat',
    //             name: 'vat',
    //             beforeEnter: isAuthorized,
    //             component: () => import('./views/Vat.vue'),
    //         },
    //         {
    //             // for customers only
    //             path: '/settings',
    //             name: 'settings',
    //             beforeEnter: isAuthorized,
    //             component: () => import('./views/Settings.vue'),
    //         },
    //         {
    //             path: 'products/import',
    //             name: 'import-products',
    //             component: () => import('./views/products/Import.vue'),
    //         },
    //         {
    //             path: 'products',
    //             name: 'productsIndex',
    //             beforeEnter: isAuthorized,
    //             component: () => import('./views/products/Overview.vue'),
    //         },
    //         {
    //             path: 'products/:productUuid',
    //             name: 'productDetails',
    //             beforeEnter: isAuthorized,
    //             component: () => import('./views/products/Detail.vue'),
    //         },

    //         {
    //             // for customers only
    //             path: '/support',
    //             name: 'support',
    //             component: () => import('./views/support/Support.vue'),
    //             children: [
    //                 {
    //                     path: '',
    //                     name: 'supportOverview',
    //                     component: () => import('./views/support/Overview.vue'),
    //                 },
    //                 {
    //                     path: ':id',
    //                     name: 'supportDetail',
    //                     component: () => import('./views/support/Detail.vue'),
    //                 },
    //                 {
    //                     path: ':id/edit',
    //                     name: 'supportEdit',
    //                     component: () => import('./views/support/Create.vue'),
    //                     props: { isEditing: true },
    //                     beforeEnter: isAuthorized,
    //                 },
    //                 {
    //                     path: 'create',
    //                     name: 'supportCreate',
    //                     component: () => import('./views/support/Create.vue'),
    //                     beforeEnter: isAuthorized,
    //                 },
    //                 {
    //                     path: 'categories/:categoryId',
    //                     name: 'supportCategoryOverview',
    //                     component: () => import('./views/support/Overview.vue'),
    //                 },
    //             ]
    //         },
    //     ]
    // },
    // {
    //     path: '/login',
    //     redirect: {
    //         name : 'login'
    //     },
    //     component: () => import('./layouts/auth/AuthLayout.vue'),
    //     children: [
    //         {
    //             path: '/login',
    //             name: 'login',
    //             beforeEnter: requireVisitor,
    //             component: () => import('./views/authentication/Login.vue')
    //         },
    //         // {
    //         //     path: '/forgot-password',
    //         //     name: 'forgot-password',
    //         //     beforeEnter: requireVisitor,
    //         //     component: () => import('./views/authentication/ForgotPassword.vue')
    //         // },
    //         // {
    //         //     path: '/reset-password',
    //         //     name: 'reset-password',
    //         //     beforeEnter: requireVisitor,
    //         //     component: () => import('./views/authentication/ResetPassword.vue')
    //         // },
    //     ]
    // },
    {
        path: '/login',
        name: 'login',
        component: () => import('./views/authentication/Login.vue')
    }
    // {
    //     path: '/500',
    //     name: 'server-error',
    //     meta: { mustBeAuthorized: false },
    //     component: () => import('./views/ServerError.vue')
    // },
    // {
    //     path: '/404',
    //     name: 'not-found',
    //     meta: { mustBeAuthorized: true },
    //     beforeEnter: requireAuth,
    //     component: () => import('./views/NotFound.vue')
    // },
    // {
    //     path: '/:pathMatch(.*)*',
    //     redirect: '/404',
    // },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router