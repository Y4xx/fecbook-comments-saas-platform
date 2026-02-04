export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

export interface FacebookPage {
    id: number;
    user_id: number;
    page_id: string;
    page_name: string;
    is_active: boolean;
    last_synced_at?: string;
    comments_count?: number;
    created_at: string;
    updated_at: string;
}

export interface FacebookComment {
    id: number;
    facebook_page_id: number;
    facebook_comment_id: string;
    post_id: string;
    message: string;
    author_name: string;
    author_id: string;
    comment_created_time: string;
    sentiment_status: 'pending' | 'analyzing' | 'analyzed' | 'failed';
    created_at: string;
    updated_at: string;
    facebook_page?: FacebookPage;
    sentiment_result?: SentimentResult;
}

export interface SentimentResult {
    id: number;
    facebook_comment_id: number;
    sentiment: 'positive' | 'negative' | 'neutral';
    confidence: number;
    reason?: string;
    created_at: string;
    updated_at: string;
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: User;
    };
    flash?: {
        success?: string;
        error?: string;
    };
};
