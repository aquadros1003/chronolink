import Timeline from "./Timeline";
import { get } from "../../api/apiClient";
import { useEffect, useState } from "react";
import { PermissionType } from "../../api/types";
import TimelineOptions from "./TimelineOptions";

const TimelineContainer = ({ timelineId }: { timelineId: string }) => {
  const [permissions, setPermissions] = useState<Array<PermissionType>>([]);
  const [isOwner, setIsOwner] = useState(false);
  useEffect(() => {
    const fetchPermissions = async () => {
      const response: any = await get(`/timelines/${timelineId}`);
      setPermissions(response.data.data.permissions);
      setIsOwner(response.data.data.is_owner);
    };
    fetchPermissions();
  }, [timelineId]);
  console.log(permissions);
  const requiredPermissions = [
    "CAN_CREATE_LABEL",
    "CAN_UPDATE_LABEL",
    "CAN_DELETE_LABEL",
  ];
  const permissionNames: string[] = permissions.map(
    (permission) => permission.name,
  );
  const haveOtherPermissions = requiredPermissions.some((permission) =>
    permissionNames.includes(permission),
  );
  console.log(permissionNames);
  return (
    <div className="timeline-container">
      <Timeline
        timelineId={timelineId}
        permissions={permissions}
        haveOtherPermissions={haveOtherPermissions}
      />
      {haveOtherPermissions && (
        <TimelineOptions
          permissions={permissionNames}
          timelineId={timelineId}
          isOwner={isOwner}
        />
      )}
    </div>
  );
};

export default TimelineContainer;
