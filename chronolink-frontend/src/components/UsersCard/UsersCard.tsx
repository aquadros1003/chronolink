import { get } from "../../api/apiClient";
import { post } from "../../api/apiClient";
import { put } from "../../api/apiClient";
import { useEffect, useState } from "react";
import { message, Select } from "antd";
import { Button } from "antd";
import { PlusOutlined } from "@ant-design/icons";
import { Collapse } from "antd";
import { Checkbox } from "antd";
import "./UsersCard.css";
const UsersCard = ({ timelineId }: { timelineId: string }) => {
  const [users, setUsers] = useState([]);
  const [permissions, setPermissions] = useState([]);
  const [userEmails, setUserEmails] = useState([]);
  const [selectedUser, setSelectedUser] = useState<string>("");
  const [loading, setLoading] = useState(true);

  const fetchPermissions = async () => {
    const response: any = await get(`/permissions`);
    setPermissions(response.data.data);
  };
  const fetchUsers = async () => {
    const response: any = await get(`/timelines/${timelineId}/users`);
    setUsers(response.data.data);
    setLoading(false);
  };
  useEffect(() => {
    fetchUsers();
    fetchPermissions();
  }, [timelineId]);
  const handleAddUser = (email: string) => {
    post(`assign-user/${timelineId}`, { email })
      .then(() => {
        fetchUsers();
        setSelectedUser("");
      })
      .catch(() => {
        message.error("User already added");
      });
  };
  console.log(selectedUser);
  const handleUpdateUserPermissions = (
    email: string,
    permissionIds: string[],
  ) => {
    put(`timelines/${timelineId}/update-user-permissions`, {
      user_email: email,
      permissions: permissionIds,
    })
      .then(() => {
        fetchUsers();
      })
      .catch(() => {
        message.error("Failed to update permissions");
      });
  };

  return (
    <div className="users-card">
      {loading && <div>Loading...</div>}
      <Button
        style={{ marginBottom: 10 }}
        onClick={() => {
          navigator.clipboard.writeText(
            `${window.location.origin}/dashboard/${timelineId}`,
          );
          message.success("Link copied to clipboard");
        }}
      >
        Copy Link to Timeline
      </Button>
      <div className="user-search">
        <Select
          showSearch
          placeholder="Search for a user by email"
          optionFilterProp="children"
          suffixIcon={<></>}
          className="user-select"
          onSelect={(value) => setSelectedUser(value)}
          onSearch={(value) => {
            if (value.length > 2) {
              get(`/users/${value}`).then((response) => {
                setUserEmails(response.data.data);
              });
            }
          }}
        >
          {userEmails.map((email) => (
            <Select.Option key={email} value={email}>
              {email}
            </Select.Option>
          ))}
        </Select>
        <Button
          icon={<PlusOutlined />}
          onClick={() => {
            handleAddUser(selectedUser);
          }}
        />
      </div>
      <Collapse>
        {users.map((user: any) => (
          <Collapse.Panel header={user.email} key={user.id}>
            <Checkbox.Group
              options={permissions.map((permission: any) => ({
                label: permission.name,
                value: permission.id,
              }))}
              defaultValue={user.permissions.map(
                (permission: any) => permission.id,
              )}
              onChange={(values) => {
                handleUpdateUserPermissions(user.email, values);
              }}
            />
          </Collapse.Panel>
        ))}
      </Collapse>
    </div>
  );
};

export default UsersCard;
