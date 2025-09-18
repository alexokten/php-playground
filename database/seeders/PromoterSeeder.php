<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class PromoterSeeder extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            ['firstName' => 'Alice', 'lastName' => 'Bristol', 'dateOfBirth' => '1980-03-15', 'city' => 'Clifton, Bristol', 'isActive' => 1, 'email' => 'alicebristol@example.com'],
            ['firstName' => 'Bob', 'lastName' => 'Merchant', 'dateOfBirth' => '1975-07-22', 'city' => 'Redland, Bristol', 'isActive' => 1, 'email' => 'bobmerchant@example.com'],
            ['firstName' => 'Carol', 'lastName' => 'Harbourside', 'dateOfBirth' => '1982-11-08', 'city' => 'Harbourside, Bristol', 'isActive' => 1, 'email' => 'carolharbourside@example.com'],
            ['firstName' => 'David', 'lastName' => 'Temple', 'dateOfBirth' => '1978-09-30', 'city' => 'Temple Meads, Bristol', 'isActive' => 1, 'email' => 'davidtemple@example.com'],
            ['firstName' => 'Eva', 'lastName' => 'Southville', 'dateOfBirth' => '1985-05-12', 'city' => 'Southville, Bristol', 'isActive' => 1, 'email' => 'evasouthville@example.com'],
            ['firstName' => 'Frank', 'lastName' => 'Bedminster', 'dateOfBirth' => '1973-12-25', 'city' => 'Bedminster, Bristol', 'isActive' => 0, 'email' => 'frankbedminster@example.com'],
            ['firstName' => 'Grace', 'lastName' => 'Montpelier', 'dateOfBirth' => '1987-01-18', 'city' => 'Montpelier, Bristol', 'isActive' => 1, 'email' => 'gracemontpelier@example.com'],
            ['firstName' => 'Henry', 'lastName' => 'Bishopston', 'dateOfBirth' => '1979-04-03', 'city' => 'Bishopston, Bristol', 'isActive' => 1, 'email' => 'henrybishopston@example.com'],
        ];

        $promoters = $this->table('promoters');
        $promoters->insert($data)->saveData();
    }
}
