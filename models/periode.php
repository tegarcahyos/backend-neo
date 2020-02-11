<?php

class Periode
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
        // die($query);
        $result = $this->db->execute($query);
        // die($query);
        $num = $result->rowCount();

        if ($num > 0) {

            $data_arr = array();

            while ($row = $result->fetchRow()) {
                extract($row);

                $data_item = array(
                    'id' => $id,
                    'name' => $name,
                    'code' => $code,
                    'status_active' => $status_active,
                    'organization_id' => $organization_id,
                );

                array_push($data_arr, $data_item);
                $msg = $data_arr;
            }

        } else {
            $msg = 'Data Kosong';
        }

        return $msg;
    }

    public function select_id($id, $tablename)
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
                'status_active' => $status_active,
                'organization_id' => $organization_id,
            );
            return $data_item;
        }
    }
    public function select_org_id($org_id, $tablename)
    {
        $query = "SELECT * FROM $tablename WHERE organization_id = '$org_id'";
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
                    'status_active' => $status_active,
                    'organization_id' => $organization_id,
                );

                array_push($data_arr, $data_item);
                $msg = $data_arr;
            }

        } else {
            $msg = '0';
        }
        return $msg;
    }

    public function insert($tablename)
    {
        // get data input from frontend
        $data = file_get_contents("php://input");
        //
        $request = json_decode($data);
        $name = $request[0]->name;
        $code = $request[0]->code;
        $status_active = $request[0]->status_active;
        $organization_id = $request[0]->organization_id;

        $query_select_status = " SELECT id from $tablename where organization_id = '$organization_id' and status_active = '$status_active'";

        $result_select = $this->db->execute($query_select_status);

        $num = $result_select->rowCount();

        if ($num > 0) {

            // $data_arr = array();

            while ($row = $result_select->fetchRow()) {
                extract($row);

                $data_item = array(
                    'id' => $id,

                );

                // array_push($data_item);
                $msg_item = $data_item;
            }
            $id_periode = implode('', $msg_item);

            if ($status_active == true) {
                $query_set_status = "UPDATE $tablename SET status_active = 'false' where id = '$id_periode'";
                // die($query_set_status);
                $this->db->execute($query_set_status);

            } else {
                $query_set_status = "UPDATE $tablename SET status_active = 'true' where id = '$id_periode'";
                // die($query_set_status);
                $this->db->execute($query_set_status);
            }

        }

        $query = "INSERT INTO $tablename (name, code, status_active,organization_id)";
        $query .= "VALUES ('$name', '$code', '$status_active', '$organization_id')";
        // die($query);
        $result = $this->db->execute($query);

        $res = $this->db->affected_rows();

        if ($res == true) {
            return $msg = array("message" => 'Data Berhasil Ditambah', "code" => 200);
        } else {
            return $msg = array("message" => 'Data tidak ditemukan', "code" => 400);
        }
    }

    public function update($idP, $tablename)
    {
        // get data input from frontend
        $data = file_get_contents("php://input");
        //
        $request = json_decode($data);
        $name = $request[0]->name;
        $code = $request[0]->code;
        $status_active = $request[0]->status_active;
        $organization_id = $request[0]->organization_id;

        $set_false = "UPDATE $tablename SET status_active = false WHERE organization_id = '$organization_id'";
        $this->db->execute($set_false);

        $activate = "UPDATE $tablename SET name = '$name',  code = '$code' ,organization_id = '$organization_id', status_active = '$status_active' WHERE id = '$idP' RETURNING *";
        // die($activate);
        $result = $this->db->execute($activate);
        $row = $result->fetchRow();
        if (is_bool($row)) {
            $msg = array("message" => 'Data Tidak Ditemukan', "code" => 400);
            return $msg;
        } else {
            extract($row);

            $data_item = array(
                'id' => $idP,
                'name' => $name,
                'code' => $code,
                'status_active' => $status_active,
                'organization_id' => $organization_id,
            );
            return $data_item;
        }
    }

    public function delete($id, $tablename)
    {
        $get_refs = "SELECT EXISTS(SELECT * FROM strategic_initiative WHERE periode_id = '$id')";
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
