function setAjaxData(object = null) {
    var data = {};
    data[BaseConfig.csrfTokenName] = $('meta[name="X-CSRF-TOKEN"]').attr('content');
    if (object != null) {
        Object.assign(data, object);
    }
    return data;
}

function setSerializedData(serializedData) {
    serializedData.push({name: BaseConfig.csrfTokenName, value: $('meta[name="X-CSRF-TOKEN"]').attr('content')});
    return serializedData;
}

//delete item
function deleteItem(url, id, message) {
    swal({
        text: message,
        icon: "warning",
        buttons: [BaseConfig.textCancel, BaseConfig.textOk],
        dangerMode: true,
    }).then(function (willDelete) {
        if (willDelete) {
            var data = {
                'id': id,
            };
            $.ajax({
                type: 'POST',
                url: BaseConfig.baseURL + url,
                data: setAjaxData(data),
                success: function (response) {
                    location.reload();
                }
            });
        }
    });
};