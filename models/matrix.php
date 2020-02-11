<?php

class Matrix
{
    public $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function get($tablename)
    {
        $query = "SELECT * FROM  $tablename ";
        // die($query);
        $result = $this->db->execute($query);
        // hitung result
        $num = $result->rowCount();

        if ($num > 0) {

            $data_arr = array();

            while ($row = $result->fetchRow()) {
                extract($row);

                $data_item = array(
                    'id' => $id,
                    'si_id' => $si_id,
                    'unit_id' => $unit_id,
                    'matrix' => $matrix,
                );

                array_push($data_arr, $data_item);
                $msg = $data_arr;
            }

        } else {
            $msg = 'Data Kosong';
        }

        return $msg;
    }

    public function findById($id, $tablename)
    {
        $query = "SELECT * FROM $tablename WHERE id = '$id'";
        $result = $this->db->execute($query);
        if (empty($result)) {
            $msg = array("message" => 'Data Tidak Ditemukan', "code" => 400);
            return $msg;
        } else {
            $row = $result->fetchRow();
            extract($row);

            // Push to data_arr

            $data_item = array(
                'id' => $id,
                'si_id' => $si_id,
                'unit_id' => $unit_id,
                'matrix' => $matrix,
            );

            $msg = $data_item;
            return $msg;
        }
    }

    public function getByValues($attr, $val, $tablename)
    {

        $query = "SELECT * FROM $tablename WHERE ";
        $values = explode('AND', $val);
        $attr = explode('AND', $attr);
        for ($i = 0; $i < count($attr); $i++) {
            if ($i == 0) {

            } else {
                $query .= " AND ";
            }
            // $query .= "values @> '{\"" . $attr[$i] . "\": \"" . $values[$i] . "\"}'";
            $query .= "values ->> '" . $attr[$i] . "' = '" . $values[$i] . "'";

        }

        $query_real = str_replace("%20", " ", $query);
        die($query_real);
        $result = $this->db->execute($query_real);

        $num = $result->rowCount();

        if ($num > 0) {
            $data_arr = array();

            while ($row = $result->fetchRow()) {
                extract($row);
                $data_item = array(
                    'id' => $id,
                    'si_id' => $si_id,
                    'unit_id' => $unit_id,
                    'matrix' => $matrix,
                );

                array_push($data_arr, $data_item);
                $msg = $data_arr;
            }

        } else {
            $msg = 'Data Kosong';
        }

        return $msg;
    }

    public function insert($tablename)
    {
        $data = file_get_contents("php://input");
        //
        $request = json_decode($data);
        $variable = array('si_id', 'unit_id', 'matrix');
        foreach ($variable as $item) {
            if (!isset($request[0]->{$item})) {
                return "402";
            }

            $$item = $request[0]->{$item};
        }

        $query = 'INSERT INTO ' . $tablename . ' (si_id, unit_id, matrix) ';
        $query .= "VALUES ('$si_id','$unit_id', '$matrix') RETURNING *";
        // die($query);
        $result = $this->db->execute($query);
        if (empty($result)) {
            return "402";
        } else {
            $num = $result->rowCount();

            // jika ada hasil
            if ($num > 0) {

                $data_arr = array();

                while ($row = $result->fetchRow()) {
                    extract($row);

                    // Push to data_arr

                    $data_item = array(
                        'id' => $id,
                        'si_id' => $si_id,
                        'unit_id' => $unit_id,
                        'matrix' => $matrix,
                    );

                    array_push($data_arr, $data_item);
                    $msg = $data_arr;
                }
            }

            return $msg;

        }
    }

    public function update($id, $tablename)
    {
        // init attribute dan values

        $data = file_get_contents("php://input");

        $request = json_decode($data);

        $variable = array('si_id', 'unit_id', 'matrix');
        foreach ($variable as $item) {
            if (!isset($request[0]->{$item})) {
                return "402";
            }

            $$item = $request[0]->{$item};
        }

        $query = "UPDATE $tablename SET si_id = '$si_id', unit_id = '$unit_id',matrix = '$matrix' WHERE id = '$id' RETURNING *";

        $result = $this->db->execute($query);
        if (empty($result)) {
            return "402";
        } else {
            $num = $result->rowCount();

            // jika ada hasil
            if ($num > 0) {

                $data_arr = array();

                while ($row = $result->fetchRow()) {
                    extract($row);

                    // Push to data_arr

                    $data_item = array(
                        'id' => $id,
                        'si_id' => $si_id,
                        'unit_id' => $unit_id,
                        'matrix' => $matrix,
                    );

                    array_push($data_arr, $data_item);
                    $msg = $data_arr;
                }

            }

            return $msg;
        }

    }

    public function delete($id, $tablename)
    {
        $query = "DELETE FROM $tablename WHERE id = '$id'";

        $result = $this->db->execute($query);
        // return $result;
        $res = $this->db->affected_rows();

        if ($res == true) {
            return $msg = array("message" => 'Data Berhasil Dihapus', "code" => 200);
        } else {
            return $msg = array("message" => 'Data tidak ditemukan', "code" => 400);
        }
    }

    public function deleteByValues($attr, $val, $tablename)
    {

        $condition_values = explode('AND', $val);
        $condition_attr = explode('AND', $attr);
        $query = "DELETE FROM  $tablename";
        for ($i = 0; $i < count($condition_attr); $i++) {
            if ($i == 0) {

            } else {
                $query .= " AND ";
            }
            $query .= "values @> '{\"" . $condition_attr[$i] . "\": \"" . $condition_values[$i] . "\"}'";

        }
        $query_real = str_replace("%20", " ", $query);
        // die($query_real);
        return $this->db->execute($query_real);
    }

}
