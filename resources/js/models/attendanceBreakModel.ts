import type { AttendanceBreak } from "@/types/attendanceBreakType";

export class AttendanceBreakModel implements AttendanceBreak {
    public id: number
    public attendance_id: number
    public clock_in: string
    public clock_out?: string
    public start_time: string
    public end_time: string
    public break_value: number

    public constructor(data: any) {
        this.id = data.id
        this.attendance_id = data.attendance_id
        this.clock_in = data.clock_in
        this.clock_out = data.clock_out ?? ''
        this.start_time = data.start_time
        this.end_time = data.end_time
        this.break_value = data.break_value
    }

}