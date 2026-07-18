(function(blocks, editor, components, element) {
    var el = element.createElement;
    var SelectControl = components.SelectControl;

    blocks.registerBlockType('sanirtech-lead-chat-forms/form-select', {
        title: 'SanirTech WhatsApp Form',
        icon: 'format-chat',
        category: 'widgets',
        attributes: {
            formId: {
                type: 'string',
                default: ''
            }
        },
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            var formOptions = [{ label: '-- Select Form --', value: '' }];

            if (window.stlcf_block_forms_list && Array.isArray(window.stlcf_block_forms_list)) {
                window.stlcf_block_forms_list.forEach(function(f) {
                    formOptions.push({ label: f.title + ' (ID: ' + f.id + ')', value: String(f.id) });
                });
            }

            function onChangeForm(newId) {
                setAttributes({ formId: newId });
            }

            return el('div', { className: 'stlcf-gutenberg-block-editor-wrapper', style: { padding: '15px', background: '#f8fafc', border: '2px dashed #10b981', borderRadius: '6px', fontFamily: 'sans-serif' } },
                el('h4', { style: { margin: '0 0 10px 0', color: '#10b981', fontSize: '14px', fontWeight: 'bold' } }, 'SanirTech WhatsApp Lead Chat Form'),
                el(SelectControl, {
                    label: 'Select Form:',
                    value: attributes.formId,
                    options: formOptions,
                    onChange: onChangeForm
                })
            );
        },
        save: function(props) {
            return null; // Render dynamically on backend php template callback
        }
    });
})(
    window.wp.blocks,
    window.wp.blockEditor || window.wp.editor,
    window.wp.components,
    window.wp.element
);
