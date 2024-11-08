import { useEffect } from "react";
import { useState } from "react";
import { Timeline } from "../api/types";
import { apiClient } from "../api/apiClient";
import { Tabs } from 'antd';
import { PlusOutlined } from '@ant-design/icons';

const Timelines = () => {
  const [timelines, setTimelines] = useState<Timeline[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchTimelines = async () => {
      const response = await apiClient.get("/timelines");
      setTimelines(response.data.data);
      setLoading(false);
    };

    fetchTimelines();
  }, []);
  return (
    <div>
      <div style={{ width: 800, margin: "auto" }}>
      <h1>Timelines</h1>
      {loading ? (
        <p>Loading...</p>
      ) : (
        <Tabs defaultActiveKey="1" tabPosition="top" style={{ height: 220 }}>
          <Tabs.TabPane tab={<PlusOutlined />} key="plus">
            <p>All timelines</p>
          </Tabs.TabPane>
          {timelines.map((timeline) => (
            <Tabs.TabPane tab={timeline.title} key={timeline.id}>
              <p>{timeline.description}</p>
            </Tabs.TabPane>
          ))}
        </Tabs>
      )}
    </div>
    </div>
  );
}

export default Timelines;
