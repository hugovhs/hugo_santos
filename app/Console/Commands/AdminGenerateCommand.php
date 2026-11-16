<?php

namespace App\Console\Commands;

use App\Models\User;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class AdminGenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea un usuario administrador de forma interactiva (nombre, email y contraseña)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('=== Generar nuevo usuario administrador ===');

        $name = $this->askRequired('Nombre');
        $email = $this->askEmail();
        $password = $this->askPassword();

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password
        ]);

        $this->newLine();
        $this->info("Usuario creado correctamente. ID: {$user->id} | Email: {$user->email}");

        return self::SUCCESS;
    }

    /**
     * Pide un texto obligatorio hasta obtener una respuesta válida.
     */
    protected function askRequired(string $label): string
    {
        do {
            $value = trim((string) $this->ask($label));

            if ($value === '') {
                $this->error("El campo {$label} es obligatorio.");
            }
        } while ($value === '');

        return $value;
    }

    /**
     * Pide el email hasta obtener uno válido y no registrado.
     */
    protected function askEmail(): string
    {
        do {
            $email = trim((string) $this->ask('Email'));

            $validator = Validator::make(
                ['email' => $email],
                ['email' => ['required', 'email', 'unique:users,email']],
                [
                    'email.required' => 'El campo Email es obligatorio.',
                    'email.email' => 'Debe ingresar un email válido.',
                    'email.unique' => 'Ya existe un usuario registrado con ese email.',
                ]
            );

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $this->error($error);
                }
            }
        } while ($validator->fails());

        return $email;
    }

    /**
     * Pide la contraseña hasta obtener una válida.
     */
    protected function askPassword(): string
    {
        do {
            $password = (string) $this->secret('Contraseña');

            $validator = Validator::make(
                ['password' => $password],
                ['password' => ['required', 'string', 'min:8']],
                [
                    'password.required' => 'El campo Contraseña es obligatorio.',
                    'password.min' => 'La Contraseña debe tener al menos 8 caracteres.',
                ]
            );

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $this->error($error);
                }
            }
        } while ($validator->fails());

        return $password;
    }
}
