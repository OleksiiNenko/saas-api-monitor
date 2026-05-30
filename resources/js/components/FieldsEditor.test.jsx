import React from 'react';
import { describe, expect, it, vi } from 'vitest';
import { render, screen, fireEvent } from '@testing-library/react';
import FieldsEditor from './FieldsEditor.jsx';

describe('FieldsEditor', () => {
    it('renders nothing when there are no fields', () => {
        const { container } = render(<FieldsEditor fields={{}} onChange={() => {}} />);
        expect(container).toBeEmptyDOMElement();
    });

    it('groups fields and edits a value', () => {
        const onChange = vi.fn();
        render(
            <FieldsEditor
                fields={{ seo_title_injector: 'Title', block2_title: 'Order' }}
                onChange={onChange}
            />,
        );

        expect(screen.getByText('SEO')).toBeInTheDocument();
        expect(screen.getByText('Блок #2')).toBeInTheDocument();

        const input = screen.getByDisplayValue('Title');
        fireEvent.change(input, { target: { value: 'New title' } });

        expect(onChange).toHaveBeenCalledWith({
            seo_title_injector: 'New title',
            block2_title: 'Order',
        });
    });
});
