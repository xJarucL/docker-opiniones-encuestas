<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // ...
public function up(): void
{
    Schema::table('encuestas', function (Blueprint $table) {
        // Se añade después de la columna 'estado'
        $table->boolean('resultados_publicos')->default(false)->after('estado');
    });
}

public function down(): void
{
    Schema::table('encuestas', function (Blueprint $table) {
        $table->dropColumn('resultados_publicos');
    });
}
};
