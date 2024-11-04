const REGISTRATION_VALIDATION = {
  username: [
    {
      required: true,
      message: "Please input your username",
    },
    {
      min: 3,
      message: "Username must be at least 3 characters",
    },
  ],
  password: [
    {
      required: true,
      message: "Please input your password",
    },
    {
      min: 8,
      message: "Password must be at least 8 characters",
    },
    {
      max: 20,
      message: "Password must be at most 20 characters",
    },
    {
      pattern: /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])/,
      message:
        "Password must contain at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character",
    },
  ],
  confirm: [
    {
      required: true,
      message: "Please confirm your password!",
    },
    ({ getFieldValue }: { getFieldValue: (field: string) => string }) => ({
      validator(_: any, value: string): Promise<void> {
        if (!value || getFieldValue("password") === value) {
          return Promise.resolve();
        }
        return Promise.reject(
          "The two passwords that you entered do not match!",
        );
      },
    }),
  ],
};

export default REGISTRATION_VALIDATION;
