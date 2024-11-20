import "./LoginCard.css";
import { Button, Form, Input, Alert } from "antd";
import { MailOutlined, LockOutlined } from "@ant-design/icons";
import "./LoginCard.css";
import { useAuthApi } from "../../api/auth";
import { useState } from "react";
import { useNavigate } from "react-router-dom";

type LoginPayload = {
  email: string;
  password: string;
};

export const LoginForm = () => {
  const { login } = useAuthApi();
  const navigate = useNavigate();
  const [incorrectCredentials, setIncorrectCredentials] = useState(false);

  const onSubmit = async (values: LoginPayload) => {
    try {
      const response = await login(values);
      localStorage.setItem("access_token", response.access_token);
      navigate("/dashboard");
    } catch {
      setIncorrectCredentials(true);
    }
  };

  const initialCredential = {
    email: "presentation@dev.pl",
    password: "Polak!23",
  };
  return (
    <>
      {incorrectCredentials && (
        <Alert message="Invalid credentials" type="error" />
      )}
      <br></br>
      <Form
        layout="vertical"
        name="login-form"
        initialValues={initialCredential}
        onFinish={onSubmit}
      >
        <Form.Item
          name="email"
          label="Email"
          rules={[
            {
              required: true,
              message: "Please input your email",
            },
            {
              type: "email",
              message: "Please enter a valid email!",
            },
          ]}
        >
          <Input prefix={<MailOutlined className="text-primary" />} />
        </Form.Item>
        <Form.Item
          name="password"
          label={
            <div>
              <span>Password</span>
            </div>
          }
          rules={[
            {
              required: true,
              message: "Please input your password",
            },
          ]}
        >
          <Input.Password prefix={<LockOutlined className="text-primary" />} />
        </Form.Item>
        <div className="registration-label">
          <p>
            Don't have an account yet? <a href="/register">Sign Up!</a>
          </p>
        </div>
        <Form.Item>
          <Button type="primary" htmlType="submit" block>
            Sign In
          </Button>
        </Form.Item>
      </Form>
    </>
  );
};

export default LoginForm;
