import React, { useEffect, useState } from 'react';
import api from './api.js';
import ConnectSite from './components/ConnectSite.jsx';
import PageBriefForm from './components/PageBriefForm.jsx';
import FieldsEditor from './components/FieldsEditor.jsx';
import MetaPanel from './components/MetaPanel.jsx';
import PushButton from './components/PushButton.jsx';

export default function App() {
    const [sites, setSites] = useState([]);
    const [selectedId, setSelectedId] = useState(null);
    const [page, setPage] = useState(null); // the generated page record
    const [fields, setFields] = useState({});
    const [meta, setMeta] = useState({});

    useEffect(() => {
        api.get('/wordpress-sites').then(({ data }) => {
            setSites(data.data);
            if (data.data.length && !selectedId) setSelectedId(data.data[0].id);
        });
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    function onConnected(site) {
        setSites((prev) => [site, ...prev]);
        setSelectedId(site.id);
    }

    async function onDelete(id) {
        await api.delete(`/wordpress-sites/${id}`);
        setSites((prev) => prev.filter((s) => s.id !== id));
        if (selectedId === id) {
            setSelectedId(null);
            setPage(null);
        }
    }

    function onGenerated(generated) {
        setPage(generated);
        setFields(generated.fields || {});
        setMeta({
            title: generated.title || '',
            slug: generated.slug || '',
            language: generated.language || 'ru',
            parent_id: generated.parent_id || '',
            author_id: generated.author_id || '',
            category_ids: generated.category_ids || [],
            tags: generated.tags || [],
        });
    }

    return (
        <div className="mx-auto max-w-3xl space-y-6 px-4 py-8">
            <header>
                <h1 className="text-2xl font-bold text-slate-900">AI Page Builder для WordPress</h1>
                <p className="text-sm text-slate-500">
                    Подключите сайт, сгенерируйте страницу с помощью AI и отправьте её черновиком.
                </p>
            </header>

            <ConnectSite
                sites={sites}
                selectedId={selectedId}
                onConnected={onConnected}
                onSelect={setSelectedId}
                onDelete={onDelete}
            />

            {selectedId && <PageBriefForm siteId={selectedId} onGenerated={onGenerated} />}

            {page && (
                <>
                    <FieldsEditor fields={fields} onChange={setFields} />
                    <MetaPanel siteId={selectedId} meta={meta} onChange={setMeta} />
                    <PushButton page={page} meta={meta} fields={fields} />
                </>
            )}
        </div>
    );
}
