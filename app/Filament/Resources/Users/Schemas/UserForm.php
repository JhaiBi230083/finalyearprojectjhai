<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->required(),
                Select::make('role')
                    ->options(['student' => 'Student', 'staff' => 'Staff', 'vendor' => 'Vendor', 'admin' => 'Admin'])
                    ->default('student')
                    ->required(),
                TextInput::make('phone_number')
                    ->tel()
                    ->default(null),
                TextInput::make('faculty')
                    ->default(null),
                TextInput::make('matric_number')
                    ->default(null),
            ]);
    }
}
