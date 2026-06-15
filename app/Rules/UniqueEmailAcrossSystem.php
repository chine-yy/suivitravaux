<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UniqueEmailAcrossSystem implements Rule
{
    protected $exceptId = null;
    protected $exceptTable = null;

    /**
     * Create a new rule instance.
     *
     * @param mixed $exceptId - ID to exclude from validation (for updates)
     * @param string $exceptTable - Table name of the record to exclude
     */
    public function __construct($exceptId = null, $exceptTable = null)
    {
        $this->exceptId = $exceptId;
        $this->exceptTable = $exceptTable;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $normalizedValue = strtolower(trim((string) $value));
        if ($normalizedValue === '') {
            return false;
        }

        // Tables to check for email uniqueness
        $tables = [
            'users',
            'partenaires',
            'membres',
            'chef_projets',
            'chef_equipes'
        ];

        foreach ($tables as $table) {
            // Skip legacy tables that may not exist in the current schema.
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'email')) {
                continue;
            }

            // Skip the current table and ID if this is an update
            $query = DB::table($table)->whereRaw('LOWER(TRIM(email)) = ?', [$normalizedValue]);

            if (
                $this->exceptId &&
                $this->exceptTable === $table &&
                Schema::hasColumn($table, 'id')
            ) {
                $query->where('id', '!=', $this->exceptId);
            }

            if ($query->exists()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'le mail existe deja sur un compte';
    }
}
