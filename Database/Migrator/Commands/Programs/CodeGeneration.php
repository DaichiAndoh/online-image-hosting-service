<?php

namespace Database\Migrator\Commands\Programs;

use Database\Migrator\Commands\AbstractCommand;
use Database\Migrator\Commands\Argument;

class CodeGeneration extends AbstractCommand {
    // 使用するコマンド名を設定
    protected static ?string $alias = 'code-gen';
    protected static bool $requiredCommandValue = true;

    // 引数を割り当て
    public static function getArguments(): array {
        return [
            (new Argument('name'))->description('Name of the file that is to be generated.')->required(true),
        ];
    }

    public function execute(): int {
        $codeGenType = $this->getCommandValue();
        $this->log('Generating code for.......' . $codeGenType);

        if ($codeGenType === 'migration') {
            $migrationName = $this->getArgumentValue('name');
            $this->generateMigrationFile($migrationName);
        }

        return 0;
    }

    private function generateMigrationFile(string $migrationName): void {
        $filename = sprintf(
            '%s_%s_%s.php',
            date('Y-m-d'),
            time(),
            $migrationName
        );

        $migrationContent = $this->getMigrationContent($migrationName);

        // 移行ファイルを保存するパスを指定
        $path = sprintf("%s/../../../../Database/Migrations/%s", __DIR__, $filename);

        file_put_contents($path, $migrationContent);
        $this->log("Migration file {$filename} has been generated!");
    }

    private function getMigrationContent(string $migrationName): string {
        $className = $this->pascalCase($migrationName);

        return <<<MIGRATION
<?php

namespace Database\Migrations;

use Database\Migrator\SchemaMigration;

class {$className} implements SchemaMigration {
    public function up(): array {
        // マイグレーションロジックをここに追加
        return [];
    }

    public function down(): array {
        // ロールバックロジックを追加
        return [];
    }
}
MIGRATION;
    }

    private function pascalCase(string $string): string {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }
}
