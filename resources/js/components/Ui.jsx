import React from 'react';

export function Button({ children, variant = 'primary', className = '', ...props }) {
    const styles = {
        primary: 'bg-indigo-600 text-white hover:bg-indigo-700 disabled:bg-indigo-300',
        secondary: 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50',
        danger: 'bg-red-600 text-white hover:bg-red-700',
    };
    return (
        <button
            className={`inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium transition disabled:cursor-not-allowed ${styles[variant]} ${className}`}
            {...props}
        >
            {children}
        </button>
    );
}

export function Field({ label, hint, children }) {
    return (
        <label className="block">
            <span className="mb-1 block text-sm font-medium text-slate-700">{label}</span>
            {children}
            {hint && <span className="mt-1 block text-xs text-slate-400">{hint}</span>}
        </label>
    );
}

export function Input(props) {
    return (
        <input
            className="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
            {...props}
        />
    );
}

export function Textarea(props) {
    return (
        <textarea
            className="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
            {...props}
        />
    );
}

export function Select({ children, ...props }) {
    return (
        <select
            className="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
            {...props}
        >
            {children}
        </select>
    );
}

export function Card({ title, children, actions }) {
    return (
        <section className="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            {(title || actions) && (
                <header className="mb-4 flex items-center justify-between">
                    {title && <h2 className="text-lg font-semibold text-slate-800">{title}</h2>}
                    {actions}
                </header>
            )}
            {children}
        </section>
    );
}

export function Alert({ type = 'info', children }) {
    if (!children) return null;
    const styles = {
        info: 'bg-sky-50 text-sky-800 border-sky-200',
        success: 'bg-emerald-50 text-emerald-800 border-emerald-200',
        error: 'bg-red-50 text-red-800 border-red-200',
    };
    return <div className={`rounded-md border px-3 py-2 text-sm ${styles[type]}`}>{children}</div>;
}
