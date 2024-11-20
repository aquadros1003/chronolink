import { apiClient } from "./apiClient";
import { RegisterRequest, LoginRequest, JWTTokenResponse, User } from "./types";

export type ApiResponse<T> = {
  data: T;
  success: boolean;
  message?: string;
  invalidFields?: { [key: string]: string[] };
};

export function useAuthApi() {
  const login = async (payload: LoginRequest) => {
    const response = await apiClient.post("/auth/login", payload);
    const apiResponse = response.data;
    if (!apiResponse) {
      throw new Error("An error occurred while logging in");
    }
    if (!apiResponse?.error) {
      return apiResponse as JWTTokenResponse;
    }
    throw new Error(apiResponse.error);
  };

  const register = async (values: RegisterRequest) => {
    const formData = new FormData();

    formData.append("name", values.name);
    formData.append("email", values.email);
    formData.append("password", values.password);
    formData.append("password_confirmation", values.password_confirmation);

    const response = await apiClient.post("/auth/register", formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });

    return response;
  };

  const logout = async () => {
    const response = await apiClient.post("/auth/logout");
    return response;
  };

  return {
    login,
    register,
    logout,
  };
}
