import AdminLayout from '@/layouts/adminLayout.vue'
import { userStore } from '@/stores/user'
import Dashboard from '@/views/auth/dashboard.vue'
import Login from '@/views/auth/login.vue'
import Logout from '@/views/auth/logout.vue'
import Register from '@/views/auth/register.vue'
import Verified_email from '@/views/auth/verified_email.vue'
import { createRouter, createWebHistory } from 'vue-router'


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/register',
      name: 'register',
      component: Register,
      meta: { guestOnly: true }, 
      
    },
    {
      path: '/',
      name: 'login',
      component: Login,
      meta: { guestOnly: true }
    },
    {
      path: '/email_verified',
      component: Verified_email,
      meta: { guestOnly: true }
    },
    {
      path: '/logout',
      component: Logout,
      name: 'logout',
      meta: {requiresAuth: true}
    },
    {
      path: '/dashboard',
      component: AdminLayout,
      name: 'admin.dashboard',
      children: [
        {
          path: 'index', // no need slash /
          component: Dashboard,
          name: 'dashboard.index',
          meta: { requiresAuth: true }
        }
      ]
    }
    // {
    //   path: '/',
    //   name: 'home',
    //   component: HomeView,
    // },
    // {
    //   path: '/about',
    //   name: 'about',
    //   // route level code-splitting
    //   // this generates a separate chunk (About.[hash].js) for this route
    //   // which is lazy-loaded when the route is visited.
    //   component: () => import('../views/AboutView.vue'),
    // },
  ],
})
router.beforeEach((to) => {
    const store = userStore()
    // Protected route
    if (to.meta.requiresAuth && !store.isAuthenticated) {
        return {
            name: 'login'
        }
    }

    // Guest-only route
    if (to.meta.guestOnly && store.isAuthenticated) {
        return {
            name: 'dashboard.index'
        }
    }
})

export default router
