import { useState } from "react";
import "./Timeline.css";
import { Button, Form, Input, DatePicker, message } from "antd";
import { CreateEventRequest, LabelType } from "../../api/types";
import { post, get } from "../../api/apiClient";
import { useEffect } from "react";
import { Select } from "antd";
import dayjs from "dayjs";
import { useNavigate } from "react-router-dom";

const { TextArea } = Input;

const CreateEventForm = ({ timelineId }: { timelineId: string }) => {
  const navigate = useNavigate();
  const [labels, setLabels] = useState<LabelType[]>([]);
  const [loading, setLoading] = useState(false);

  const [form] = Form.useForm();

  const fetchLabels = async () => {
    const response: any = await get(`/labels/${timelineId}`);
    setLabels(response.data.data);
  };
  useEffect(() => {
    fetchLabels();
  }, [timelineId]);

  const onFinish = async (values: CreateEventRequest) => {
    setLoading(true);
    if (dayjs(values.start_date).isAfter(dayjs(values.end_date))) {
      message.error("End date should be after start date");
      setLoading(false);
      return;
    }
    await post(`/create-event`, {
      title: values.title,
      location: values.location,
      start_date: dayjs(values.start_date).format("YYYY-MM-DD"),
      end_date: dayjs(values.end_date).format("YYYY-MM-DD"),
      description: values.description,
      label_id: values.label_id,
      timeline_id: timelineId,
    });
    setLoading(false);
    form.resetFields();
    navigate(`/dashboard/${timelineId}`);
    window.location.reload();
  };

  return (
    <div className="create-event-form">
      <Form form={form} onFinish={onFinish} layout="vertical">
        <Form.Item
          name="title"
          label="Title"
          rules={[{ required: true, message: "Please input the title!" }]}
        >
          <Input />
        </Form.Item>
        <Form.Item name="description" label="Description">
          <TextArea rows={4} autoSize={{ minRows: 5, maxRows: 5 }} />
        </Form.Item>
        <Form.Item
          name="start_date"
          label="Start Date"
          rules={[{ required: true, message: "Please input the start date!" }]}
        >
          <DatePicker format="DD/MM/YYYY" style={{ width: "100%" }} />
        </Form.Item>
        <Form.Item
          name="end_date"
          label="End Date"
          rules={[{ required: true, message: "Please input the end date!" }]}
        >
          <DatePicker format="DD/MM/YYYY" style={{ width: "100%" }} />
        </Form.Item>
        <Form.Item name="location" label="Location">
          <Input />
        </Form.Item>
        <Form.Item name="label_id" label="Label">
          <Select>
            {labels.map((label) => (
              <Select.Option key={label.id} value={label.id}>
                {label.name}
              </Select.Option>
            ))}
          </Select>
        </Form.Item>
        <Form.Item>
          <Button type="primary" htmlType="submit" loading={loading}>
            Add Event
          </Button>
        </Form.Item>
      </Form>
    </div>
  );
};

export default CreateEventForm;
