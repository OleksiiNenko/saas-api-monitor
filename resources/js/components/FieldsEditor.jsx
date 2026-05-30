import React, { useMemo } from 'react';
import { Card, Field, Input, Textarea } from './Ui.jsx';

// Logical names whose value is rich text — render a larger textarea.
const WYSIWYG = new Set([
    'block2_desc', 'block3_desc', 'block6_desc', 'block8_desc', 'block9_desc',
]);

// Group logical field names by their block prefix for a tidy layout.
function groupOf(name) {
    if (name.startsWith('seo_')) return 'SEO';
    const m = name.match(/^block(\d+)/);
    return m ? `Блок #${m[1]}` : 'Прочее';
}

export default function FieldsEditor({ fields, onChange }) {
    const groups = useMemo(() => {
        const out = {};
        Object.keys(fields || {}).forEach((name) => {
            const g = groupOf(name);
            (out[g] ||= []).push(name);
        });
        return out;
    }, [fields]);

    function set(name, value) {
        onChange({ ...fields, [name]: value });
    }

    if (!fields || Object.keys(fields).length === 0) {
        return null;
    }

    return (
        <Card title="3. Превью и правка полей">
            <p className="mb-4 text-sm text-slate-500">
                Эти значения будут записаны в ACF-поля страницы. Изображения и галереи не трогаются.
            </p>
            <div className="space-y-6">
                {Object.entries(groups).map(([group, names]) => (
                    <div key={group}>
                        <h3 className="mb-2 text-sm font-semibold uppercase tracking-wide text-slate-400">{group}</h3>
                        <div className="grid gap-3">
                            {names.map((name) => (
                                <Field key={name} label={name}>
                                    {WYSIWYG.has(name) ? (
                                        <Textarea rows={4} value={fields[name] ?? ''} onChange={(e) => set(name, e.target.value)} />
                                    ) : (
                                        <Input value={fields[name] ?? ''} onChange={(e) => set(name, e.target.value)} />
                                    )}
                                </Field>
                            ))}
                        </div>
                    </div>
                ))}
            </div>
        </Card>
    );
}
