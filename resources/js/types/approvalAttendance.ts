import type { User } from "./userType"
import type { ApprovalStatus } from "./approvalStatus"
export interface ApprovalAttendance {
    id: number
    user: User
    date: string
    clock_in: string
    clock_out: string
    created_at: string
    approval_status: ApprovalStatus
}