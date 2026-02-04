import { Head } from '@inertiajs/react';
import Layout from '@/components/Layout';
import { PageProps, FacebookComment } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';

interface CommentsPageProps extends PageProps {
    comments: {
        data: FacebookComment[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    filters?: {
        sentiment?: string;
        page_id?: number;
        status?: string;
    };
}

function getSentimentVariant(sentiment: string): 'positive' | 'negative' | 'neutral' {
    return sentiment as 'positive' | 'negative' | 'neutral';
}

export default function Index({ comments, filters }: CommentsPageProps) {
    return (
        <Layout>
            <Head title="Comments Analyzer" />

            <div className="space-y-6">
                {/* Header */}
                <div>
                    <h2 className="text-3xl font-bold text-gray-900">Comments Analyzer</h2>
                    <p className="mt-1 text-sm text-gray-500">
                        View and analyze sentiment of Facebook comments
                    </p>
                </div>

                {/* Filters */}
                <Card>
                    <CardHeader>
                        <CardTitle>Filters</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="flex gap-4">
                            <select className="rounded-md border border-gray-300 px-3 py-2 text-sm">
                                <option value="">All Sentiments</option>
                                <option value="positive">Positive</option>
                                <option value="negative">Negative</option>
                                <option value="neutral">Neutral</option>
                            </select>
                            <select className="rounded-md border border-gray-300 px-3 py-2 text-sm">
                                <option value="">All Statuses</option>
                                <option value="analyzed">Analyzed</option>
                                <option value="pending">Pending</option>
                                <option value="analyzing">Analyzing</option>
                            </select>
                        </div>
                    </CardContent>
                </Card>

                {/* Comments Table */}
                <Card>
                    <CardHeader>
                        <CardTitle>Comments</CardTitle>
                        <CardDescription>
                            {comments.total} total comments
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        {comments.data.length > 0 ? (
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Comment</TableHead>
                                        <TableHead>Author</TableHead>
                                        <TableHead>Page</TableHead>
                                        <TableHead>Sentiment</TableHead>
                                        <TableHead>Confidence</TableHead>
                                        <TableHead>Date</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {comments.data.map((comment) => (
                                        <TableRow key={comment.id}>
                                            <TableCell className="max-w-md">
                                                <p className="line-clamp-2 text-sm">{comment.message}</p>
                                            </TableCell>
                                            <TableCell className="text-sm">{comment.author_name}</TableCell>
                                            <TableCell className="text-sm">
                                                {comment.facebook_page?.page_name}
                                            </TableCell>
                                            <TableCell>
                                                {comment.sentiment_result ? (
                                                    <Badge variant={getSentimentVariant(comment.sentiment_result.sentiment)}>
                                                        {comment.sentiment_result.sentiment}
                                                    </Badge>
                                                ) : (
                                                    <Badge variant="secondary">{comment.sentiment_status}</Badge>
                                                )}
                                            </TableCell>
                                            <TableCell className="text-sm">
                                                {comment.sentiment_result
                                                    ? `${Math.round(comment.sentiment_result.confidence * 100)}%`
                                                    : '-'}
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
                                <p className="text-gray-500">No comments found</p>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </Layout>
    );
}
