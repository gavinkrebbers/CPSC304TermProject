import AppLayout from "@/Layouts/AppLayout";
import { router } from "@inertiajs/react";
import { useState } from "react";
import { Button } from "@/Components/ui/button";

export default function CreateGroupChat() {
    const [groupChatName, setGroupChatName] = useState("");

    const handleSubmit = (e) => {
        e.preventDefault();
        if (!groupChatName.trim()) return;
        router.post(route("groupChat.store", [groupChatName]));
    };

    return (
        <AppLayout>
            <div className="flex items-center justify-center h-[calc(100vh-4rem)] bg-gray-50">
                <div className="w-full max-w-md p-8 bg-white shadow-md rounded-2xl">
                    <h1 className="mb-6 text-xl font-semibold text-center text-gray-800">
                        Create a New Group Chat
                    </h1>

                    <form onSubmit={handleSubmit} className="space-y-4">
                        <input
                            type="text"
                            placeholder="Enter group chat name"
                            value={groupChatName}
                            onChange={(e) => setGroupChatName(e.target.value)}
                            className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        />
                        <Button
                            type="submit"
                            className="w-full"
                            disabled={!groupChatName.trim()}
                        >
                            Create Chat
                        </Button>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
