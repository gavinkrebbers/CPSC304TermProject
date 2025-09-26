import { Button } from "@/Components/ui/button";
import AppLayout from "@/Layouts/AppLayout";
import { Head, router } from "@inertiajs/react";

export default function Welcome({ auth }) {
    return (
        <AppLayout>
            <div className="flex flex-col items-center justify-center h-[calc(100vh-4rem)] bg-gray-50">
                <div className="w-full max-w-md p-8 space-y-6 text-center bg-white shadow-md rounded-2xl">
                    <h1 className="text-2xl font-bold text-gray-800">
                        Welcome, {auth.user?.name || "Guest"}
                    </h1>

                    <div className="flex flex-col space-y-3">
                        <Button
                            className="w-full"
                            onClick={() => {
                                router.get(route("groupChat.create"));
                            }}
                        >
                            Create Group Chat
                        </Button>
                        <Button
                            variant="outline"
                            className="w-full"
                            onClick={() => {
                                router.get(route("groupChat.index"));
                            }}
                        >
                            Join Group Chat
                        </Button>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
