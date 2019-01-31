require(['jquery', 'jquery/ui', 'uiRegistry'], function($, ui, reg){
    console.log(reg);

    let prefixForm = 'marvelic_job_export_form.marvelic_job_export_form';

    reg.get([
            prefixForm + '.source.data_export_source',
            prefixForm + '.date.date_config',
            prefixForm + '.date',
            prefixForm + '.date.type_time',
            prefixForm + '.date.period',
            prefixForm + '.source',
            prefixForm + '.source.export_source',
            prefixForm + '.source.file_path',
            prefixForm + '.source.host',
            prefixForm + '.source.port',
            prefixForm + '.source.username',
            prefixForm + '.source.password'
            ], function (dataExport,
                         dataDate,
                         date,
                         dateTypeTime,
                         datePeriod,
                         source,
                         typeExport,
                         filePath,
                         host,
                         port,
                         username,
                         password){

        if(dataDate.value() !== '' || dataExport.value() !== ''){
            let jsonExport  = JSON.parse(dataExport.value());
            let jsonDate    = JSON.parse(dataDate.value());

            console.log(jsonExport);

            date.toggleOpened();
            dateTypeTime.value(jsonDate.type_time);
            datePeriod.value(jsonDate.period);

            source.toggleOpened();

            let intervalHideInputDone = setInterval(function(){
                console.log(filePath.visible());
                if (filePath.visible() === false
                    && host.visible() === false
                    && port.visible() === false
                    && username.visible() === false
                    && password.visible() === false
                ){
                    typeExport.value(jsonExport.type);
                    displayExportSource(typeExport.value());
                    filePath.value(jsonExport.file_path);
                    host.value(jsonExport.host);
                    port.value(jsonExport.port);
                    username.value(jsonExport.username);
                    password.value(jsonExport.password);
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