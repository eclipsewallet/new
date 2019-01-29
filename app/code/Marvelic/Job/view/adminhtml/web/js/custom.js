require(['jquery', 'jquery/ui', 'uiRegistry'], function($, ui, reg){
    console.log(reg);

    let prefixForm = 'marvelic_job_export_form.marvelic_job_export_form';

    reg.get([
            prefixForm + '.source.data_export_source',
            prefixForm + '.date.date_config',
            prefixForm + '.source.file_path',
            prefixForm + '.source.host',
            prefixForm + '.source.port',
            prefixForm + '.source.username',
            prefixForm + '.source.password'
            ], function (dataExport, dataDate, filePath, host, port, username, password){

        if(dataDate.value() === '' || dataExport.value() === ''){
            let jsonExport  = JSON.parse(dataExport.value());
            let jsonDate    = JSON.parse(dataDate.value());

            reg.get(prefixForm + '.date.type_time').value(jsonDate.type_time);
            reg.get(prefixForm + '.date.period').value(jsonDate.period);

            reg.get(prefixForm + '.source').toggleOpened();
            reg.get(prefixForm + '.source.export_source').value(jsonExport.type);

            let intervalHideInputDone = setInterval(function(){
                console.log(filePath.visible());
                if (filePath.visible() === false
                    || host.visible() === false
                    || port.visible() === false
                    || username.visible() === false
                    || password.visible() === false
                ){
                    displayExportSource(reg.get(prefixForm + '.source.export_source').value());
                    clearInterval(intervalHideInputDone);
                }
            }, 200);
        }
    });

    $(document).ready( function () {
        $(document).on("change","select[name='export_source_job']", function (){
            displayExportSource($(this).val());
        })
    });

    function displayExportSource(valueInput){
        switch(valueInput) {
            case '':
                reg.get(prefixForm + '.source.file_path').hide();
                reg.get(prefixForm + '.source.host').hide();
                reg.get(prefixForm + '.source.port').hide();
                reg.get(prefixForm + '.source.username').hide();
                reg.get(prefixForm + '.source.password').hide();
                break;
            case 'file':
                reg.get(prefixForm + '.source.file_path').show();
                reg.get(prefixForm + '.source.host').hide();
                reg.get(prefixForm + '.source.host').value('none');
                reg.get(prefixForm + '.source.port').hide();
                reg.get(prefixForm + '.source.port').value('none');
                reg.get(prefixForm + '.source.username').hide();
                reg.get(prefixForm + '.source.username').value('none');
                reg.get(prefixForm + '.source.password').hide();
                reg.get(prefixForm + '.source.password').value('none');
                break;
            default:
                reg.get(prefixForm + '.source.file_path').show();
                reg.get(prefixForm + '.source.host').show();
                reg.get(prefixForm + '.source.host').value('');
                reg.get(prefixForm + '.source.port').show();
                reg.get(prefixForm + '.source.port').value('');
                reg.get(prefixForm + '.source.username').show();
                reg.get(prefixForm + '.source.username').value('');
                reg.get(prefixForm + '.source.password').show();
                reg.get(prefixForm + '.source.password').value('');
                break;
        }
    }

});
// notification_area.notification_area_data_source
// VM10451:2 marvelic_job_export_form.export_form_data_source
// VM10451:2 notification_area.notification_area_data_source_storage
// VM10451:2 notification_area.notification_area
// VM10451:2 notification_area.notification_area.modalContainer
// VM10451:2 notification_area.notification_area.modalContainer.modal
// VM10451:2 notification_area.notification_area.columns.created_at
// VM10451:2 notification_area.notification_area.columns.actions
// VM10451:2 notification_area.notification_area.columns.dismiss
// VM10451:2 marvelic_job_export_form.marvelic_job_export_form.general
// VM10451:2 marvelic_job_export_form.marvelic_job_export_form.date
// VM10451:2 marvelic_job_export_form.marvelic_job_export_form.source
// VM10451:2 notification_area.notification_area.columns
// VM10451:2 notification_area.notification_area.modalContainer.modal.insertBulk
// VM10451:2 notification_area.notification_area.columns_dnd
// VM10451:2 marvelic_job_export_form.marvelic_job_export_form
// VM10451:2 marvelic_job_export_form.marvelic_job_export_form.general.title
// VM10451:2 marvelic_job_export_form.marvelic_job_export_form.date.period
// VM10451:2 marvelic_job_export_form.marvelic_job_export_form.date.date_config
// VM10451:2 marvelic_job_export_form.marvelic_job_export_form.source.file_path
// VM10451:2 marvelic_job_export_form.marvelic_job_export_form.source.host
// VM10451:2 marvelic_job_export_form.marvelic_job_export_form.source.port
// VM10451:2 marvelic_job_export_form.marvelic_job_export_form.source.username
// VM10451:2 marvelic_job_export_form.marvelic_job_export_form.source.password
// VM10451:2 marvelic_job_export_form.marvelic_job_export_form.source.data_export_source
// VM10451:2 marvelic_job_export_form.marvelic_job_export_form.general.is_active
// VM10451:2 marvelic_job_export_form.marvelic_job_export_form.general.cron
// VM10451:2 marvelic_job_export_form.marvelic_job_export_form.general.entity
// VM10451:2 marvelic_job_export_form.marvelic_job_export_form.date.type_time
// VM10451:2 marvelic_job_export_form.marvelic_job_export_form.general.frequency
// VM10451:2 marvelic_job_export_form.marvelic_job_export_form.source.export_source