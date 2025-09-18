import { useState } from "react";
import { Link, usePage, router } from "@inertiajs/react";
import { Sheet, SheetContent, SheetTrigger } from "@/Components/ui/sheet";
import { Button } from "@/Components/ui/button";
import { Menu } from "lucide-react";

const Navbar = () => {
    const { auth, url } = usePage().props;
    const [isOpen, setIsOpen] = useState(false);

    return (
        <nav className="sticky top-0 z-50 w-full bg-white border-b border-slate-200 bg-opacity-95 backdrop-blur-md dark:bg-slate-900 dark:border-slate-700 dark:bg-opacity-95">
            <div className="container flex items-center justify-between h-16 px-4 mx-auto">
                <Link
                    href="/"
                    className="flex items-center gap-2 text-xl font-bold text-primary sm:text-2xl"
                >
                    <span className="xs:hidden">Sample Project</span>
                </Link>
                <div className="items-center hidden space-x-4 md:flex">
                    <AuthButton />
                </div>

                <Sheet open={isOpen} onOpenChange={setIsOpen}>
                    <SheetTrigger asChild className="md:hidden">
                        <Button
                            variant="outline"
                            size="sm"
                            className="p-2 bg-slate-50 hover:bg-slate-100 border-slate-200"
                        >
                            <Menu className="w-5 h-5" />
                            <span className="sr-only">Toggle menu</span>
                        </Button>
                    </SheetTrigger>
                    <SheetContent
                        side="right"
                        className="w-[80%] sm:w-[350px] pt-10 bg-white dark:bg-slate-900"
                    >
                        <div className="flex flex-col space-y-6">
                            <div className="pt-4">
                                <AuthButton
                                    isMobile
                                    onClick={() => setIsOpen(false)}
                                />
                            </div>
                        </div>
                    </SheetContent>
                </Sheet>
            </div>
        </nav>
    );
};

const AuthButton = ({ isMobile = false, onClick = () => {} }) => {
    const { auth, url } = usePage().props;
    const user = auth.user;

    if (user || auth.id) {
        return (
            <Button
                onClick={() => {
                    router.post(route("logout"));
                    onClick();
                }}
                className={`text-white bg-red-600 hover:bg-red-700 ${
                    isMobile ? "w-full" : ""
                }`}
            >
                Logout
            </Button>
        );
    }

    return (
        <div
            className={`flex ${
                isMobile ? "flex-col space-y-2" : "flex-row space-x-2"
            }`}
        >
            <Button
                asChild
                variant="outline"
                className={`bg-slate-50 hover:bg-slate-100 border-slate-200 text-slate-700 ${
                    isMobile ? "w-full" : ""
                }`}
                onClick={onClick}
            >
                <Link href="/login">Login</Link>
            </Button>
            <Button
                asChild
                className={`text-white bg-primary hover:bg-primary/90 ${
                    isMobile ? "w-full" : ""
                }`}
                onClick={onClick}
            >
                <Link href="/register">Register</Link>
            </Button>
        </div>
    );
};

export default Navbar;
