if ( ir_settings_data.length ) {
    ir_settings_data.forEach(setting => {
        wp.data.dispatch( 'core/edit-post').removeEditorPanel( setting );
    });
}
