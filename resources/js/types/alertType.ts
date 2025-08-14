export enum AlertType {
    Success = 'success',
    Error = 'error',
}

export class Alert {
    message: string
    type: AlertType
    duration?: number

    constructor(message: string, type: AlertType = AlertType.Success, duration: number = 5000) {
        this.message = message
        this.type = type
        this.duration = duration
    }
}
