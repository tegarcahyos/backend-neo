<?php

class StraIn
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
             $tablename order by created_at asc";

        $result = $this->db->execute($query);
        $num = $result->rowCount();

        if ($num > 0) {

            $data_arr = array();

            while ($row = $result->fetchRow()) {
                extract($row);

                $data_item = array(
                    'id' => $id,
                    'name' => $name,
                    'code' => $code,
                    'parent_id' => $parent_id,
                    'periode_id' => $periode_id,
                );

                array_push($data_arr, $data_item);
                $msg = $data_arr;
            }

        } else {
            $msg = 'Data Kosong';
        }

        return $msg;
    }

    private $parentArray = [""];
    public function getParentSIBy($id)
    {
        // Jika parent
        if ($id == '0') {
            // is root
        } else {
            // bukan root
            // select name, parentId by $id,
            $query = "SELECT * FROM strategic_initiative WHERE id = '$id'";
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
            $this->getParentSIBy($idParentTemp);
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
                $this->getParentSIBy($parent_id);
                $data_item = array(
                    'id' => $id,
                    // 'organization_id' => $organization_id,
                    // 'organization_name' => $organization_name,
                    // 'organization_code' => $organization_code,
                    'parent_id' => $parent_id,
                    'name' => $name,
                    'code' => $code,
                    'periode_id' => $periode_id,
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

    public function select_periode($periode_id, $tablename)
    {
        $query = "SELECT
           *
          FROM
             $tablename WHERE periode_id = '$periode_id'";
        // die($query);
        $result = $this->db->execute($query);

        $num = $result->rowCount();

        if ($num > 0) {

            $data_arr = array();

            while ($row = $result->fetchRow()) {
                extract($row);
                $data_item = array(
                    'id' => $id,
                    // 'organization_id' => $organization_id,
                    // 'organization_name' => $organization_name,
                    // 'organization_code' => $organization_code,
                    'parent_id' => $parent_id,
                    'name' => $name,
                    'code' => $code,
                    'periode_id' => $periode_id,
                );

                array_push($data_arr, $data_item);
                $msg = $data_arr;
            }

        } else {
            $msg = [];
        }

        return $msg;
    }

    public function getLeafByRootId($id, $tablename)
    {
        $query = "WITH RECURSIVE children AS (
            SELECT
               id,
                0               AS number_of_ancestors,
               parent_id,
               code,
               name,
               periode_id
            FROM  $tablename where id = '$id'
           UNION
            SELECT
               tp.id,
               c.number_of_ancestors + 1                   AS ancestry_size,
               tp.parent_id,
               tp.code,
               tp.name,
               tp.periode_id
            FROM  $tablename tp
            JOIN children c ON tp.parent_id::text = c.id::text
           )
           SELECT *
            FROM children t1 WHERE NOT EXISTS (SELECT * FROM children t2 WHERE t1.id::text = t2.parent_id::text);";
        $result = $this->db->execute($query);
        $num = $result->rowCount();

        if ($num > 0) {

            $data_arr = array();

            while ($row = $result->fetchRow()) {
                extract($row);

                $data_item = array(
                    'id' => $id,
                    'name' => $name,
                    'code' => $code,
                    'parent_id' => $parent_id,
                    'periode_id' => $periode_id,
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
                'code' => $code,
                'parent_id' => $parent_id,
                'periode_id' => $periode_id,
            );
            return $data_item;
        }
    }

    public function getLeaf($tablename)
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
                    'parent_id' => $parent_id,
                    'name' => $name,
                    'code' => $code,
                    'periode_id' => $periode_id,
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

        $variable = array('name', 'code', 'parent_id', 'periode_id');
        foreach ($variable as $item) {
            if (!isset($request[0]->{$item})) {
                return "402";
            }

            $$item = $request[0]->{$item};
        }

        $query = "INSERT INTO $tablename (name, code, parent_id,periode_id)";
        $query .= "VALUES ('$name', '$code', '$parent_id','$periode_id') RETURNING *";
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

                    $data_item = array(
                        'id' => $id,
                        'name' => $name,
                        'code' => $code,
                        'parent_id' => $parent_id,
                        'periode_id' => $periode_id,
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
        $variable = array('name', 'code', 'parent_id', 'periode_id');
        foreach ($variable as $item) {
            if (!isset($request[0]->{$item})) {
                return "402";
            }

            $$item = $request[0]->{$item};
        }

        $query = "UPDATE $tablename SET name = '$name', code = '$code', parent_id = '$parent_id', periode_id = '$periode_id' WHERE id = '$id' RETURNING *";
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

                    $data_item = array(
                        'id' => $id,
                        'name' => $name,
                        'code' => $code,
                        'parent_id' => $parent_id,
                        'periode_id' => $periode_id,
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
        $get_refs = "SELECT EXISTS(SELECT 1
        from (
            select si_id::text as si_id from matrix
            union all
            select si_id::text from si_target
			union all
            select parent_id::text from strategic_initiative
        ) a
        where si_id = '$id')";
        $result = $this->db->execute($get_refs);
        $row = $result->fetchRow();
        if ($row['exists'] == 't') {
            return "403";
        } else {
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
}
