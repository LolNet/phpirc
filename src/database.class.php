<?php
/**
 * Database wrapper, extending PDO
 *
 * @author Gussi <gussi@gussi.is>
 */

class database extends PDO {
    public function __construct($dsn, $user = null, $pass = null) {
        parent::__construct($dsn, $user, $pass, [
            self::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        ]);
        $this->setAttribute(self::ATTR_ERRMODE, self::ERRMODE_EXCEPTION);
    }

    /**
     * Overwrite PDO::exec() with our own
     *
     * @param sql               SQL statement
     * @param ...               Optional bind parameters
     */
    public function exec() {
        list($sql, $param) = $this->extract_params(func_get_args());

        if (empty($param)) {
            return parent::exec($sql);
        } else {
            $st = $this->prepare($sql);
            return $st->execute($param);
        }
    }

    /**
     * Get all rows
     *
     * @param sql               SQL statement
     * @param ...               Optional bind parameters
     */
    public function get_all() {
        list($sql, $param) = $this->extract_params(func_get_args());

        $st = $this->prepare($sql);
        $st->execute($param);
        return $st->fetchAll(self::FETCH_ASSOC);
    }

    /**
     * Get single row
     *
     * @param sql               SQL statement
     * @param ...               Optional bind parameters
     */
    public function get_row() {
        list($sql, $param) = $this->extract_params(func_get_args());

        $st = $this->prepare($sql);
        $st->execute($param);
        return $st->fetch(self::FETCH_ASSOC);
    }

    /**
     * Get single field
     *
     * @param sql               SQL statement
     * @param ...               Optional bind parameters
     */
    public function get_field() {
        list($sql, $param) = $this->extract_params(func_get_args());

        $st = $this->prepare($sql);
        $st->execute($param);
        $row = $st->fetch(self::FETCH_NUM);
        return $row[0];
    }

    /**
     * Extract params for all of our sql methods
     */
    private function extract_params($param) {
        $sql = array_shift($param);

        if (!isset($param[0])) {
            $param = []
        } else if (is_array($param[0])) {
            $param = $param[0];
        }

        return [$sql, $param];
    }

    /**
     * Shorthand for lastInsertId()
     */
    public function id() {
        return $this->lastInsertId();
    }
}
