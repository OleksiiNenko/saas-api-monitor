import React, { useState } from 'react';
import api, { errorMessage } from '../api.js';
import { Alert, Button, Card, Field, Input, Select, Textarea } from './Ui.jsx';

const PAGE_TYPES = ['landing', 'about', 'services', 'faq', 'article'];
const TONES = ['деловой', 'дружелюбный', 'продающий', 'экспертный'];

export default function PageBriefForm({ siteId, onGenerated }) {
    const [form, setForm] = useState({
        topic: '',
        page_type: 'landing',
        audience: '',
        tone: 'продающий',
        language: 'ru',
        keywords: '',
        cta: '',
        sections: '',
        extra_instructions: '',
    });
    const [busy, setBusy] = useState(false);
    const [error, setError] = useState('');

    const update = (k) => (e) => setForm({ ...form, [k]: e.target.value });

    async function submit(e) {
        e.preventDefault();
        setBusy(true);
        setError('');
        try {
            const { data } = await api.post('/generated-pages/generate', {
                wordpress_site_id: siteId,
                ...form,
            });
            onGenerated(data.data);
        } catch (err) {
            setError(errorMessage(err));
        } finally {
            setBusy(false);
        }
    }

    return (
        <Card title="2. Бриф для AI">
            <form onSubmit={submit} className="grid gap-3 sm:grid-cols-2">
                <div className="sm:col-span-2">
                    <Field label="Тема / цель страницы *">
                        <Input value={form.topic} onChange={update('topic')} placeholder="Например: Установка ГБО в Киеве" required />
                    </Field>
                </div>
                <Field label="Тип страницы">
                    <Select value={form.page_type} onChange={update('page_type')}>
                        {PAGE_TYPES.map((t) => (
                            <option key={t} value={t}>{t}</option>
                        ))}
                    </Select>
                </Field>
                <Field label="Язык">
                    <Select value={form.language} onChange={update('language')}>
                        <option value="ru">Русский</option>
                        <option value="uk">Украинский</option>
                        <option value="en">English</option>
                    </Select>
                </Field>
                <Field label="Аудитория">
                    <Input value={form.audience} onChange={update('audience')} placeholder="Автовладельцы" />
                </Field>
                <Field label="Тон">
                    <Select value={form.tone} onChange={update('tone')}>
                        {TONES.map((t) => (
                            <option key={t} value={t}>{t}</option>
                        ))}
                    </Select>
                </Field>
                <Field label="Ключевые слова (SEO)">
                    <Input value={form.keywords} onChange={update('keywords')} placeholder="гбо, установка, киев" />
                </Field>
                <Field label="Призыв к действию (CTA)">
                    <Input value={form.cta} onChange={update('cta')} placeholder="Заказать установку" />
                </Field>
                <div className="sm:col-span-2">
                    <Field label="Желаемые секции (необязательно)">
                        <Textarea rows={2} value={form.sections} onChange={update('sections')} />
                    </Field>
                </div>
                <div className="sm:col-span-2">
                    <Field label="Доп. инструкции">
                        <Textarea rows={2} value={form.extra_instructions} onChange={update('extra_instructions')} />
                    </Field>
                </div>
                <div className="sm:col-span-2">
                    <Button type="submit" disabled={busy || !form.topic}>
                        {busy ? 'Генерируем…' : 'Сгенерировать страницу'}
                    </Button>
                </div>
            </form>
            <div className="mt-3">
                <Alert type="error">{error}</Alert>
            </div>
        </Card>
    );
}
