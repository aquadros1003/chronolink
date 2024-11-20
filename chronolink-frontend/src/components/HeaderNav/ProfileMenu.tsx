import { Dropdown, Row, Avatar } from "antd";
import { LogoutOutlined } from "@ant-design/icons";
import type { MenuProps } from "antd";
import DefaultAvatar from "../../assets/avatar.png";
import { message } from "antd";
import { useAuthApi } from "../../api/auth";
import { useNavigate } from "react-router-dom";

const items: MenuProps["items"] = [
  {
    label: "Logout",
    key: "1",
    icon: <LogoutOutlined />,
  },
];
const ProfileMenu = () => {
  const navigate = useNavigate();
  const { logout } = useAuthApi();
  const onClick: MenuProps["onClick"] = ({ key }) => {
    if (key === "1") {
      logout()
        .then(() => {
          localStorage.removeItem("access_token");
          navigate("/");
        })
        .catch((error) => {
          message.error(error.message);
        });
    }
  };
  return (
    <>
      <Dropdown
        menu={{ items, onClick }}
        trigger={["click"]}
        placement="bottomRight"
      >
        <Row align="middle">
          <Avatar src={DefaultAvatar} style={{ marginRight: 10 }} />
        </Row>
      </Dropdown>
    </>
  );
};
export default ProfileMenu;
