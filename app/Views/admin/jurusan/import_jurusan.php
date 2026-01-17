<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <?= view('admin/_messages'); ?>
                <div class="row">
                    <div class="col-12 col-xl-8">
                        <div class="card">
                            <div class="card-header card-header-tabs card-header-primary">
                                <div class="nav-tabs-navigation">
                                    <div class="row">
                                        <div class="col-md-4 col-lg-5">
                                            <h4 class="card-title"><b>Import Jurusan</b></h4>
                                            <p class="card-category">Import data jurusan dari CSV</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="dm-uploader-container">
                                        <div id="drag-and-drop-zone" class="dm-uploader p-2">
                                            <p class="dm-upload-icon">
                                                <i class="material-icons">cloud_upload</i>
                                            </p>
                                            <h3 class="text-muted">Tarik dan letakkan file di sini</h3>
                                            <div class="btn btn-primary mb-5">
                                                <span>Cari File</span>
                                                <input type="file" title='Click to add Files' />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div id="csv_upload_spinner" class="csv-upload-spinner">
                                                <strong class="text-csv-importing">Importing Jurusan...</strong>
                                                <strong class="text-csv-import-completed">Selesai!</strong>
                                                <div class="spinner-bounce">
                                                    <div class="bounce1"></div>
                                                    <div class="bounce2"></div>
                                                    <div class="bounce3"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="csv-uploaded-files-container">
                                                <ul id="csv_uploaded_files" class="list-group csv-uploaded-files"></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-4">
                        <div class="card">
                            <div class="card-header card-header-tabs card-header-primary">
                                <h4 class="card-title"><b>Bantuan</b></h4>
                                <p class="card-category">Download template CSV</p>
                            </div>
                            <div class="card-body">
                                <form action="<?= base_url('admin/jurusan/downloadCSVFilePost'); ?>" method="post">
                                    <?= csrf_field(); ?>
                                    <button class="btn btn-success btn-block" name="submit"
                                        value="csv_jurusan_template">Download Template CSV</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function () {
        $('#drag-and-drop-zone').dmUploader({
            url: '<?= base_url("admin/jurusan/generateCSVObjectPost"); ?>',
            multiple: false,
            extFilter: ["csv"],
            extraData: function (id) {
                return {
                    '<?= csrf_token() ?>': '<?= csrf_hash(); ?>'
                };
            },
            onDragEnter: function () {
                this.addClass('active');
            },
            onDragLeave: function () {
                this.removeClass('active');
            },
            onNewFile: function (id, file) {
                $("#csv_upload_spinner").show();
                $("#csv_upload_spinner .spinner-bounce").show();
                $("#csv_upload_spinner .text-csv-importing").show();
                $("#csv_upload_spinner .text-csv-import-completed").hide();
                $("#csv_uploaded_files").empty();
            },
            onUploadSuccess: function (id, response) {
                var numberOfItems = 0;
                var txtFileName = "";
                try {
                    var obj = JSON.parse(response);
                    if (obj.result == 1) {
                        numberOfItems = obj.numberOfItems;
                        txtFileName = obj.txtFileName;
                        if (numberOfItems > 0) {
                            addCSVItem(numberOfItems, txtFileName, 1);
                        } else {
                            $("#csv_upload_spinner").hide();
                        }
                    } else {
                        $("#csv_upload_spinner").hide();
                    }

                } catch (e) {
                    alert("Invalid CSV file! Make sure there are no double quotes in your content. Double quotes can brake the CSV structure.");
                }
            }
        });
    });

    function addCSVItem(numberOfItems, txtFileName, index) {
        if (index <= numberOfItems) {
            var data = {
                'txtFileName': txtFileName,
                'index': index
            };
            $.ajax({
                type: "POST",
                url: '<?= base_url("admin/jurusan/importCSVItemPost"); ?>',
                data: setAjaxData(data),
                success: function (response) {
                    var objSub = JSON.parse(response);
                    if (objSub.result == 1) {
                        if (objSub.status == 'duplicate') {
                            $("#csv_uploaded_files").prepend('<li class="list-group-item list-group-item-warning">&nbsp;' + objSub.index + '.&nbsp; - ' + objSub.jurusan.jurusan + ' (Sudah Ada)</li>');
                        } else {
                            $("#csv_uploaded_files").prepend('<li class="list-group-item list-group-item-success">&nbsp;' + objSub.index + '.&nbsp; - ' + objSub.jurusan.jurusan + '</li>');
                        }
                    } else {
                        $("#csv_uploaded_files").prepend('<li class="list-group-item list-group-item-danger">&nbsp;' + objSub.index + '.</li>');
                    }
                    if (objSub.index == numberOfItems) {
                        $("#csv_upload_spinner .text-csv-importing").hide();
                        $("#csv_upload_spinner .spinner-bounce").hide();
                        $("#csv_upload_spinner .text-csv-import-completed").css('display', 'block');
                    }
                    index = index + 1;
                    addCSVItem(numberOfItems, txtFileName, index);
                },
                error: function (xhr, status, thrown) {
                    swal({
                        text: 'Ada Kesalahan Pada CSV silahkan Cek Log',
                        icon: "warning"
                    }).then(function (willDelete) {
                        if (willDelete) {
                            location.reload();
                        }
                    });
                },
            });
        }
    }
</script>
<?= $this->endSection() ?>