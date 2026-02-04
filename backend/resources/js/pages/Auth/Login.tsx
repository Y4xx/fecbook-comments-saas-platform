import { FormEventHandler, useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import axios from 'axios';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';

export default function Login() {
    const [data, setData] = useState({
        email: '',
        password: '',
    });
    const [errors, setErrors] = useState<any>({});
    const [processing, setProcessing] = useState(false);

    const submit: FormEventHandler = async (e) => {
        e.preventDefault();
        setProcessing(true);
        setErrors({});

        try {
            const response = await axios.post('/api/login', data);
            
            // Store token
            localStorage.setItem('token', response.data.token);
            
            // Redirect to dashboard
            window.location.href = '/dashboard';
        } catch (error: any) {
            setErrors(error.response?.data?.errors || { email: ['Invalid credentials'] });
        } finally {
            setProcessing(false);
        }
    };

    return (
        <>
            <Head title="Log in" />

            <div className="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
                <div className="max-w-md w-full">
                    <div className="text-center mb-8">
                        <h1 className="text-3xl font-bold text-gray-900">Facebook Sentiment SaaS</h1>
                        <p className="mt-2 text-sm text-gray-600">
                            Analyze Facebook comments sentiment with AI
                        </p>
                    </div>

                    <Card>
                        <CardHeader>
                            <CardTitle>Sign in to your account</CardTitle>
                            <CardDescription>
                                Enter your email and password to access your dashboard
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <form onSubmit={submit} className="space-y-4">
                                <div>
                                    <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-1">
                                        Email
                                    </label>
                                    <Input
                                        id="email"
                                        type="email"
                                        name="email"
                                        value={data.email}
                                        onChange={(e) => setData({ ...data, email: e.target.value })}
                                        required
                                        autoFocus
                                        autoComplete="username"
                                    />
                                    {errors.email && (
                                        <p className="mt-1 text-sm text-red-600">{errors.email[0]}</p>
                                    )}
                                </div>

                                <div>
                                    <label htmlFor="password" className="block text-sm font-medium text-gray-700 mb-1">
                                        Password
                                    </label>
                                    <Input
                                        id="password"
                                        type="password"
                                        name="password"
                                        value={data.password}
                                        onChange={(e) => setData({ ...data, password: e.target.value })}
                                        required
                                        autoComplete="current-password"
                                    />
                                    {errors.password && (
                                        <p className="mt-1 text-sm text-red-600">{errors.password[0]}</p>
                                    )}
                                </div>

                                <Button type="submit" className="w-full" disabled={processing}>
                                    {processing ? 'Signing in...' : 'Sign in'}
                                </Button>
                            </form>

                            <div className="mt-4 text-center text-sm">
                                <span className="text-gray-600">Don't have an account? </span>
                                <Link href="/register" className="font-medium text-blue-600 hover:text-blue-500">
                                    Sign up
                                </Link>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}
