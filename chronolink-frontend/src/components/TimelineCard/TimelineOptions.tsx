import { Tabs } from "antd";
import CreateEventForm from "./CreateEventForm";
import "./Timeline.css";
import LabelsCard from "../Labels/LabelsCard";
import UsersCard from "../UsersCard/UsersCard";

const TimelineOptions = ({
  permissions,
  timelineId,
  isOwner,
}: {
  permissions: string[];
  timelineId: string;
  isOwner: boolean;
}) => {
  const tabs = [];
  if (permissions?.includes("CAN_CREATE_EVENT")) {
    tabs.push({
      key: "add",
      label: "Add Event",
      children: <CreateEventForm timelineId={timelineId} />,
    });
  }
  if (
    permissions?.includes("CAN_CREATE_LABEL") ||
    permissions?.includes("CAN_UPDATE_LABEL") ||
    permissions?.includes("CAN_DELETE_LABEL")
  ) {
    tabs.push({
      key: "add-label",
      label: "Labels",
      children: (
        <LabelsCard timelineId={timelineId} permissions={permissions} />
      ),
    });
  }
  if (isOwner) {
    tabs.push({
      key: "users",
      label: "Users",
      children: <UsersCard timelineId={timelineId} />,
    });
  }
  return (
    <div className="timeline-options">
      <Tabs
        defaultActiveKey={timelineId || "add"}
        tabPosition="top"
        style={{ height: 220 }}
        items={tabs}
      />
    </div>
  );
};

export default TimelineOptions;
