<?php

class ReviewerPlan
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
                    'data' => json_decode($data),
                    'name' => $name,
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
                'data' => json_decode($data),
                'name' => $name,
            );

            $msg = $data_item;
            return $msg;
        }
    }

    public function insert($tablename)
    {
        $data = file_get_contents("php://input");
        //
        $request = json_decode($data);

        $variable = array('name', 'data');
        foreach ($variable as $item) {
            if (!isset($request[0]->{$item})) {
                return "402";
            }

            $$item = $request[0]->{$item};
        }

        $data = json_encode($data);

        $query = 'INSERT INTO ' . $tablename . ' (data, name) ';
        $query .= "VALUES ('$data', '$name') RETURNING *";
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
                        'data' => json_decode($data),
                        'name' => $name,
                    );

                    array_push($data_arr, $data_item);
                    $msg = $data_arr;
                }

            }
        }

        return $msg;

    }

    public function update($id, $tablename)
    {
        // init attribute dan values

        $data = file_get_contents("php://input");

        $request = json_decode($data);
        $variable = array('name', 'data');
        foreach ($variable as $item) {
            if (!isset($request[0]->{$item})) {
                return "402";
            }

            $$item = $request[0]->{$item};
        }

        $data = json_encode($data);

        $query = "UPDATE $tablename SET data = '$data', name = '$name' WHERE id = '$id' RETURNING *";

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
                        'data' => json_decode($data),
                        'name' => $name,
                    );

                    array_push($data_arr, $data_item);
                    $msg = $data_arr;
                }

            }
        }

        return $msg;
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

}
