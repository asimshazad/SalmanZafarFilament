<?php

namespace App\Filament\Resources\GuestResource\Pages;

use App\Models\Role;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register as AuthRegister;
use Filament\Forms\Components\Component;
use Filament\Forms;

class Register extends AuthRegister
{

    // public function getTitle(): string
    // {
    //     return 'Register';
    // }

    public function form(Form $form): Form
    {
        $roleId = Role::where('name', 'User')->first()?->id;
        return $form
            ->schema([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                FileUpload::make('user_photo')
                ->image()
                ->disk('public')
                ->directory('user_photos')
                ->nullable(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }
}
