import { apiClient } from "./apiClient";
import { RegisterRequest, LoginRequest, JWTTokenResponse } from "./types";

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

  //   const getUser = async () => {
  //     const response: AxiosResponse<ApiResponse<User>> =
  //       await apiClient.get('/me');
  //     const apiResponse: ApiResponse<User> = response.data;

  //     if (!apiResponse.success) {
  //       if (!apiResponse.message) {
  //         throw new Error('wiadomosc z frontu');
  //       }

  //       throw new Error('wiadomosc z backu');
  //     }

  //     return apiResponse.data;
  //   };

  return {
    login,
    register,
  };
}
