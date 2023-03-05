<div class="card-body">
    <div class="row justify-content-between">
        <div class="col-auto me-auto">
            <div class="pt-3 pl-3">
                <h4><b>Absen Siswa</b></h4>
                <p>Daftar siswa muncul disini</p>
            </div>
        </div>
        <div class="col-auto">
            <div class="px-4">
                <h3 class="text-end">
                    <b class="text-primary"><?= $kelas; ?></b>
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
                    <th>Jam masuk</th>
                    <th>Jam pulang</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($data as $value) : ?>
                        <?php
                        $id_kehadiran = intval($value['id_kehadiran'] ?? ($lewat ? 5 : 4));
                        $kehadiran = kehadiran($id_kehadiran);
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
                            <td><?= $value['jam_masuk'] ?? '-'; ?></td>
                            <td><?= $value['jam_keluar'] ?? '-'; ?></td>
                            <td><?= $value['keterangan'] ?? '-'; ?></td>
                            <td>
                                <?php if (!$lewat) : ?>
                                    <div class="dropstart">
                                        <button type="button" class="btn btn-info p-2 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" id="<?= $value['nis']; ?>">
                                            <i class="material-icons">edit</i>
                                            Edit
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="<?= $value['nis']; ?>">
                                            <li>
                                                <h5 class="dropdown-header">Ubah kehadiran</h5>
                                            </li>
                                            <?php foreach ($listKehadiran as $value2) : ?>
                                                <?php $color = kehadiran($value2['id_kehadiran'])['color'] ?>
                                                <?php if ($value2['id_kehadiran'] == $id_kehadiran) : ?>
                                                    <li class="p-1 mr-2">
                                                        <button disabled="disabled" class="w-100 dropdown-item text-white btn btn-<?= $color; ?>">
                                                            <?= $value2['kehadiran']; ?>
                                                        </button>
                                                    </li>
                                                <?php else : ?>
                                                    <li class="p-1 mr-2">
                                                        <?php if ($value2['id_kehadiran'] == 1) : ?>
                                                            <button onclick="" class="w-100 dropdown-item text-white btn btn-<?= $color; ?>">
                                                                <?= $value2['kehadiran']; ?>
                                                            </button>
                                                        <?php else : ?>
                                                            <button onclick="ubahKehadiran(<?= $value2['id_kehadiran']; ?>, <?= $value['id_siswa']; ?>, <?= $value['id_kelas']; ?>)" class="w-100 dropdown-item text-white btn btn-<?= $color; ?>">
                                                                <?= $value2['kehadiran']; ?>
                                                            </button>
                                                        <?php endif; ?>
                                                    </li>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <li class="p-2">
                                                <label class="bmd-label-floating">Keterangan</label>
                                                <input type="text" class="form-control">
                                            </li>
                                        </ul>
                                    </div>
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