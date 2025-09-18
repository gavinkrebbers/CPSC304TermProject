import InputError from "@/Components/PreMade/InputError";
import InputLabel from "@/Components/PreMade/InputLabel";
import PrimaryButton from "@/Components/PreMade/PrimaryButton";
import TextInput from "@/Components/PreMade/TextInput";
import { Head, Link, useForm } from "@inertiajs/react";

export default function Login({ status }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: "",
        password: "",
    });

    const submit = (e) => {
        e.preventDefault();

        post(route("login"), {
            onFinish: () => reset("password"),
        });
    };

    return (
        <div className="flex flex-col items-center min-h-screen pt-6 bg-gray-100 sm:justify-center sm:pt-0">
            <div className="w-full px-6 py-4 mt-6 overflow-hidden bg-white shadow-md sm:max-w-md sm:rounded-lg">
                <Head title="Log in" />

                {status && (
                    <div className="mb-4 text-sm font-medium text-green-600">
                        {status}
                    </div>
                )}

                <form onSubmit={submit}>
                    <div>
                        <InputLabel htmlFor="email" value="Email" />

                        <TextInput
                            id="email"
                            type="email"
                            name="email"
                            value={data.email}
                            className="block w-full mt-1"
                            autoComplete="username"
                            isFocused={true}
                            onChange={(e) => setData("email", e.target.value)}
                        />

                        <InputError message={errors.email} className="mt-2" />
                    </div>

                    <div className="mt-4">
                        <InputLabel htmlFor="password" value="Password" />

                        <TextInput
                            id="password"
                            type="password"
                            name="password"
                            value={data.password}
                            className="block w-full mt-1"
                            autoComplete="current-password"
                            onChange={(e) =>
                                setData("password", e.target.value)
                            }
                        />

                        <InputError
                            message={errors.password}
                            className="mt-2"
                        />
                    </div>

                    <div className="mt-4 text-sm text-gray-600">
                        <p>
                            Don’t have an account?{" "}
                            <Link
                                href={route("register")}
                                className="font-medium text-blue-600 hover:underline"
                            >
                                Create one here
                            </Link>
                        </p>
                    </div>

                    <div className="flex items-center justify-end mt-4">
                        <PrimaryButton className="ms-4" disabled={processing}>
                            Log in
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    );
}
