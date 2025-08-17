import type { Attendance } from "@/types/attendanceType";
import { AttendanceBreakModel } from "./attendanceBreakModel";

export class AttendanceModel implements Attendance {
    public id: number;
    public user_id: number;
    public date: string;
    public clock_in: string;
    public clock_out?: string;
    public start_time: string;
    public end_time: string;
    public work_value: number;
    public attendance_breaks: AttendanceBreakModel[];
    public break_value: number

    public constructor(data: any) {
        this.id = data.id;
        this.user_id = data.user_id;
        this.date = data.date;
        this.clock_in = data.clock_in;
        this.clock_out = data.clock_out ?? '';
        this.start_time = data.start_time;
        this.end_time = data.end_time;
        this.work_value = data.work_value;

        // attendance_breaksが存在する場合のみマッピング
        this.attendance_breaks = Array.isArray(data.attendance_breaks)
            ? data.attendance_breaks.map((b: any) => new AttendanceBreakModel(b))
            : [];
        this.break_value = 0
        this.attendance_breaks.map((b: AttendanceBreakModel) => {
            this.break_value += b.break_value
        })
    }
}
