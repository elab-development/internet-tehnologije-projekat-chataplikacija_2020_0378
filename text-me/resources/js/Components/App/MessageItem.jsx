import { usePage } from "@inertiajs/react";
import ReactMarkdown from "react-markdown";
import React from "react";
import UserAvatar from "./UserAvatar";
import { formatMessageDateLong } from "@/helpers";
import MessageAttachments from "./MessageAttachmens";
import MessageOptionsDropdown from "./MessageOptionsDropdown";


const MessageItem = ({ message, attachmentClick })=>{
    //const currentUser = usePage().props.auth.user;

    const { user: currentUser } = usePage().props.auth;
    // Uslov za prikaz tri tačke
    const canShowOptions = currentUser.role !== 'user' && message.sender_id === currentUser.id;

    return (
        <div
        className={
            "chat " +
            (message.sender_id === currentUser.id 
                ? "chat-end"
                : "chat-start")
        }
        >
            {<UserAvatar user={message.sender} />}

            <div className="chat-header">
                {message.sender_id !== currentUser.id
                    ? message.sender.name 
                    : ""}

                <time className="text-xs opacity-50 ml-2">
                    {formatMessageDateLong(message.created_at)}
                </time>

            </div>

            <div className={"chat-bubble relative " + 
                (message.sender_id === currentUser.id
                    ? "chat-bubble-info"
                    : "")
            }>
                {/* {message.sender_id == currentUser.id && (
                    <MessageOptionsDropdown message={message} /> 
                )} */}
                {canShowOptions && (
                    <MessageOptionsDropdown message={message} /> 
                )}
                <div className="chat-message">
                    <div className="chat-message-content">
                    {message.gif_url ? ( // **Dodato za prikaz GIF-a**
                            <img src={message.gif_url} alt="GIF" /> // **Prikaz GIF-a**
                        ) : (
                            <ReactMarkdown>{message.message}</ReactMarkdown>
                        )}
                    </div>
                    <MessageAttachments
                        attachments={message.attachments}
                        attachmentClick={attachmentClick}
                    />
                </div>
            </div>
        </div>
    );
};

export default MessageItem;