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

export type TimelineType = {
  name: any;
  id: number;
  title: string;
  description: string;
};

export type User = {
  id: number;
  name: string;
  email_verified_at: string | null;
  email: string;
  created_at: string;
  updated_at: string;
  timelines: TimelineType[];
};

export type CreateTimelineRequest = {
  title: string;
  description: string;
};

export type LabelType = {
  id: string;
  name: string;
  color: string;
};

export type EventType = {
  id: string;
  title: string;
  description: string;
  start_date: string;
  end_date: string;
  location: string;
  label: LabelType;
};

export type CreateEventRequest = {
  title: string;
  start_date: string;
  end_date: string;
  location: string;
  description: string;
  label_id: number;
};

export type PermissionType = {
  id: number;
  name: string;
};

export type CreateLabelRequest = {
  name: string;
  color: string;
};
