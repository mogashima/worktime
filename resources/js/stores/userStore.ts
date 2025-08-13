import { defineStore } from 'pinia'
import axios from '@/plugins/axios'
import type { User } from '@/types/userType';
import { RoleCode } from '@/types/roleType';

export const useUserStore = defineStore('user', {
    state: () => ({
        user: null as null | User
    }),
    actions: {
        setUser(userData: User) {
            this.user = userData
        },
        isAdmin(): boolean {
            return this.user?.role_code === RoleCode.Admin
        },
        isUser(): boolean {
            return this.user?.role_code === RoleCode.User
        },
        async fetchUser() {
            try {
                const res = await axios.get('/api/user')
                this.user = res.data
            } catch (error) {
                this.user = null
                throw error
            }
        },
        logout() {
            this.user = null
            localStorage.removeItem('auth_token')
            // ここでログアウトAPI呼ぶ、トークン削除などもする想定
        }
    }
})
