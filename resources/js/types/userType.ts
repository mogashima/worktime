import { Attendance } from "./attendanceType";
export interface User {
    id: number;
    name: string;
    role_code: string;
    created_at: string;
    updated_at: string;
}

export interface UserWithAttendances extends User {
    attendances: Attendance[];
}
