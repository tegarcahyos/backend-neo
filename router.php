<?php
include "adodb/adodb.inc.php";
include "vendor/autoload.php";
include "models/organization.php";
include "models/unit.php";
include "models/config_gann.php";
include "models/config_alignment.php";
include "models/metric.php";
include "models/user.php";
include "models/role.php";
include "models/attachment.php";
include "models/group_chat.php";
include "models/group_member.php";
include "models/group_message.php";
include "models/user_login.php";
include "models/matrix.php";
include "models/strategic_initiative.php";
include "models/main_program.php";
include "models/program_charter.php";
include "models/priority_data.php";
include "models/priority_criteria.php";
include "models/ceo_notes.php";
include "models/user_detail.php";
include "models/kpi.php";
include "models/master_data.php";
include "models/data_from_master.php";
include "models/si_target.php";
include "models/upload_file.php";
include "models/periode.php";
include "login.php";
if (file_exists('settings.php')) {
    include 'settings.php';
} else {
    define('db_username', 'pmo');
    define('db_password', 'pass4pmo');
    define('db_name1', "core");
    define("db_host", "10.62.161.10");
    define("db_port", "5432");
}

class Router
{
    public $url;
    public $db;
    public $db_config;
    public function core_connect()
    {

        $this->db = newADOConnection('pgsql');
        $this->db->connect(db_host, db_username, db_password, db_name1);
        return $this->db;
    }
    // MESSAGES
    public function msg($header, $type = null, $msg, $keterangan, $status)
    {
        die($type == 201);
        if ($type == 200) {
            $array = array(
                'status' => $status,
                'type' => $type,
                'keterangan' => $keterangan . '',
                'data' => $msg,
            );
            echo json_encode($array);
        } else if ($type == 201) {
            $array = array(
                'status' => $status,
                'type' => $type,
                'keterangan' => $keterangan . '',
                'data' => $msg,
            );
            echo json_encode($array);
        } else if ($type == 203) {
            $array = array(
                'type' => $type,
                'error-msg' => $msg,
                'keterangan' => $keterangan . '',
                'status' => $status,
            );
            echo json_encode($array);
        } else if ($type == 204) {
            $array = array(
                'type' => $type,
                'error-msg' => $msg,
                'keterangan' => $keterangan . '',
                'status' => $status,
            );
            echo json_encode($array);
        } else {
            return "kosong";
        }
    }

    public function check_token($token)
    {
        $tempDb = $this->core_connect();
        $query = "SELECT * FROM users WHERE token = '$token'";
        // die($query);
        $result = $tempDb->execute($query);
        $row = $result->fetchRow();
        if (is_bool($row)) {

        } else {
            extract($row);
            $expireAt = $row['expire_at'];
        }
        if (strtotime('now') < $expireAt) {
            return 'true';
        } else {
            return 'false';
            // return http_response_code(101);
        }
    }

