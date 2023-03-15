<div class="card-body">
    <div class="row">
        <div class="col-auto me-auto">
            <div class="pt-3 pl-3">
                <h4><b>Absen Siswa</b></h4>
                <p>Daftar siswa muncul disini</p>
            </div>
        </div>
        <div class="col">
            <a href="#" class="btn btn-primary pl-3 mr-3 mt-3" onclick="kelas = onDateChange()" data-toggle="tab">
                <i class="material-icons mr-2">refresh</i> Refresh
            </a>
        </div>
        <div class="col-auto">
            <div class="px-4">
                <h3 class="text-end">
                    <b class="text-primary"><?= $kelas; ?></b>
                </h3>
            </div>
        </div>
    </div>

    <div id="dataSiswa" class="card-body table-responsive pb-5">
        <?php if (!empty($data)) : ?>
            <table class="table table-hover">
                <thead class="text-primary">
                    <th><b>No.</b></th>
                    <th><b>NIS</b></th>
                    <th><b>Nama Siswa</b></th>
                    <th><b>Kehadiran</b></th>
                    <th><b>Jam masuk</b></th>
                    <th><b>Jam pulang</b></th>
                    <th><b>Keterangan</b></th>
                    <th><b>Aksi</b></th>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($data as $value) : ?>
                        <?php
                        $idKehadiran = intval($value['id_kehadiran'] ?? ($lewat ? 5 : 4));
                        $kehadiran = kehadiran($idKehadiran);
                        ?>
                        <tr>
                            <td><?= $no; ?></td>
                            <td><?= $value['nis']; ?></td>
                            <td><b><?= $value['nama_siswa']; ?></b></td>
                            <td>
                                <p class="p-2 w-100 btn btn-<?= $kehadiran['color']; ?> text-center">
                                    <b><?= $kehadiran['text']; ?></b>
                                </p>
                            </td>
                            <td><b><?= $value['jam_masuk'] ?? '-'; ?></b></td>
                            <td><b><?= $value['jam_keluar'] ?? '-'; ?></b></td>
                            <td><?= $value['keterangan'] ?? '-'; ?></td>
                            <td>
                                <?php if (!$lewat) : ?>
                                    <button data-toggle="modal" data-target="#ubahModal" onclick="getDataKehadiran(<?= $value['id_presensi'] ?? '-1'; ?>, <?= $value['id_siswa']; ?>)" class="btn btn-info p-2" id="<?= $value['nis']; ?>">
                                        <i class="material-icons">edit</i>
                                        Edit
                                    </button>
                                <?php else : ?>
                                    <button class="btn btn-disabled p-2">No Action</button>
                                <?php endif; ?>
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
</div>

<?php
function kehadiran($kehadiran): array
{
    $text = '';
    $color = '';
    switch ($kehadiran) {
        case 1:
            $color = 'success';
            $text = 'Hadir';
            break;
        case 2:
            $color = 'warning';
            $text = 'Sakit';
            break;
        case 3:
            $color = 'info';
            $text = 'Izin';
            break;
        case 4:
            $color = 'danger';
            $text = 'Tanpa keterangan';
            break;
        case 5:
        default:
            $color = 'disabled';
            $text = 'Belum tersedia';
            break;
    }

    return ['color' => $color, 'text' => $text];
}
?>