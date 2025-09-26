import AppLayout from "@/Layouts/AppLayout";
import { router } from "@inertiajs/react";
import { useState } from "react";

export default function ListGroupChats({ chatsIn, chatsNotIn }) {
    const [inChats, setInChats] = useState(chatsIn);
    const [notInChats, setNotInChats] = useState(chatsNotIn);

    const handleJoin = (chat) => {
        setInChats((prev) => [...prev, chat]);
        setNotInChats((prev) => prev.filter((c) => c.id !== chat.id));

        router.post(
            route("groupChat.join", [chat.id]),
            {},
            {
                onError: () => {
                    setNotInChats((prev) => [...prev, chat]);
                    setInChats((prev) => prev.filter((c) => c.id !== chat.id));
                },
            }
        );
    };

    const handleLeave = (chat) => {
        setNotInChats((prev) => [...prev, chat]);
        setInChats((prev) => prev.filter((c) => c.id !== chat.id));

        router.delete(route("groupChat.leave", [chat.id]), {
            onError: () => {
                setInChats((prev) => [...prev, chat]);
                setNotInChats((prev) => prev.filter((c) => c.id !== chat.id));
            },
        });
    };

    const handleView = (chatId) => {
        router.get(route("groupChat.show", [chatId]));
    };

    return (
        <AppLayout>
            <div className="max-w-3xl p-6 mx-auto space-y-8">
                <section>
                    <h2 className="mb-4 text-xl font-semibold">
                        Your Group Chats
                    </h2>
                    {inChats.length > 0 ? (
                        <div className="space-y-3">
                            {inChats.map((chat) => (
                                <div
                                    key={chat.id}
                                    className="flex items-center justify-between p-4 bg-white border rounded-lg shadow-sm"
                                >
                                    <div>
                                        <p className="font-medium">
                                            {chat.name}
                                        </p>
                                        <p className="text-sm text-gray-500">
                                            Created by {chat.creator_name}
                                        </p>
                                    </div>
                                    <div className="flex space-x-2">
                                        <button
                                            onClick={() => handleView(chat.id)}
                                            className="px-3 py-1 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700"
                                        >
                                            View
                                        </button>
                                        <button
                                            onClick={() => handleLeave(chat)}
                                            className="px-3 py-1 text-sm text-white bg-red-600 rounded-md hover:bg-red-700"
                                        >
                                            Leave
                                        </button>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <p className="text-gray-500">
                            You’re not in any chats yet.
                        </p>
                    )}
                </section>

                <section>
                    <h2 className="mb-4 text-xl font-semibold">
                        Other Group Chats
                    </h2>
                    {notInChats.length > 0 ? (
                        <div className="space-y-3">
                            {notInChats.map((chat) => (
                                <div
                                    key={chat.id}
                                    className="flex items-center justify-between p-4 bg-white border rounded-lg shadow-sm"
                                >
                                    <div>
                                        <p className="font-medium">
                                            {chat.name}
                                        </p>
                                        <p className="text-sm text-gray-500">
                                            Created by {chat.creator_name}
                                        </p>
                                    </div>
                                    <button
                                        onClick={() => handleJoin(chat)}
                                        className="px-3 py-1 text-sm text-white bg-green-600 rounded-md hover:bg-green-700"
                                    >
                                        Join
                                    </button>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <p className="text-gray-500">
                            No chats available to join.
                        </p>
                    )}
                </section>
            </div>
        </AppLayout>
    );
}
