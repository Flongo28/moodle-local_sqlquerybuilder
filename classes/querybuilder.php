<?php
namespace local_sqlquerybuilder;

use stdClass;

class querybuilder {

    protected $table;
    protected $wheres;

    public function table(string $tablename): self {
        $this->table = $tablename;
        $this->wheres = []; // Reset on new table.
        return $this;
    }

    public function get(): array {
        global $DB;

        if (empty($this->wheres)) {
            return $DB->get_records($this->table);
        }

        $conditions = [];
        $params = [];
        foreach ($this->wheres as $index => [$field, $operator, $value]) {
            $param = "param{$index}";
            $conditions[] = "{$field} {$operator} :{$param}";
            $params[$param] = $value;
        }

        $sqlwhere = implode(' AND ', $conditions);

        return $DB->get_records_select($this->table, $sqlwhere, $params);
    }

    public function first(): ?\stdClass {
        global $DB;

        if (empty($this->wheres)) {
            $record = $DB->get_record($this->table, [], '*', IGNORE_MULTIPLE);
        } else {
            // Only supports simple '=' and '<>' for now.
            [$field, $operator, $value] = $this->wheres[0];
            if ($operator === '=') {
                $record = $DB->get_record($this->table, [$field => $value], '*', IGNORE_MULTIPLE);
            } else if ($operator === '<>') {
                $records = $DB->get_records_select($this->table, "{$field} <> :val", ['val' => $value], 'id ASC', '*', 0, 1);
                $record = reset($records) ?: false;
            } else {
                throw new \coding_exception("Operator $operator not supported in first()");
            }
        }

        return $record === false ? null : $record;
    }

    public function where(string $field, string $operator, $value): self {
        $allowed = ['=', '<>'];
        if (!in_array($operator, $allowed, true)) {
            throw new \coding_exception("Operator $operator not supported yet");
        }

        $this->wheres[] = [$field, $operator, $value];
        return $this;
    }

    public function find(int $id): stdClass|bool {
        global $DB;
        return $DB->get_record($this->table, ['id' => $id]);
    }
}
