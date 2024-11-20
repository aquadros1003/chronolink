import HeaderNav from "../components/HeaderNav/HeaderNav";
import { Outlet } from "react-router-dom";
import { Layout } from "antd";

const { Header, Content } = Layout;

export const AuthLayout = () => {
  return (
    <Layout style={{ width: "100%", height: "100%" }}>
      <Header
        style={{
          padding: 0,
          backgroundColor: "white",
        }}
      >
        <HeaderNav />
      </Header>
      <Layout>
        <Layout
          style={{
            padding: "0 24px 24px",
          }}
        >
          <Content
            style={{
              padding: 24,
              margin: 0,
              minHeight: 280,
            }}
          >
            <Outlet />
          </Content>
        </Layout>
      </Layout>
    </Layout>
  );
};

export default AuthLayout;
