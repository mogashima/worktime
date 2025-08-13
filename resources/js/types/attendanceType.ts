import type { AttendanceBreak } from "./attendanceBreakType"
export interface Attendance {
    id: number
    user_id?: number
    date: string
    clock_in: string
    clock_out?: string
    attendance_breaks?: AttendanceBreak[]
}
