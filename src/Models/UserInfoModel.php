<?php

namespace MVC\Models;

use PDO;

class UserInfoModel extends BaseModel {
    public function __construct(PDO $pdo) {
        parent::__construct($pdo, 'user_info');
    }

    public function getById($id) {
        $result = $this->get([['field' => 'user_id', 'operator' => '=', 'value' => $id]]);
        return $result[0] ?? null;
    }

    public function createUserInfo($age, $address, $college, $userId) {
        return $this->insert([
            'age' => $age,
            'address' => $address,
            'college' => $college,
            'user_id' => $userId,
        ]);
    }

    public function updateUserInfo($age, $address, $college, $userId) {
        return $this->update([
            'age' => $age,
            'address' => $address,
            'college' => $college,
        ], [
            ['field' => 'user_id', 'operator' => '=', 'value' => $userId]
        ]);
    }
}
