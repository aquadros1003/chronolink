import axios, { AxiosResponse } from "axios";
import { useNavigate } from "react-router-dom";

export const apiClient = axios.create({
  baseURL: `${import.meta.env.VITE_API_URL}/api`,
  headers: {
    "Content-Type": "application/json",
  },
});

apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("access_token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  },
);

apiClient.interceptors.response.use(
  (response) => {
    return response;
  },
  async (error) => {
    const originalRequest = error.config;
    if (error.response.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true;
      const refreshToken = localStorage.getItem("access_token");
      if (refreshToken) {
        try {
          const response = await axios.post(
            `${import.meta.env.VITE_API_URL}/api/auth/refresh`,
          );
          const newAccessToken = response.data.accessToken;
          localStorage.setItem("accessToken", newAccessToken); //set new access token
          originalRequest.headers.Authorization = `Bearer ${newAccessToken}`;
          return axios(originalRequest);
        } catch (error) {
          return Promise.reject(error);
        }
      }
    }
    return Promise.reject(error);
  },
);

export const get = async <T>(url: string): Promise<AxiosResponse<T>> => {
  return apiClient.get(url);
};

export const post = async <T>(
  url: string,
  data: T,
): Promise<AxiosResponse<T>> => {
  return apiClient.post(url, data);
};

export const put = async <T>(
  url: string,
  data: T,
): Promise<AxiosResponse<T>> => {
  return apiClient.put(url, data);
};

export const del = async <T>(url: string): Promise<AxiosResponse<T>> => {
  return apiClient.delete(url);
};