    // REQUEST
    public function request()
    {

        $dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {

            $getHeader = getallheaders();
            $token = "";
            foreach ($getHeader as $key => $value) {
                if ($key == 'Authorization') {
                    $token = $value;
                }
            }

            // --- LOGIN ---
            $r->post('/api/index.php/login', 'Login/authenticate');
            $r->post('/api/index.php/login_chat', 'LoginChat/authenticate');

            // --- CHECK TOKEN ---
            // if (!empty($token)) {
            //     $passed = $this->check_token($token);
            //     if ($passed == 'true') {

            //FILES
            $r->post('/api/index.php/file/upload', 'Upload/upload_file');
            $r->get('/api/index.php/upload_file/download/{id_file}', 'Upload/downloadFile');
            $r->get('/api/index.php/upload_file/get', 'Upload/get');
            $r->get('/api/index.php/upload_file/select_file/{id}', 'Upload/select_id');

            // --- USER ---
            $r->get('/api/index.php/users/get', 'User/get');
            $r->get('/api/index.php/users/find_id/{id}', 'User/findById');
            $r->get('/api/index.php/users/delete/{id}', 'User/delete');
            $r->post('/api/index.php/users/insert', 'User/insert');
            $r->post('/api/index.php/users/update/{id}', 'User/update');

            // --- MASTER DATA ---
            $r->get('/api/index.php/master_data/get', 'MasterData/get');
            $r->get('/api/index.php/master_data/find_id/{id}', 'MasterData/findById');
            $r->get('/api/index.php/master_data/delete/{id}', 'MasterData/delete');
            $r->post('/api/index.php/master_data/insert', 'MasterData/insert');
            $r->post('/api/index.php/master_data/update/{id}', 'MasterData/update');

            // --- DATA FROM MASTER ---
            $r->get('/api/index.php/data_from_master/get', 'DataMaster/get');
            $r->get('/api/index.php/data_from_master/find_id/{id}', 'DataMaster/findById');
            $r->get('/api/index.php/data_from_master/delete/{id}', 'DataMaster/delete');
            $r->post('/api/index.php/data_from_master/insert', 'DataMaster/insert');
            $r->post('/api/index.php/data_from_master/update/{id}', 'DataMaster/update');

            // SI
            $r->get('/api/index.php/strategic_initiative/get', 'StraIn/get');
            $r->get('/api/index.php/strategic_initiative/find_id/{id}', 'StraIn/findById');
            $r->get('/api/index.php/strategic_initiative/get_leaf', 'StraIn/getLeaf');
            $r->get('/api/index.php/strategic_initiative/get_by_parent/{parent_id}', 'StraIn/getByParent');
            $r->get('/api/index.php/strategic_initiative/delete/{id}', 'StraIn/delete');
            $r->post('/api/index.php/strategic_initiative/insert', 'StraIn/insert');
            $r->post('/api/index.php/strategic_initiative/update/{id}', 'StraIn/update');

            // Main Program
            $r->get('/api/index.php/main_program/get', 'MainProgram/get');
            $r->get('/api/index.php/main_program/find_id/{id}', 'MainProgram/findById');
            $r->get('/api/index.php/main_program/delete/{id}', 'MainProgram/delete');
            $r->post('/api/index.php/main_program/insert', 'MainProgram/insert');
            $r->post('/api/index.php/main_program/update/{id}', 'MainProgram/update');

            // CEO Notes
            $r->get('/api/index.php/ceo_notes/get', 'CeoNotes/get');
            $r->get('/api/index.php/ceo_notes/find_id/{id}', 'CeoNotes/findById');
            $r->get('/api/index.php/ceo_notes/delete/{id}', 'CeoNotes/delete');
            $r->post('/api/index.php/ceo_notes/insert', 'CeoNotes/insert');
            $r->post('/api/index.php/ceo_notes/update/{id}', 'CeoNotes/update');

            // User Detail
            $r->get('/api/index.php/user_detail/get', 'UserDetail/get');
            $r->get('/api/index.php/user_detail/find_id/{id}', 'UserDetail/findById');
            $r->get('/api/index.php/user_detail/get_by_user/{user_id}', 'UserDetail/getByUser');
            $r->get('/api/index.php/user_detail/delete/{id}', 'UserDetail/delete');
            $r->post('/api/index.php/user_detail/insert', 'UserDetail/insert');
            $r->post('/api/index.php/user_detail/update/{id}', 'UserDetail/update');

            // Criteria Priority
            $r->get('/api/index.php/criteria_priority/get', 'PriorityCriteria/get');
            $r->get('/api/index.php/criteria_priority/find_id/{id}', 'PriorityCriteria/findById');
            $r->get('/api/index.php/criteria_priority/delete/{id}', 'PriorityCriteria/delete');
            $r->post('/api/index.php/criteria_priority/insert', 'PriorityCriteria/insert');
            $r->post('/api/index.php/criteria_priority/update/{id}', 'PriorityCriteria/update');

            // Data Priority
            $r->get('/api/index.php/data_priority/get', 'PriorityData/get');
            $r->get('/api/index.php/data_priority/find_id/{id}', 'PriorityData/findById');
            $r->get('/api/index.php/data_priority/delete/{id}', 'PriorityData/delete');
            $r->post('/api/index.php/data_priority/insert', 'PriorityData/insert');
            $r->post('/api/index.php/data_priority/update/{id}', 'PriorityData/update');

            // Program Charter
            $r->get('/api/index.php/program_charter/get', 'ProgramCharter/get');
            $r->get('/api/index.php/program_charter/find_id/{id}', 'ProgramCharter/findById');
            $r->get('/api/index.php/program_charter/delete/{id}', 'ProgramCharter/delete');
            $r->post('/api/index.php/program_charter/insert', 'ProgramCharter/insert');
            $r->post('/api/index.php/program_charter/update/{id}', 'ProgramCharter/update');

            // // --- CONFIG ALIGNMENT ---
            $r->get('/api/index.php/config_alignment/get', 'ConfigAlignment/get');
            $r->get('/api/index.php/config_alignment/find_id/{id}', 'ConfigAlignment/findById');
            $r->get('/api/index.php/config_alignment/delete/{id}', 'ConfigAlignment/delete');
            $r->post('/api/index.php/config_alignment/insert_alignment', 'ConfigAlignment/insertAlignData');
            $r->post('/api/index.php/config_alignment/insert_data_alignment/{id}', 'ConfigAlignment/insertData');
            $r->post('/api/index.php/config_alignment/update/{id}', 'ConfigAlignment/update');

            // // --- CONFIG GANN ---
            $r->get('/api/index.php/config_gann/get', 'ConfigGann/get');
            $r->get('/api/index.php/config_gann/find_id/{id}', 'ConfigGann/findById');
            $r->get('/api/index.php/config_gann/delete/{id}', 'ConfigGann/delete');
            $r->post('/api/index.php/config_gann/insert_gann', 'ConfigGann/insertGann');
            $r->post('/api/index.php/config_gann/insert_gann_data/{id}', 'ConfigGann/insertGannData');
            $r->post('/api/index.php/config_gann/update/{id}', 'ConfigGann/update');

            // METRIC
            $r->get('/api/index.php/metric/get', 'Metric/get');
            $r->get('/api/index.php/metric/find_id/{id}', 'Metric/findById');
            $r->get('/api/index.php/metric/delete/{id}', 'Metric/delete');
            $r->post('/api/index.php/metric/insert', 'Metric/insert');
            $r->post('/api/index.php/metric/update/{id}', 'Metric/update');

            // ROLE
            $r->get('/api/index.php/role/get', 'Role/get');
            $r->get('/api/index.php/role/find_id/{id}', 'Role/findById');
            $r->get('/api/index.php/role/delete/{id}', 'Role/delete');
            $r->post('/api/index.php/role/insert', 'Role/insert');
            $r->post('/api/index.php/role/update/{id}', 'Role/update');

            // KPI
            $r->get('/api/index.php/kpi/get', 'Kpi/get');
            $r->get('/api/index.php/kpi/find_id/{id}', 'Kpi/findById');
            $r->get('/api/index.php/kpi/delete/{id}', 'Kpi/delete');
            $r->post('/api/index.php/kpi/insert', 'Kpi/insert');
            $r->post('/api/index.php/kpi/update/{id}', 'Kpi/update');

            // KPI
            $r->get('/api/index.php/si_target/get', 'SITarget/get');
            $r->get('/api/index.php/si_target/find_id/{id}', 'SITarget/findById');
            $r->get('/api/index.php/si_target/delete/{id}', 'SITarget/delete');
            $r->post('/api/index.php/si_target/insert', 'SITarget/insert');
            $r->post('/api/index.php/si_target/update/{id}', 'SITarget/update');

            // ORGANIZATION
            $r->get('/api/index.php/organization/get', 'Organization/get');
            $r->get('/api/index.php/organization/find_id/{id}', 'Organization/findById');
            $r->get('/api/index.php/organization/delete/{id}', 'Organization/delete');
            $r->post('/api/index.php/organization/insert', 'Organization/insert');
            $r->post('/api/index.php/organization/update/{id}', 'Organization/update');

            // UNIT
            $r->get('/api/index.php/unit/get', 'Unit/get');
            $r->get('/api/index.php/unit/get_leaf_unit', 'Unit/getLeafUnit');
            $r->get('/api/index.php/unit/find_id/{id}', 'Unit/findById');
            $r->get('/api/index.php/unit/get_by_parent_unit_id/{parent_id}', 'Unit/getByParent');
            $r->get('/api/index.php/unit/get_root_parent/{id}', 'Unit/getRootParent');
            $r->get('/api/index.php/unit/get_by_organization/{org_id}', 'Unit/findByOrgId');
            $r->get('/api/index.php/unit/delete/{id}', 'Unit/delete');
            $r->post('/api/index.php/unit/insert', 'Unit/insert');
            $r->post('/api/index.php/unit/update/{id}', 'Unit/update');

            // OBJECT DATA
            $r->get('/api/index.php/{tablename}/select_all_get', 'ObjectData/select_all_get');
            $r->get('/api/index.php/{tablename}/select_id_get/{id}', 'ObjectData/select_id_get');
            $r->get('/api/index.php/{tablename}/select_where_get/{attr}/{val}', 'ObjectData/select_where_get');
            $r->get('/api/index.php/{tablename}/delete_all_get/{id}', 'ObjectData/delete_all_get');
            $r->post('/api/index.php/{tablename}/insert_object', 'ObjectData/insert');
            $r->post('/api/index.php/{tablename}/update_id/{id}', 'ObjectData/update_id');

            // MATRIX
            $r->get('/api/index.php/matrix/get', 'Matrix/get');
            $r->get('/api/index.php/matrix/find_id/{id}', 'Matrix/findById');
            $r->get('/api/index.php/matrix/select_where_get/{attr}/{val}', 'Matrix/getByValues');
            $r->get('/api/index.php/matrix/delete/{id}', 'Matrix/delete');
            $r->post('/api/index.php/matrix/insert', 'Matrix/insert');
            $r->post('/api/index.php/matrix/update/{id}', 'Matrix/update');

            //PERIOD
            $r->post('/api/index.php/periode/insert', 'Periode/insert');

            //GROUP CHAT
            $r->get('/api/index.php/group_chat/select_group_chat/{id}', 'GroupChat/findById');
            $r->get('/api/index.php/group_chat/select_all_group_chat', 'GroupChat/get');
            $r->get('/api/index.php/group_chat/delete/{id}', 'GroupChat/delete');
            $r->post('/api/index.php/group_chat/insert_group_chat', 'GroupChat/insert');
            $r->post('/api/index.php/group_chat/update/{id}', 'GroupChat/update');
            $r->get('/api/index.php/group_chat/group_member/join_chat/{user_id}/{group_id}', 'GroupChat/join_chat');
            $r->get('/api/index.php/group_chat/group_member/join_group_chat/{user_id}/{group_id}', 'GroupChat/join_group_chat');

            //USER LOGIN
            $r->post('/api/index.php/user_login/insert_user_device', 'UserLogin/insert');
            $r->get('/api/index.php/user_login/select_all_device', 'UserLogin/get');
            $r->get('/api/index.php/user_login/select_device/{device_id}', 'UserLogin/findByDeviceId');
            $r->get('/api/index.php/user_login/delete_device/{device_id}', 'UserLogin/delete');

            //ATTACHMENT
            $r->post('/api/index.php/attachment/insert_attachment', 'Attachment/insert');
            $r->get('/api/index.php/attachment/select_all_attachment', 'Attachment/get');
            $r->get('/api/index.php/attachment/select_attachment', 'Attachment/select_id');
            $r->get('/api/index.php/attachment/select_group_id/{group_id}', 'Attachment/select_group_id');
            $r->get('/api/index.php/attachment/select_group_message_id/{message_id}', 'Attachment/select_group_message_id');
            $r->get('/api/index.php/attachment/update/{id}', 'Attachment/update');

            //GROUP MEMBER
            $r->post('/api/index.php/group_member/insert_group_member', 'GroupMember/insert');
            $r->get('/api/index.php/group_member/select_group_member/{id}', 'GroupMember/select_id');
            $r->get('/api/index.php/group_member/select_all_member', 'GroupMember/get');
            $r->get('/api/index.php/group_member/select_group_id/{group_id}', 'GroupMember/select_group_id');
            $r->get('/api/index.php/group_member/select_user_id/{user_id}', 'GroupMember/select_user_id');
            $r->get('/api/index.php/group_member/select_push_id/{push_id}', 'GroupMember/select_push_id');
            $r->post('/api/index.php/group_member/update/{id}', 'GroupMember/update');

            //GROUP MESSAGE
            $r->post('/api/index.php/group_message/insert_message', 'GroupMessage/insert');
            $r->get('/api/index.php/group_message/select_all_message', 'GroupMessage/get');
            $r->get('/api/index.php/group_message/select_message/{id}', 'GroupMessage/select_id');
            $r->get('/api/index.php/group_message/select_group_id/{group_id}', 'GroupMessage/select_group_id');
            $r->get('/api/index.php/group_message/select_user_id/{user_id}', 'GroupMessage/select_user_id');
            $r->post('/api/index.php/group_message/update/{id}', 'GroupMessage/update');
            $r->get('/api/index.php/group_message/status_read/{group_id}', 'GroupMessage/status_read');

            //

            //     } else {
            //         return $this->msg(405, 'Token Expired', "gagal", 0);
            //     }
            // } else {
            //     return $this->msg(407, 'Token Not Found', "gagal", 0);
            // }

        });

