import AppLayout from "@/Layouts/AppLayout";
import { Head, Link } from "@inertiajs/react";

export default function Welcome({ auth, laravelVersion, phpVersion }) {
    return (
        <AppLayout>
            <div className="flex justify-center">
                <p>wow this sure is a test component</p>
            </div>
        </AppLayout>
    );
}
