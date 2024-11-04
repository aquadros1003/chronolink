export type RegisterRequest = {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
};

export type LoginRequest = {
  email: string;
  password: string;
};

export type JWTTokenResponse = {
  access_token: string;
  expires_in: number;
  token_type: string;
};

export type User = {
  id: number;
  name: string;
  email_verified_at: string | null;
  email: string;
  created_at: string;
  updated_at: string;
  workspaces: {
    id: number;
    name: string;
    pivot: {
      user_id: number;
      workspace_id: number;
      current: boolean;
    };
  }[];
};
