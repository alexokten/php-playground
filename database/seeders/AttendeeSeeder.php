<?php
declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class AttendeeSeeder extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            ['firstName' => 'John', 'lastName' => 'Smith', 'dateOfBirth' => '1990-05-15', 'city' => 'New York', 'isActive' => 1, 'email' => 'johnsmith@example.com'],
            ['firstName' => 'Sarah', 'lastName' => 'Johnson', 'dateOfBirth' => '1985-08-22', 'city' => 'Los Angeles', 'isActive' => 1, 'email' => 'sarahjohnson@example.com'],
            ['firstName' => 'Michael', 'lastName' => 'Brown', 'dateOfBirth' => '1992-12-03', 'city' => 'Chicago', 'isActive' => 1, 'email' => 'michaelbrown@example.com'],
            ['firstName' => 'Emma', 'lastName' => 'Davis', 'dateOfBirth' => '1988-03-17', 'city' => 'Houston', 'isActive' => 1, 'email' => 'emmadavis@example.com'],
            ['firstName' => 'James', 'lastName' => 'Wilson', 'dateOfBirth' => '1995-07-09', 'city' => 'Phoenix', 'isActive' => 0, 'email' => 'jameswilson@example.com'],
            ['firstName' => 'Olivia', 'lastName' => 'Miller', 'dateOfBirth' => '1987-11-30', 'city' => 'Philadelphia', 'isActive' => 1, 'email' => 'oliviamiller@example.com'],
            ['firstName' => 'William', 'lastName' => 'Moore', 'dateOfBirth' => '1993-01-25', 'city' => 'San Antonio', 'isActive' => 1, 'email' => 'williammoore@example.com'],
            ['firstName' => 'Ava', 'lastName' => 'Taylor', 'dateOfBirth' => '1989-09-14', 'city' => 'San Diego', 'isActive' => 1, 'email' => 'avataylor@example.com'],
            ['firstName' => 'Alexander', 'lastName' => 'Anderson', 'dateOfBirth' => '1986-04-06', 'city' => 'Dallas', 'isActive' => 1, 'email' => 'alexanderanderson@example.com'],
            ['firstName' => 'Isabella', 'lastName' => 'Thomas', 'dateOfBirth' => '1994-10-21', 'city' => 'San Jose', 'isActive' => 0, 'email' => 'isabellathomas@example.com'],
            ['firstName' => 'Benjamin', 'lastName' => 'Jackson', 'dateOfBirth' => '1991-06-18', 'city' => 'Austin', 'isActive' => 1, 'email' => 'benjaminjackson@example.com'],
            ['firstName' => 'Sophia', 'lastName' => 'White', 'dateOfBirth' => '1983-02-12', 'city' => 'Jacksonville', 'isActive' => 1, 'email' => 'sophiawhite@example.com'],
            ['firstName' => 'Mason', 'lastName' => 'Harris', 'dateOfBirth' => '1996-08-07', 'city' => 'Fort Worth', 'isActive' => 1, 'email' => 'masonharris@example.com'],
            ['firstName' => 'Charlotte', 'lastName' => 'Martin', 'dateOfBirth' => '1990-12-29', 'city' => 'Columbus', 'isActive' => 1, 'email' => 'charlottemartin@example.com'],
            ['firstName' => 'Lucas', 'lastName' => 'Garcia', 'dateOfBirth' => '1988-05-03', 'city' => 'Charlotte', 'isActive' => 1, 'email' => 'lucasgarcia@example.com'],
        ];

        $attendees = $this->table('attendees');
        $attendees->insert($data)->saveData();
    }
}