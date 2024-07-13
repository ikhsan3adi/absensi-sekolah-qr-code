<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Seeder;
use Config\Database;

class GuruSeeder extends Seeder
{
    private \Faker\Generator $faker;

    public function __construct(Database $config, ?BaseConnection $db = null)
    {
        parent::__construct($config, $db);
        $this->faker = \Faker\Factory::create('id_ID');
    }

    public function run()
    {
        $this->db->table('tb_guru')->insertBatch(
            $this->createGuru(20)
        );
    }

    protected function createGuru($count = 1)
    {
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $gender = $this->faker->randomElement(['Laki-laki', 'Perempuan']);

            array_push($data, [
                'nuptk' => $this->faker->numerify('################'),
                'nama_guru' => $this->faker->name($gender == 'Laki-laki' ? 'male' : 'female'),
                'jenis_kelamin' => $gender,
                'alamat' => $this->faker->address(),
                'no_hp' => $this->faker->numerify('08##########'),
                'unique_code' => $this->faker->uuid()
            ]);
        }

        return $data;
    }
}
