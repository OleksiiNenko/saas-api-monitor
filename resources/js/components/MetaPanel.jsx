import React, { useEffect, useState } from 'react';
import api, { errorMessage } from '../api.js';
import { Alert, Card, Field, Input, Select } from './Ui.jsx';

export default function MetaPanel({ siteId, meta: value, onChange }) {
    const [meta, setMeta] = useState({ pages: [], categories: [], authors: [], languages: [] });
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        let active = true;
        setLoading(true);
        api.get(`/wordpress-sites/${siteId}/meta`)
            .then(({ data }) => {
                if (active) setMeta({ pages: [], categories: [], authors: [], languages: [], ...data });
            })
            .catch((err) => active && setError(errorMessage(err)))
            .finally(() => active && setLoading(false));
        return () => {
            active = false;
        };
    }, [siteId]);

    const set = (k) => (e) => onChange({ ...value, [k]: e.target.value });

    function toggleCategory(id) {
        const current = value.category_ids || [];
        const next = current.includes(id) ? current.filter((c) => c !== id) : [...current, id];
        onChange({ ...value, category_ids: next });
    }

    return (
        <Card title="4. Параметры публикации">
            {error && <div className="mb-3"><Alert type="error">{error}</Alert></div>}
            {loading ? (
                <p className="text-sm text-slate-400">Загружаем данные сайта…</p>
            ) : (
                <div className="grid gap-3 sm:grid-cols-2">
                    <Field label="Заголовок страницы">
                        <Input value={value.title ?? ''} onChange={set('title')} />
                    </Field>
                    <Field label="Ярлык (slug)">
                        <Input value={value.slug ?? ''} onChange={set('slug')} placeholder="ustanovka-gbo" />
                    </Field>
                    <Field label="Язык (WPML)">
                        <Select value={value.language ?? 'ru'} onChange={set('language')}>
                            {(meta.languages.length ? meta.languages : [{ code: 'ru', name: 'Русский' }, { code: 'uk', name: 'Украинский' }]).map((l) => (
                                <option key={l.code} value={l.code}>{l.name}</option>
                            ))}
                        </Select>
                    </Field>
                    <Field label="Родительская страница">
                        <Select value={value.parent_id ?? ''} onChange={set('parent_id')}>
                            <option value="">(нет родительской)</option>
                            {meta.pages.map((p) => (
                                <option key={p.id} value={p.id}>{p.title}</option>
                            ))}
                        </Select>
                    </Field>
                    <Field label="Автор">
                        <Select value={value.author_id ?? ''} onChange={set('author_id')}>
                            <option value="">(по умолчанию)</option>
                            {meta.authors.map((a) => (
                                <option key={a.id} value={a.id}>{a.name}</option>
                            ))}
                        </Select>
                    </Field>
                    <Field label="Статус">
                        <Input value="Черновик (draft)" disabled />
                    </Field>
                    <div className="sm:col-span-2">
                        <span className="mb-1 block text-sm font-medium text-slate-700">Рубрики</span>
                        <div className="flex flex-wrap gap-2">
                            {meta.categories.length === 0 && <span className="text-xs text-slate-400">Нет рубрик</span>}
                            {meta.categories.map((c) => {
                                const on = (value.category_ids || []).includes(c.id);
                                return (
                                    <button
                                        key={c.id}
                                        type="button"
                                        onClick={() => toggleCategory(c.id)}
                                        className={`rounded-full border px-3 py-1 text-xs ${
                                            on ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-slate-300 text-slate-600'
                                        }`}
                                    >
                                        {c.name}
                                    </button>
                                );
                            })}
                        </div>
                    </div>
                    <div className="sm:col-span-2">
                        <Field label="Метки (через запятую)">
                            <Input
                                value={(value.tags || []).join(', ')}
                                onChange={(e) => onChange({ ...value, tags: e.target.value.split(',').map((t) => t.trim()).filter(Boolean) })}
                            />
                        </Field>
                    </div>
                </div>
            )}
        </Card>
    );
}
