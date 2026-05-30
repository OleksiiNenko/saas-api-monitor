import React, { useState } from 'react';
import api, { errorMessage } from '../api.js';
import { Alert, Button, Card, Field, Input } from './Ui.jsx';

export default function ConnectSite({ sites, onConnected, onSelect, selectedId, onDelete }) {
    const [form, setForm] = useState({ name: '', base_url: '', username: '', app_password: '' });
    const [busy, setBusy] = useState(false);
    const [error, setError] = useState('');

    const update = (k) => (e) => setForm({ ...form, [k]: e.target.value });

    async function submit(e) {
        e.preventDefault();
        setBusy(true);
        setError('');
        try {
            const { data } = await api.post('/wordpress-sites', form);
            setForm({ name: '', base_url: '', username: '', app_password: '' });
            onConnected(data.data);
        } catch (err) {
            setError(errorMessage(err));
        } finally {
            setBusy(false);
        }
    }

    return (
        <Card title="1. Подключить сайт WordPress">
            {sites.length > 0 && (
                <ul className="mb-5 divide-y divide-slate-100 rounded-md border border-slate-200">
                    {sites.map((s) => (
                        <li
                            key={s.id}
                            className={`flex items-center justify-between px-3 py-2 ${
                                s.id === selectedId ? 'bg-indigo-50' : ''
                            }`}
                        >
                            <button className="text-left" onClick={() => onSelect(s.id)}>
                                <div className="text-sm font-medium text-slate-800">{s.name}</div>
                                <div className="text-xs text-slate-400">{s.base_url}</div>
                            </button>
                            <Button variant="danger" onClick={() => onDelete(s.id)} className="px-2 py-1 text-xs">
                                Удалить
                            </Button>
                        </li>
                    ))}
                </ul>
            )}

            <form onSubmit={submit} className="grid gap-3 sm:grid-cols-2">
                <Field label="Название">
                    <Input value={form.name} onChange={update('name')} placeholder="Мой сайт" required />
                </Field>
                <Field label="URL сайта" hint="https://example.com">
                    <Input value={form.base_url} onChange={update('base_url')} placeholder="https://example.com" required />
                </Field>
                <Field label="Логин WordPress">
                    <Input value={form.username} onChange={update('username')} required />
                </Field>
                <Field label="Application Password" hint="Users → Profile → Application Passwords">
                    <Input value={form.app_password} onChange={update('app_password')} required />
                </Field>
                <div className="sm:col-span-2">
                    <Button type="submit" disabled={busy}>
                        {busy ? 'Проверяем…' : 'Проверить и сохранить'}
                    </Button>
                </div>
            </form>

            <div className="mt-3">
                <Alert type="error">{error}</Alert>
            </div>
        </Card>
    );
}
