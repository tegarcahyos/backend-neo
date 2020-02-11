<?php
include 'transformer_connect.php';

class ProgramCharter
{
    public $db;
    public $db_transformer;

    public function __construct($db)
    {
        $this->db = $db;
        $this->db_transformer = new TransformerConnect();
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
                    'unit_id' => $unit_id,
                    'weight' => $weight,
                    'description' => $description,
                    'refer_to' => json_decode($refer_to),
                    'stakeholders' => json_decode($stakeholders),
                    'kpi' => json_decode($kpi),
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
                'unit_id' => $unit_id,
                'weight' => $weight,
                'description' => $description,
                'refer_to' => json_decode($refer_to),
                'stakeholders' => json_decode($stakeholders),
                'kpi' => json_decode($kpi),
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
        $unit_id = $request[0]->unit_id;
        $weight = $request[0]->weight;
        $description = $request[0]->description;
        $refer_to = json_encode($request[0]->refer_to);
        $stakeholders = json_encode($request[0]->stakeholders);
        $kpi = json_encode($request[0]->kpi);
        $main_activities = json_encode($request[0]->main_activities);
        $key_asks = json_encode($request[0]->key_asks);
        $risks = $request[0]->risks;
        $status = $request[0]->status;
        $generator_id = $request[0]->generator_id;

        if (empty($weight)) {
            $weight = 'NULL';
        }
        if (empty($description)) {
            $description = 'NULL';
        }
        if (empty($risks)) {
            $risks = 'NULL';
        }
        if (empty($stakeholders)) {
            $stakeholders = 'NULL';
        }
        if (empty($kpi)) {
            $kpi = 'NULL';
        }
        if (empty($main_activities)) {
            $main_activities = 'NULL';
        }
        if (empty($key_asks)) {
            $key_asks = 'NULL';
        }
        if (empty($risks)) {
            $risks = 'NULL';
        }

        $query = "INSERT INTO $tablename (
        title,
        code,
        strategic_initiative,
        unit_id,
        weight,
        description,
        refer_to,
        stakeholders,
        kpi,
        main_activities,
        key_asks,
        risks,
        status,
        generator_id)";
        $query .= "VALUES (
            '$title',
            '$code',
            '$strategic_initiative',
            '$unit_id',
            NULLIF('$weight', 'NULL'),
            NULLIF('$description', 'NULL'),
            '$refer_to',
            '$stakeholders',
            '$kpi',
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
                    'title' => $title,
                    'code' => $code,
                    'strategic_initiative' => $strategic_initiative,
                    'unit_id' => $unit_id,
                    'weight' => $weight,
                    'description' => $description,
                    'refer_to' => json_decode($refer_to),
                    'stakeholders' => json_decode($stakeholders),
                    'kpi' => json_decode($kpi),
                    'main_activities' => json_decode($main_activities),
                    'key_asks' => json_decode($key_asks),
                    'risks' => $risks,
                    'status' => $status,
                    'generator_id' => $generator_id,
                );

                array_push($data_arr, $data_item);
                $msg = $data_arr;

                $unit_push = array();
                $user_push = array();
                $main_activities_push = array();

                // STAKEHOLDER
                $stakeholders_push = array(
                    $data_item['stakeholders']->boe,
                    $data_item['stakeholders']->controller,
                    $data_item['stakeholders']->program_leader,

                );

                for ($i = 0; $i < count($data_item['stakeholders']->member); $i++) {
                    array_push($user_push, $data_item['stakeholders']->member[$i]);
                }

                // KURANG REVIEWERRRRR

                for ($i = 0; $i < count($stakeholders_push); $i++) {
                    array_push($user_push, $stakeholders_push[$i]);
                }

                // MAIN ACTIVITIES
                for ($i = 0; $i < count($data_item['main_activities']->mainAct->task->data); $i++) {
                    array_push($main_activities_push, $data_item['main_activities']->mainAct->task->data[$i]->assign);
                }

                // KEY ASKS
                for ($i = 1; $i < count($data_item['key_asks']->alignment->nodeDataArray); $i++) {
                    array_push($unit_push, $data_item['key_asks']->alignment->nodeDataArray[$i]->assign);
                }

                $data_push = array(
                    "user_id" => $user_push,
                    "unit_id" => $unit_push,
                    "main_activities" => $main_activities_push,
                );

                if (!empty($data_push['unit_id'])) {
                    // KEY ASK INSERT
                    for ($i = 0; $i < count($data_push['unit_id']); $i++) {
                        $pushKeyAsk = "INSERT INTO log_notification (unit_id_or_user_id, pc_id, type, status, sender_id) VALUES ('" . $data_push['unit_id'][$i] . "', '$id', 'Key Ask', 0, '$generator_id')";
                        die($pushKeyAsk);
                        $this->db->execute($pushKeyAsk);
                    }
                }

                if (!empty($data_push['user_id'])) {
                    // STAKEHOLDER INSERT
                    for ($i = 0; $i < count($data_push['user_id']); $i++) {
                        $pushU = "INSERT INTO log_notification (unit_id_or_user_id, pc_id, type, status, sender_id) VALUES ('" . $data_push['user_id'][$i] . "', '$id', 'Stakeholder', 0, '$generator_id')";
                        // die($pushU);
                        $this->db->execute($pushU);
                    }
                }

                if (!empty($data_push['main_activities'])) {
                    // MAIN ACT INSERT
                    for ($i = 0; $i < count($data_push['main_activities']); $i++) {
                        $pushKeyMain = "INSERT INTO log_notification (unit_id_or_user_id, pc_id, type, status, sender_id) VALUES ('" . $data_push['user_id'][$i] . "', '$id', 'Main Activities', 0, '$generator_id')";
                        // die($pushKeyMain);
                        $this->db->execute($pushKeyMain);
                    }
                }

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
        $unit_id = $request[0]->unit_id;
        $weight = $request[0]->weight;
        $description = $request[0]->description;
        $refer_to = json_encode($request[0]->refer_to);
        $stakeholders = json_encode($request[0]->stakeholders);
        $kpi = json_encode($request[0]->kpi);
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
            unit_id = '$unit_id',
            weight = '$weight',
            description = NULLIF('$description', 'NULL'),
            refer_to = '$refer_to',
            stakeholders = '$stakeholders',
            kpi = '$kpi',
            main_activities = '$main_activities',
            key_asks = '$key_asks' ,
            risks = NULLIF('$risks', 'NULL'),
            status = '$status',
            generator_id = '$generator_id'
        WHERE id = '$id' RETURNING *";
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
                        'title' => $title,
                        'code' => $code,
                        'strategic_initiative' => $strategic_initiative,
                        'unit_id' => $unit_id,
                        'weight' => $weight,
                        'description' => $description,
                        'refer_to' => json_decode($refer_to),
                        'stakeholders' => json_decode($stakeholders),
                        'kpi' => json_decode($kpi),
                        'main_activities' => json_decode($main_activities),
                        'key_asks' => json_decode($key_asks),
                        'risks' => $risks,
                        'status' => $status,
                        'generator_id' => $generator_id,
                    );

                    array_push($data_arr, $data_item);
                    $msg = $data_arr;

                    $unit_push = array();
                    $user_push = array();
                    $main_activities_push = array();

                    // STAKEHOLDER
                    $stakeholders_push = array(
                        $data_item['stakeholders']->boe,
                        $data_item['stakeholders']->controller,
                        $data_item['stakeholders']->program_leader,

                    );

                    for ($i = 0; $i < count($data_item['stakeholders']->member); $i++) {
                        array_push($user_push, $data_item['stakeholders']->member[$i]);
                    }

                    for ($i = 0; $i < count($stakeholders_push); $i++) {
                        array_push($user_push, $stakeholders_push[$i]);
                    }

                    // MAIN ACTIVITIES
                    for ($i = 0; $i < count($data_item['main_activities']->mainAct->task->data); $i++) {
                        array_push($main_activities_push, $data_item['main_activities']->mainAct->task->data[$i]->assign);
                    }

                    // KEY ASKS
                    for ($i = 1; $i < count($data_item['key_asks']->alignment->nodeDataArray); $i++) {
                        array_push($unit_push, $data_item['key_asks']->alignment->nodeDataArray[$i]->assign);
                    }

                    $data_push = array(
                        "user_id" => $user_push,
                        "unit_id" => $unit_push,
                        "main_activities" => $main_activities_push,
                    );

                    $delete = "DELETE FROM log_notification WHERE pc_id = '$id'";
                    $this->db->execute($delete);
                    // die("Terdelete");
                    // KEY ASK INSERT
                    for ($i = 0; $i < count($data_push['unit_id']); $i++) {
                        $pushKeyAsk = "INSERT INTO log_notification (unit_id_or_user_id, pc_id, type, status, sender_id) VALUES ('" . $data_push['unit_id'][$i] . "', '$id', 'Key Ask', 0, '$generator_id')";
                        // die($pushKeyAsk);
                        $this->db->execute($pushKeyAsk);
                    }

                    // STAKEHOLDER INSERT
                    for ($i = 0; $i < count($data_push['user_id']); $i++) {
                        $pushU = "INSERT INTO log_notification (unit_id_or_user_id, pc_id, type, status, sender_id) VALUES ('" . $data_push['user_id'][$i] . "', '$id', 'Stakeholder', 0, '$generator_id')";
                        // die($pushU);
                        $this->db->execute($pushU);
                    }

                    // MAIN ACT INSERT
                    for ($i = 0; $i < count($data_push['main_activities']); $i++) {
                        $pushKeyMain = "INSERT INTO log_notification (unit_id_or_user_id, pc_id, type, status, sender_id) VALUES ('" . $data_push['user_id'][$i] . "', '$id', 'Main Activities', 0, '$generator_id')";
                        // die($pushKeyMain);
                        $this->db->execute($pushKeyMain);
                    }
                }

            }
        }

        return $msg;

    }

    public function delete($id_pc, $tablename)
    {
        $query = "DELETE FROM $tablename WHERE id = '$id_pc'";
        // die($query);
        $result = $this->db->execute($query);

        $delete_notif = "DELETE FROM log_notification WHERE pc_id = '$id'";
        $this->db->execute($delete_notif);

        $delete_ej = "SELECT * FROM expert_judgement WHERE program_charter LIKE '%$id_pc%'";
        $result = $this->db->execute($delete_ej);
        if (empty($result)) {
            return "404";
        } else {
            $num = $result->rowCount();

            // jika ada hasil
            if ($num > 0) {

                $data_arr = array();

                while ($row = $result->fetchRow()) {
                    extract($row);

                    // Push to data_arr

                    $data_item = array(
                        'id_ej' => $id,
                        'user_id' => $user_id,
                        'program_charter' => $program_charter,
                    );

                    array_push($data_arr, $data_item);
                }

            }
        }
        // die(gettype());
        for ($i = 0; $i < count($data_arr); $i++) {

            $string = $data_arr[$i]['program_charter'];
            $string = str_replace('[', "", $string);
            $string = str_replace(']', "", $string);
            $string = str_replace('"', "", $string);
            $current_temp = $data_arr[$i]['id_ej'];

            if (strpos($string, ',') !== false) {
                $explode = explode(', ', $string);
            } else {
                $explode = array($string);
            }

            for ($j = 0; $j < count($explode); $j++) {
                if ($explode[$j] == $id_pc) {
                    array_splice($explode, $j, 1);
                }
            }
            $explode = json_encode($explode);
            $update_ej = "UPDATE expert_judgement SET program_charter = '$explode' WHERE id = '$current_temp'";
            $this->db->execute($update_ej);
        }

        $res = $this->db->affected_rows();

        if ($res == true) {
            return $msg = array("message" => 'Data Berhasil Dihapus', "code" => 200);
        } else {
            return $msg = array("message" => 'Data tidak ditemukan', "code" => 400);
        }
    }
}
