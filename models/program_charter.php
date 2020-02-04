<?php

class ProgramCharter
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
             $tablename order by updated_at desc";

        // die($query);
        $result = $this->db->execute($query);

        $num = $result->rowCount();

        if ($num > 0) {

            $data_arr = array();

            while ($row = $result->fetchRow()) {
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
                    'generator_id' => $generator_id,
                );

                array_push($data_arr, $data_item);
                $msg = $data_arr;
            }

        } else {
            $msg = 'Data Kosong';
        }

        return $msg;
    }

    public function callAPI($method, $url, $data)
    {
        $curl = curl_init();
        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }

                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }

                break;
            default:
                if ($data) {
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                }

        }

        // OPTIONS:
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            // 'x-authorization: ' . $this->apiKey,
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // EXECUTE:
        $result = curl_exec($curl);
        // die("ini token" . $this->apiKey);
        if (!$result) {die("Connection Failure");}
        curl_close($curl);
        return $result;
    }

    public function getAcceptedPC()
    {
        $getData = $this->callAPI('GET', '10.62.161.11/api/index.php/program_charter/get', false);
        $request = json_decode($getData);
        die(print_r($request->data));
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
                'generator_id' => $generator_id,
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
        $title = $request[0]->title;
        $code = $request[0]->code;
        $strategic_initiative = $request[0]->strategic_initiative;
        $cfu_fu = $request[0]->cfu_fu;
        $weight = $request[0]->weight;
        $matrix = $request[0]->matrix;
        $description = $request[0]->description;
        $refer_to = json_encode($request[0]->refer_to);
        $stakeholders = json_encode($request[0]->stakeholders);
        $kpi = json_encode($request[0]->kpi);
        $budget = json_encode($request[0]->budget);
        $main_activities = json_encode($request[0]->main_activities);
        $key_asks = json_encode($request[0]->key_asks);
        $risks = $request[0]->risks;
        $status = $request[0]->status;
        $generator_id = $request[0]->generator_id;

        if (empty($description)) {
            $description = 'NULL';
        }
        if (empty($risks)) {
            $risks = 'NULL';
        }
        if (empty($approval)) {
            $approval = 'NULL';
        }

        $query = "INSERT INTO $tablename (
        title,
        code,
        strategic_initiative,
        cfu_fu,
        weight,
        matrix,
        description,
        refer_to,
        stakeholders,
        kpi,
        budget,
        main_activities,
        key_asks,
        risks,
        status,
        generator_id)";
        $query .= "VALUES (
            '$title',
            '$code',
            '$strategic_initiative',
            '$cfu_fu',
            '$weight',
            '$matrix',
            NULLIF('$description', 'NULL'),
            '$refer_to',
            '$stakeholders',
            '$kpi',
            '$budget',
            '$main_activities',
            '$key_asks',
            NULLIF('$risks', 'NULL'),
            '$status',
            '$generator_id'
            ) RETURNING *";
        // die($query);
        $result = $this->db->execute($query);
        $num = $result->rowCount();

        // jika ada hasil
        if ($num > 0) {

            $data_arr = array();

            while ($row = $result->fetchRow()) {
                extract($row);

                // Push to data_arr

                $data_item = array(
                    'id' => $id,
                );

                array_push($data_arr, $data_item);
                $msg = $data_arr;
            }

        } else {
            $msg = 'Data Kosong';
        }

        return $msg;

    }

    public function update($id, $tablename)
    {
        // get data input from frontend
        $data = file_get_contents("php://input");
        //
        $request = json_decode($data);
        $title = $request[0]->title;
        $code = $request[0]->code;
        $strategic_initiative = $request[0]->strategic_initiative;
        $cfu_fu = $request[0]->cfu_fu;
        $weight = $request[0]->weight;
        $matrix = $request[0]->matrix;
        $description = $request[0]->description;
        $refer_to = json_encode($request[0]->refer_to);
        $stakeholders = json_encode($request[0]->stakeholders);
        $kpi = json_encode($request[0]->kpi);
        $budget = json_encode($request[0]->budget);
        $main_activities = json_encode($request[0]->main_activities);
        $key_asks = json_encode($request[0]->key_asks);
        $risks = $request[0]->risks;
        $status = $request[0]->status;
        $generator_id = $request[0]->generator_id;

        if (empty($description)) {
            $description = 'NULL';
        }
        if (empty($risks)) {
            $risks = 'NULL';
        }
        if (empty($approval)) {
            $approval = 'NULL';
        }

        $query = "UPDATE $tablename SET
            title = '$title',
            code = '$code',
            strategic_initiative = '$strategic_initiative',
            cfu_fu = '$cfu_fu',
            weight = '$weight',
            matrix = '$matrix',
            description = NULLIF('$description', 'NULL'),
            refer_to = '$refer_to',
            stakeholders = '$stakeholders',
            kpi = '$kpi',
            budget = '$budget',
            main_activities = '$main_activities',
            key_asks = '$key_asks' ,
            risks = NULLIF('$risks', 'NULL'),
            status = '$status',
            generator_id = '$generator_id'
        WHERE id = '$id'";
        // die($query);
        $result = $this->db->execute($query);
        $res = $this->db->affected_rows();

        if ($res == true) {
            return $msg = array("message" => 'Data Berhasil Diubah', "code" => 200);
        } else {
            return $msg = array("message" => 'Data tidak ditemukan', "code" => 400);
        }
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
