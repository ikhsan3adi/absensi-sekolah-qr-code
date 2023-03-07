<div class="card-body table-responsive">
    <table class="table table-hover">
        <thead class="text-primary">
            <th>ID</th>
            <th>Nama Siswa</th>
            <th>Jenis Kelamin</th>
            <th>Kelas</th>
            <th>Jurusan</th>
            <th>No HP</th>
        </thead>
        <tbody>
            <?php foreach ($data as $value) : ?>
                <tr>
                    <td><?= $value['id_siswa']; ?></td>
                    <td><?= $value['nama_siswa']; ?></td>
                    <td><?= $value['jenis_kelamin']; ?></td>
                    <td><?= $value['kelas']; ?></td>
                    <td><?= $value['jurusan']; ?></td>
                    <td><?= $value['no_hp']; ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>