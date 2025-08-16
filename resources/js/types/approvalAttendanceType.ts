import type { User } from "./userType"
import type { ApprovalStatus } from "./approvalStatusType"
export interface ApprovalAttendance {
    id: number
    user: User
    date: string
    start_time: string
    end_time: string
    created_at: string
    approval_status: ApprovalStatus
}