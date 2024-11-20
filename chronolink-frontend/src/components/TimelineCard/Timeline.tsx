import {
  VerticalTimeline,
  VerticalTimelineElement,
} from "react-vertical-timeline-component";
import "react-vertical-timeline-component/style.min.css";
import { get } from "../../api/apiClient";
import { del } from "../../api/apiClient";
import { EventType } from "../../api/types";
import { useState } from "react";
import { useEffect } from "react";
import "./Timeline.css";
import { Button, Modal } from "antd";
import { DeleteOutlined, EditOutlined } from "@ant-design/icons";
import EditEventModal from "../EditEventModal/EditEventModal";

const { confirm } = Modal;

const Timeline = ({
  timelineId,
  permissions,
  haveOtherPermissions,
}: {
  timelineId: string;
  permissions: any[];
  haveOtherPermissions: boolean;
}) => {
  const [events, setEvents] = useState<EventType[]>([]);
  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(false);
  const [loading, setLoading] = useState(true);
  const [isEditModalVisible, setIsEditModalVisible] = useState(false);
  const [selectedEvent, setSelectedEvent] = useState<EventType | null>(null);
  const canEdit = permissions
    ?.map((permission) => permission.name)
    .includes("CAN_DELETE_EVENT");
  const canDelete = permissions
    ?.map((permission) => permission.name)
    .includes("CAN_DELETE_EVENT");

  useEffect(() => {
    const fetchEvents = async () => {
      const response: any = await get(`/events/${timelineId}?per_page=3`);
      setEvents(response.data.data.data);
      setLoading(false);
      setHasMore(response.data.data.total > 3);
    };
    fetchEvents();
  }, [timelineId]);

  const handleEditEvent = (event: EventType) => {
    setSelectedEvent(event);
    setIsEditModalVisible(true);
  };

  const handleDeleteEvent = async (eventId: string) => {
    await del(`/delete-event/${eventId}`);
    const response: any = await get(`/events/${timelineId}?per_page=3`);
    setEvents(response.data.data.data);
    setHasMore(response.data.data.total > 3);
  };

  const showDeleteConfirm = (eventId: string) => {
    confirm({
      title: "Are you sure you want to delete this event?",
      onOk() {
        handleDeleteEvent(eventId);
      },
    });
  };
  const loadMore = async () => {
    const response: any = await get(
      `/events/${timelineId}?per_page=3&page=${page + 1}`,
    );
    setEvents([...events, ...response.data.data.data]);
    setHasMore(response.data.data.total > (page + 1) * 3);
    setPage(page + 1);
  };

  return (
    <div
      className="timeline-card"
      style={{ width: haveOtherPermissions ? "70%" : "100%" }}
    >
      {loading && <p>Loading...</p>}
      {isEditModalVisible && (
        <EditEventModal
          event={selectedEvent}
          timelineId={timelineId}
          onClose={() => {
            setIsEditModalVisible(false);
            setSelectedEvent(null);
          }}
          onSucess={() => {
            const fetchEvents = async () => {
              const response: any = await get(
                `/events/${timelineId}?per_page=3`,
              );
              setEvents(response.data.data.data);
              setLoading(false);
              setHasMore(response.data.data.total > 3);
            };
            fetchEvents();
          }}
        />
      )}
      {events.length === 0 && !loading && (
        <div className="no-events">
          Oops, looks like the calendar is clear! 📅 Time to plan something
          amazing—why not make your next big event happen here?
        </div>
      )}
      {events.length > 0 && !loading && (
        <div className="timeline">
          <VerticalTimeline className="vertical-timeline-line">
            {events.map((event) => (
              <VerticalTimelineElement
                key={event.id}
                date={event.start_date + " - " + event.end_date}
                iconStyle={{
                  background: event?.label?.color || "black",
                  color: "#fff",
                }}
                contentStyle={{
                  borderTop: `3px solid ${event?.label?.color || "black"}`,
                }}
                visible={true}
              >
                <div className="event-card-header">
                  <div className="event-title">
                    {event?.title}{" "}
                    {event?.label?.name && `#${event?.label?.name}`}
                  </div>
                  <div className="event-actions">
                    {canEdit && (
                      <Button
                        type="text"
                        icon={<EditOutlined />}
                        onClick={() => handleEditEvent(event)}
                      />
                    )}
                    {canDelete && (
                      <Button
                        type="text"
                        icon={<DeleteOutlined />}
                        onClick={() => showDeleteConfirm(event.id)}
                      />
                    )}
                  </div>
                </div>
                <p className="label">{event?.description}</p>
                <p className="label">{event?.location}</p>
              </VerticalTimelineElement>
            ))}
            {hasMore && (
              <VerticalTimelineElement
                iconStyle={{ background: "white", color: "#fff" }}
                icon={
                  <button
                    style={{
                      background: "white",
                      color: "black",
                      border: "none",
                    }}
                    onClick={loadMore}
                  >
                    Load More
                  </button>
                }
              />
            )}
          </VerticalTimeline>
        </div>
      )}
    </div>
  );
};

export default Timeline;
