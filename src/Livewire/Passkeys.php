<?php

declare(strict_types=1);

namespace MarcelWeidum\Passkeys\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Spatie\LaravelPasskeys\Actions\StorePasskeyAction;
use Spatie\LaravelPasskeys\Livewire\PasskeysComponent;
use Spatie\LaravelPasskeys\Support\Config;

final class Passkeys extends PasskeysComponent implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->label(__('passkeys::passkeys.delete'))
            ->color('danger')
            ->requiresConfirmation()
            ->action(fn (array $arguments) => $this->deletePasskey($arguments['passkey']));
    }

    public function deletePasskey(int $passkeyId): void
    {
        parent::deletePasskey($passkeyId);

        Notification::make()
            ->title(__('filament-passkeys::passkeys.deleted_notification_title'))
            ->success()
            ->send();
    }

    public function storePasskey(string $passkey): void
    {
        $storePasskeyAction = Config::getAction('store_passkey', StorePasskeyAction::class);

        try {
            $storePasskeyAction->execute(
                $this->currentUser(),
                $passkey, $this->previouslyGeneratedPasskeyOptions(),
                request()->getHost(),
                ['name' => $this->name],
                Filament::getCurrentOrDefaultPanel()->getAuthGuard()
            );
        } catch (Throwable $e) {
            throw ValidationException::withMessages([
                'name' => __('passkeys::passkeys.error_something_went_wrong_generating_the_passkey'),
            ])->errorBag('passkeyForm');
        }

        $this->clearForm();

        Notification::make()
            ->title(__('filament-passkeys::passkeys.created_notification_title'))
            ->success()
            ->send();
    }

    public function render(): View
    {
        return view('filament-passkeys::livewire.passkeys', data: [
            'passkeys' => $this->currentUser()->passkeys,
        ]);
    }
}
