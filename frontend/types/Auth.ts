export interface LoginRequest {
  username: string;
  password: string;
  _remember_me?: boolean;
}

export interface RegisterRequest {
  firstName: string;
  lastName: string;
  email: string;
  password: string;
}

export interface VerifyEmailRequest {
  email: string;
  code: string;
}

export interface ResendCodeRequest {
  email: string;
}

export interface ForgotPasswordRequest {
  email: string;
}

export interface ResetPasswordRequest {
  email: string;
  token: string;
  newPassword: string;
}

export interface AuthUser {
  id: number;
  firstName: string;
  lastName: string;
  email: string;
  isVerified: boolean;
}

export interface AuthResponse {
  user: AuthUser;
  message?: string;
}

export interface AuthError {
  message: string;
  violations?: Array<{
    propertyPath: string;
    message: string;
  }>;
}