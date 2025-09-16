<?php

declare(strict_types=1);

namespace MarcelWeidum\Passkeys\Http\Controllers;

use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class PasskeyRedirectController extends BaseController
{
    /**
     * This controller redirect authenticated users to the current panel
     */
    public function __invoke(Request $request, string $panelId): RedirectResponse
    {
        $panel = Filament::getPanel($panelId);

        if (! $panel) {
            throw new NotFoundHttpException();
        }

        return redirect()->intended($panel->getUrl());
    }
}
