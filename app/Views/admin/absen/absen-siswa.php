<div class="row">
    <div class="col">
        <div class="pt-3 pl-3">
            <h4>Absen Siswa</h4>
            <p>Daftar siswa muncul disini</p>
        </div>
    </div>
    <div class="col-md-auto">
        <div class="pr-4">
            <h3 class="text-primary">
                <b><?= $kelas; ?></b>
            </h3>
        </div>
    </div>
</div>

<div id="dataSiswa" class="card-body table-responsive">
    <?php if (!empty($data)) : ?>
        <table class="table table-hover">
            <thead class="text-success">
                <th>No.</th>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Kehadiran</th>
                <th>Aksi</th>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php foreach ($data as $value) : ?>
                    <?php $kehadiran  = $value['kehadiran'] ?? 'Tanpa keterangan'; ?>
                    <tr>
                        <td><?= $no; ?></td>
                        <td><?= $value['nis']; ?></td>
                        <td><b><?= $value['nama_siswa']; ?></b></td>
                        <td>
                            <p class="btn btn-<?= kehadiran($kehadiran)['color']; ?>">
                                <?= $kehadiran; ?>
                            </p>
                        </td>
                        <td>
                            <a href="" class="btn btn-success">
                                <i class="material-icons">edit</i>
                                Edit
                            </a>
                        </td>
                    </tr>
                <?php $no++;
                endforeach ?>
            </tbody>
        </table>
    <?php
    else :
    ?>
        <div class="row">
            <div class="col">
                <h4 class="text-center text-danger">Data tidak ditemukan</h4>
            </div>
        </div>
    <?php
    endif; ?>
</div>

<?php

function kehadiran($kehadiran): array
{
    $color = '';
    switch ($kehadiran) {
        case 'Hadir':
            $color = 'success';
            break;
        case 'Sakit':
            $color = 'warning';
            break;
        case 'Izin':
            $color = 'warning';
            break;
        case 'Belum tersedia':
            $color = 'disabled';
            break;
        case 'Tanpa keterangan':
        default:
            $color = 'danger';
            break;
    }

    return ['color' => $color];
}
?>