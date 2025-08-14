export interface ApprovalStatus {
    id: number
    status_code: string
    name: string
}

export enum StatusCode {
    Pending = 'pending',
    Approved = 'approved',
    Rejected = 'rejected',
}
