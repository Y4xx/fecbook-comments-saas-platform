import { Head, router } from '@inertiajs/react';
import Layout from '@/components/Layout';
import { PageProps } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';

interface FacebookPageData {
    id: string;
    name: string;
    access_token: string;
    category?: string;
}

interface SelectPageProps extends PageProps {
    pages: FacebookPageData[];
}

export default function SelectPage({ pages }: SelectPageProps) {
    const handleConnect = (page: FacebookPageData) => {
        router.post('/facebook/pages/connect', {
            page_id: page.id,
            page_name: page.name,
            access_token: page.access_token,
        });
    };

    return (
        <Layout>
            <Head title="Select Facebook Page" />

            <div className="max-w-4xl mx-auto space-y-6">
                {/* Header */}
                <div>
                    <h2 className="text-3xl font-bold text-gray-900">Select a Facebook Page</h2>
                    <p className="mt-1 text-sm text-gray-500">
                        Choose which page you want to connect for sentiment analysis
                    </p>
                </div>

                {/* Pages Grid */}
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    {pages.map((page) => (
                        <Card key={page.id} className="hover:shadow-lg transition-shadow">
                            <CardHeader>
                                <CardTitle className="text-lg">{page.name}</CardTitle>
                                {page.category && (
                                    <CardDescription>{page.category}</CardDescription>
                                )}
                            </CardHeader>
                            <CardContent>
                                <Button onClick={() => handleConnect(page)} className="w-full">
                                    Connect This Page
                                </Button>
                            </CardContent>
                        </Card>
                    ))}
                </div>

                {pages.length === 0 && (
                    <Card>
                        <CardContent className="py-12 text-center">
                            <p className="text-gray-500">
                                No pages found. Make sure you have Facebook pages you manage.
                            </p>
                        </CardContent>
                    </Card>
                )}
            </div>
        </Layout>
    );
}
