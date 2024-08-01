function setAjaxData(object = null) {
  var data = {};
  data[BaseConfig.csrfTokenName] = $('meta[name="X-CSRF-TOKEN"]').attr('content');
  if (object != null) {
    Object.assign(data, object);
  }
  return data;
}

function setSerializedData(serializedData) {
  serializedData.push({ name: BaseConfig.csrfTokenName, value: $('meta[name="X-CSRF-TOKEN"]').attr('content') });
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
        },
        error: function (xhr, status, thrown) {
          console.log(xhr);
          console.log(status);
          console.log(thrown);
        },
      });
    }
  });
};

function fetchKelasJurusanData(type, target) {
  const url = type === 'kelas' ? BaseConfig.baseURL + 'admin/kelas/list-data' : BaseConfig.baseURL + 'admin/jurusan/list-data';
  const textProcessing = type === 'kelas' ? 'Daftar kelas muncul disini' : 'Daftar Jurusan muncul disini';

  $(target).html('<div id="loadingSpinner" class="spinner"></div><p class="text-center mt-3">' + textProcessing + '</p>');

  $.ajax({
    url: url,
    type: 'post',
    data: setAjaxData({}),
    success: function (response) {
      const obj = JSON.parse(response);
      if (obj.result === 1) {
        $(target).html(obj.htmlContent);
      } else {
        $(target).html('<p class="text-center mt-3">Data tidak ditemukan</p>');
      }
    },
    error: function (xhr, status, thrown) {
      $(target).html('<p class="text-center mt-3">' + thrown + '</p>');
    },
    complete: function () {
      $('#loadingSpinner').hide();
    }
  });
}