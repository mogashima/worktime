export interface AttendanceBreak {
    id: number
    attendance_id: number
    clock_in: string
    clock_out?: string
    start_time: string
    end_time: string
    break_value: number
}
