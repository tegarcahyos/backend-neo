<?php
class Milestone
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
                    'weight' => $weight,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'description' => $description,
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
                'name' => $name,
                'weight' => $weight,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'description' => $description,
            );
            return $data_item;
        }
    }

    public function insert($tablename)
    {
        // get data input from frontend
        $data = file_get_contents("php://input");
        $request = json_decode($data);
        $variable = array('name', 'weight', 'start_date', 'end_date', 'description');
        foreach ($variable as $item) {
            if (!isset($request[0]->{$item})) {
                return "422";
            }

            $$item = $request[0]->{$item};
        }

        $query = "INSERT INTO $tablename (name,  weight, start_date, end_date, description)";
        $query .= "VALUES ('$name', $weight, '$start_date', '$end_date', '$description') RETURNING *";
        // die($query);
        $result = $this->db->execute($query);
        if (empty($result)) {
            return "422";
        }else{
            $num = $result->rowCount();
            if ($num > 0) {

                $data_arr = array();
    
                while ($row = $result->fetchRow()) {
                    extract($row);
    
                    $data_item = array(
                        'id' => $id,
                        'name' => $name,
                        'weight' => $weight,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'description' => $description,
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
        $data = file_get_contents("php://input");
        $request = json_decode($data);
        $variable = array('name', 'weight', 'start_date', 'end_date', 'description');
        foreach ($variable as $item) {
            if (!isset($request[0]->{$item})) {
                return "422";
            }

            $$item = $request[0]->{$item};
        }

        $query = "UPDATE $tablename SET name = '$name', description = '$description' WHERE id = '$id'";
        // die($query);
        return $this->db->execute($query);
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
            return $msg = "Data Kosong";
        }
    }
}
