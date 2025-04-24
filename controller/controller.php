<?php
require_once __DIR__ . '/../model/model.php';
$model = new Model();

// simulate logged-in user (entrepreneur1 = user_id 2)
$currentUser = $model->getUserById(2);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $act = $_POST['action'];

    switch ($act) {
        case 'selectContact':
            $cid  = (int) $_POST['contactId'];
            $msgs = $model->getMessages($currentUser['user_id'], $cid);
            echo json_encode(['success'=>true,'messages'=>$msgs]);
            break;

        case 'sendMessage':
            $cid = (int) $_POST['contactId'];
            $txt = trim($_POST['content'] ?? '');
            if ($txt !== '' && $cid > 0) {
                $msg = $model->sendMessage($currentUser['user_id'], $cid, $txt);
                echo json_encode(['success'=>true,'message'=>$msg]);
            } else {
                echo json_encode(['success'=>false,'error'=>'Empty or invalid']);
            }
            break;

        case 'editMessage':
            $mid = (int) $_POST['messageId'];
            $txt = trim($_POST['content'] ?? '');
            $ok  = $model->editMessage($mid, $txt);
            echo json_encode(['success'=>(bool)$ok]);
            break;

        case 'deleteMessage':
            $mid = (int) $_POST['messageId'];
            $ok  = $model->deleteMessage($mid);
            echo json_encode(['success'=>(bool)$ok]);
            break;
    }
    exit;
}


$users = $model->getUsers();



require __DIR__ . '/../view/view.php';
