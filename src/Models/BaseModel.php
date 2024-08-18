<?php

namespace MVC\Models;

use Exception;
use InvalidArgumentException;
use PDO;
use PDOException;

class BaseModel
{
    protected $pdo;
    protected $table;

    public function __construct(PDO $pdo, $table)
    {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    // Get all rows or a specific row
    public function get($conditions = [], $limit = null, $offset = null, $orderBy = null)
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
    
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $key => $value) {
                if (is_array($value) && isset($value['field'], $value['operator'], $value['value'])) {
                    $whereClause[] = "{$value['field']} {$value['operator']} :{$value['field']}";
                    $params[$value['field']] = $value['value'];
                } elseif (is_string($key)) {
                    $whereClause[] = "{$key} = :{$key}";
                    $params[$key] = $value;
                } else {
                    throw new InvalidArgumentException("Invalid condition format");
                }
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
    
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
    
        if ($limit !== null && $offset !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $params['limit'] = $limit;
            $params['offset'] = $offset;
        }
    
        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }
            
            $stmt->execute();
          
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching data: " . $e->getMessage());
        }
    }



    // Insert a new row
    public function insert($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->pdo->prepare($sql);
        foreach ($data as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        return $stmt->execute();
    }

    // Update existing rows
    public function update($data, $conditions)
    {
        $set = implode(', ', array_map(function ($key) {
            return "{$key} = :{$key}";
        }, array_keys($data)));
        $where = implode(' AND ', array_map(function ($condition) {
            return "{$condition['field']} {$condition['operator']} :{$condition['field']}";
        }, $conditions));
        $sql = "UPDATE {$this->table} SET {$set} WHERE {$where}";
        $stmt = $this->pdo->prepare($sql);
        foreach ($data as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        foreach ($conditions as $condition) {
            $stmt->bindValue(":{$condition['field']}", $condition['value']);
        }
        return $stmt->execute();
    }

    // Delete rows
    public function delete($conditions)
    {
        $where = implode(' AND ', array_map(function ($condition) {
            return "{$condition['field']} {$condition['operator']} :{$condition['field']}";
        }, $conditions));
        $sql = "DELETE FROM {$this->table} WHERE {$where}";
        $stmt = $this->pdo->prepare($sql);
        foreach ($conditions as $condition) {
            $stmt->bindValue(":{$condition['field']}", $condition['value']);
        }
        return $stmt->execute();
    }

    // Limit the number of rows
    public function limit($limit, $offset = 0)
    {
        $sql = "SELECT * FROM {$this->table} LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
