<?php
namespace App\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'actualizar:contrasenas')]
class ActualizarContrasenasCommand extends Command
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        parent::__construct();
        $this->connection = $connection;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $usuarios = $this->connection->fetchAllAssociative("SELECT id, contraseña FROM a_login");

        foreach ($usuarios as $usuario) {
            if (!password_get_info($usuario['contraseña'])['algo']) { // Verifica si la contraseña ya está hasheada
                $hashedPassword = password_hash($usuario['contraseña'], PASSWORD_BCRYPT);
                $this->connection->executeStatement("UPDATE a_login SET contraseña = ? WHERE id = ?", [$hashedPassword, $usuario['id']]);
            }
        }

        $output->writeln('✅ Contraseñas actualizadas correctamente.');
        return Command::SUCCESS;
    }
}

