require(['jquery', 'jquery/ui', 'uiRegistry'], function($, ui, reg){
    $(document).ready( function() {
        $(document).on("change","select[name='export_source_job']",function(){
            switch($(this).val()) {
                case '':
                    reg.get('marvelic_job_export_form.marvelic_job_export_form.source.file_path').hide();
                    reg.get('marvelic_job_export_form.marvelic_job_export_form.source.host').hide();
                    reg.get('marvelic_job_export_form.marvelic_job_export_form.source.port').hide();
                    reg.get('marvelic_job_export_form.marvelic_job_export_form.source.username').hide();
                    reg.get('marvelic_job_export_form.marvelic_job_export_form.source.password').hide();
                    break;
                case 'file':
                    reg.get('marvelic_job_export_form.marvelic_job_export_form.source.file_path').show();
                    reg.get('marvelic_job_export_form.marvelic_job_export_form.source.host').hide();
                    reg.get('marvelic_job_export_form.marvelic_job_export_form.source.host').value('none');
                    reg.get('marvelic_job_export_form.marvelic_job_export_form.source.port').hide();
                    reg.get('marvelic_job_export_form.marvelic_job_export_form.source.port').value('none');
                    reg.get('marvelic_job_export_form.marvelic_job_export_form.source.username').hide();
                    reg.get('marvelic_job_export_form.marvelic_job_export_form.source.username').value('none');
                    reg.get('marvelic_job_export_form.marvelic_job_export_form.source.password').hide();
                    reg.get('marvelic_job_export_form.marvelic_job_export_form.source.password').value('none');
                    break;
                default:
                    reg.get('marvelic_job_export_form.marvelic_job_export_form.source.file_path').show();
                    reg.get('marvelic_job_export_form.marvelic_job_export_form.source.host').show();
                    reg.get('marvelic_job_export_form.marvelic_job_export_form.source.host').value('');
                    reg.get('marvelic_job_export_form.marvelic_job_export_form.source.port').show();
                    reg.get('marvelic_job_export_form.marvelic_job_export_form.source.port').value('');
                    reg.get('marvelic_job_export_form.marvelic_job_export_form.source.username').show();
                    reg.get('marvelic_job_export_form.marvelic_job_export_form.source.username').value('');
                    reg.get('marvelic_job_export_form.marvelic_job_export_form.source.password').show();
                    reg.get('marvelic_job_export_form.marvelic_job_export_form.source.password').value('');
                    break;
            }

        })
    });
});