import AppLayout from "@/Layouts/AppLayout";
import { router } from "@inertiajs/react";
import { useEffect, useState } from "react";

export default function GroupChat(data) {
    const currentUserId = data.auth.user.id;
    const groupChat = data.groupChat;

    const [newMessage, setNewMessage] = useState("");
    const [messages, setMessages] = useState(groupChat.messages || []);

    useEffect(() => {
        const interval = setInterval(async () => {
            if (!messages.length) return;
            const lastCreatedAt = messages[messages.length - 1].created_at;
            const response = await fetch(
                `/group-chats/${groupChat.id}/messages`
            );
            const newMessages = await response.json();
            const freshMessages = newMessages.filter(
                (msg) => new Date(msg.created_at) > new Date(lastCreatedAt)
            );

            if (freshMessages.length) {
                setMessages((prev) => [...prev, ...freshMessages]);
            }
        }, 2000);

        return () => clearInterval(interval);
    }, [messages]);

    const handleSubmit = (e) => {
        e.preventDefault();
        if (!newMessage.trim()) return;

        const tempMessage = {
            id: Date.now(),
            data: newMessage,
            user_id: currentUserId,
            user_name: data.auth.user.name,
            created_at: new Date().toISOString(),
        };

        setMessages((prev) => [...prev, tempMessage]);
        setNewMessage("");

        router.post(
            route("messages.store", [groupChat.id]),
            { message: tempMessage.data },
            {
                preserveScroll: true,
                onError: () => {
                    setMessages((prev) =>
                        prev.filter((msg) => msg.id !== tempMessage.id)
                    );
                },
            }
        );
    };

    return (
        <AppLayout>
            <div className="flex flex-col max-w-3xl mx-auto h-[calc(100vh-4rem)] bg-white rounded-2xl shadow-md overflow-hidden">
                <div className="px-6 py-4 bg-gray-900">
                    <h1 className="text-lg font-semibold text-center text-white">
                        {groupChat.name}
                    </h1>
                </div>

                <div className="flex-1 p-6 overflow-y-auto bg-gray-50">
                    {messages.length ? (
                        <div className="space-y-4">
                            {messages.map((msg) => {
                                const isCurrentUser =
                                    msg.user_id === currentUserId;
                                return (
                                    <div
                                        key={msg.id}
                                        className={`flex items-start ${
                                            isCurrentUser
                                                ? "justify-end"
                                                : "justify-start"
                                        }`}
                                    >
                                        <div
                                            className={`max-w-xs px-4 py-2 rounded-2xl shadow-sm text-sm ${
                                                isCurrentUser
                                                    ? "bg-blue-500 text-white rounded-br-md"
                                                    : "bg-white text-gray-800 border border-gray-200 rounded-bl-md"
                                            }`}
                                        >
                                            {!isCurrentUser && (
                                                <p className="mb-1 text-xs font-semibold text-gray-600">
                                                    {msg.user_name}
                                                </p>
                                            )}
                                            <p>{msg.data}</p>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    ) : (
                        <div className="flex flex-col items-center justify-center h-full text-gray-500">
                            <div className="flex items-center justify-center w-12 h-12 mb-3 bg-gray-200 rounded-full">
                                <svg
                                    className="w-6 h-6 text-gray-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                                    />
                                </svg>
                            </div>
                            <p>No messages yet</p>
                            <p className="text-xs text-gray-400">
                                Start the conversation!
                            </p>
                        </div>
                    )}
                </div>

                <div className="p-4 bg-white border-t border-gray-200">
                    <form
                        onSubmit={handleSubmit}
                        className="flex items-center space-x-2"
                    >
                        <input
                            type="text"
                            placeholder="Type a message..."
                            value={newMessage}
                            onChange={(e) => setNewMessage(e.target.value)}
                            className="flex-1 px-4 py-2 text-sm border border-gray-300 rounded-full outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                        <button
                            type="submit"
                            disabled={!newMessage.trim()}
                            className="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-full hover:bg-blue-600 disabled:bg-gray-300 disabled:cursor-not-allowed"
                        >
                            Send
                        </button>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
