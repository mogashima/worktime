import { AxiosError } from "axios"

export class ApiError {
    message: string
    errors: {}

    constructor(error: AxiosError) {
        this.message = ''
        this.errors = {}
        if (error.response?.status !== 422) {
            this.message = 'エラーが発生しました'
        }
        
        if (error.response?.data) {
            const data: any = error.response?.data
            this.errors = data.errors
        }

    }
}