<?php

class Approval
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
                    'pc_id' => $pc_id,
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
                'pc_id' => $pc_id,
            );

            $msg = $data_item;
            return $msg;
        }
    }

    public function findByPCId($pc_id, $tablename)
    {
        $query = "SELECT * FROM $tablename WHERE pc_id = '$pc_id'";
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
                'pc_id' => $pc_id,
            );

            $msg = $data_item;
            return $msg;
        }
    }

    public function getPCByUserId($user_id, $tablename)
    {
        $pc_id;
        $pc_collection = array();

        $query = "SELECT * FROM $tablename
                    WHERE data @> '[{\"user_id\": \"" . $user_id . "\"}]'";
        // die($query);
        $result = $this->db->execute($query);
        $num = $result->rowCount();

        if ($num > 0) {

            $data_arr = array();

            while ($row = $result->fetchRow()) {
                extract($row);

                $data_item = array(
                    'id' => $id,
                    'data' => json_decode($data),
                    'pc_id' => $pc_id,
                );

                array_push($data_arr, $data_item);
                $msg = $data_arr;
            }

        } else {
            $msg = 'Data Kosong';
        }

        for ($i = 0; $i < count($msg); $i++) {
            $pc_id = $msg[$i]['pc_id'];
            $getPC = "SELECT * FROM program_charter WHERE id = '$pc_id'";
            $PC = $this->db->execute($getPC);
            if (empty($PC)) {
                $msg = array("message" => 'Data Tidak Ditemukan', "code" => 400);
                return $msg;
            } else {
                $row = $PC->fetchRow();
                extract($row);

                $data_item = array(
                    'id' => $id,
                    'title' => $title,
                    'code' => $code,
                    'strategic_initiative' => $strategic_initiative,
                    'cfu_fu' => $cfu_fu,
                    'weight' => $weight,
                    'matrix' => $matrix,
                    'description' => $description,
                    'refer_to' => json_decode($refer_to),
                    'stakeholders' => json_decode($stakeholders),
                    'kpi' => json_decode($kpi),
                    'budget' => $budget,
                    'main_activities' => json_decode($main_activities),
                    'key_asks' => json_decode($key_asks),
                    'risks' => $risks,
                    'status' => $status,
                );
            }

            array_push($pc_collection, $data_item);

        }

        return $pc_collection;
    }

    public function insert($tablename)
    {
        $data = file_get_contents("php://input");
        //
        $request = json_decode($data);
        // die(print_r($request));
        $variable = array('data', 'pc_id');
        foreach ($variable as $item) {
            if (!isset($request[0]->{$item})) {
                return "402";
            }

            $$item = $request[0]->{$item};
        }

        $data = json_encode($data);

        $query = 'INSERT INTO ' . $tablename . ' (data, pc_id) ';
        $query .= "VALUES ('$data', '$pc_id') RETURNING *";
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
                        'pc_id' => $pc_id,
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

        $variable = array('data', 'pc_id');
        foreach ($variable as $item) {
            if (!isset($request[0]->{$item})) {
                return "402";
            }

            $$item = $request[0]->{$item};
        }

        $data = json_encode($data);

        $query = "UPDATE $tablename SET data = '$data', pc_id = '$pc_id' WHERE id = '$id' RETURNING *";

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
                        'pc_id' => $pc_id,
                    );

                    array_push($data_arr, $data_item);
                    $msg = $data_arr;
                }

            };
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
