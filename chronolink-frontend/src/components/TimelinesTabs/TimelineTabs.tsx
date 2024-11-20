import { useEffect } from "react";
import { useState } from "react";
import { TimelineType } from "../../api/types";
import { apiClient } from "../../api/apiClient";
import { Tabs } from "antd";
import { PlusOutlined, CrownOutlined } from "@ant-design/icons";
import CreateTimelineForm from "../TimelineForm/TimelineForm";
import "./TimelineTabs.css";
import { useParams } from "react-router-dom";
import TimelineContainer from "../TimelineCard/TimelineContainer";

const TimelineTabs = () => {
  const [timelines, setTimelines] = useState<TimelineType[]>([]);
  const [loading, setLoading] = useState(true);
  const timelineId = useParams<{ timelineId: string }>().timelineId;

  useEffect(() => {
    const fetchTimelines = async () => {
      const response = await apiClient.get("/timelines");
      setTimelines(response.data.data);
      setLoading(false);
    };

    fetchTimelines();
  }, []);
  return (
    <div className="timeline-tabs">
      {loading ? (
        <p>Loading...</p>
      ) : (
        <Tabs
          defaultActiveKey={timelineId || "add"}
          tabPosition="top"
          className="timeline-tabs"
          style={{ height: 220 }}
          items={[
            {
              key: "add",
              label: (
                <div className="add-timeline-tab">
                  <PlusOutlined />
                </div>
              ),
              children: <CreateTimelineForm />,
            },
            ...timelines.map((timeline) => ({
              key: timeline.id.toString(),
              label: <p>{timeline.title} </p>,
              children: (
                <TimelineContainer timelineId={timeline.id.toString()} />
              ),
            })),
          ]}
        />
      )}
    </div>
  );
};

export default TimelineTabs;
