<?php

namespace App\Filament\Resources\ProfileResource\Pages;

use App\Models\Role;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;

class EditProfile extends BaseEditProfile
{

    protected function getRedirectUrl(): string
    {
        return route('filament.admin.pages.dashboard');
    }

    public function getTitle(): string
    {
        return 'Update Profile';
    }

    public function form(Form $form): Form
    {
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
                // Select::make('roles')
                // ->multiple() // This allows the selection of multiple roles if required
                // ->relationship('roles', 'name') // Relationship to roles
                // ->options(Role::all()->pluck('name', 'id')) // Get all roles from the database
                // ->label('Assign Roles')
                // ->required(),
            ]);
    }
}
