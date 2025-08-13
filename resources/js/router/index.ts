import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'
import { useUserStore } from '@/stores/userStore'
import { RoleCode } from '@/types/roleType'
import LoginPage from '@/pages/auth/LoginPage.vue'
import RegisterPage from '@/pages/auth/RegisterPage.vue'
import ReservationPage from '@/pages/ReservationPage.vue'
import NotFoundPage from '@/pages/NotFoundPage.vue'
import DashboardPage from '@/pages/DashboardPage.vue'
import AttendancePage from '@/pages/user/AttendancePage.vue'
import ApprovalPage from '@/pages/user/ApprovalPage.vue'
import AdminUserPage from '@/pages/admin/AdminUserPage.vue'
import AdminApprovalPage from '@/pages/admin/AdminApprovalPage.vue'

// ルート定義
const routes: Array<RouteRecordRaw> = [
    {
        path: '/',
        redirect: '/login'
    },
    {
        path: '/login',
        name: 'Login',
        component: LoginPage,
        meta: { requiresAuth: false }
    },
    {
        path: '/register',
        name: 'Register',
        component: RegisterPage,
        meta: { requiresAuth: false }
    },
    {
        path: '/dashboard',
        name: 'Dashboard',
        component: DashboardPage,
        meta: { requiresAuth: false }
    },
    {
        path: '/reservation',
        name: 'Reservation',
        component: ReservationPage,
        meta: { requiresAuth: true, role_code: RoleCode.Admin }
    },
    {
        path: '/attendance',
        name: 'Attendance',
        component: AttendancePage,
        meta: { requiresAuth: true, role_code: RoleCode.User }
    },
    {
        path: '/approval',
        name: 'Approval',
        component: ApprovalPage,
        meta: { requiresAush: true, role_code: RoleCode.User }
    },
    {
        path: '/admin/user',
        name: 'AdminUser',
        component: AdminUserPage,
        meta: { requiresAuth: true, role_code: RoleCode.Admin }
    },
    {
        path: '/admin/approval',
        name: 'AdminApproval',
        component: AdminApprovalPage,
        meta: { requiresAuth: true, role_code: RoleCode.Admin }
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'NotFound',
        component: NotFoundPage
    }
]

// Routerインスタンス作成
const router = createRouter({
    history: createWebHistory('/webphp/worktime/public/'),
    routes
})

// 認証＆権限チェック
router.beforeEach(async (to, from, next) => {
    const isAuthenticated = !!localStorage.getItem('auth_token')
    const userStore = useUserStore()

    // ログインしていなければログインページへ（認証必要なページのみ）
    if (to.meta.requiresAuth && !isAuthenticated) {
        return next('/login')
    }

    // ログイン済みかつ認証必要なページの場合、ユーザー情報をストアにセット
    if (isAuthenticated && !userStore.user) {
        try {
            await userStore.fetchUser()  // ユーザー情報を取得してstoreにセットする想定のメソッド
        } catch (error) {
            // ユーザー情報取得失敗時はログアウトしてログイン画面へ
            userStore.logout()
            return next('/login')
        }
    }

    // 権限チェック
    if (to.meta.role_code) {
        if (!userStore.user || userStore.user.role_code !== to.meta.role_code) {
            // 権限不足ならトップかログインにリダイレクト
            return next('/dashboard')
        }
    }

    next()
})

export default router
