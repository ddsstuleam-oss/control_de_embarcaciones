<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CedulaEcuatoriana implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->validar($value)) {
            $fail('La cédula ecuatoriana no es válida.');
        }
    }

    private function validar(string $cedula): bool
    {
        if (strlen($cedula) !== 10 || !ctype_digit($cedula)) {
            return false;
        }

        $provincia = (int) substr($cedula, 0, 2);
        if ($provincia < 1 || $provincia > 24) {
            return false;
        }

        $digitos    = array_map('intval', str_split($cedula));
        $verificador = $digitos[9];
        $suma        = 0;

        for ($i = 0; $i < 9; $i++) {
            $d = $digitos[$i];
            if ($i % 2 === 0) {
                $d *= 2;
                if ($d > 9) $d -= 9;
            }
            $suma += $d;
        }

        $residuo = $suma % 10;
        $resultado = $residuo === 0 ? 0 : 10 - $residuo;

        return $resultado === $verificador;
    }
}