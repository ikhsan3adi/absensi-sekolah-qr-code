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
                                            <h4 class="card-title"><b>Bulk Post Upload Siswa</b></h4>
                                            <p class="card-category">Angkatan <?= $generalSettings->school_year; ?></p>
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
                                            <h3 class="text-muted">Drag &amp; drop files here</h3>
                                            <div class="btn btn-primary mb-5">
                                                <span>Open the file Browser</span>
                                                <input type="file" title='Click to add Files' />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div id="csv_upload_spinner" class="csv-upload-spinner">
                                                <strong class="text-csv-importing">Importing Siswa...</strong>
                                                <strong class="text-csv-import-completed">completed!</strong>
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
                                <h4 class="card-title"><b>Help Documents</b></h4>
                                <p class="card-category">documents to generate your CSV file</p>
                            </div>
                            <div class="card-body">
                                <form action="<?= base_url('admin/siswa/downloadCSVFilePost'); ?>" method="post">
                                    <?= csrf_field(); ?>
                                    <button type="button" class="btn btn-success btn-block" data-toggle="modal" data-target="#modalKelas">List Kelas</button>
                                    <button class="btn btn-success btn-block" name="submit" value="csv_siswa_template">Download CSV Template</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalKelas" tabindex="-1" aria-labelledby="modalKelasTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalKelasTitle">List Kelas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <table class="table table-hover">
                        <thead class="text-primary">
                            <th><b>ID</b></th>
                            <th><b>Kelas / Tingkat</b></th>
                            <th><b>Jurusan</b></th>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($kelas as $value) : ?>
                                <tr>
                                    <td><?= $value['id_kelas']; ?></td>
                                    <td><b><?= $value['kelas']; ?></b></td>
                                    <td><?= $value['jurusan']; ?></td>
                                </tr>
                            <?php
                            endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('#drag-and-drop-zone').dmUploader({
            url: '<?= base_url("admin/siswa/generateCSVObjectPost"); ?>',
            multiple: false,
            extFilter: ["csv"],
            extraData: function(id) {
                return {
                    '<?= csrf_token() ?>': '<?= csrf_hash(); ?>'
                };
            },
            onDragEnter: function() {
                this.addClass('active');
            },
            onDragLeave: function() {
                this.removeClass('active');
            },
            onNewFile: function(id, file) {
                $("#csv_upload_spinner").show();
                $("#csv_upload_spinner .spinner-bounce").show();
                $("#csv_upload_spinner .text-csv-importing").show();
                $("#csv_upload_spinner .text-csv-import-completed").hide();
                $("#csv_uploaded_files").empty();
            },
            onUploadSuccess: function(id, response) {
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
                url: '<?= base_url("admin/siswa/importCSVItemPost"); ?>',
                data: setAjaxData(data),
                success: function(response) {
                    var objSub = JSON.parse(response);
                    if (objSub.result == 1) {
                        $("#csv_uploaded_files").prepend('<li class="list-group-item list-group-item-success">&nbsp;' + objSub.index + '.&nbsp;' + objSub.siswa.nis + '.&nbsp; - ' + objSub.siswa.nama_siswa +'</li>');
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
                error: function(xhr, status, thrown) {
                    swal({
                        text: 'Ada Kesalahan Pada CSV silahkan Cek Log',
                        icon: "warning"
                    }).then(function(willDelete) {
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