<?php

class AHPCriteria
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
             $tablename ORDER BY updated_at desc limit 1";

        // die($query);
        $result = $this->db->execute($query);

        $num = $result->rowCount();

        if ($num > 0) {

            $data_arr = array();

            while ($row = $result->fetchRow()) {
                extract($row);

                $data_item = array(
                    'id' => $id,
                    'criteria' => json_decode($criteria),
                    'periode_id' => $periode_id,
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

    public function getByOrganization($id, $tablename)
    {
        $query = "SELECT
           *
          FROM
             $tablename WHERE organization_id = '$id' ORDER BY updated_at desc limit 1";

        // die($query);
        $result = $this->db->execute($query);

        $num = $result->rowCount();

        if ($num > 0) {

            $data_arr = array();

            while ($row = $result->fetchRow()) {
                extract($row);

                $data_item = array(
                    'id' => $id,
                    'criteria' => json_decode($criteria),
                    'periode_id' => $periode_id,
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

    public function getByPeriode($id, $tablename)
    {
        $query = "SELECT
           *
          FROM
             $tablename WHERE periode_id = '$id' ORDER BY updated_at desc limit 1";

        // die($query);
        $result = $this->db->execute($query);

        $num = $result->rowCount();

        if ($num > 0) {

            $data_arr = array();

            while ($row = $result->fetchRow()) {
                extract($row);

                $data_item = array(
                    'id' => $id,
                    'criteria' => json_decode($criteria),
                    'periode_id' => $periode_id,
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

    public function findById($id, $tablename)
    {
        $query = "SELECT * FROM $tablename WHERE id = '$id'";
        $result = $this->db->execute($query);
        $row = $result->fetchRow();
        if (is_bool($row)) {
            $msg = "Data Kosong";
            return $msg;
        } else {
            extract($row);

            $data_item = array(
                'id' => $id,
                'criteria' => json_decode($criteria),
                'periode_id' => $periode_id,
                'organization_id' => $organization_id,
            );
            return $data_item;
        }
    }

    public function insert($tablename)
    {
        // get data input from frontend
        $data = file_get_contents("php://input");
        //
        $request = json_decode($data);
        // $criteria = json_encode($request[0]->criteria);
        $variable = array('criteria', 'periode_id', 'organization_id');
        foreach ($variable as $item) {
            if (!isset($request[0]->{$item})) {
                return "422";
            }

            $$item = $request[0]->{$item};
        }
        $criteria = json_encode($criteria);

        $query = "INSERT INTO $tablename (criteria, periode_id, organization_id)";
        $query .= "VALUES ('$criteria') RETURNING *";
        // die($query);
        $result = $this->db->execute($query);
        if (empty($result)) {
            return "422";
        } else {
            $num = $result->rowCount();

            if ($num > 0) {

                $data_arr = array();

                while ($row = $result->fetchRow()) {
                    extract($row);

                    $data_item = array(
                        'id' => $id,
                        'criteria' => json_decode($criteria),
                        'periode_id' => $periode_id,
                        'organization_id' => $organization_id,
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
        $variable = array('criteria', 'periode_id', 'organization_id');
        foreach ($variable as $item) {
            if (!isset($request[0]->{$item})) {
                return "422";
            }

            $$item = $request[0]->{$item};
        }

        $criteria = json_encode($criteria);

        $query = "UPDATE $tablename SET criteria = '$criteria', periode_id = '$periode_id', organization_id = '$organization_id' WHERE id = '$id' RETURNING *";
        // die($query);
        $result = $this->db->execute($query);
        if (empty($result)) {
            return "422";
        } else {
            $num = $result->rowCount();

            if ($num > 0) {

                $data_arr = array();

                while ($row = $result->fetchRow()) {
                    extract($row);

                    $data_item = array(
                        'id' => $id,
                        'criteria' => json_decode($criteria),
                        'periode_id' => $periode_id,
                        'organization_id' => $organization_id,
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
        $get_refs = "SELECT EXISTS(SELECT * FROM ahp_featured_program_charter WHERE criteria_id = '$id')";
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
                return $msg = "Data Kosong";
            }
        }
    }
}
