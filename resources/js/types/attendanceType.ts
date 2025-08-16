import type { AttendanceBreak } from "./attendanceBreakType"
export interface Attendance {
    id: number
    user_id?: number
    date: string
    clock_in: string
    clock_out?: string
    start_time: string
    end_time: string
    work_value: number
    attendance_breaks?: AttendanceBreak[]
}
