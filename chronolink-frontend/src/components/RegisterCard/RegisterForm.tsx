import { LockOutlined, MailOutlined, MehOutlined } from "@ant-design/icons";
import { Button, Form, Input } from "antd";
import { useNavigate } from "react-router";
import { useAuthApi } from "../../api/auth";
import { RegisterRequest } from "../../api/types";
import REGISTRATION_VALIDATION from "./RegisterValidation";

export const RegisterForm = () => {
  const [form] = Form.useForm();
  const { register } = useAuthApi();
  const navigate = useNavigate();

  const onSubmit = async (values: RegisterRequest) => {
    try {
      await register(values);
      navigate("/");
    } catch {
      form.setFields([
        {
          name: "email",
          errors: ["Email is already taken"],
        },
      ]);
    }
  };

  return (
    <>
      <Form
        form={form}
        layout="vertical"
        name="register-form"
        onFinish={onSubmit}
      >
        <Form.Item
          name="name"
          rules={REGISTRATION_VALIDATION.username}
          hasFeedback
        >
          <Input
            prefix={<MehOutlined className="text-primary" />}
            placeholder="Username"
          />
        </Form.Item>
        <Form.Item
          name="email"
          rules={[
            {
              type: "email",
              message: "The input is not valid E-mail!" as const,
            },

            { required: true, message: "Please input your E-mail!" },
          ]}
          hasFeedback
        >
          <Input
            prefix={<MailOutlined className="text-primary" />}
            placeholder="Email"
          />
        </Form.Item>
        <Form.Item
          name="password"
          rules={REGISTRATION_VALIDATION.password}
          hasFeedback
        >
          <Input.Password
            prefix={<LockOutlined className="text-primary" />}
            placeholder="Password"
          />
        </Form.Item>
        <Form.Item
          name="password_confirmation"
          rules={REGISTRATION_VALIDATION.confirm}
          hasFeedback
        >
          <Input.Password
            prefix={<LockOutlined className="text-primary" />}
            placeholder="Confirm Password"
          />
        </Form.Item>
        <Form.Item>
          <Button type="primary" htmlType="submit" block>
            Sign Up
          </Button>
        </Form.Item>
      </Form>
    </>
  );
};

export default RegisterForm;
