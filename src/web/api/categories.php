<?php
declare(strict_types=1);

require_once __DIR__ . '/../../api/includes/db_config.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                $stmt = $pdo->prepare(
                    "SELECT categoryID AS id, categoryName AS name, categoryDesc AS description
                     FROM categories WHERE categoryID = ?"
                );
                $stmt->execute([$_GET['id']]);
                $row = $stmt->fetch();
                if (!$row) { send_json(['success'=>false,'error'=>'Category not found'],404); exit; }
                send_json(['success'=>true,'data'=>$row]); exit;
            }
            $stmt = $pdo->query(
                "SELECT categoryID AS id, categoryName AS name, categoryDesc AS description
                 FROM categories ORDER BY categoryName ASC"
            );
            send_json(['success'=>true,'data'=>$stmt->fetchAll()]);
            break;

        case 'POST':
            $in = json_input();
            if (empty($in['name'])) {
                send_json(['success'=>false,'error'=>'Missing required field: name'],400); exit;
            }
            $desc = $in['description'] ?? '';
            $stmt = $pdo->prepare("INSERT INTO categories (categoryName, categoryDesc) VALUES (?, ?)");
            $stmt->execute([$in['name'], $desc]);
            send_json(['success'=>true,'id'=>$pdo->lastInsertId(),'message'=>'Category created'],201);
            break;

        case 'PUT':
            $in = json_input();
            if (empty($in['id']) || empty($in['name'])) {
                send_json(['success'=>false,'error'=>'Missing required fields: id, name'],400); exit;
            }
            $desc = $in['description'] ?? '';
            $stmt = $pdo->prepare("UPDATE categories SET categoryName = ?, categoryDesc = ? WHERE categoryID = ?");
            $stmt->execute([$in['name'], $desc, $in['id']]);
            send_json(['success'=>true,'message'=>'Category updated']);
            break;

        case 'DELETE':
            $in = json_input();
            if (empty($in['id'])) {
                send_json(['success'=>false,'error'=>'Missing required field: id'],400); exit;
            }
            // Prevent deleting categories in use by inventory (FK may also enforce)
            $stmt = $pdo->prepare("SELECT 1 FROM inventory WHERE categoryID = ? LIMIT 1");
            $stmt->execute([$in['id']]);
            if ($stmt->fetch()) {
                send_json(['success'=>false,'error'=>'Category is in use by inventory items'],409); exit;
            }
            $stmt = $pdo->prepare("DELETE FROM categories WHERE categoryID = ?");
            $stmt->execute([$in['id']]);
            send_json(['success'=>true,'message'=>'Category deleted']);
            break;

        case 'OPTIONS':
            http_response_code(204);
            break;

        default:
            send_json(['success'=>false,'error'=>'Method not allowed'],405);
    }
} catch (PDOException $e) {
    send_json(['success'=>false,'error'=>'Database error'],500);
}
