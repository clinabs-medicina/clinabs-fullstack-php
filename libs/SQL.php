<?php
class SQL
{
    public static function fetchTable($connector, $tableName, $Extraquery = null)
    {
        $sql = 'SELECT * FROM ' . $tableName . ($Extraquery != null ? $Extraquery : '');

        $stmt = $connector->prepare($sql);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public static function createInsertStmt($connector, $tableName, $fields)
    {
        $sql = sprintf('INSERT INTO %s ( %s ) VALUES ( :%s )', $tableName, implode(', ', array_keys($fields)), implode(', :', array_keys($fields)));

        $stmt = $connector->prepare($sql);

        foreach ($fields as $key => $val) {
            $stmt->bindValue(':' . $key, $val);
        }

        return $stmt;
    }

    public static function createUpdateStmt($connector, $tableName, $fields, $primaryKey = 'id')
    {
        $map = array_map(function ($key, $value) {
            return "SET {$key} = :{$value}";
        }, array_keys($fields), array_keys($fields));

        $sql = sprintf('UPDATE %s %s WHERE %s = :%s', $tableName, implode(', ', $map), $primaryKey, $primaryKey);

        $stmt = $connector->prepare($sql);

        foreach ($fields as $key => $val) {
            $stmt->bindValue(':' . $key, $val);
        }

        $stmt->bindValue(':' . $primaryKey, $fields[$primaryKey]);

        return $stmt;
    }
}
