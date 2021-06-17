<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Services\Phone\LocalizePhoneNumberTransformator;
use App\Models\GuestBook;
use Illuminate\Http\Request;

class SignUpAction extends Controller
{
    private $localizeTransformator;

    public function __construct(LocalizePhoneNumberTransformator $localizeTransformator)
    {
        $this->localizeTransformator = $localizeTransformator;
    }

    public function __invoke(Request $request)
    {
        $this->validate($request, [
            'phoneNumber' => 'required|string|min:4|max:20',
            'name' => 'required|string|min:1',
            'email' => 'required|email'
        ]);

        $guestBook = new GuestBook([
            'phone_number' => $this->localizeTransformator->transform($request->get('phoneNumber')),
            'name' => $request->get('name'),
            'email' => $request->get('email')
        ]);

        $guestBook->save();

        return $this->processSucceed();
    }
}
