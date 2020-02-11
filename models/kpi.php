<?php

class Kpi
{
    public $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function get($tablename)
    {
        $query = "SELECT
           *
          FROM
             $tablename";

        $result = $this->db->execute($query);

        $num = $result->rowCount();

        if ($num > 0) {

            $data_arr = array();

            while ($row = $result->fetchRow()) {
                extract($row);

                $data_item = array(
                    'id' => $id,
                    'name' => $name,
                    'metric' => $metric,
                    'status' => $status,
                    'parent_id' => $parent_id,
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
        $row = $result->fetchRow();
        if (is_bool($row)) {
            $msg = array("message" => 'Data Tidak Ditemukan', "code" => 400);
            return $msg;
        } else {
            extract($row);

            $data_item = array(
                'id' => $id,
                'name' => $name,
                'metric' => $metric,
                'status' => $status,
                'parent_id' => $parent_id,
            );
            return $data_item;
        }
    }

    private $parentArray = [""];
    public function getParentKpiBy($id)
    {
        // Jika parent
        if ($id == '0') {
            // is root
        } else {
            // bukan root
            // select name, parentId by $id,
            $query = "SELECT * FROM kpi WHERE id = '$id'";
            //
            $result = $this->db->execute($query);
            // die(print_r($result->fetchRow()));
            $row = $result->fetchRow();
            extract($row);

            $nameTemp = $row['name'];
            // SUNTIK nama array
            array_push($this->parentArray, $nameTemp);
            // Ambil parent id, buat dicari lagi atasnya
            $idParentTemp = $row['parent_id'];
            // Cari atasnya
            $this->getParentKpiBy($idParentTemp);
        }
    }

    public function getByParent($parent_id, $tablename)
    {
        $query = "SELECT
           *
          FROM
             $tablename WHERE parent_id = '$parent_id'";
        // die($query);
        $result = $this->db->execute($query);

        $num = $result->rowCount();

        if ($num > 0) {

            $data_arr = array();

            while ($row = $result->fetchRow()) {
                extract($row);
                // ambil parent parentnya pake $parent_id
                $this->getParentKpiBy($parent_id);
                $data_item = array(
                    'id' => $id,
                    'name' => $name,
                    'metric' => $metric,
                    'status' => $status,
                    'parent_id' => $parent_id,
                    'parent_list' => $this->parentArray,
                );

                array_push($data_arr, $data_item);
                $msg = $data_arr;
            }

        } else {
            $msg = [];
        }

        return $msg;
    }

    public function getLeafKpi($tablename)
    {
        $query = "SELECT
        *
       FROM
          $tablename t1
           WHERE NOT EXISTS (SELECT * FROM $tablename t2 WHERE t1.id::text = t2.parent_id::text)";
        // die($query);
        $result = $this->db->execute($query);
        $num = $result->rowCount();

        if ($num > 0) {

            $data_arr = array();

            while ($row = $result->fetchRow()) {
                extract($row);

                $data_item = array(
                    'id' => $id,
                    'name' => $name,
                    'metric' => $metric,
                    'status' => $status,
                    'parent_id' => $parent_id,
                );

                array_push($data_arr, $data_item);
                $msg = $data_arr;
            }

        } else {
            $msg = [];
        }

        return $msg;

    }

    public function insert($tablename)
    {
        // get data input from frontend
        $data = file_get_contents("php://input");
        //
        $request = json_decode($data);
        $variable = array('name', 'metric', 'status', 'parent_id');
        foreach ($variable as $item) {
            if (!isset($request[0]->{$item})) {
                return "402";
            }

            $$item = $request[0]->{$item};
        }

        $query = "INSERT INTO $tablename (name, metric, status, parent_id)";
        $query .= "VALUES ('$name', '$metric', '$status', '$parent_id') RETURNING *";
        // die($query);
        $result = $this->db->execute($query);
        if (empty($result)) {
            return "402";
        } else {
            $num = $result->rowCount();

            if ($num > 0) {

                $data_arr = array();

                while ($row = $result->fetchRow()) {
                    extract($row);
                    // ambil parent parentnya pake $parent_id
                    $data_item = array(
                        'id' => $id,
                        'name' => $name,
                        'metric' => $metric,
                        'status' => $status,
                        'parent_id' => $parent_id,
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
        // get data input from frontend
        $data = file_get_contents("php://input");
        //
        $request = json_decode($data);
        $variable = array('name', 'metric', 'status', 'parent_id');
        foreach ($variable as $item) {
            if (!isset($request[0]->{$item})) {
                return "402";
            }

            $$item = $request[0]->{$item};
        }

        $query = "UPDATE $tablename SET name = '$name', metric = '$metric', status = '$status', parent_id = '$parent_id' WHERE id = '$id' RETURNING *";
        // die($query);
        $result = $this->db->execute($query);
        if (is_bool($row)) {
            return "402";
        } else {
            $num = $result->rowCount();

            if ($num > 0) {

                $data_arr = array();

                while ($row = $result->fetchRow()) {
                    extract($row);
                    // ambil parent parentnya pake $parent_id
                    $this->getParentKpiBy($parent_id);
                    $data_item = array(
                        'id' => $id,
                        'name' => $name,
                        'metric' => $metric,
                        'status' => $status,
                        'parent_id' => $parent_id,
                        'parent_list' => $this->parentArray,
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
        // die($query);
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
