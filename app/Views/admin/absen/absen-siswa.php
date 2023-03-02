<table class="table table-hover">
    <thead class="text-success">
        <th>ID</th>
        <th>NIS</th>
        <th>Nama Siswa</th>
    </thead>
    <tbody>
        <?php foreach ($data as $value) : ?>
            <tr>
                <td><?= $value['id_siswa']; ?></td>
                <td><?= $value['nis']; ?></td>
                <td><?= $value['nama_siswa']; ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>