TableExportPdf = function(dt,url = '/site/download',config) {
    $.ajax({
        method: "POST",
        url: url,
        data: {config: config.exportOptions,export_content: cleanTable(dt.table().node().id)},
        //contentType: false,
        //processData: false,
        xhrFields: {
            responseType: "blob"
        },
        success: function (response, status, xhr) {

            var filename = "";                   
            var disposition = xhr.getResponseHeader("Content-Disposition");

            if (disposition) {
                var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                var matches = filenameRegex.exec(disposition);
                if (matches !== null && matches[1]) filename = matches[1].replace(/['"]/g, '');
            } 
            var linkelem = document.createElement('a');
            try {
                var blob = new Blob([response], { type: 'application/octet-stream' });                        

                if (typeof window.navigator.msSaveBlob !== 'undefined') {
                    window.navigator.msSaveBlob(blob, filename);
                } else {
                    var URL = window.URL || window.webkitURL;
                    var downloadUrl = URL.createObjectURL(blob);

                    if (filename) { 
                        var a = document.createElement("a");

                        if (typeof a.download === 'undefined') {
                            window.location = downloadUrl;
                        } else {
                            a.href = downloadUrl;
                            a.download = filename;
                            document.body.appendChild(a);
                            a.target = "_blank";
                            a.click();
                        }
                    } else {
                        window.location = downloadUrl;
                    }
                }
            } catch (ex) {
                console.log(ex);
            } 
        }
    });
};

cleanTable = function(selector) {
    var $table = $('#'+selector), ColCount = 0, crrColCount = 0;

    $table.find("tr").eq(0).find("th,td").each(function ()
    {
        crrColCount += $(this).attr("colspan") ? parseInt($(this).attr("colspan")) : 1;
    });
    $table = $table.clone();
    $table.find('th').find('a').each(function (i,v) {
        $(this).contents().unwrap();
    });
    $table.find('td').find('a,span').each(function (i,v) {
        if($( this ).hasClass( "skip-export" )){
            $(this).remove();
        }else{
            $(this).contents().unwrap();
        }
    });
    $table.find('input').remove();
    $table.find('.select2').remove();
    $table.find('.skip-export').remove();
    $table.find("tr").eq(0).find("th,td").each(function (){
        ColCount += $(this).attr("colspan") ? parseInt($(this).attr("colspan")) : 1;
    });

    var $tfoot = $table.find('tfoot tr th');
    var Counttfoot = 0;
    $tfoot.each(function (i,n) {
        if($(this).attr("colspan") && $(this).attr("colspan") != '1'){
            Counttfoot += parseInt($(this).attr("colspan"));
            $(this).attr("colspan",(ColCount-$tfoot.length)+1);
        }else{
            Counttfoot += 1;
        }
    });

    var $tbody = $table.find('tbody tr td');
    $tbody.each(function (i,n) {
        if($(this).attr("colspan") && $(this).attr("colspan") != '1'){
            $(this).attr("colspan",ColCount);
        }
    });

    var $rowGroup = $table.find('td.dt-group');
    $rowGroup.each(function (i,n) {
        if($(this).css('display') == 'none'){
            $(this).remove();
        }
    });
    return $table.prop('outerHTML');
}