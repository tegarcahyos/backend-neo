<?php
include "adodb/adodb.inc.php";
include "vendor/autoload.php";
include "models/organization.php";
include "models/unit.php";
include "models/user.php";
include "models/role.php";
include "models/stakeholders.php";
include "models/employee.php";
include "models/notification.php";
include "models/attachment.php";
include "models/group_chat.php";
include "models/group_member.php";
include "models/group_message.php";
include "models/user_login.php";
include "models/cfu_fu.php";
include "models/request_account.php";
include "models/matrix.php";
include "models/milestone.php";
include "models/strategic_initiative.php";
include "models/main_program.php";
include "models/program_charter.php";
include "models/priority_data.php";
include "models/priority_criteria.php";
include "models/ceo_notes.php";
include "models/user_detail.php";
include "models/migrate_staging.php";
include "models/kpi.php";
include "models/si_target.php";
include "models/unit_target.php";
include "models/upload_file.php";
include "models/expert_judgement.php";
include "models/ahp_criteria.php";
include "models/ahp_featured_program_charter.php";
include "models/ahp_expert_judgement.php";
include "models/approval.php";
include "models/reviewer_plan.php";
include "models/quadran.php";
include "models/periode.php";
include "models/tara.php";
include "models/help.php";
include "login.php";
if (file_exists('settings.php')) {
    include 'settings.php';
} else {
    define('db_username', 'pmo');
    define('db_password', 'pass4pmo');
    define('db_name1', "neotransformer");
    define("db_host", "10.62.161.10");
    define("db_port", "5432");
}

class Router
{
    public $url;
    public $db;
    public $db_transformer;
    public function core_connect()
    {

        $this->db = newADOConnection('pgsql');
        $this->db->connect(db_host, db_username, db_password, db_name1);

        return $this->db;
    }

