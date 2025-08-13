export interface Role {
    id: number
    name: string
    role_code: string
}

export enum RoleCode {
  Admin = 'admin',
  User = 'user',
}