import Navbar from "@/Components/NavBar";
import { Head } from "@inertiajs/react";

export default function AppLayout({ children }) {
    return (
        <div className="min-h-screen ">
            <Head title="Sample Project" />
            <Navbar />
            <main className="h-full ">{children}</main>
        </div>
    );
}
