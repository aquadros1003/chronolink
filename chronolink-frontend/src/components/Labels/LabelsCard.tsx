import { get } from "../../api/apiClient";
import { post } from "../../api/apiClient";
import { del } from "../../api/apiClient";
import { put } from "../../api/apiClient";
import { LabelType } from "../../api/types";
import { useEffect, useState } from "react";
import { Button, Input, message, ColorPicker, Tag } from "antd";
import { useRef } from "react";
import type { InputRef } from "antd";
import { Tooltip } from "antd";

import "./Labels.css";

const LabelsCard = ({
  timelineId,
  permissions,
}: {
  timelineId: string;
  permissions: string[];
}) => {
  const [labels, setLabels] = useState<LabelType[]>([]);
  const [_, setLoading] = useState(false);
  const [inputVisible, setInputVisible] = useState(false);
  const [inputValue, setInputValue] = useState("");
  const [color, setColor] = useState("");
  const [editInputIndex, setEditInputIndex] = useState(-1);
  const [editInputValue, setEditInputValue] = useState("");
  const canAddLabel = permissions?.includes("CAN_CREATE_LABEL");
  const canUpdateLabel = permissions?.includes("CAN_UPDATE_LABEL");
  const canDeleteLabel = permissions?.includes("CAN_DELETE_LABEL");
  const [editColor, setEditColor] = useState("");
  const inputRef = useRef<InputRef>(null);
  const editInputRef = useRef<InputRef>(null);

  const fetchLabels = async () => {
    const response: any = await get(`/labels/${timelineId}`);
    setLabels(response.data.data);
  };
  useEffect(() => {
    fetchLabels();
  }, [timelineId]);

  const deleteLabel = async (labelId: string) => {
    setLoading(true);
    await del(`/delete-label/${labelId}`);
    setLoading(false);
    fetchLabels();
  };

  const updateLabel = async (labelId: string, name: string, color: string) => {
    setLoading(true);
    if (color === "") {
      message.error("Color is required");
      setLoading(false);
      return;
    }
    if (name === "") {
      message.error("Name is required");
      setLoading(false);
      return;
    }
    await put(`/update-label/${labelId}`, {
      name: name,
      color: color,
      timeline_id: timelineId,
    });
    setLoading(false);
    setEditInputIndex(-1);
    setEditInputValue("");
    setEditColor("");
    setInputVisible(false);
    fetchLabels();
  };

  const addLabel = async (name: string, color: string) => {
    if (color === "") {
      message.error("Color is required");
      return;
    }
    if (name === "") {
      message.error("Name is required");
      return;
    }
    setLoading(true);
    await post(`/create-label`, {
      name: name,
      color: color,
      timeline_id: timelineId,
    });
    setLoading(false);
    fetchLabels();
    setInputVisible(false);
    setInputValue("");
    setColor("");
    setEditInputIndex(-1);
    setEditInputValue("");
    setEditColor("");
  };

  const handleClose = (removedTag: LabelType) => {
    deleteLabel(removedTag.id);
  };
  const showInput = () => {
    setInputVisible(true);
  };
  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setInputValue(e.target.value);
  };
  const handleInputConfirm = () => {
    if (inputValue) {
      addLabel(inputValue, color);
    }
    setInputVisible(false);
    setInputValue("");
  };
  const handleEditInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setEditInputValue(e.target.value);
  };
  const handleEditInputConfirm = (label: LabelType) => {
    updateLabel(label.id, editInputValue, editColor);
    setEditInputIndex(-1);
  };
  const handleColorChange = (value: any) => {
    setColor(value.toHexString());
  };

  const handleEditColorChange = (value: any) => {
    setEditColor(value.toHexString());
  };
  const saveInputRef = (input: InputRef) => {
    (inputRef.current as any) = input;
  };
  const saveEditInputRef = (input: InputRef) => {
    (editInputRef.current as any) = input;
  };

  return (
    <div className="labels-card">
      <div className="labels">
        {labels.map((label, index) => {
          if (editInputIndex === index) {
            return (
              <div key={label.id} className="edit-label">
                <Input
                  ref={saveEditInputRef}
                  type="text"
                  className="edit-label-input"
                  value={editInputValue}
                  onChange={handleEditInputChange}
                  suffix={
                    <>
                      <ColorPicker
                        value={editColor}
                        onChangeComplete={handleEditColorChange}
                      />{" "}
                      <Button onClick={() => handleEditInputConfirm(label)}>
                        Save
                      </Button>
                    </>
                  }
                />
              </div>
            );
          }
          const isLongTag = label.name.length > 20;
          const tagElem = (
            <Tag
              key={label.id}
              className="label"
              closable={canDeleteLabel}
              onClose={() => handleClose(label)}
              color={label.color}
            >
              {canUpdateLabel && (
                <span
                  onDoubleClick={(e) => {
                    setEditInputIndex(index);
                    setEditInputValue(label.name);
                    setEditColor(label.color);
                    e.preventDefault();
                  }}
                >
                  {isLongTag ? `${label.name.slice(0, 20)}...` : label.name}
                </span>
              )}
              {!canUpdateLabel &&
                (isLongTag ? `${label.name.slice(0, 20)}...` : label.name)}
            </Tag>
          );
          return isLongTag ? (
            <Tooltip title={label.name} key={label.id}>
              {tagElem}
            </Tooltip>
          ) : (
            tagElem
          );
        })}
        {inputVisible && (
          <div className="add-label">
            <Input
              ref={saveInputRef}
              className="add-label-input"
              type="text"
              size="small"
              style={{ width: "100%", margin: "0 8px 8px 0" }}
              value={inputValue}
              onChange={handleInputChange}
              onPressEnter={handleInputConfirm}
              suffix={
                <>
                  <ColorPicker
                    value={color}
                    onChangeComplete={handleColorChange}
                  />{" "}
                  <Button onClick={handleInputConfirm}>Save</Button>
                </>
              }
            />
          </div>
        )}
        {!inputVisible && canAddLabel && (
          <Button className="add-label-button" onClick={showInput}>
            Add Label
          </Button>
        )}
      </div>
    </div>
  );
};

export default LabelsCard;
