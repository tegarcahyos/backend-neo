<?php

class Employee
{
    public $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function get()
    {
        $query = "SELECT
           *
          FROM
             employee";

        $result = $this->db->execute($query);

        $num = $result->rowCount();

        if ($num > 0) {

            $data_arr = array();

            while ($row = $result->fetchRow()) {
                extract($row);

                $data_item = array(
                    'c_company_code' => $c_company_code,
                    'v_company_code' => $v_company_code,
                    'c_kode_divisi' => $c_kode_divisi,
                    'v_short_divisi' => $v_short_divisi,
                    'c_kode_unit' => $c_kode_unit,
                    'v_short_unit' => $v_short_unit,
                    'v_long_unit' => $v_long_unit,
                    'objidposisi' => $objidposisi,
                    'c_kode_posisi' => $c_kode_posisi,
                    'v_short_posisi' => $v_short_posisi,
                    'v_long_posisi' => $v_long_posisi,
                    'c_flag_chief' => $c_flag_chief,
                    'n_nik' => $n_nik,
                    'v_nama_karyawan' => $v_nama_karyawan,
                    'v_jenis_kelamin' => $v_jenis_kelamin,
                    'v_personnel_subarea' => $v_personnel_subarea,
                );

                array_push($data_arr, $data_item);
                $msg = $data_arr;
            }

        } else {
            $msg = 'Data Kosong';
        }

        return $msg;
    }

    public function find($value)
    {
        $query = "SELECT DISTINCT * FROM employee  WHERE n_nik = '$value' OR v_nama_karyawan ilike '%$value%'";
        // die($query);
        $result = $this->db->execute($query);
        $num = $result->rowCount();

        if ($num > 0) {

            $data_arr = array();

            while ($row = $result->fetchRow()) {
                extract($row);

                $data_item = array(
                    'c_company_code' => $c_company_code,
                    'v_company_code' => $v_company_code,
                    'c_kode_divisi' => $c_kode_divisi,
                    'v_short_divisi' => $v_short_divisi,
                    'c_kode_unit' => $c_kode_unit,
                    'v_short_unit' => $v_short_unit,
                    'v_long_unit' => $v_long_unit,
                    'objidposisi' => $objidposisi,
                    'c_kode_posisi' => $c_kode_posisi,
                    'v_short_posisi' => $v_short_posisi,
                    'v_long_posisi' => $v_long_posisi,
                    'c_flag_chief' => $c_flag_chief,
                    'n_nik' => $n_nik,
                    'v_nama_karyawan' => $v_nama_karyawan,
                    'v_jenis_kelamin' => $v_jenis_kelamin,
                    'v_personnel_subarea' => $v_personnel_subarea,
                );

                array_push($data_arr, $data_item);
                $msg = $data_arr;
            }

        } else {
            $msg = 'Data Kosong';
        }

        return $msg;
    }

}