    // MESSAGES
    public function msg($header, $type = null, $msg, $keterangan, $status)
    {
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
                'error_msg' => $msg,
                'keterangan' => $keterangan . '',
                'status' => $status,
            );
            echo json_encode($array);
        } else if ($type == 506) {
            $array = array(
                'type' => $type,
                'error_msg' => $msg,
                'keterangan' => $keterangan . '',
                'status' => $status,
            );
            echo json_encode($array);
        } else if ($type == 204) {
            $array = array(
                'type' => $type,
                'error_msg' => $msg,
                'keterangan' => $keterangan . '',
                'status' => $status,
            );
            echo json_encode($array);
        } else if ($type == 400) {
            $array = array(
                'type' => $type,
                'error_msg' => $msg,
                'keterangan' => $keterangan . '',
                'status' => $status,
                'data' => $msg,
            );
            echo json_encode($array);
        } else if ($type == 401) {
            $array = array(
                'type' => $type,
                'error_msg' => $msg,
                'keterangan' => $keterangan . '',
                'status' => $status,
                'data' => $msg,
            );
            echo json_encode($array);
        } else if ($type == 422) {
            $array = array(
                'type' => $type,
                'error_msg' => $msg,
                'keterangan' => $keterangan . '',
                'status' => $status,
            );
            echo json_encode($array);
        } else if ($type == 403) {
            $array = array(
                'type' => $type,
                'error_msg' => $msg,
                'keterangan' => $keterangan . '',
                'status' => $status,
            );
            echo json_encode($array);
        } else if ($type == 404) {
            $array = array(
                'type' => $type,
                'error_msg' => $msg,
                'keterangan' => $keterangan . '',
                'status' => $status,
                'data' => $msg,
            );
            echo json_encode($array);
        } else if ($type == 405) {
            $array = array(
                'type' => $type,
                'error_msg' => $msg,
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
            // die(print_r($getHeader));
            $token = "";
            foreach ($getHeader as $key => $value) {
                if ($key == 'Authorization') {
                    $token = $value;
                }
            }

            // --- LOGIN ---
            $r->post('/apineo/index.php/login', 'Login/authenticate');
            $r->post('/apineo/index.php/loginLdap', 'Login/LDAPLogin');
            $r->get('/apineo/index.php/migrate', 'MigrateStaging/get');

            // --- CHECK TOKEN ---
            // if (!empty($token)) {
            //     $passed = $this->check_token($token);
            //     if ($passed == 'true') {
            //FILES
            $r->post('/apineo/index.php/file/upload', 'Upload/upload_file');
            $r->get('/apineo/index.php/upload_file/download/{id_file}', 'Upload/downloadFile');
            $r->get('/apineo/index.php/upload_file/get', 'Upload/get');
            $r->get('/apineo/index.php/upload_file/select_file/{id}', 'Upload/select_id');

            // --- USER ---
            $r->get('/apineo/index.php/users/get', 'User/get');
            $r->get('/apineo/index.php/users/find_id/{id}', 'User/findById');
            $r->get('/apineo/index.php/users/delete/{id}', 'User/delete');
            $r->post('/apineo/index.php/users/insert', 'User/insert');
            $r->post('/apineo/index.php/users/update/{id}', 'User/update');

            // --- MILESTONE ---
            $r->get('/apineo/index.php/milestone/get', 'Milestone/get');
            $r->get('/apineo/index.php/milestone/find_id/{id}', 'Milestone/findById');
            $r->get('/apineo/index.php/milestone/delete/{id}', 'Milestone/delete');
            $r->post('/apineo/index.php/milestone/insert', 'Milestone/insert');
            $r->post('/apineo/index.php/milestone/update/{id}', 'Milestone/update');

            // Stakeholders
            $r->get('/apineo/index.php/stakeholders/get', 'Stakeholders/get');
            $r->get('/apineo/index.php/stakeholders/find_id/{id}', 'Stakeholders/findById');
            $r->get('/apineo/index.php/stakeholders/find_unit_id/{id}', 'Stakeholders/findByUnitId');
            $r->get('/apineo/index.php/stakeholders/delete/{id}', 'Stakeholders/delete');
            $r->post('/apineo/index.php/stakeholders/insert', 'Stakeholders/insert');
            $r->post('/apineo/index.php/stakeholders/update/{id}', 'Stakeholders/update');

            // --- ACCOUNT REQUEST ---
            $r->get('/apineo/index.php/request_account/get', 'RequestAccount/get');
            $r->get('/apineo/index.php/request_account/find_id/{id}', 'RequestAccount/findById');
            $r->get('/apineo/index.php/request_account/delete/{id}', 'RequestAccount/delete');
            $r->post('/apineo/index.php/request_account/insert', 'RequestAccount/insert');
            $r->post('/apineo/index.php/request_account/update/{id}', 'RequestAccount/update');

            // --- EXPERT JUDGEMENT ---
            $r->get('/apineo/index.php/expert_judgement/get', 'ExpertJudgement/get');
            $r->get('/apineo/index.php/expert_judgement/find_id/{id}', 'ExpertJudgement/findById');
            $r->get('/apineo/index.php/expert_judgement/get_by_user/{user_id}', 'ExpertJudgement/findByUserId');
            $r->get('/apineo/index.php/expert_judgement/delete/{id}', 'ExpertJudgement/delete');
            $r->get('/apineo/index.php/expert_judgement/delete_by_user/{user_id}', 'ExpertJudgement/deleteByUserId');
            $r->post('/apineo/index.php/expert_judgement/insert', 'ExpertJudgement/insert');
            $r->post('/apineo/index.php/expert_judgement/update/{id}', 'ExpertJudgement/update');

            // --- AHP ---
            $r->get('/apineo/index.php/ahp_criteria/get', 'AHPCriteria/get');
            $r->get('/apineo/index.php/ahp_criteria/get_by_organization/{org_id}', 'AHPCriteria/getByOrganization');
            $r->get('/apineo/index.php/ahp_criteria/get_by_periode_id/{periode_id}', 'AHPCriteria/getByPeriode');
            $r->get('/apineo/index.php/ahp_criteria/find_id/{id}', 'AHPCriteria/findById');
            $r->get('/apineo/index.php/ahp_criteria/delete/{id}', 'AHPCriteria/delete');
            $r->post('/apineo/index.php/ahp_criteria/insert', 'AHPCriteria/insert');
            $r->post('/apineo/index.php/ahp_criteria/update/{id}', 'AHPCriteria/update');

            // AHP Featured Porgram Charter
            $r->get('/apineo/index.php/ahp_featured_program_charter/get', 'AHPFeaturedPC/get');
            $r->get('/apineo/index.php/ahp_featured_program_charter/get_by_organization/{org_id}', 'AHPFeaturedPC/getByOrganization');
            $r->get('/apineo/index.php/ahp_featured_program_charter/get_by_periode_id/{periode_id}', 'AHPFeaturedPC/getByPeriode');
            $r->get('/apineo/index.php/ahp_featured_program_charter/get_by_criteria/{id}', 'AHPFeaturedPC/getByCriteria');
            $r->get('/apineo/index.php/ahp_featured_program_charter/delete/{id}', 'AHPFeaturedPC/delete');
            $r->post('/apineo/index.php/ahp_featured_program_charter/insert', 'AHPFeaturedPC/insert');
            $r->post('/apineo/index.php/ahp_featured_program_charter/update/{id}', 'AHPFeaturedPC/update');

            // AHP Expert Judgement
            $r->get('/apineo/index.php/ahp_expert_judgement/get', 'AHPExpertJudgement/get');
            $r->get('/apineo/index.php/ahp_expert_judgement/find_id/{id}', 'AHPExpertJudgement/findById');
            $r->get('/apineo/index.php/ahp_expert_judgement/delete/{id}', 'AHPExpertJudgement/delete');
            $r->post('/apineo/index.php/ahp_expert_judgement/insert', 'AHPExpertJudgement/insert');
            $r->post('/apineo/index.php/ahp_expert_judgement/update/{id}', 'AHPExpertJudgement/update');

            // --- QUADRAn ---
            $r->get('/apineo/index.php/quadran/get', 'Quadran/get');
            $r->get('/apineo/index.php/quadran/find_id/{id}', 'Quadran/findById');
            $r->get('/apineo/index.php/quadran/get_by_user/{user_id}', 'Quadran/findByUserId');
            $r->get('/apineo/index.php/quadran/delete/{id}', 'Quadran/delete');
            $r->get('/apineo/index.php/quadran/delete_by_user/{user_id}', 'Quadran/deleteByUserId');
            $r->post('/apineo/index.php/quadran/insert', 'Quadran/insert');
            $r->post('/apineo/index.php/quadran/update/{id}', 'Quadran/update');

            // SI
            $r->get('/apineo/index.php/strategic_initiative/get', 'StraIn/get');
            $r->get('/apineo/index.php/strategic_initiative/find_id/{id}', 'StraIn/findById');
            $r->get('/apineo/index.php/strategic_initiative/get_leaf', 'StraIn/getLeaf');
            $r->get('/apineo/index.php/strategic_initiative/get_by_parent/{parent_id}', 'StraIn/getByParent');
            $r->get('/apineo/index.php/strategic_initiative/get_by_periode_id/{periode_id}', 'StraIn/select_periode');
            $r->get('/apineo/index.php/strategic_initiative/get_leaf_by_root_id/{id}', 'StraIn/getLeafByRootId');
            $r->get('/apineo/index.php/strategic_initiative/delete/{id}', 'StraIn/delete');
            $r->post('/apineo/index.php/strategic_initiative/insert', 'StraIn/insert');
            $r->post('/apineo/index.php/strategic_initiative/update/{id}', 'StraIn/update');

            // Main Program
            $r->get('/apineo/index.php/main_program/get', 'MainProgram/get');
            $r->get('/apineo/index.php/main_program/find_id/{id}', 'MainProgram/findById');
            $r->get('/apineo/index.php/main_program/delete/{id}', 'MainProgram/delete');
            $r->post('/apineo/index.php/main_program/insert', 'MainProgram/insert');
            $r->post('/apineo/index.php/main_program/update/{id}', 'MainProgram/update');

            //Employee
            $r->get('/apineo/index.php/employee/get', 'Employee/get');
            $r->get('/apineo/index.php/employee/find/{value}', 'Employee/find');

            // Approval
            $r->get('/apineo/index.php/approval/get', 'Approval/get');
            $r->get('/apineo/index.php/approval/find_id/{id}', 'Approval/findById');
            $r->get('/apineo/index.php/approval/get_pc_by_user/{user_id}', 'Approval/getPCByUserId');
            $r->get('/apineo/index.php/approval/find_by_pc/{pc_id}', 'Approval/findByPCId');
            $r->get('/apineo/index.php/approval/delete/{id}', 'Approval/delete');
            $r->post('/apineo/index.php/approval/insert', 'Approval/insert');
            $r->post('/apineo/index.php/approval/update/{id}', 'Approval/update');

            // Reviewer Plan
            $r->get('/apineo/index.php/reviewer_plan/get', 'ReviewerPlan/get');
            $r->get('/apineo/index.php/reviewer_plan/find_id/{id}', 'ReviewerPlan/findById');
            $r->get('/apineo/index.php/reviewer_plan/delete/{id}', 'ReviewerPlan/delete');
            $r->post('/apineo/index.php/reviewer_plan/insert', 'ReviewerPlan/insert');
            $r->post('/apineo/index.php/reviewer_plan/update/{id}', 'ReviewerPlan/update');

            // Tara
            $r->get('/apineo/index.php/tara/get', 'Tara/get');
            $r->get('/apineo/index.php/tara/find_id/{id}', 'Tara/findById');
            $r->get('/apineo/index.php/tara/delete/{id}', 'Tara/delete');
            $r->post('/apineo/index.php/tara/insert', 'Tara/insert');
            $r->post('/apineo/index.php/tara/update/{id}', 'Tara/update');

            // CEO Notes
            $r->get('/apineo/index.php/ceo_notes/get', 'CeoNotes/get');
            $r->get('/apineo/index.php/ceo_notes/find_id/{id}', 'CeoNotes/findById');
            $r->get('/apineo/index.php/ceo_notes/delete/{id}', 'CeoNotes/delete');
            $r->post('/apineo/index.php/ceo_notes/insert', 'CeoNotes/insert');
            $r->post('/apineo/index.php/ceo_notes/update/{id}', 'CeoNotes/update');

            // CFU FU
            $r->get('/apineo/index.php/cfu_fu/get', 'CfuFu/get');
            $r->get('/apineo/index.php/cfu_fu/get_users/{id}', 'CfuFu/getAllUsers');
            $r->get('/apineo/index.php/cfu_fu/get_units/{id}', 'CfuFu/getAllUnits');
            $r->get('/apineo/index.php/cfu_fu/find_id/{id}', 'CfuFu/findById');
            $r->get('/apineo/index.php/cfu_fu/get_by_organization/{org_id}', 'CfuFu/findByOrgId');
            $r->get('/apineo/index.php/cfu_fu/delete/{id}', 'CfuFu/delete');
            $r->post('/apineo/index.php/cfu_fu/insert', 'CfuFu/insert');
            $r->post('/apineo/index.php/cfu_fu/update/{id}', 'CfuFu/update');

            // User Detail
            $r->get('/apineo/index.php/user_detail/get', 'UserDetail/get');
            $r->get('/apineo/index.php/user_detail/find_id/{id}', 'UserDetail/findById');
            $r->get('/apineo/index.php/user_detail/get_by_user/{user_id}', 'UserDetail/getByUser');
            $r->get('/apineo/index.php/user_detail/delete/{id}', 'UserDetail/delete');
            $r->post('/apineo/index.php/user_detail/insert', 'UserDetail/insert');
            $r->post('/apineo/index.php/user_detail/update/{id}', 'UserDetail/update');
            $r->post('/apineo/index.php/user_detail/update_user_id/{user_id}', 'UserDetail/update_user_id');

            // Criteria Priority
            $r->get('/apineo/index.php/criteria_priority/get', 'PriorityCriteria/get');
            $r->get('/apineo/index.php/criteria_priority/find_id/{id}', 'PriorityCriteria/findById');
            $r->get('/apineo/index.php/criteria_priority/delete/{id}', 'PriorityCriteria/delete');
            $r->post('/apineo/index.php/criteria_priority/insert', 'PriorityCriteria/insert');
            $r->post('/apineo/index.php/criteria_priority/update/{id}', 'PriorityCriteria/update');

            // Data Priority
            $r->get('/apineo/index.php/data_priority/get', 'PriorityData/get');
            $r->get('/apineo/index.php/data_priority/find_id/{id}', 'PriorityData/findById');
            $r->get('/apineo/index.php/data_priority/delete/{id}', 'PriorityData/delete');
            $r->post('/apineo/index.php/data_priority/insert', 'PriorityData/insert');
            $r->post('/apineo/index.php/data_priority/update/{id}', 'PriorityData/update');

            // Program Charter
            $r->get('/apineo/index.php/program_charter/get', 'ProgramCharter/get');
            $r->get('/apineo/index.php/program_charter/get_by_cfu/{id}', 'ProgramCharter/getByCfu');
            $r->get('/apineo/index.php/program_charter/get_by_root/{id}', 'ProgramCharter/getByRootUnit');
            $r->get('/apineo/index.php/program_charter/find_id/{id}', 'ProgramCharter/findById');

            $r->get('/apineo/index.php/program_charter/delete/{id}', 'ProgramCharter/delete');
            $r->post('/apineo/index.php/program_charter/insert', 'ProgramCharter/insert');
            $r->post('/apineo/index.php/program_charter/update/{id}', 'ProgramCharter/update');

            // ROLE
            $r->get('/apineo/index.php/role/get', 'Role/get');
            $r->get('/apineo/index.php/role/find_id/{id}', 'Role/findById');
            $r->get('/apineo/index.php/role/delete/{id}', 'Role/delete');
            $r->post('/apineo/index.php/role/insert', 'Role/insert');
            $r->post('/apineo/index.php/role/update/{id}', 'Role/update');

            // KPI
            $r->get('/apineo/index.php/kpi/get', 'Kpi/get');
            $r->get('/apineo/index.php/kpi/find_id/{id}', 'Kpi/findById');
            $r->get('/apineo/index.php/kpi/get_leaf', 'Kpi/getLeafKpi');
            $r->get('/apineo/index.php/kpi/get_by_parent/{parent_id}', 'Kpi/getByParent');
            $r->get('/apineo/index.php/kpi/delete/{id}', 'Kpi/delete');
            $r->post('/apineo/index.php/kpi/insert', 'Kpi/insert');
            $r->post('/apineo/index.php/kpi/update/{id}', 'Kpi/update');

            // SI TARGET
            $r->get('/apineo/index.php/si_target/get', 'SITarget/get');
            $r->get('/apineo/index.php/si_target/find_id/{id}', 'SITarget/findById');
            $r->get('/apineo/index.php/si_target/delete/{id}', 'SITarget/delete');
            $r->post('/apineo/index.php/si_target/insert', 'SITarget/insert');
            $r->post('/apineo/index.php/si_target/update/{id}', 'SITarget/update');

            // UNIT TARGET
            $r->get('/apineo/index.php/unit_target/get', 'UnitTarget/get');
            $r->get('/apineo/index.php/unit_target/find_id/{id}', 'UnitTarget/findById');
            $r->get('/apineo/index.php/unit_target/delete/{id}', 'UnitTarget/delete');
            $r->post('/apineo/index.php/unit_target/insert', 'UnitTarget/insert');
            $r->post('/apineo/index.php/unit_target/update/{id}', 'UnitTarget/update');

            // NOTIFICATION
            $r->get('/apineo/index.php/log_notification/read_notification/{id}', 'Notification/readNotification');
            $r->get('/apineo/index.php/log_notification/check_notif/{id}', 'Notification/checkNotif');

            // ORGANIZATION
            $r->get('/apineo/index.php/organization/get', 'Organization/get');
            $r->get('/apineo/index.php/organization/find_id/{id}', 'Organization/findById');
            $r->get('/apineo/index.php/organization/delete/{id}', 'Organization/delete');
            $r->post('/apineo/index.php/organization/insert', 'Organization/insert');
            $r->post('/apineo/index.php/organization/update/{id}', 'Organization/update');

            // HELP
            $r->get('/apineo/index.php/help/get', 'Help/get');
            $r->get('/apineo/index.php/help/find_id/{id}', 'Help/findById');
            $r->get('/apineo/index.php/help/delete/{id}', 'Help/delete');
            $r->post('/apineo/index.php/help/insert', 'Help/insert');
            $r->post('/apineo/index.php/help/update/{id}', 'Help/update');

            // UNIT
            $r->get('/apineo/index.php/unit/get', 'Unit/get');
            $r->get('/apineo/index.php/unit/get_leaf_unit', 'Unit/getLeafUnit');
            $r->get('/apineo/index.php/unit/find_id/{id}', 'Unit/findById');
            $r->get('/apineo/index.php/unit/get_users/{id}', 'Unit/getAllUsers');
            $r->get('/apineo/index.php/unit/get_by_parent_unit_id/{parent_id}', 'Unit/getByParent');
            $r->get('/apineo/index.php/unit/get_root_parent/{id}', 'Unit/getRootParent');
            $r->get('/apineo/index.php/unit/get_by_organization/{org_id}', 'Unit/findByOrgId');
            $r->get('/apineo/index.php/unit/delete/{id}', 'Unit/delete');
            $r->post('/apineo/index.php/unit/insert', 'Unit/insert');
            $r->post('/apineo/index.php/unit/update/{id}', 'Unit/update');

            // MATRIX
            $r->get('/apineo/index.php/matrix/get', 'Matrix/get');
            $r->get('/apineo/index.php/matrix/find_id/{id}', 'Matrix/findById');
            $r->get('/apineo/index.php/matrix/select_where_get/{attr}/{val}', 'Matrix/getByValues');
            $r->get('/apineo/index.php/matrix/delete/{id}', 'Matrix/delete');
            $r->post('/apineo/index.php/matrix/insert', 'Matrix/insert');
            $r->post('/apineo/index.php/matrix/update/{id}', 'Matrix/update');

            //PERIOD
            $r->get('/apineo/index.php/periode/get', 'Periode/get');
            $r->get('/apineo/index.php/periode/select/{id}', 'Periode/select_id');
            $r->get('/apineo/index.php/periode/select_org_id/{org_id}', 'Periode/select_org_id');
            $r->get('/apineo/index.php/periode/delete/{id}', 'Periode/delete');
            $r->post('/apineo/index.php/periode/insert', 'Periode/insert');
            $r->post('/apineo/index.php/periode/update/{id}', 'Periode/update');

            //GROUP CHAT
            $r->get('/apineo/index.php/group_chat/select_group_chat/{id}', 'GroupChat/findById');
            $r->get('/apineo/index.php/group_chat/select_by_title/{title}', 'GroupChat/findByTitle');
            $r->get('/apineo/index.php/group_chat/select_all_group_chat', 'GroupChat/get');
            $r->get('/apineo/index.php/group_chat/delete/{id}', 'GroupChat/delete');
            $r->post('/apineo/index.php/group_chat/insert_group_chat', 'GroupChat/insert');
            $r->post('/apineo/index.php/group_chat/update/{id}', 'GroupChat/update');
            $r->get('/apineo/index.php/group_chat/group_member/join_chat/{user_id}/{group_id}', 'GroupChat/join_chat');
            $r->get('/apineo/index.php/group_chat/group_member/join_group_chat/{user_id}/{group_id}', 'GroupChat/join_group_chat');

            //USER LOGIN
            $r->get('/apineo/index.php/user_login/select_all_device', 'UserLogin/get');
            $r->get('/apineo/index.php/user_login/select_device/{device_id}', 'UserLogin/findByDeviceId');
            $r->get('/apineo/index.php/user_login/delete_device/{device_id}', 'UserLogin/delete');
            $r->post('/apineo/index.php/user_login/insert_user_device', 'UserLogin/insert');

            //ATTACHMENT
            $r->post('/apineo/index.php/attachment/insert_attachment', 'Attachment/insert');
            $r->get('/apineo/index.php/attachment/select_all_attachment', 'Attachment/get');
            $r->get('/apineo/index.php/attachment/select_attachment/{id}', 'Attachment/select_id');
            $r->get('/apineo/index.php/attachment/select_group_id/{group_id}', 'Attachment/select_group_id');
            $r->get('/apineo/index.php/attachment/select_group_message_id/{message_id}', 'Attachment/select_group_message_id');
            $r->post('/apineo/index.php/attachment/update/{id}', 'Attachment/update');

            //GROUP MEMBER
            $r->post('/apineo/index.php/group_member/insert_group_member', 'GroupMember/insert');
            $r->get('/apineo/index.php/group_member/select_group_member/{id}', 'GroupMember/select_id');
            $r->get('/apineo/index.php/group_member/select_all_member', 'GroupMember/get');
            $r->get('/apineo/index.php/group_member/select_group_id/{group_id}', 'GroupMember/select_group_id');
            $r->get('/apineo/index.php/group_member/select_user_id/{user_id}', 'GroupMember/select_user_id');
            $r->get('/apineo/index.php/group_member/select_push_id/{push_id}', 'GroupMember/select_push_id');
            $r->post('/apineo/index.php/group_member/update/{id}', 'GroupMember/update');

            //GROUP MESSAGE
            $r->post('/apineo/index.php/group_message/insert_message', 'GroupMessage/insert');
            $r->get('/apineo/index.php/group_message/select_all_message', 'GroupMessage/get');
            $r->get('/apineo/index.php/group_message/select_message/{id}', 'GroupMessage/select_id');
            $r->get('/apineo/index.php/group_message/select_group_id/{group_id}', 'GroupMessage/select_group_id');
            $r->get('/apineo/index.php/group_message/select_user_id/{user_id}', 'GroupMessage/select_user_id');
            $r->post('/apineo/index.php/group_message/update/{id}', 'GroupMessage/update');
            $r->get('/apineo/index.php/group_message/status_read/{group_id}', 'GroupMessage/status_read');

            // } else {
            //     return $this->msg(http_response_code(401), 401, 'Unauthorized', "gagal", 0);
            // }
            // }

        });
        header('Content-Type: application/json');

        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 1000');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                // may also be using PUT, PATCH, HEAD etc
                header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
            }

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: Accept, Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token, Authorization");
            }
            exit(0);
        }

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
                $result = "404";
                // ... 404 Not Found
                break;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                $result = "405";
                // ... 405 Method Not Allowed
                break;
            case FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                list($class, $method) = explode("/", $handler, 2);

                if ($explodeUri[3] == 'login') {
                    $result = call_user_func_array(array(new $class($connection), $method), array('users'));

                } else if (
                    $explodeUri[3] == "loginLdap") {
                    $result = call_user_func_array(array(new $class($connection), $method), array('employee'));
                } else if (
                    $explodeUri[3] == "migrate") {
                    $result = call_user_func_array(array(new $class($connection), $method), array('program_charter'));

                } else if ($explodeUri[4] == "select_group_message_id") {
                    $result = call_user_func_array(array(new $class($connection), $method), array($vars['message_id'], $explodeUri[3]));

                } else if ($explodeUri[4] == "select_org_id") {
                    $result = call_user_func_array(array(new $class($connection), $method), array($vars['org_id'], $explodeUri[3]));

                } else if ($explodeUri[4] == "select_group_id" ||
                    $explodeUri[4] == "status_read") {

                    $result = call_user_func_array(array(new $class($connection), $method), array($vars['group_id'], $explodeUri[3]));

                } else if (
                    $explodeUri[4] == "select_user_id" ||
                    $explodeUri[4] == "update_user_id" ||
                    $explodeUri[4] == "get_pc_by_user"
                ) {

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
                    $explodeUri[4] == "select_attachment" ||
                    $explodeUri[4] == "select" ||
                    $explodeUri[4] == "find_id" ||
                    $explodeUri[4] == "get_by_cfu" ||
                    $explodeUri[4] == "get_by_root" ||
                    $explodeUri[4] == "get_users" ||
                    $explodeUri[4] == "get_units" ||
                    $explodeUri[4] == "find_unit_id" ||
                    $explodeUri[4] == "check_notif" ||
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
                    $explodeUri[4] == "get_root_parent" ||
                    $explodeUri[4] == "get_leaf_by_root_id" ||
                    $explodeUri[4] == "read_notification" ||
                    $explodeUri[4] == "get_by_criteria"
                ) {
                    $result = call_user_func_array(array(new $class($connection), $method), array($vars['id'], $explodeUri[3]));
                } else if (
                    $explodeUri[4] == "get_by_organization"
                ) {
                    $result = call_user_func_array(array(new $class($connection), $method), array($vars['org_id'], $explodeUri[3]));
                } else if (
                    $explodeUri[4] == "get_by_user" ||
                    $explodeUri[4] == "delete_by_user"
                ) {
                    $result = call_user_func_array(array(new $class($connection), $method), array($vars['user_id'], $explodeUri[3]));
                } else if (
                    $explodeUri[4] == "find_by_page_id"
                ) {
                    $result = call_user_func_array(array(new $class($connection), $method), array($vars['page_id'], $explodeUri[3]));
                } else if (
                    $explodeUri[4] == "get_by_periode_id"
                ) {
                    $result = call_user_func_array(array(new $class($connection), $method), array($vars['periode_id'], $explodeUri[3]));
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
                    $explodeUri[4] == "select_all" ||
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
                    $explodeUri[4] == "select_all_get" ||
                    $explodeUri[4] == "get_all_parent"
                ) {
                    $result = call_user_func_array(array(new $class($connection), $method), array($explodeUri[3]));
                } else if ($explodeUri[4] == "select_where_get") {
                    $result = call_user_func_array(array(new $class($connection), $method), array($vars['attr'], $vars['val'], $explodeUri[3]));
                } else if ($explodeUri[5] == "join_chat" ||
                    $explodeUri[5] == "join_group_chat") {

                    $result = call_user_func_array(array(new $class($connection), $method), array($vars['user_id'], $vars['group_id'], $explodeUri[3], $explodeUri[4]));

                } else if (
                    $explodeUri[4] == "find_by_pc") {
                    $result = call_user_func_array(array(new $class($connection), $method), array($vars['pc_id'], $explodeUri[3]));
                } else if (
                    $explodeUri[4] == "find") {
                    $result = call_user_func_array(array(new $class($connection), $method), array($vars['value'], $explodeUri[3]));
                } else if (
                    $explodeUri[4] == "select_by_title") {
                    $result = call_user_func_array(array(new $class($connection), $method), array($vars['title'], $explodeUri[3]));
                }
                break;
        }

        // die($result == 'Data Kosong');

        try {
            if ($result == [] || $result === "Data Kosong" || $result == '0') {
                $this->msg(http_response_code(404), 404, $result, "gagal", 0);
            } else if ($result == "422") {
                $this->msg(http_response_code(422), 422, 'Incomplete Data', "gagal", 0);
            } else if ($result == "403") {
                $this->msg(http_response_code(403), 403, 'You Can\'t Delete This Data ', "gagal", 0);
            } else if ($result == "203") {
                $this->msg(http_response_code(203), 203, 'Account Not Found', "gagal", 0);
            } else if ($result == "404") {
                $this->msg(http_response_code(404), 404, 'Page Not Found', "gagal", 0);
            } else if ($result == "405") {
                $this->msg(http_response_code(405), 405, 'Method Not Allowed', "gagal", 0);
            } else if ($result == "506") {
                $this->msg(http_response_code(506), 506, 'Wrong Password', "gagal", 0);
            } else {
                $this->msg(http_response_code(200), 200, $result, "berhasil", 1);
            }

        } catch (\Throwable $th) {
            $this->msg(203, $th, "Terjadi Kesalahan");
        }

    }

}
