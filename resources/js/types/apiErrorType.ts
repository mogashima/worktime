import { AxiosError } from "axios"

export class ApiError {
    message: string
    errors: {}

    constructor(error: AxiosError) {
        this.message = ''
        this.errors = {}
        if (error.response?.data) {
            const data: any = error.response?.data
            this.message = data.message
            this.errors = data.errors
        }

    }
}