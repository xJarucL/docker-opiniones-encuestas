<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // Importante importar Carbon

class Encuesta extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descripcion',
        'categoria_id',
        'estado', // Este actuará como "Interruptor Maestro" (ON/OFF manual)
        'fecha_inicio',
        'fecha_fin',
        'resultados_publicos',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'resultados_publicos' => 'boolean',
    ];

    // --- NUEVO: Atributo Calculado 'status' ---
    // Esto no se guarda en BD, se calcula cada vez que llamas a $encuesta->status
    public function getStatusAttribute()
    {
        // 1. Si está desactivada manualmente
        if (!$this->estado) {
            return 'disabled'; 
        }

        $now = Carbon::now();

        // 2. Programada: Si tiene fecha inicio y aún no llega (usamos startOfDay por seguridad)
        if ($this->fecha_inicio && $now->lessThan($this->fecha_inicio->copy()->startOfDay())) {
            return 'scheduled';
        }

        // 3. Finalizada: AQUÍ ESTÁ EL CAMBIO CLAVE
        // Usamos ->copy()->endOfDay() para que la fecha fin sea a las 23:59:59
        if ($this->fecha_fin && $now->greaterThan($this->fecha_fin->copy()->endOfDay())) {
            return 'finished';
        }

        // 4. Activa
        return 'active';
    }
    // --- NUEVO: Scope para filtrar encuestas visibles al público ---
    // Se usa como: Encuesta::publicas()->get();
public function scopePublicas($query)
    {
        $now = Carbon::now();

        return $query->where('estado', 1)
            ->where(function($q) use ($now) {
                $q->whereNull('fecha_inicio')
                  ->orWhere('fecha_inicio', '<=', $now); // Aquí está bien que sea 00:00
            })
            ->where(function($q) use ($now) {
                $q->whereNull('fecha_fin')
                  // Aquí comparamos contra el final del día de la fecha guardada en BD
                  // Como en SQL es difícil hacer el "endOfDay" dinámico simple,
                  // lo mejor es decir: "Que la fecha fin sea mayor o igual al INICIO de hoy"
                  // O simplemente que la fecha_fin sea >= hoy (formato fecha pura).
                  ->orWhereDate('fecha_fin', '>=', $now->toDateString());
            });
    }
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function preguntas()
    {
        return $this->hasMany(Pregunta::class);
    }
}