        // Fetch method and URI from somewhere
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];
        // Strip query string (?foo=bar) and decode URI
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }

        $uri = rawurldecode($uri);
        $explodeUri = explode("/", $uri);
        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
        $connection = $this->core_connect();

        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                return header("HTTP/1.0 404 Not Found");
                // ... 404 Not Found
                break;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                return header("HTTP/1.0 405 Method Not Allowed");
                // ... 405 Method Not Allowed
                break;
            case FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                list($class, $method) = explode("/", $handler, 2);

                if ($explodeUri[3] == 'login') {
                    $result = call_user_func_array(array(new $class($connection), $method), array('users'));

                } else if ($explodeUri[4] == "select_group_message_id") {

                    $result = call_user_func_array(array(new $class($connection), $method), array($vars['message_id'], $explodeUri[3]));

                } else if ($explodeUri[4] == "select_group_id" ||
                    $explodeUri[4] == "status_read") {

                    $result = call_user_func_array(array(new $class($connection), $method), array($vars['group_id'], $explodeUri[3]));

                } else if ($explodeUri[4] == "select_user_id") {

                    $result = call_user_func_array(array(new $class($connection), $method), array($vars['user_id'], $explodeUri[3]));

                } else if ($explodeUri[4] == "select_unit_id") {

                    $result = call_user_func_array(array(new $class($connection), $method), array($vars['unit_id'], $explodeUri[3]));

                } else if ($explodeUri[4] == "select_push_id") {

                    $result = call_user_func_array(array(new $class($connection), $method), array($vars['push_id'], $explodeUri[3]));

                } else if (
                    $explodeUri[4] == "select_device" ||
                    $explodeUri[4] == "delete_device") {

                    $result = call_user_func_array(array(new $class($connection), $method), array($vars['device_id'], $explodeUri[3]));
                } else if (
                    $explodeUri[4] == "select_message" ||
                    $explodeUri[4] == "select_file" ||
                    $explodeUri[4] == "select_group_member" ||
                    $explodeUri[4] == "find_id" ||
                    $explodeUri[4] == "select_group_chat" ||
                    $explodeUri[4] == "get_layout" ||
                    $explodeUri[4] == "update" ||
                    $explodeUri[4] == "insert_data_alignment" ||
                    $explodeUri[4] == "add_page" ||
                    $explodeUri[4] == "insert_form_layout" ||
                    $explodeUri[4] == "insert_gann_data" ||
                    $explodeUri[4] == "insert_page_layout" ||
                    $explodeUri[4] == "update_object" ||
                    $explodeUri[4] == "delete_all_get" ||
                    $explodeUri[4] == "delete" ||
                    $explodeUri[4] == "update_id" ||
                    $explodeUri[4] == "select_id_get" ||
                    $explodeUri[4] == "get_root_parent"
                ) {
                    $result = call_user_func_array(array(new $class($connection), $method), array($vars['id'], $explodeUri[3]));
                } else if (
                    $explodeUri[4] == "get_by_organization"
                ) {
                    $result = call_user_func_array(array(new $class($connection), $method), array($vars['org_id'], $explodeUri[3]));
                } else if (
                    $explodeUri[4] == "get_by_user"
                ) {
                    $result = call_user_func_array(array(new $class($connection), $method), array($vars['user_id'], $explodeUri[3]));
                } else if (
                    $explodeUri[4] == "find_by_page_id"
                ) {
                    $result = call_user_func_array(array(new $class($connection), $method), array($vars['page_id'], $explodeUri[3]));
                } else if (
                    $explodeUri[4] == "download"
                ) {
                    $result = call_user_func_array(array(new $class($connection), $method), array($vars['id_file'], $explodeUri[3]));
                } else if (
                    $explodeUri[4] == "get_by_parent_unit_id" ||
                    $explodeUri[4] == "get_by_parent"
                ) {
                    $result = call_user_func_array(array(new $class($connection), $method), array($vars['parent_id'], $explodeUri[3]));
                } else if (
                    $explodeUri[4] == "insert_message" ||
                    $explodeUri[4] == "select_all_message" ||
                    $explodeUri[4] == "insert_group_member" ||
                    $explodeUri[4] == "select_all_member" ||
                    $explodeUri[4] == "insert_attachment" ||
                    $explodeUri[4] == "select_all_attachment" ||
                    $explodeUri[4] == "select_all_device" ||
                    $explodeUri[4] == "insert_user_device" ||
                    $explodeUri[4] == "select_all_group_chat" ||
                    $explodeUri[4] == "insert_group_chat" ||
                    $explodeUri[4] == "get" ||
                    $explodeUri[4] == "insert" ||
                    $explodeUri[4] == "upload" ||
                    $explodeUri[4] == "insert_alignment" ||
                    $explodeUri[4] == "insert_form_data" ||
                    $explodeUri[4] == "insert_gann" ||
                    $explodeUri[4] == "insert_page_data" ||
                    $explodeUri[4] == "get_leaf_unit" ||
                    $explodeUri[4] == "get_leaf" ||
                    $explodeUri[4] == "insert_object" ||
                    $explodeUri[4] == "select_all_get"
                ) {
                    $result = call_user_func_array(array(new $class($connection), $method), array($explodeUri[3]));
                } else if ($explodeUri[4] == "select_where_get") {
                    $result = call_user_func_array(array(new $class($connection), $method), array($vars['attr'], $vars['val'], $explodeUri[3]));
                } else if ($explodeUri[5] == "join_chat" ||
                    $explodeUri[5] == "join_group_chat") {

                    $result = call_user_func_array(array(new $class($connection), $method), array($vars['user_id'], $vars['group_id'], $explodeUri[3], $explodeUri[4]));

                }
                break;
        }

        // die($result);

        try {
            if ($result == [] || $result == 'Data Kosong') {
                $this->msg(http_response_code(204), 204, $result, "gagal", 0);
            } else if ($httpMethod == 'POST') {
                $this->msg(http_response_code(201), 201, $result, "berhasil", 1);
            } else if ($httpMethod == 'GET') {
                $this->msg(http_response_code(200), 200, $result, "berhasil", 1);
            }

        } catch (\Throwable $th) {
            $this->msg(203, $th, "Terjadi Kesalahan");
        }

    }

}
