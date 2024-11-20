import { PlusOutlined } from "@ant-design/icons";
import { Button, Form, Input, Tag } from "antd";
import { useNavigate } from "react-router";
import { CreateTimelineRequest } from "../../api/types";
import { post } from "../../api/apiClient";
import "./TimelineForm.css";
import { useState } from "react";
import { useRef } from "react";
import { InputRef } from "antd/lib/input";
import { Tooltip } from "antd";
import { ColorPicker } from "antd";

const { TextArea } = Input;
const CreateTimelineForm = () => {
  const navigate = useNavigate();
  const [form] = Form.useForm();
  const [tags, setTags] = useState<{ color: string; text: string }[]>([
    { color: "#f50", text: "Travel" },
    { color: "#2db7f5", text: "Life" },
    { color: "#87d068", text: "Work" },
    { color: "#108ee9", text: "Study" },
  ]);
  const [inputVisible, setInputVisible] = useState(false);
  const [inputValue, setInputValue] = useState("");
  const [inputColorValue, setInputColorValue] = useState("#f50");
  const inputRef = useRef<InputRef>(null);

  const handleClose = (removedTag: string) => {
    const newTags = tags.filter((tag) => tag.text !== removedTag);
    setTags(newTags);
  };

  const showInput = () => {
    setInputVisible(true);
  };

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setInputValue(e.target.value);
  };

  const handleColorChange = (_: any, css: string) => {
    setInputColorValue(css);
  };

  const handleInputConfirm = () => {
    if (
      inputValue &&
      tags.filter((tag) => tag.text === inputValue).length === 0
    ) {
      const newTags = [...tags, { color: inputColorValue, text: inputValue }];
      setTags(newTags);
    }
    setInputVisible(false);
    setInputValue("");
    setInputColorValue("");
  };

  const onFinish = async (values: CreateTimelineRequest) => {
    try {
      const response: any = await post("/create-timeline", {
        title: values.title,
        description: values.description,
        labels: tags.map((tag) => {
          return {
            name: tag.text,
            color: tag.color,
          };
        }),
      });
      navigate(`/dashboard/${response.data.data.id}`);
      window.location.reload();
    } catch (error) {
      console.error(error);
    }
  };

  return (
    <div className="form-container">
      <Form form={form} onFinish={onFinish} layout="vertical">
        <Form.Item
          label="Title"
          name="title"
          rules={[{ required: true, message: "Please input the title!" }]}
        >
          <Input placeholder="Title" />
        </Form.Item>
        <Form.Item
          label="Description"
          name="description"
          rules={[{ required: true, message: "Please input the description!" }]}
        >
          <TextArea placeholder="Description" rows={6} />
        </Form.Item>
        <Form.Item
          label="Labels"
          rules={[{ required: true, message: "Please input the tags!" }]}
        >
          {tags.map<React.ReactNode>((tag, _) => {
            const isLongTag = tag.text.length > 20;
            const tagElem = (
              <Tag
                className="label-tag"
                key={tag.text}
                color={tag.color}
                closable
                onClose={() => handleClose(tag.text)}
              >
                <span>
                  {isLongTag ? `${tag.text.slice(0, 20)}...` : tag.text}
                </span>
              </Tag>
            );
            return isLongTag ? (
              <Tooltip title={tag.text} key={tag.text}>
                {tagElem}
              </Tooltip>
            ) : (
              tagElem
            );
          })}
          {inputVisible && (
            <Input
              ref={inputRef}
              type="text"
              size="small"
              className="label-input"
              value={inputValue}
              onChange={handleInputChange}
              onPressEnter={handleInputConfirm}
              prefix={
                <ColorPicker
                  value={inputColorValue}
                  onChange={handleColorChange}
                />
              }
            />
          )}
          {!inputVisible && (
            <Tag className="add-label-tag" onClick={showInput}>
              <PlusOutlined /> New Tag
            </Tag>
          )}
        </Form.Item>
        <Form.Item>
          <Button type="primary" htmlType="submit" block>
            Create Timeline
          </Button>
        </Form.Item>
      </Form>
    </div>
  );
};

export default CreateTimelineForm;
