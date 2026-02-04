import { Head, Link } from '@inertiajs/react';
import Layout from '@/components/Layout';
import { PageProps, FacebookPage } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';

export default function Index({ pages }: PageProps<{ pages: FacebookPage[] }>) {
    return (
        <Layout>
            <Head title="Connected Pages" />

            <div className="space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h2 className="text-3xl font-bold text-gray-900">Connected Facebook Pages</h2>
                        <p className="mt-1 text-sm text-gray-500">
                            Manage your connected Facebook pages and monitor comments
                        </p>
                    </div>
                    <Link href="/auth/facebook/redirect">
                        <Button>Connect New Page</Button>
                    </Link>
                </div>

                {/* Pages Table */}
                <Card>
                    <CardHeader>
                        <CardTitle>Your Pages</CardTitle>
                        <CardDescription>
                            {pages.length === 0
                                ? 'No pages connected yet. Click "Connect New Page" to get started.'
                                : `You have ${pages.length} connected page(s)`}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        {pages.length > 0 ? (
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Page Name</TableHead>
                                        <TableHead>Page ID</TableHead>
                                        <TableHead>Status</TableHead>
                                        <TableHead>Comments</TableHead>
                                        <TableHead>Last Synced</TableHead>
                                        <TableHead className="text-right">Actions</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {pages.map((page) => (
                                        <TableRow key={page.id}>
                                            <TableCell className="font-medium">{page.page_name}</TableCell>
                                            <TableCell className="text-gray-500">{page.page_id}</TableCell>
                                            <TableCell>
                                                <Badge variant={page.is_active ? 'default' : 'secondary'}>
                                                    {page.is_active ? 'Active' : 'Inactive'}
                                                </Badge>
                                            </TableCell>
                                            <TableCell>{page.comments_count || 0}</TableCell>
                                            <TableCell className="text-gray-500">
                                                {page.last_synced_at
                                                    ? new Date(page.last_synced_at).toLocaleDateString()
                                                    : 'Never'}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <Button variant="outline" size="sm">
                                                    View Comments
                                                </Button>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        ) : (
                            <div className="text-center py-12">
                                <svg
                                    className="mx-auto h-12 w-12 text-gray-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    />
                                </svg>
                                <h3 className="mt-2 text-sm font-medium text-gray-900">No pages connected</h3>
                                <p className="mt-1 text-sm text-gray-500">
                                    Get started by connecting your first Facebook page.
                                </p>
                                <div className="mt-6">
                                    <Link href="/auth/facebook/redirect">
                                        <Button>Connect Facebook Page</Button>
                                    </Link>
                                </div>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </Layout>
    );
}
