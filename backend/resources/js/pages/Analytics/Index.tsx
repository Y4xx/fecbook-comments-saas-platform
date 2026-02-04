import { Head } from '@inertiajs/react';
import Layout from '@/components/Layout';
import { PageProps, FacebookComment } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';

interface AnalyticsStats {
    totalComments: number;
    analyzedComments: number;
    pendingComments: number;
    sentimentDistribution: {
        positive?: number;
        negative?: number;
        neutral?: number;
    };
    sentimentPercentages: {
        positive?: number;
        negative?: number;
        neutral?: number;
    };
}

interface AnalyticsPageProps extends PageProps {
    stats: AnalyticsStats;
    negativeComments: FacebookComment[];
}

export default function Index({ stats, negativeComments }: AnalyticsPageProps) {
    return (
        <Layout>
            <Head title="Analytics Dashboard" />

            <div className="space-y-6">
                {/* Header */}
                <div>
                    <h2 className="text-3xl font-bold text-gray-900">Analytics Dashboard</h2>
                    <p className="mt-1 text-sm text-gray-500">
                        Monitor sentiment trends and key metrics
                    </p>
                </div>

                {/* Stats Cards */}
                <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader className="pb-2">
                            <CardDescription>Total Comments</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="text-3xl font-bold">{stats.totalComments}</div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="pb-2">
                            <CardDescription>Analyzed</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="text-3xl font-bold text-green-600">{stats.analyzedComments}</div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="pb-2">
                            <CardDescription>Pending</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="text-3xl font-bold text-yellow-600">{stats.pendingComments}</div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="pb-2">
                            <CardDescription>Negative Comments</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="text-3xl font-bold text-red-600">
                                {stats.sentimentDistribution.negative || 0}
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Sentiment Distribution */}
                <Card>
                    <CardHeader>
                        <CardTitle>Sentiment Distribution</CardTitle>
                        <CardDescription>Breakdown of analyzed comments by sentiment</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="space-y-4">
                            <div className="flex items-center justify-between">
                                <div className="flex items-center gap-2">
                                    <div className="h-4 w-4 rounded-full bg-green-500"></div>
                                    <span className="text-sm font-medium">Positive</span>
                                </div>
                                <div className="flex items-center gap-4">
                                    <span className="text-sm text-gray-500">
                                        {stats.sentimentDistribution.positive || 0} comments
                                    </span>
                                    <span className="text-sm font-bold">
                                        {stats.sentimentPercentages.positive || 0}%
                                    </span>
                                </div>
                            </div>

                            <div className="flex items-center justify-between">
                                <div className="flex items-center gap-2">
                                    <div className="h-4 w-4 rounded-full bg-red-500"></div>
                                    <span className="text-sm font-medium">Negative</span>
                                </div>
                                <div className="flex items-center gap-4">
                                    <span className="text-sm text-gray-500">
                                        {stats.sentimentDistribution.negative || 0} comments
                                    </span>
                                    <span className="text-sm font-bold">
                                        {stats.sentimentPercentages.negative || 0}%
                                    </span>
                                </div>
                            </div>

                            <div className="flex items-center justify-between">
                                <div className="flex items-center gap-2">
                                    <div className="h-4 w-4 rounded-full bg-gray-400"></div>
                                    <span className="text-sm font-medium">Neutral</span>
                                </div>
                                <div className="flex items-center gap-4">
                                    <span className="text-sm text-gray-500">
                                        {stats.sentimentDistribution.neutral || 0} comments
                                    </span>
                                    <span className="text-sm font-bold">
                                        {stats.sentimentPercentages.neutral || 0}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Latest Negative Comments */}
                <Card>
                    <CardHeader>
                        <CardTitle>Latest Negative Comments</CardTitle>
                        <CardDescription>Comments requiring immediate attention</CardDescription>
                    </CardHeader>
                    <CardContent>
                        {negativeComments.length > 0 ? (
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Comment</TableHead>
                                        <TableHead>Author</TableHead>
                                        <TableHead>Page</TableHead>
                                        <TableHead>Confidence</TableHead>
                                        <TableHead>Date</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {negativeComments.map((comment) => (
                                        <TableRow key={comment.id}>
                                            <TableCell className="max-w-md">
                                                <p className="line-clamp-2 text-sm">{comment.message}</p>
                                            </TableCell>
                                            <TableCell className="text-sm">{comment.author_name}</TableCell>
                                            <TableCell className="text-sm">
                                                {comment.facebook_page?.page_name}
                                            </TableCell>
                                            <TableCell>
                                                <Badge variant="negative">
                                                    {comment.sentiment_result
                                                        ? `${Math.round(comment.sentiment_result.confidence * 100)}%`
                                                        : '-'}
                                                </Badge>
                                            </TableCell>
                                            <TableCell className="text-sm text-gray-500">
                                                {new Date(comment.comment_created_time).toLocaleDateString()}
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        ) : (
                            <div className="text-center py-12">
                                <p className="text-gray-500">No negative comments found</p>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </Layout>
    );
}
