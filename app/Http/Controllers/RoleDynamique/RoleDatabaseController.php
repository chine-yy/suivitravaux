<?php

namespace App\Http\Controllers\RoleDynamique;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RoleDatabaseController extends Controller
{
    public function index()
    {
        $this->authorizeAnyDatabaseAccess();

        $tables = $this->getDatabaseTables();
        $excludedTables = ['migrations', 'personal_access_tokens', 'password_reset_tokens', 'password_reset_otps', 'admins', 'superadmins'];
        $clearableTables = array_diff($tables, $excludedTables);

        $tableData = [];
        foreach ($clearableTables as $table) {
            $tableData[] = [
                'name' => $table,
                'count' => DB::table($table)->count(),
            ];
        }

        return view('role-dynamique.database.index', [
            'tableData' => $tableData,
            'canViewDatabase' => $this->hasAnyDatabasePermission(['view-base-de-donnees']),
            'canClearDatabase' => $this->hasAnyDatabasePermission(['clear-base-de-donnees']),
            'canBackupDatabase' => $this->hasAnyDatabasePermission(['sauvegarde-base-de-donnees']),
            'canExportDatabase' => $this->hasAnyDatabasePermission(['sauvegarde-base-de-donnees']),
        ]);
    }

    public function clearTable(Request $request)
    {
        $this->authorizeDatabasePermission('clear-base-de-donnees');

        $request->validate([
            'table_name' => 'required|string',
            'confirmation' => 'required|string|in:CONFIRMER',
        ]);

        $tableName = $request->table_name;
        $excludedTables = ['migrations', 'personal_access_tokens', 'password_reset_tokens', 'password_reset_otps', 'admins', 'superadmins'];

        if (in_array($tableName, $excludedTables, true)) {
            return back()->with('error', "Vous ne pouvez pas vider la table {$tableName}.");
        }

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table($tableName)->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return back()->with('success', "La table {$tableName} a été vidée avec succès.");
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return back()->with('error', 'Erreur lors du vidage de la table: ' . $e->getMessage());
        }
    }

    public function clearAll(Request $request)
    {
        $this->authorizeDatabasePermission('clear-base-de-donnees');

        $request->validate([
            'confirmation' => 'required|string|in:SUPPRIMER_TOUT',
        ]);

        try {
            $tables = $this->getDatabaseTables();
            $excludedTables = ['migrations', 'personal_access_tokens', 'password_reset_tokens', 'password_reset_otps', 'admins', 'superadmins'];

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            foreach ($tables as $table) {
                if (!in_array($table, $excludedTables, true)) {
                    DB::table($table)->truncate();
                }
            }
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return back()->with('success', 'Toutes les tables (excepté les tables système) ont été supprimées avec succès.');
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return back()->with('error', 'Erreur lors de la suppression complète: ' . $e->getMessage());
        }
    }

    public function backup(): StreamedResponse
    {
        $this->authorizeDatabasePermission('sauvegarde-base-de-donnees');

        $tables = $this->getDatabaseTables();
        $fileName = 'sauvegarde-' . now()->format('Y-m-d_H-i-s') . '.sql';

        return response()->streamDownload(function () use ($tables) {
            echo "-- Sauvegarde de la base de données\n";
            echo '-- Générée le ' . now()->format('Y-m-d H:i:s') . "\n\n";
            echo "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $table) {
                $createTable = DB::select("SHOW CREATE TABLE `{$table}`");
                $createStatement = $createTable[0]->{'Create Table'} ?? null;

                if (!$createStatement) {
                    continue;
                }

                echo "DROP TABLE IF EXISTS `{$table}`;\n";
                echo $createStatement . ";\n\n";

                $rows = DB::table($table)->get();
                foreach ($rows as $row) {
                    $values = array_map(function ($value) {
                        if ($value === null) {
                            return 'NULL';
                        }
                        if (is_bool($value)) {
                            return $value ? '1' : '0';
                        }
                        return "'" . str_replace("'", "''", (string) $value) . "'";
                    }, array_values((array) $row));

                    echo "INSERT INTO `{$table}` VALUES (" . implode(', ', $values) . ");\n";
                }

                if ($rows->isNotEmpty()) {
                    echo "\n";
                }
            }

            echo "SET FOREIGN_KEY_CHECKS=1;\n";
        }, $fileName, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    public function exportTable(Request $request): StreamedResponse
    {
        $this->authorizeDatabasePermission('sauvegarde-base-de-donnees');

        $request->validate([
            'table' => 'required|string',
        ]);

        $tableName = $request->table;
        $excludedTables = ['migrations', 'personal_access_tokens', 'password_reset_tokens', 'password_reset_otps', 'admins', 'superadmins'];

        if (in_array($tableName, $excludedTables, true)) {
            abort(403, "Cette table ne peut pas être exportée.");
        }

        $fileName = $tableName . '-' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return response()->streamDownload(function () use ($tableName) {
            $data = DB::table($tableName)->get();
            $output = fopen('php://output', 'w');

            if ($data->isNotEmpty()) {
                fwrite($output, "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n");
                fwrite($output, "<?mso-application progid=\"Excel.Sheet\"?>\n");
                fwrite($output, "<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\" ");
                fwrite($output, "xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\">\n");
                fwrite($output, "<Worksheet ss:Name=\"" . $tableName . "\">\n<Table>\n");

                $headers = array_keys((array) $data->first());
                fwrite($output, "<Row>\n");
                foreach ($headers as $header) {
                    fwrite($output, "<Cell><Data ss:Type=\"String\">" . htmlspecialchars($header) . "</Data></Cell>\n");
                }
                fwrite($output, "</Row>\n");

                foreach ($data as $row) {
                    $values = array_values((array) $row);
                    fwrite($output, "<Row>\n");
                    foreach ($values as $value) {
                        $value = is_null($value) ? '' : (string) $value;
                        fwrite($output, "<Cell><Data ss:Type=\"String\">" . htmlspecialchars($value) . "</Data></Cell>\n");
                    }
                    fwrite($output, "</Row>\n");
                }

                fwrite($output, "</Table>\n</Worksheet>\n</Workbook>\n");
            }

            fclose($output);
        }, $fileName, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    protected function authorizeAnyDatabaseAccess(): void
    {
        if (!$this->hasAnyDatabasePermission([
            'view-base-de-donnees',
            'clear-base-de-donnees',
            'sauvegarde-base-de-donnees',
        ])) {
            abort(403, 'Accès refusé. Une permission base de données est requise.');
        }
    }

    protected function authorizeDatabasePermission(string $permission): void
    {
        if (!$this->hasAnyDatabasePermission([$permission])) {
            abort(403, 'Accès refusé. Permission "' . $permission . '" requise.');
        }
    }

    protected function hasAnyDatabasePermission(array $permissions): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        $userPermissions = $this->getUserPermissionSlugs($user);

        return collect($permissions)->contains(fn ($permission) => in_array($permission, $userPermissions, true));
    }

    protected function getUserPermissionSlugs($user): array
    {
        if (!method_exists($user, 'permissions')) {
            return [];
        }

        $permissions = $user->permissions();

        if ($permissions instanceof \Illuminate\Support\Collection) {
            return $permissions->pluck('slug')->all();
        }

        return $permissions->pluck('slug')->all();
    }

    protected function getDatabaseTables(): array
    {
        return array_map(function ($table) {
            return array_values((array) $table)[0];
        }, DB::select('SHOW TABLES'));
    }
}