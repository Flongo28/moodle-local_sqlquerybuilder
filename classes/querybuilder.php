<?php
namespace local_sqlquerybuilder;

class querybuilder {

    protected $table;

    public function table(string $tablename) {
        // Return $this with the table name set.
        $this->table = $tablename;
        return $this;
    }

    public function get() {
        global $DB;
        return $DB->get_records($this->table);
    }

    public function first(): ?\stdClass {
        global $DB;
        // Fetch the first record (IGNORE_MULTIPLE avoids exceptions if more than one row).
        return $DB->get_record($this->table, [], '*', IGNORE_MULTIPLE);
    }

    public function find(int $id) {
        global $DB;
        return $DB->get_record($this->table, ['id' => $id]);
    }
}