import { useState } from "react";
import { Form, Input, Modal, DatePicker, Select } from "antd";
import { put, get } from "../../api/apiClient";
import { EventType, LabelType } from "../../api/types";
import { useEffect } from "react";
import dayjs from "dayjs";

const { TextArea } = Input;

const EditEventModal = ({
  event,
  timelineId,
  onClose,
  onSucess,
}: {
  event: any;
  timelineId: string;
  onClose: () => void;
  onSucess: () => void;
}) => {
  const [loading, setLoading] = useState(false);
  const [form] = Form.useForm();
  const [labels, setLabels] = useState<LabelType[]>([]);
  const fetchLabels = async () => {
    const response: any = await get(`/labels/${timelineId}`);
    setLabels(response.data.data);
  };
  useEffect(() => {
    fetchLabels();
  }, [timelineId]);
  const handleOk = async () => {
    setLoading(true);
    try {
      const values = await form.validateFields();
      await put(`/update-event/${event.id}`, {
        title: values.title,
        description: values.description,
        start_date: dayjs(values.start_date).format("YYYY-MM-DD"),
        end_date: dayjs(values.end_date).format("YYYY-MM-DD"),
        location: values.location,
        label_id: values.label_id,
        timeline_id: timelineId,
      });
      onClose();
      onSucess();
    } catch (errorInfo) {
      console.log("Failed:", errorInfo);
    }
    setLoading(false);
  };

  const handleCancel = () => {
    onClose();
  };

  const onFinish = async (values: EventType) => {
    setLoading(true);
    try {
      await put(`/events/${event.id}`, values);
      onClose();
    } catch (errorInfo) {
      console.log("Failed:", errorInfo);
    }
    setLoading(false);
  };

  return (
    <Modal
      title="Edit Event"
      open={true}
      onCancel={handleCancel}
      onOk={handleOk}
      confirmLoading={loading}
      okText="Save"
    >
      <div className="edit-event-modal">
        <Form form={form} onFinish={onFinish} layout="vertical">
          <Form.Item
            name="title"
            label="Title"
            rules={[{ required: true, message: "Please input the title!" }]}
            initialValue={event.title}
          >
            <Input />
          </Form.Item>
          <Form.Item
            name="description"
            label="Description"
            initialValue={event.description}
          >
            <TextArea rows={4} autoSize={{ minRows: 5, maxRows: 5 }} />
          </Form.Item>
          <Form.Item
            name="start_date"
            label="Start Date"
            initialValue={dayjs(event.start_date)}
            rules={[
              { required: true, message: "Please input the start date!" },
            ]}
          >
            <DatePicker format="DD/MM/YYYY" style={{ width: "100%" }} />
          </Form.Item>
          <Form.Item
            name="end_date"
            label="End Date"
            rules={[{ required: true, message: "Please input the end date!" }]}
            initialValue={dayjs(event.end_date)}
          >
            <DatePicker format="DD/MM/YYYY" style={{ width: "100%" }} />
          </Form.Item>
          <Form.Item
            name="location"
            label="Location"
            initialValue={event.location}
          >
            <Input />
          </Form.Item>
          <Form.Item
            name="label_id"
            label="Label"
            initialValue={event.label.id}
          >
            <Select>
              {labels.map((label) => (
                <Select.Option key={label.id} value={label.id}>
                  {label.name}
                </Select.Option>
              ))}
            </Select>
          </Form.Item>
        </Form>
      </div>
    </Modal>
  );
};

export default EditEventModal;
