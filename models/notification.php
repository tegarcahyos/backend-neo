<?php
class Notification
{
    public $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function readNotification($id, $tablename)
    {
        $update = "UPDATE $tablename SET status = 1 RETURNING *";

        $result = $this->db->execute($query);
        $row = $result->fetchRow();
        extract($row);
        $data_item = array(
            'id' => $id,
            'unit_or_user_id' => $unit_or_user_id,
            'pc_id' => $pc_id,
            'type' => $type,
            'status' => $status,
            'sender_id' => $sender_id,
        );
        return $data_item;
    }

    public function checkNotif($id, $tablename)
    {
        $limitCheck = 15;
        $counterCheck = 0;

        while (true) {
            $result = $this->findById($id, $tablename);
            if (count($result) > 0) {
                return $result;
            } else if (++$counterCheck == $limitCheck) {
                return "Data Kosong";
            }
        }
    }

    public function findById($id, $tablename)
    {
        $query = "SELECT * FROM $tablename WHERE unit_id_or_user_id = '$id' AND status = 0";
        // die($query);
        $result = $this->db->execute($query);
        $num = $result->rowCount();

        if ($num > 0) {

            $data_arr = array();

            while ($row = $result->fetchRow()) {
                extract($row);

                $data_item = array(
                    'id' => $id,
                    'unit_or_user_id' => $unit_or_user_id,
                    'pc_id' => $pc_id,
                    'type' => $type,
                    'status' => $status,
                    'sender_id' => $sender_id,
                );

                array_push($data_arr, $data_item);
                $msg = $data_arr;
            }

        } else {
            $msg = 'Data Kosong';
        }

        return $msg;
    }
}
