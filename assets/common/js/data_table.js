jQuery(document).ready(function ($) {
    "use strict";  
});

function ajax_data_table(table_id, url, columns, column_defs = [], page_length = 30) {
    //extras
    var extras = $('.ajax_dt_table').data('extras');    
    if ( ! $.isEmptyObject(extras)) {
        var prepend = [], append = [];
        //checker
        if (extras.checker) {
            var checker_obj = [{"data": "checker", "searchable": false, "orderable": false}];
            prepend = [...prepend, ...checker_obj];
        }
        //actions
        if (extras.actions) {
            var actions_obj = [{"data": "actions", "searchable": false, "orderable": false}];
            prepend = [...prepend, ...actions_obj];
        }
        columns = [...prepend, ...columns];
        //date created
        if (extras.created) {
            var created_obj = [{"data": "created_on"}];
            append = [...append, ...created_obj];
        }
        //date updated
        if (extras.updated) {
            var updated_obj = [{"data": "updated_on"}];
            append = [...append, ...updated_obj];
        }
        columns = [...columns, ...append];
    }
    var table = $('#'+table_id).dataTable({ 
        initComplete: function() {
            var api = this.api();
            $('#'+table_id+'_filter input')
                .off('.DT')
                .on('input.DT', function() {
                    api.search(this.value).draw();
                 });
        },
        searching: true,
        language: {
           processing: "loading..."
        },
        paging: true,
        pageLength : page_length,
        // scrollX: true,
        stateSave: true,
        processing: true,
        serverSide: true,
        info: true,
        ajax: {
            url: base_url + url + '/' + trashed, 
            type: "POST",
            data: secure_req_data({}, false)
        },
        columns: columns,
        columnDefs: column_defs,
        order: [],
        buttons: ExportButtons,
        dom: "<'dt_buttons'B>frtip",
        rowCallback: function(row, data, iDisplayIndex) {
            var index = iDisplayIndex + 1;
            var info = this.fnPagingInfo();
            var page = info.iPage;
            var length = info.iLength;
            //$('td:eq(1)', row).html(index); //counter
        },
        //TODO: might fail. revisit when testing tables
        drawCallback: function(jres) {
            update_csrf_token(jres);
        }
    });
}


//print and export buttons for DataTables
var ExportButtons = [
    {
        extend: 'colvis', //column visibility
    },
    {
        extend: 'print',
        exportOptions: {
            columns: ':visible'
        }
    },
    {
        extend: 'excel',
        exportOptions: {
            text: 'Export',
            columns: ':visible'
        }
    },
    {
        extend: 'csv',
        exportOptions: {
            columns: ':visible'
        }
    },
    {
        extend: 'pdf',
        exportOptions: {
            columns: ':visible'
        }
    }
];


// Setup datatables
$.fn.dataTableExt.oApi.fnPagingInfo = function(oSettings) {
    return {
  "iStart": oSettings._iDisplayStart,
  "iEnd": oSettings.fnDisplayEnd(),
  "iLength": oSettings._iDisplayLength,
  "iTotal": oSettings.fnRecordsTotal(),
  "iFilteredTotal": oSettings.fnRecordsDisplay(),
  "iPage": Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength),
  "iTotalPages": Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength)
    };
};

function record_image(url) { 
    // var src = image_exists(url) ? url : base_url+'assets/common/img/icons/not_found.png';
    return '<img class="record_image clickable tm_image" src='+url+'>'; 
}

function dt_image_col(data = 'image_file') {
    return {
        data: data, 
        searchable: false,
        orderable: false,
        render: function (data) { 
            return record_image(data);
        }
    };
}

function dt_name_col(pfx = 'u_') {
    return {
        data: pfx+'first_name',
        render: function (data, type, row) { 
            var p_title = row[pfx+'title'], 
                p_first_name = row[pfx+'first_name'],
                p_last_name = row[pfx+'last_name'],
                p_other_name = row[pfx+'other_name'];
            var title = p_title != null ? p_title+' ' : '', 
                first_name = p_first_name != null ? p_first_name+' ' : '', 
                last_name = p_last_name != null ? p_last_name+' ' : '', 
                other_name = p_other_name != null ? p_other_name+' ' : '';
            return `${title}${first_name}${last_name}${other_name}`;
        }
    };
}

function dt_status_badge_col(data, text, succ_val = 1, fail_val = 0, succ_bg = 'success', fail_bg = 'danger') {
    return {
        data: data,
        render: function(data, type, row) {
            var bg = data == succ_val ? succ_bg : fail_bg;
            return `<span class="badge badge-pill badge-${bg} text-bold">${row[text]}</span>`;
        }
    };
}

function dt_custom_status_badge_col(data, text, bg = 'primary') {
    return {
        data: data,
        render: function(data, type, row) {
            return `<span class="badge badge-pill badge-${row[bg]} text-bold">${row[text]}</span>`;
        }
    };
}

function table_query_var(name) {
    var data = $('[name="'+name+'"]').val();
    data = data === "undefined" || data == '' ? 0 : data;
    return data;
}
