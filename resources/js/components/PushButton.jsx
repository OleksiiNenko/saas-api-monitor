import React, { useState } from 'react';
import api, { errorMessage } from '../api.js';
import { Alert, Button, Card } from './Ui.jsx';

export default function PushButton({ page, meta, fields, onPushed }) {
    const [busy, setBusy] = useState(false);
    const [error, setError] = useState('');
    const [result, setResult] = useState(page.status === 'pushed' ? page : null);

    async function push() {
        setBusy(true);
        setError('');
        try {
            // Persist edits first, then push as draft.
            await api.patch(`/generated-pages/${page.id}`, { ...meta, fields });
            const { data } = await api.post(`/generated-pages/${page.id}/push`);
            setResult(data.data);
            onPushed?.(data.data);
        } catch (err) {
            setError(errorMessage(err));
        } finally {
            setBusy(false);
        }
    }

    return (
        <Card title="5. Отправить в WordPress">
            <p className="mb-4 text-sm text-slate-500">
                Страница будет создана как <strong>черновик</strong>. Существующий контент не меняется.
            </p>
            <Button onClick={push} disabled={busy}>
                {busy ? 'Отправляем…' : 'Создать черновик в WordPress'}
            </Button>

            <div className="mt-3 space-y-2">
                <Alert type="error">{error}</Alert>
                {result?.wp_edit_link && (
                    <Alert type="success">
                        Черновик создан (ID {result.wp_post_id}).{' '}
                        <a className="font-medium underline" href={result.wp_edit_link} target="_blank" rel="noreferrer">
                            Открыть в WordPress
                        </a>
                    </Alert>
                )}
            </div>
        </Card>
    );
}
