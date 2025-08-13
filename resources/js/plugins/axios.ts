import axios, { AxiosResponse, AxiosError } from 'axios'
import router from '@/router'

// axiosの基本設定
axios.defaults.baseURL = 'http://localhost/webphp/worktime/public'
axios.defaults.withCredentials = true

// レスポンスインターセプターで401エラーを捕捉
axios.interceptors.response.use(
    (response: AxiosResponse) => response,
    (error: AxiosError) => {
        if (error.response?.status === 401) {
            router.push('/login')  // ルートは/loginだけでOKです
        }
        return Promise.reject(error)
    }
)

export default axios
